<?php

namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Translit;
use Okay\Core\Modules\Extender\ExtenderFacade;

class FeaturesValuesEntity extends Entity
{
    protected static $fields = [
        'id',
        'feature_id',
        'position',
        'to_index',
        'external_id',
    ];

    protected static $langFields = [
        'value',
        'translit',
        'position',
    ];

    protected static $defaultOrderFields = [
        'position ASC',
        'value ASC',
    ];

    protected static $searchFields = [
        'value',
    ];

    protected static $table = '__features_values';
    protected static $langObject = 'feature_value';
    protected static $langTable = 'features_values';
    protected static $tableAlias = 'fv';

    /*добавление значения свойства*/
    public function add($featureValue)
    {

        $featureValue = (object)$featureValue;
        /** @var object{value: string|null, feature_id: mixed, translit: string|null}&\stdClass $featureValue */

        if ($featureValue->value === null || $featureValue->value === '' || empty($featureValue->feature_id)) {
            return false;
        }

        $featureValue->value = trim($featureValue->value);

        if (empty($featureValue->translit)) {
            $featureValue->translit = Translit::translitAlpha($featureValue->value);
        }
        $featureValue->translit = Translit::translitAlpha($featureValue->translit);

        return parent::add($featureValue);
    }

    /*Обновление значения свойства*/
    public function update($ids, $featureValue)
    {
        $featureValue = (object)$featureValue;
        /** @var object{value: string|null, translit: string|null}&\stdClass $featureValue */

        if (!empty($featureValue->value)) {
            $featureValue->value = trim($featureValue->value);
        }

        if (empty($featureValue->translit)) {
            // если не передали транслит, попробуем найти его в базе
            if ($translit = $this->cols(['translit'])->findOne(['id' => $ids])) {
                $featureValue->translit = $translit;
            } elseif (!empty($featureValue->value)) {
                $featureValue->translit = Translit::translitAlpha($featureValue->value);
            }
        }

        if (!empty($featureValue->translit)) {
            $featureValue->translit = Translit::translitAlpha($featureValue->translit);
        }

        return parent::update($ids, $featureValue);
    }

    public function find(array $filter = [])
    {
        $this->select->groupBy([$this->getTableAlias() . '.id']);

        // ВАЖНО: Обробляємо feature_id ПЕРЕД autoFilter, щоб правильно розгорнути масив
        // autoFilter не розгортає масиви для підзапитів, тому потрібна вручна обробка
        $featureIdProcessed = false;
        if (isset($filter['feature_id']) && is_array($filter['feature_id'])) {
            // Якщо це асоціативний масив (наприклад, {"1":1,"10":10,...}), конвертуємо в простий масив
            $featureIds = array_values($filter['feature_id']);

            // Розгортаємо масив на окремі плейсхолдери
            $expandedBindValues = [];
            $placeholders = [];
            foreach ($featureIds as $index => $featureId) {
                $placeholder = 'feature_id_expanded_' . $index;
                $placeholders[] = ':' . $placeholder;
                $expandedBindValues[$placeholder] = (int)$featureId;
            }

            // Додаємо where з розгорнутим списком
            $tableAlias = $this->getTableAlias();
            $this->select->where("{$tableAlias}.feature_id IN (" . implode(', ', $placeholders) . ")");
            $this->select->bindValues($expandedBindValues);

            // Видаляємо feature_id з фільтра, щоб autoFilter не додав свою умову
            unset($filter['feature_id']);
            $featureIdProcessed = true;
        }

        // ВАЖНО: Використовуємо LEFT JOIN для звичайного пошуку значень
        // Навіть якщо є фільтр по features, нам потрібно використовувати LEFT JOIN,
        // щоб отримати всі значення властивостей, навіть якщо вони не мають зв'язку з товарами з підзапиту
        // Фільтрація по товарах з підзапиту відбувається через WHERE pf.product_id IN (subquery)
        $this->select->join('LEFT', '__products_features_values AS pf', 'pf.value_id=fv.id');

        // Нужно фильтр по свойствам и другим параметрам (visible/in_stock/price) применить до остальных JOIN-ов.
        // JOIN с таблицей __products (алиас p) нужен, если:
        //  - есть фильтр по свойствам (features), т.к. filter__features использует p.id
        //  - або є visible/in_stock/price, т.к. соответствующие фильтры звертаються до p.*
        $needProductsJoin = isset($filter['features'])
            || isset($filter['visible'])
            || isset($filter['in_stock'])
            || isset($filter['price']);

        if ($needProductsJoin) {
            $this->select->join('LEFT', '__products AS p', 'p.id=pf.product_id');
        }

        // Нужно фильтр по свойствам применить здесь, чтобы он отработал до всех джоинов
        if (isset($filter['features'])) {
            $this->filter__features($filter['features']);
            unset($filter['features']);
        }

        $this->select->join('LEFT', '__features AS f', 'f.id=fv.feature_id');
        //$this->select->groupBy(['l.value']); // TODO: разобраться, вроде не нужная группировка
        //$this->select->groupBy(['l.translit']);

        $result = parent::find($filter);

        return $result;
    }

    protected function filter__in_stock()
    {
        if ($this->settings->get('is_preorder')) {
            return;
        }

        $this->select->where("(SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1");
    }

    /*Удаление значения свойства*/
    public function delete($ids = null)
    {
        if (parent::delete($ids)) {
            $ids = (array)$ids;
            /** @var list<int> $ids */
            $ids = array_values(array_map('intval', $ids));

            $this->deleteProductValue(null, $ids);
            $this->deleteAliases($ids);
        }

        return parent::delete($ids);
    }

    /**
     * @param list<int> $valuesIds
     */
    public function countProductsByValueId(array $valuesIds)
    {
        if (empty($valuesIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
        }

        $select = $this->queryFactory->newSelect();
        $select->cols([
            'COUNT(product_id) AS count',
            'value_id',
        ])
            ->from('__products_features_values')
            ->where('value_id IN (?)', $valuesIds)
            ->groupBy(['value_id']);

        $this->db->query($select);
        $count = $this->db->results(null, 'value_id');

        return ExtenderFacade::execute([static::class, __FUNCTION__], $count, func_get_args());
    }

    protected function filter__product_id($productsIds)
    {
        $this->select->where('pf.product_id IN (:products_ids)')
            ->bindValue('products_ids', (array)$productsIds);
    }

    protected function filter__brand_id($brandsIds)
    {
        $this->select->where('pf.product_id IN (SELECT id FROM __products WHERE brand_id IN (:brands_ids))')
            ->bindValue('brands_ids', (array)$brandsIds);
    }

    protected function filter__features($features)
    {
        // ВАЖНО: Створюємо один підзапит для всіх features, як у ProductsEntity
        // Це дозволяє уникнути конфліктів bind-значень та проблем з кількома joinSubSelect
        $subQuery = $this->queryFactory->newSelect();
        $allValueIds = [];
        $featuresConditions = [];

        foreach ($features as $featureId => $value) {
            // ВАЖНО: Структура $features: [featureId => [valueId => translit, ...]]
            // Тобто $value - це асоціативний масив, де ключі - це ID значень (value_id)
            // Нам потрібні тільки ключі (ID значень), а не значення (translit)
            if (empty($value) || !is_array($value)) {
                continue;
            }

            // Отримуємо масив ID значень з ключів асоціативного масиву
            $valueIds = array_keys($value);
            $allValueIds = array_merge($allValueIds, $valueIds);

            // Додаємо умову для поточної властивості
            // ВАЖНО: Використовуємо (int)$featureId для типізації індексів (згідно з docs/migration/aura-74-8.md)
            $placeholderValueIds = "value_ids_" . (int)$featureId;
            $placeholderFeatureId = "feature_id_" . (int)$featureId;

            $featuresConditions[] = "(pf_sub.value_id IN (:{$placeholderValueIds}) AND fv_sub.feature_id=:{$placeholderFeatureId})";

            // Прив'язуємо значення до підзапиту
            // ВАЖНО: Примусове приведення до масиву для безпеки IN (?) (згідно з docs/migration/aura-74-8.md)
            $subQuery->bindValue($placeholderValueIds, (array)$valueIds);
            $subQuery->bindValue($placeholderFeatureId, (int)$featureId);
        }

        if (empty($featuresConditions)) {
            return;
        }

        // Створюємо один підзапит для всіх features
        // ВАЖНО: Використовуємо INNER JOIN замість LEFT JOIN, щоб уникнути проблем з NULL значеннями
        $subQuery->from('__products_features_values AS pf_sub')
            ->cols(['DISTINCT(pf_sub.product_id) as product_id'])
            ->where('(' . implode(' OR ', $featuresConditions) . ')')
            ->join('INNER', '__features_values AS fv_sub', 'fv_sub.id=pf_sub.value_id')
            ->groupBy(['pf_sub.product_id'])
            ->having('COUNT(DISTINCT fv_sub.feature_id) >= ' . count($features));

        // ВАЖНО: Розгортаємо масиви в біндінгах для підзапиту, оскільки perform() може не розгорнути
        // масиви для плейсхолдерів підзапиту (підзапит вже є частиною SQL-рядка)
        // Метод perform() автоматично замінює IN (:id) на IN (:id_0, :id_1, ...) для масивів,
        // але це працює тільки для основних запитів, а не для підзапитів
        $subQueryBindValues = $subQuery->getBindValues();
        $expandedBindValues = [];
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                // Розгортаємо масив на окремі плейсхолдери
                foreach ($value as $index => $item) {
                    $expandedBindValues[$key . '_' . $index] = $item;
                }
            } else {
                $expandedBindValues[$key] = $value;
            }
        }

        // Замінюємо IN (:placeholder) на IN (:placeholder_0, :placeholder_1, ...) в SQL
        // ВАЖНО: Використовуємо str_ireplace для заміни всіх входжень (якщо є кілька однакових плейсхолдерів)
        $subQueryStatement = $subQuery->getStatement();
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $index => $item) {
                    $placeholders[] = ':' . $key . '_' . $index;
                }
                // Замінюємо всі входження, не тільки перше
                $subQueryStatement = str_ireplace('IN (:' . $key . ')', 'IN (' . implode(', ', $placeholders) . ')', $subQueryStatement);
            }
        }

        // Для поиска значений других свойств в товарах с выбранной свойством
        // ВАЖНО: Використовуємо joinSubSelect з INNER JOIN замість WHERE p.id IN (subquery)
        // тому що це дозволяє знайти значення інших властивостей для товарів, які мають обране значення властивості
        // Використовуємо INNER JOIN, щоб обмежити результати тільки товарами з підзапиту
        // З'єднуємо підзапит з основним запитом через pf.product_id
        // ВАЖНО: Передаємо об'єкт Select замість getStatement(), як у інших місцях (AbstractPresetAdapter)
        // Aura SQL Query автоматично обробить bind-значення з підзапиту
        // Це дозволяє уникнути проблем з обробкою плейсхолдерів в getStatement()
        // ВАЖНО: Використовуємо joinSubSelect з getStatement(), як у ProductsEntity
        // Але передаємо bindValues ДО joinSubSelect, як у ProductsEntity
        // Це критично важливо для правильної обробки підзапитів в Aura SQL Query

        // ВАЖНО: Об'єднуємо біндінги з попередніми, а не перезаписуємо їх
        // Це критично важливо, коли є кілька фільтрів по характеристикам
        $existingBindValues = $this->select->getBindValues();
        $mergedBindValues = array_merge($existingBindValues, $expandedBindValues);

        // Передаємо об'єднані біндінги в основний запит
        $this->select->bindValues($mergedBindValues);

        $joinAlias = 'products_with_features';
        $this->select->joinSubSelect(
            'INNER',
            $subQueryStatement,
            $joinAlias,
            $joinAlias . '.product_id = pf.product_id'
        );
    }

    /**
     * @param array<string, mixed> $price product price filter (e.g. min/max), passed to {@see ProductsEntity::getSelect()}
     */
    protected function filter__price(array $price)
    {
        $productsEntity = $this->entity->get(ProductsEntity::class);

        $productsSelect = $productsEntity->getSelect(['price' => $price, 'visible' => 1]);

        $productsSelect
            ->resetCols()
            ->resetOrderBy();

        $productsSelect
            ->join(
                'LEFT',
                '__products_features_values AS pfv',
                'pfv.product_id = ' . ProductsEntity::getTableAlias() . '.id'
            )
            ->cols(['pfv.value_id as product_value_id']);

        // ВАЖНО: Передаємо bindValues ДО joinSubSelect (згідно з docs/migration/aura-74-8.md та FILTER_BUG_SUMMARY.md)
        // Це критично важливо для правильної обробки підзапитів в Aura SQL Query
        // Метод getStatement() повертає лише SQL-рядок, тому біндінги потрібно передавати вручну
        $subQueryBindValues = $productsSelect->getBindValues();

        // ВАЖНО: Розгортаємо масиви в біндінгах для підзапиту, оскільки perform() може не розгорнути
        // масиви для плейсхолдерів підзапиту (підзапит вже є частиною SQL-рядка)
        // Метод perform() автоматично замінює IN (:id) на IN (:id_0, :id_1, ...) для масивів,
        // але це працює тільки для основних запитів, а не для підзапитів
        $expandedBindValues = [];
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                // Розгортаємо масив на окремі плейсхолдери
                foreach ($value as $index => $item) {
                    $expandedBindValues[$key . '_' . $index] = $item;
                }
            } else {
                $expandedBindValues[$key] = $value;
            }
        }

        // Замінюємо IN (:placeholder) на IN (:placeholder_0, :placeholder_1, ...) в SQL
        // ВАЖНО: Використовуємо str_ireplace для заміни всіх входжень (якщо є кілька однакових плейсхолдерів)
        $subQueryStatement = $productsSelect->getStatement();
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $index => $item) {
                    $placeholders[] = ':' . $key . '_' . $index;
                }
                // Замінюємо всі входження, не тільки перше
                $subQueryStatement = str_ireplace('IN (:' . $key . ')', 'IN (' . implode(', ', $placeholders) . ')', $subQueryStatement);
            }
        }

        // ВАЖНО: Об'єднуємо біндінги з попередніми, а не перезаписуємо їх
        // Це критично важливо, коли є кілька фільтрів
        $existingBindValues = $this->select->getBindValues();
        $mergedBindValues = array_merge($existingBindValues, $expandedBindValues);

        // Передаємо об'єднані біндінги в основний запит
        $this->select->bindValues($mergedBindValues);

        $this->select->joinSubSelect(
            'INNER',
            $subQueryStatement,
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias(),
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias() . '.product_value_id = fv.id'
        );
    }

    protected function filter__category_id($categoriesIds)
    {
        // Берём значения тех свойств, которые назначены на указанные категории
        $this->select->join('INNER', '__categories_features AS cf', 'cf.feature_id=f.id AND cf.category_id IN (:category_id)')
            ->bindValue('category_id', (array)$categoriesIds);
    }

    protected function filter__have_products_in_categories($categoriesIds)
    {
        $this->select->join('INNER', '__products_categories AS pc', 'pc.product_id=pf.product_id AND pc.category_id IN (:category_id)')
            ->bindValue('category_id', (array)$categoriesIds);
    }

    protected function filter__visible($visible)
    {
        $this->select->where('p.visible=:visible')
            ->bindValue('visible', (int)$visible);
    }

    protected function filter__other_filter($filters)
    {
        if (empty($filters)) {
            return;
        }

        if ($otherFilter = $this->executeOtherFilter($filters)) {
            $this->select->where("(" . implode(' OR ', $otherFilter) . ")");
        }
    }

    private function executeOtherFilter($filters)
    {
        $otherFilter = [];
        if (in_array("featured", $filters)) {
            $otherFilter[] = 'pf.product_id IN (SELECT id FROM __products WHERE featured=1)';
        }

        if (in_array("discounted", $filters)) {
            $otherFilter[] = '(SELECT 1 FROM __variants pv WHERE pv.product_id=pf.product_id AND pv.compare_price>pv.price LIMIT 1) = 1';
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $otherFilter, func_get_args());
    }


    /**
     * Метод возвращает только мультиязычные поля значений свойств, используется для построения alternate на странице фильтра
     *
     * @param array<int|string, list<int|string>> $features example: $features[feature_id] = [value1_id, value2_id ...]
     * @return array<int|string, array<int|string, array<int|string, object{translit: string}&\stdClass>>> lang_id => feature_id => value_id => row object
     */
    public function getFeaturesValuesAllLang($features = [])
    {

        if (empty($features)) {
            return [];
        }

        $select = $this->queryFactory->newSelect();
        $select->from('__lang_features_values AS lv')
            ->cols([
                'lv.lang_id',
                'lv.feature_value_id',
                'lv.value',
                'lv.translit',
                'fv.feature_id',
            ])
            ->join('left', '__features_values AS fv', 'fv.id = lv.feature_value_id');

        foreach ($features as $featureId => $valuesIds) {
            if (!empty($valuesIds)) {
                $select->orWhere("(fv.feature_id=:feature_id_{$featureId} AND feature_value_id IN (:values_ids_{$featureId}))")
                    ->bindValues([
                        "feature_id_{$featureId}" => $featureId,
                        "values_ids_{$featureId}" => $valuesIds,
                    ]);
            }
        }

        $result = [];
        $this->db->query($select);
        foreach ($this->db->results() as $res) {
            $result[$res->lang_id][$res->feature_id][$res->feature_value_id] = $res;
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    /*добавление значения свойства товара*/
    public function addProductValue($productId, $valueId)
    {

        if (empty($productId) || empty($valueId)) {
            return false;
        }

        $insert = $this->queryFactory->newInsert();
        $insert->into('__products_features_values')
            ->cols([
                'product_id',
                'value_id',
            ])
            ->bindValues([
                'product_id' => $productId,
                'value_id' => $valueId,
            ])
            ->ignore();

        if ($this->db->query($insert)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
    }

    /**
     * Метод возвращает ID всех значений свойств товаров
     *
     * @param list<int> $productIds
     * @return list<object>
     * @throws \Exception
     */
    public function getProductValuesIds(array $productIds)
    {

        if (empty($productIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
        }

        $select = $this->queryFactory->newSelect();
        $select->from('__products_features_values')
            ->cols([
                'product_id',
                'value_id',
            ])
            ->where('product_id IN (:product_id)')
            ->bindValue('product_id', $productIds);

        if ($this->db->query($select)) {
            $results = $this->db->results();
            return ExtenderFacade::execute([static::class, __FUNCTION__], $results, func_get_args());
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
    }

    /*удаление связки значения свойства и товара*/
    public function deleteProductValue($productsIds, $valuesIds = null, $featuresIds = null)
    {
        $productIdFilter  = '';
        $valueIdFilter    = '';
        $featureIdFilter  = '';
        $featureIdJoin    = '';

        /*Удаляем только если передали хотябы один аргумент*/
        if (empty($productsIds) && empty($valuesIds) && empty($featuresIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        if (!empty($productsIds)) {
            $productIdFilter = "AND `pf`.`product_id` in (" . implode(',', (array)$productsIds) . ")";
        }

        if (!empty($valuesIds)) {
            $valueIdFilter = "AND `pf`.`value_id` in (" . implode(',', (array)$valuesIds) . ")";
        }

        if (!empty($featuresIds)) {
            $featureIdFilter = "AND `fv`.`feature_id` in (" . implode(',', (array)$featuresIds) . ")";
            $featureIdJoin   = "INNER JOIN `__features_values` as `fv` ON `pf`.`value_id`=`fv`.`id`";
        }

        $sql = $this->queryFactory->newSqlQuery();
        $sql->setStatement("DELETE `pf`
                                FROM `__products_features_values` as `pf`
                                    $featureIdJoin
                                WHERE 1
                                    $productIdFilter
                                    $valueIdFilter
                                    $featureIdFilter
                                    ");
        $this->db->query($sql);

        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    /**
     * @param list<int> $ids
     * @throws \Exception
     */
    public function deleteAliases(array $ids): void
    {
        if (!empty($ids)) {
            $this->queryFactory->newDelete()
                ->from(FeaturesValuesAliasesValuesEntity::getTable())
                ->where('feature_value_id IN (:ids)')
                ->bindValues(['ids' => $ids])
                ->execute();
        }
    }

    protected function filter__product_keyword($keyword)
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entity->get(ProductsEntity::class);

        $productsSelect = $productsEntity->getSelect(['keyword' => $keyword, 'visible' => 1]);

        $productsSelect
            ->resetCols()
            ->resetOrderBy();

        $productsSelect
            ->join(
                'LEFT',
                '__products_features_values AS pfv',
                'pfv.product_id = ' . ProductsEntity::getTableAlias() . '.id'
            )
            ->cols(['pfv.value_id as product_value_id']);

        // ВАЖНО: Передаємо bindValues ДО joinSubSelect (згідно з docs/migration/aura-74-8.md та FILTER_BUG_SUMMARY.md)
        // Це критично важливо для правильної обробки підзапитів в Aura SQL Query
        // Метод getStatement() повертає лише SQL-рядок, тому біндінги потрібно передавати вручну
        $subQueryBindValues = $productsSelect->getBindValues();

        // ВАЖНО: Розгортаємо масиви в біндінгах для підзапиту, оскільки perform() може не розгорнути
        // масиви для плейсхолдерів підзапиту (підзапит вже є частиною SQL-рядка)
        // Метод perform() автоматично замінює IN (:id) на IN (:id_0, :id_1, ...) для масивів,
        // але це працює тільки для основних запитів, а не для підзапитів
        $expandedBindValues = [];
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                // Розгортаємо масив на окремі плейсхолдери
                foreach ($value as $index => $item) {
                    $expandedBindValues[$key . '_' . $index] = $item;
                }
            } else {
                $expandedBindValues[$key] = $value;
            }
        }

        // Замінюємо IN (:placeholder) на IN (:placeholder_0, :placeholder_1, ...) в SQL
        // ВАЖНО: Використовуємо str_ireplace для заміни всіх входжень (якщо є кілька однакових плейсхолдерів)
        $subQueryStatement = $productsSelect->getStatement();
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $index => $item) {
                    $placeholders[] = ':' . $key . '_' . $index;
                }
                // Замінюємо всі входження, не тільки перше
                $subQueryStatement = str_ireplace('IN (:' . $key . ')', 'IN (' . implode(', ', $placeholders) . ')', $subQueryStatement);
            }
        }

        // ВАЖНО: Об'єднуємо біндінги з попередніми, а не перезаписуємо їх
        // Це критично важливо, коли є кілька фільтрів
        $existingBindValues = $this->select->getBindValues();
        $mergedBindValues = array_merge($existingBindValues, $expandedBindValues);

        // Передаємо об'єднані біндінги в основний запит
        $this->select->bindValues($mergedBindValues);

        $this->select->joinSubSelect(
            'INNER',
            $subQueryStatement,
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias(),
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias() . '.product_value_id = fv.id'
        );
    }

    protected function filter__brand($value)
    {
        $productsEntity = $this->entity->get(ProductsEntity::class);

        $productsSelect = $productsEntity->getSelect(['brand' => $value, 'visible' => 1]);

        $productsSelect
            ->resetCols()
            ->resetOrderBy();

        $productsSelect
            ->join(
                'LEFT',
                '__products_features_values AS pfv',
                'pfv.product_id = ' . ProductsEntity::getTableAlias() . '.id'
            )
            ->cols(['pfv.value_id as product_value_id']);

        // ВАЖНО: Передаємо bindValues ДО joinSubSelect (згідно з docs/migration/aura-74-8.md та FILTER_BUG_SUMMARY.md)
        // Це критично важливо для правильної обробки підзапитів в Aura SQL Query
        // Метод getStatement() повертає лише SQL-рядок, тому біндінги потрібно передавати вручну
        $subQueryBindValues = $productsSelect->getBindValues();

        // ВАЖНО: Розгортаємо масиви в біндінгах для підзапиту, оскільки perform() може не розгорнути
        // масиви для плейсхолдерів підзапиту (підзапит вже є частиною SQL-рядка)
        // Метод perform() автоматично замінює IN (:id) на IN (:id_0, :id_1, ...) для масивів,
        // але це працює тільки для основних запитів, а не для підзапитів
        $expandedBindValues = [];
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                // Розгортаємо масив на окремі плейсхолдери
                foreach ($value as $index => $item) {
                    $expandedBindValues[$key . '_' . $index] = $item;
                }
            } else {
                $expandedBindValues[$key] = $value;
            }
        }

        // Замінюємо IN (:placeholder) на IN (:placeholder_0, :placeholder_1, ...) в SQL
        // ВАЖНО: Використовуємо str_ireplace для заміни всіх входжень (якщо є кілька однакових плейсхолдерів)
        $subQueryStatement = $productsSelect->getStatement();
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $index => $item) {
                    $placeholders[] = ':' . $key . '_' . $index;
                }
                // Замінюємо всі входження, не тільки перше
                $subQueryStatement = str_ireplace('IN (:' . $key . ')', 'IN (' . implode(', ', $placeholders) . ')', $subQueryStatement);
            }
        }

        // ВАЖНО: Об'єднуємо біндінги з попередніми, а не перезаписуємо їх
        // Це критично важливо, коли є кілька фільтрів
        $existingBindValues = $this->select->getBindValues();
        $mergedBindValues = array_merge($existingBindValues, $expandedBindValues);

        // Передаємо об'єднані біндінги в основний запит
        $this->select->bindValues($mergedBindValues);

        $this->select->joinSubSelect(
            'INNER',
            $subQueryStatement,
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias(),
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias() . '.product_value_id = fv.id'
        );
    }

    protected function filter__selected_features($selectedFeatures)
    {
        $statements = [];
        $bindValues = [];

        foreach ($selectedFeatures as $featureId => $featureValuesTranslits) {
            // ВАЖНО: Використовуємо іменовані плейсхолдери замість позиційних `?`
            // Це дозволяє уникнути проблем з числовими індексами в біндінгах
            // Позиційні плейсхолдери `?` з числовими індексами викликають помилку "Undefined array key" в AbstractQuery.php:437
            $placeholderFeatureId = "filter_feature_id_" . (int)$featureId;
            $placeholderTranslit = "filter_translit_" . (int)$featureId;

            $statements[] = "fv.feature_id = :{$placeholderFeatureId} AND l.translit IN (:{$placeholderTranslit})";

            // ВАЖНО: Примусове приведення до масиву для безпеки IN (?) (згідно з docs/migration/aura-74-8.md)
            $bindValues[$placeholderFeatureId] = (int)$featureId;
            $bindValues[$placeholderTranslit] = (array)$featureValuesTranslits;
        }

        $statement = '((' . implode(') OR (', $statements) . '))';

        // ВАЖНО: Використовуємо bindValues() замість розгортання масиву через ...$binds
        // Це дозволяє уникнути проблем з числовими індексами в біндінгах
        $this->select->where($statement);
        $this->select->bindValues($bindValues);
    }
}
