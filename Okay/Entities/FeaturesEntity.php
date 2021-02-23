<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Translit;
use Okay\Core\Modules\Extender\ExtenderFacade;

class FeaturesEntity extends Entity
{

    protected static $fields = [
        'id',
        'position',
        'in_filter',
        'auto_name_id',
        'auto_value_id',
        'url',
        'url_in_product',
        'to_index_new_value',
    ];

    protected static $langFields = [
        'name',
        'description',
    ];

    protected static $searchFields = [
        'name',
    ];

    protected static $defaultOrderFields = [
        'f.position ASC',
    ];

    protected static $table = '__features';
    protected static $langObject = 'feature';
    protected static $langTable = 'features';
    protected static $tableAlias = 'f';
    protected static $alternativeIdField = 'url';

    /**
     * @var Translit
     */
    private $translit;

    public function __construct()
    {
        parent::__construct();
        $this->translit = $this->serviceLocator->getService(Translit::class);
    }

    public function add($feature)
    {
        $feature = (array)$feature;

        if (empty($feature['url'])) {
            $feature['url'] = $this->translit->translitAlpha($feature['name']);
        }
        $feature['url'] = preg_replace("/[\s]+/ui", '', $feature['url']);
        $feature['url'] = strtolower(preg_replace("/[^0-9a-z]+/ui", '', $feature['url']));
        while ($this->get((string)$feature['url'])) {
            if (preg_match('/(.+)([0-9]+)$/', $feature['url'], $parts)) {
                $feature['url'] = $parts[1].''.($parts[2]+1);
            } else {
                $feature['url'] = $feature['url'].'2';
            }
        }
        
        $feature = (object)$feature;
        
        return parent::add($feature);
    }

    public function update($id, $feature)
    {
        //lastModify
        $feature = (array)$feature;
        if (isset($feature['name']) && !empty($feature['name']) && !is_array($id)) {
            $oldFeature = $this->get((int)$id);
            if ($oldFeature->name != $feature['name']) {
                
                $select = $this->queryFactory->newSelect();
                $select->cols(['pv.product_id'])
                    ->from('__products_features_values AS pv')
                    ->join('inner', '__features_values AS fv', 'pv.value_id=fv.id AND fv.feature_id=:feature_id')
                    ->bindValues([
                        'feature_id' => (int)$id
                    ]);
                
                $this->db->query($select);
                $productsIds = $this->db->results('product_id');
                if (!empty($productsIds)) {
                    
                    $update = $this->queryFactory->newUpdate();
                    $update->table('__products')
                        ->set('last_modify', 'NOW()')
                        ->where('id IN (:products_ids)')
                        ->bindValue('products_ids', $productsIds);
                    $this->db->query($update);
                }
            }
        }
        
        return parent::update($id, $feature);
        
    }

    /*Удаление свойства*/
    public function delete($ids)
    {
        if (empty($ids)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }
        $ids = (array)$ids;
        
        if (!empty($ids)) {
            //lastModify
            $select = $this->queryFactory->newSelect();
            $select->cols(['pf.product_id'])
                ->from('__products_features_values AS pf')
                ->join('INNER', '__features_values AS fv', 'pf.value_id=fv.id AND fv.feature_id IN (:features_ids)')
                ->bindValue('features_ids', $ids);
            
            $this->db->query($select);
            $productsIds = $this->db->results('product_id');
            if (!empty($productsIds)) {
                $update = $this->queryFactory->newUpdate();
                $update->table(ProductsEntity::getTable())
                    ->set('last_modify', 'now()')
                    ->where('id IN (:products_ids)')
                    ->bindValue('products_ids', $productsIds);
                $this->db->query($update);
            }

            /*Удаляем значения свойств*/
            /** @var FeaturesValuesEntity $featuresValues */
            $featuresValues = $this->entity->get(FeaturesValuesEntity::class);
            if ($valuesIds = $featuresValues->cols(['id'])->find(['feature_id' => $ids])) {
                $featuresValues->delete($valuesIds);
            }
            
            $delete = $this->queryFactory->newDelete();
            $delete->from(FeaturesValuesAliasesValuesEntity::getTable())
                ->where('feature_id IN (:feature_id)')
                ->bindValue('feature_id', $ids);
            $this->db->query($delete);
            
            $delete = $this->queryFactory->newDelete();
            $delete->from('__categories_features')
                ->where('feature_id IN (:feature_id)')
                ->bindValue('feature_id', $ids);
            $this->db->query($delete);
        }
        parent::delete($ids);
    }

    /**
     * @var $featureId - id свойства
     * @return array
     * Выборка категорий, закрепленных за свойством
     */
    public function getFeatureCategories($featureId)
    {
        $select = $this->queryFactory->newSelect();
        $select->from('__categories_features AS cf')
            ->cols(['cf.category_id'])
            ->where('cf.feature_id=:feature_id')
            ->bindValue('feature_id', (int)$featureId);
        
        $this->db->query($select);
        $results = $this->db->results('category_id');

        return ExtenderFacade::execute([static::class, __FUNCTION__], $results, func_get_args());
    }
    
    /**
     * @var $featureId
     * @var $categoryId
     * Добавление связки категории и свойства
     */
    public function addFeatureCategory($featureId, $categoryId) {
        
        $insert = $this->queryFactory->newInsert();
        $insert->into('__categories_features')
            ->cols([
                'feature_id' => $featureId,
                'category_id' => $categoryId,
            ])
            ->ignore();
        
        $this->db->query($insert);
        return ExtenderFacade::execute([static::class, __FUNCTION__], true, func_get_args());
    }

    /**
     * @var int $featureId
     * @var array $categoriesIds
     * Обновление связки категории и свойства
     * @throws \Exception
     * @return
     */
    public function updateFeatureCategories($featureId, array $categoriesIds)
    {
        $featureId = (int)$featureId;
        
        //lastModify
        if (!empty($categoriesIds)) {
            $select = $this->queryFactory->newSelect();
            $select->from('__categories_features')
                ->cols(['category_id'])
                ->where('feature_id=:feature_id')
                ->bindValue('feature_id', $featureId);
            
            $this->db->query($select);
            $cIds = $this->db->results('category_id');
            $diffCategoriesIds = array_diff($cIds, $categoriesIds);
            
            if (!empty($diffCategoriesIds)) {
                $select = $this->queryFactory->newSelect();
                $select->from('__products_features_values AS pv')
                    ->cols(['pv.product_id'])
                    ->join('INNER', '__features_values AS fv', 'pv.value_id=fv.id')
                    ->join('INNER', '__products_categories AS pc', 'pc.product_id=pv.product_id')
                    ->where('fv.feature_id=:feature_id')
                    ->where('pc.category_id IN (:categories_ids)')
                    ->bindValues([
                        'feature_id' => $featureId,
                        'categories_ids' => $diffCategoriesIds,
                    ]);
                $this->db->query($select);
                $productsIds = $this->db->results('product_id');
                
                if (!empty($productsIds)) {
                    $update = $this->queryFactory->newUpdate();
                    $update->table('__products')
                        ->set('last_modify', 'NOW()')
                        ->where('id IN (:products_ids)')
                        ->bindValue('products_ids', $productsIds);
                    $this->db->query($update);
                }
            }
        } else {

            $select = $this->queryFactory->newSelect();
            $select->from('__products_features_values AS pf')
                ->cols(['pf.product_id'])
                ->join('INNER', '__features_values AS fv', 'pf.value_id=fv.id AND fv.feature_id=:feature_id')
                ->bindValues([
                    'feature_id' => $featureId,
                ]);
            
            $this->db->query($select);
            $productsIds = $this->db->results('product_id');
            
            if (!empty($productsIds)) {
                $update = $this->queryFactory->newUpdate();
                $update->table('__products')
                    ->set('last_modify', 'NOW()')
                    ->where('id IN (:products_ids)')
                    ->bindValue('products_ids', $productsIds);
                $this->db->query($update);
            }
        }

        $delete = $this->queryFactory->newDelete();
        $delete->from('__categories_features ')
            ->where('feature_id=:feature_id')
            ->bindValue('feature_id', $featureId);
        $this->db->query($delete);
        
        if (!empty($categoriesIds)) {
            
            foreach($categoriesIds as $categoryId) {
                $insert = $this->queryFactory->newInsert();
                $insert->into('__categories_features');
                $insert->cols([
                    'feature_id' => $featureId,
                    'category_id' => (int)$categoryId,
                ])
                ->ignore();
                $this->db->query($insert);
            }
            
            // Удаляем значения свойств из категорий которые не запостили (их могли отжать)
            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("DELETE `pv` FROM `__products_features_values` AS `pv`
                            INNER JOIN `__features_values` AS `fv` ON `pv`.`value_id`=`fv`.`id`
                            LEFT JOIN `__products_categories` AS `pc` ON `pc`.`product_id`=`pv`.`product_id`
                            WHERE 
                                `fv`.`feature_id`=:feature_id
                                AND `pc`.`position`=(SELECT MIN(`pc2`.`position`) FROM `__products_categories` AS `pc2` WHERE `pc`.`product_id`=`pc2`.`product_id`) 
                                AND `pc`.`category_id` NOT IN (:categories_ids)");
            $sql->bindValue('feature_id', $featureId);
            $sql->bindValue('categories_ids', $categoriesIds);
            $this->db->query($sql);
        } else {
            // Если не прислали категорий, тогда удаляем все значения этого свойства для товаров
            $sql = $this->queryFactory->newSqlQuery();
            $sql->setStatement("DELETE `pf` FROM `__products_features_values` AS `pf`
                            INNER JOIN `__features_values` AS `fv` ON `pf`.`value_id`=`fv`.`id` AND `fv`.`feature_id` = :feature_id");
            $sql->bindValue('feature_id', $featureId);
            $this->db->query($sql);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    public function checkAutoId($featureId, $autoId, $field = "auto_name_id")
    {
        if (empty($autoId)) {
            return true;
        }

        $select = $this->queryFactory->newSelect();
        $select->from('__features')
            ->cols(['id'])
            ->where("$field=:field_value")
            ->bindValues([
                'field_value' => $autoId,
            ]);
        
        $this->db->query($select);
        $existId = $this->db->result('id');
        return (!$existId || $featureId == $existId);
    }

    protected function filter__category_id($categoriesIds)
    {
        $this->select->join('INNER',
            '__categories_features AS cf',
            'cf.feature_id = ' . $this->getTableAlias() . '.id AND cf.category_id IN(:categories_ids)'
        );

        $this->select->bindValue('categories_ids', (array)$categoriesIds);
    }

    // Особый фильтр по категории. Фильтрует не по связке свойства и категории, а по имеющимся значениям свойств
    // у товаров указанной категории.
    protected function filter__product_category_id($categoriesIds)
    {
        
        $this->select->join('INNER',
            '__features_values AS fv',
            'fv.feature_id = ' . $this->getTableAlias() . '.id'
        );
        
        $this->select->join('INNER',
            '__products_features_values AS pv',
            'pv.value_id = fv.id'
        );
        
        $this->select->join('INNER',
            '__products AS p',
            'p.id = pv.product_id'
        );
        
        $this->select->join('INNER',
            '__products_categories AS pc',
            'p.id = pc.product_id AND pc.category_id IN(:export_categories_ids)'
        );
        
        $this->select->groupBy([$this->getTableAlias() . '.id']);
            
        $this->select->bindValue('export_categories_ids', (array)$categoriesIds);
    }
    
}
