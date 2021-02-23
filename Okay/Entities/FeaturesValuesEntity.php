<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Money;
use Okay\Core\Translit;
use Okay\Core\Modules\Extender\ExtenderFacade;

class FeaturesValuesEntity extends Entity
{

    protected static $fields = [
        'id',
        'feature_id',
        'position',
        'to_index',
    ];

    protected static $langFields = [
        'value',
        'translit',
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
    public function add($featureValue) {

        $featureValue = (object)$featureValue;

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
        $this->select->groupBy([$this->getTableAlias().'.id']);

        $this->select->join('LEFT', '__products_features_values AS pf', 'pf.value_id=fv.id');
        
        // Нужно фильтр по свойствам применить здесь, чтобы он отработал до всех джоинов
        if (isset($filter['features'])) {
            $this->filter__features($filter['features']);
            unset($filter['features']);
        }
        
        $this->select->join('LEFT', '__features AS f', 'f.id=fv.feature_id');
        //$this->select->groupBy(['l.value']); // TODO: разобраться, вроде не нужная группировка
        //$this->select->groupBy(['l.translit']);

        if (isset($filter['visible']) || isset($filter['in_stock']) || isset($filter['price'])) {
            $this->select->join('LEFT', '__products AS p', 'p.id=pf.product_id');
        }
        
        return parent::find($filter);
    }

    protected function filter__in_stock()
    {
        $this->select->where("(SELECT count(*)>0 FROM __variants pv WHERE pv.product_id=p.id AND (pv.stock IS NULL OR pv.stock>0) LIMIT 1) = 1");
    }
    
    /*Удаление значения свойства*/
    public function delete($valuesIds = null)
    {

        // TODO удалять с алиасов
        $this->deleteProductValue(null, $valuesIds);

        return parent::delete($valuesIds);
    }

    public function countProductsByValueId(array $valuesIds)
    {
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
        foreach ($features as $featureId=>$value) {

            $subQuery = $this->queryFactory->newSelect();
            $subQuery->from('__products_features_values AS pf')
                ->cols(['DISTINCT(pf.product_id)'])
                ->join('LEFT', '__features_values AS fv', 'fv.id=pf.value_id');

            // Алиас для таблицы без языков
            $optionsPx = 'fv';
            
            if (!empty($this->lang->getLangId())) {
                $subQuery->where('lfv.lang_id=' . (int)$this->lang->getLangId())
                    ->join('LEFT', '__lang_features_values AS lfv', 'fv.id=lfv.feature_value_id');
                // Алиас для таблицы с языками
                $optionsPx = 'lfv';
            }
            
            $subQuery->where("({$optionsPx}.translit IN (:translit_features_subquery_{$featureId}) AND fv.feature_id=:feature_id_features_subquery_{$featureId})");

            $subQuery->bindValues([
                "translit_features_subquery_{$featureId}" => (array)$value,
                "feature_id_features_subquery_{$featureId}" => $featureId,
            ]);
            
            $this->select->where("(fv.feature_id =:feature_id_{$featureId} OR p.id IN (?))", $subQuery);
            $this->select->bindValue("feature_id_{$featureId}", $featureId);
        }
    }

    protected function filter__price(array $priceRange)
    {
        /** @var Money $money */
        $money = $this->serviceLocator->getService(Money::class);
        $coef = $money->getCoefMoney();

        if (isset($priceRange['min'])) {
            $this->select->where("floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})>=:price_min")
                ->bindValue('price_min', trim($priceRange['min']));
        }
        if (isset($priceRange['max'])) {
            $this->select->where("floor(IF(pv.currency_id=0 OR c.id is null,pv.price, pv.price*c.rate_to/c.rate_from)*{$coef})<=:price_max")
                ->bindValue('price_max', trim($priceRange['max']));
        }

        $this->select->join('LEFT', '__variants AS pv', 'pv.product_id = p.id');
        $this->select->join('LEFT', '__currencies AS c', 'c.id=pv.currency_id');
    }
    
    protected function filter__category_id($categoriesIds)
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
            $otherFilter[] = '(SELECT 1 FROM __variants pv WHERE pv.product_id=pf.product_id AND pv.compare_price>0 LIMIT 1) = 1';
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $otherFilter, func_get_args());
    }
    
    
    /**
     * @param array $features
     * example $features[feature_id] = [value1_id, value2_id ...]
     * Метод возвращает только мультиязычные поля значений свойств, используется для построения alternate на странице фильтра
     * @return array
     * result [
     *  lang_id => [
     *          feature1_id => [
     *              value1_id => $value1,
     *              value2_id => $value2
     *          ],
     *          feature2_id => [
     *              value3_id => $value3,
     *              value4_id => $value4
     *          ]
     *      ]
     *  ]
     */
    public function getFeaturesValuesAllLang($features = []) {
        
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
        
        foreach ($features as $featureId=>$valuesIds) {
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
     * @param array $productIds
     * @return array
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

}
