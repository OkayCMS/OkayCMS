<?php

namespace Okay\Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Translit;
use Okay\Core\Image;

class BrandsEntity extends Entity
{
    protected static $fields = [
        'id',
        'url',
        'image',
        'last_modify',
        'visible',
        'position',
    ];

    protected static $langFields = [
        'name',
        'name_h1',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'annotation',
        'description'
    ];

    protected static $searchFields = [
        'name',
        'meta_keywords',
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = '__brands';
    protected static $langObject = 'brand';
    protected static $langTable = 'brands';
    protected static $tableAlias = 'b';
    protected static $alternativeIdField = 'url';

    public function find(array $filter = [])
    {
        $this->select->distinct(true);
        $this->select->join('left', '__products AS p', 'p.brand_id=b.id');
        return parent::find($filter);
    }

    public function count(array $filter = [])
    {
        $this->select->join('left', '__products AS p', 'p.brand_id=b.id');
        return parent::count($filter);
    }

    protected function filter__product_visible($productVisible)
    {
        $this->select->where('p.visible = ' . (int)$productVisible);
    }

    protected function filter__product_id($productsIds)
    {
        $this->select->where('p.id IN (:products_ids)');
        $this->select->bindValue('products_ids', (array)$productsIds);
    }

    protected function filter__category_id($categoryId)
    {
        $this->select->join('LEFT', '__products_categories pc', 'p.id = pc.product_id');
        $this->select->where('pc.category_id IN (:categories_ids)')
            ->bindValue('categories_ids', (array)$categoryId);
    }

    protected function filter__selected_brands($brandsIds)
    {
        $this->select->orWhere('b.id IN (:selected_brands)')
            ->bindValue('selected_brands', (array)$brandsIds);
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
            $otherFilter[] = 'p.featured=1';
        }

        if (in_array("discounted", $filters)) {
            $otherFilter[] = '(SELECT 1 FROM __variants pv WHERE pv.product_id=p.id AND pv.compare_price>pv.price LIMIT 1) = 1';
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $otherFilter, func_get_args());
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
            ->resetOrderBy()
            ->cols([ProductsEntity::getTableAlias() . '.brand_id']);

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
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias() . '.brand_id = b.id'
        );
    }

    protected function filter__features($features, $filter)
    {
        $subQuery = $this->queryFactory->newSelect();
        // Алиас для таблицы без языков
        $optionsPx = 'fv';

        if (!empty($this->lang->getLangId())) {
            $subQuery->where('lfv.lang_id=' . (int)$this->lang->getLangId())
                ->join('LEFT', '__lang_features_values AS lfv', 'pf.value_id=lfv.feature_value_id');
            // Алиас для таблицы с языками
            $optionsPx = 'lfv';
        }

        foreach ($features as $featureId => $value) {
            // Используем типизированные имена плейсхолдеров для Aura SQL (см. aura-sql-query-builder skill)
            $placeholderTranslitKey = 'filter_translit_features_' . (int)$featureId;
            $placeholderFeatureIdKey = 'filter_feature_id_features_' . (int)$featureId;

            $featuresValues[] = "({$optionsPx}.translit IN (:{$placeholderTranslitKey}) AND fv.feature_id=:{$placeholderFeatureIdKey})";
            $subQuery->bindValues([
                $placeholderTranslitKey => (array)$value,
                $placeholderFeatureIdKey => (int)$featureId,
            ]);
        }

        if (empty($featuresValues)) {
            return;
        }

        if (!empty($filter['product_visible'])) {
            $subQuery->join('LEFT', '__products AS p', 'p.id=pf.product_id')
                ->where('p.visible = ' . (int)$filter['product_visible']);
        }

        $subQuery->from('__products_features_values AS pf')
            ->cols(['pf.product_id'])
            ->where('(' . implode(' OR ', $featuresValues) . ')')
            ->join('LEFT', '__features_values AS fv', 'fv.id=pf.value_id')
            ->groupBy(['pf.product_id'])
            ->having('COUNT(*) >=' . count($features));

        // ВАЖНО: вместо "WHERE p.id IN (?)" с subquery используем joinSubSelect,
        // а также заранее передаём bindValues, разворачивая массивы (см. aura-sql-query-builder skill)
        $subQueryBindValues = $subQuery->getBindValues();

        // Разворачиваем массивы плейсхолдеров вида IN (:placeholder) в :placeholder_0, :placeholder_1, ...
        $expandedBindValues = [];
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $index => $item) {
                    $expandedBindValues[$key . '_' . $index] = $item;
                }
            } else {
                $expandedBindValues[$key] = $value;
            }
        }

        // Заменяем IN (:placeholder) на IN (:placeholder_0, :placeholder_1, ...) в SQL подзапроса
        $subQueryStatement = $subQuery->getStatement();
        foreach ($subQueryBindValues as $key => $value) {
            if (is_array($value)) {
                $placeholders = [];
                foreach ($value as $index => $item) {
                    $placeholders[] = ':' . $key . '_' . $index;
                }
                $subQueryStatement = str_ireplace('IN (:' . $key . ')', 'IN (' . implode(', ', $placeholders) . ')', $subQueryStatement);
            }
        }

        // Объединяем новые бинд‑значения с уже существующими в основном запросе
        $existingBindValues = $this->select->getBindValues();
        $mergedBindValues = array_merge($existingBindValues, $expandedBindValues);
        $this->select->bindValues($mergedBindValues);

        // Теперь подключаем подзапрос через joinSubSelect
        $subQueryAlias = __FUNCTION__ . '__pf';
        $this->select->joinSubSelect(
            'INNER',
            $subQueryStatement,
            $subQueryAlias,
            $subQueryAlias . '.product_id = p.id'
        );
    }

    public function add($brand)
    {
        /** @var Translit $translit */
        $translit = $this->serviceLocator->getService(Translit::class);

        $brand = (object)$brand;
        /** @var object{name: string, url?: string|null}&\stdClass $brand */
        if (empty($brand->url)) {
            $brand->url = $translit->translit($brand->name);
            $brand->url = str_replace('.', '', $brand->url);
        }

        $brand->url = preg_replace("/[\s]+/ui", '', $brand->url);

        while ($this->get((string)$brand->url)) {
            if (preg_match('/(.+)([0-9]+)$/', $brand->url, $parts)) {
                $brand->url = $parts[1] . '' . ($parts[2] + 1);
            } else {
                $brand->url = $brand->url . '2';
            }
        }

        return parent::add($brand);
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (empty($ids)) {
            parent::delete($ids);
        }

        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);
        foreach ($ids as $id) {
            $imageCore->deleteImage(
                $id,
                'image',
                self::class,
                $this->config->original_brands_dir,
                $this->config->resized_brands_dir
            );
        }

        $update = $this->queryFactory->newUpdate();
        $update->table(ProductsEntity::getTable())
            ->set('brand_id', 0)
            ->where('brand_id IN (:brands_ids)')
            ->bindValue('brands_ids', $ids);
        $this->db->query($update);

        parent::delete($ids);
    }

    public function duplicate($brandId)
    {
        $brand = $this->findOne(['id' => $brandId]);
        if ($brand === false) {
            return false;
        }
        /** @var object{position: int|string|float}&\stdClass $brand */

        //Запоминаем текущую позицию, на нее станет новая запись
        $position = $brand->position;

        $newBrand = new \stdClass();

        $fields = array_merge($this->getFields(), $this->getLangFields());

        foreach ($fields as $field) {
            if (!empty($field) && property_exists($brand, $field)) {
                $newBrand->$field = $brand->$field;
            }
        }

        $newBrand->id = null;
        $newBrand->url = '';

        //Добавляем новую запись в бд
        $newBrandId = $this->add($newBrand);

        // Сдвигаем страницы вперед и вставляем копию на соседнюю позицию
        $update = $this->queryFactory->newUpdate();
        $update->table('__brands')
            ->set('position', 'position+1')
            ->where('position>=:position')
            ->bindValue('position', $brand->position);
        $this->db->query($update);

        $update = $this->queryFactory->newUpdate();
        $update->table('__brands')
            ->set('position', ':position')
            ->where('id=:id')
            ->bindValues([
                'position' => $position,
                'id' => $newBrandId,
            ]);
        $this->db->query($update);

        $this->multiDuplicateBrand($brandId, $newBrandId);
        return $newBrandId;
    }

    private function multiDuplicateBrand($brandId, $newBrandId)
    {
        $langId = $this->lang->getLangId();
        if (!empty($langId)) {

            /** @var LanguagesEntity $langEntity */
            $langEntity = $this->entity->get(LanguagesEntity::class);

            $languages = $langEntity->find();
            $brandLangFields = $this->getLangFields();

            foreach ($languages as $language) {
                if ($language->id != $langId) {
                    $this->lang->setLangId($language->id);

                    if (!empty($brandLangFields)) {
                        $sourceBrand = $this->findOne(['id' => $brandId]);
                        $destinationBrand = new \stdClass();
                        foreach ($brandLangFields as $field) {
                            $destinationBrand->{$field} = $sourceBrand->{$field};
                        }
                        $this->update($newBrandId, $destinationBrand);
                    }

                    $this->lang->setLangId($langId);
                }
            }
        }
    }

    public function filter__product_keyword($keyword)
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entity->get(ProductsEntity::class);

        $productsSelect = $productsEntity->getSelect(['keyword' => $keyword, 'visible' => 1]);

        $productsSelect
            ->resetCols()
            ->resetOrderBy()
            ->cols([ProductsEntity::getTableAlias() . '.brand_id']);

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
            __FUNCTION__ . '__' . ProductsEntity::getTableAlias() . '.brand_id = b.id'
        );
    }
}
