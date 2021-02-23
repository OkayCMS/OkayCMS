<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Request;
use Okay\Core\Translit;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\EntityFactory;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendFeaturesHelper
{
    /**
     * @var FeaturesValuesEntity
     */
    private $featuresValuesEntity;

    /**
     * @var CategoriesEntity
     */
    private $categoriesEntity;

    /**
     * @var FeaturesEntity
     */
    private $featuresEntity;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Translit
     */
    private $translit;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        QueryFactory  $queryFactory,
        Translit      $translit,
        Database      $db,
        Request       $request
    ) {
        $this->featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        $this->featuresEntity       = $entityFactory->get(FeaturesEntity::class);
        $this->queryFactory         = $queryFactory;
        $this->translit             = $translit;
        $this->db                   = $db;
        $this->request              = $request;
    }

    public function updateProductFeatures($product, $featuresValues, $featuresValuesText, $newFeaturesNames, $newFeaturesValues, $productsCategories)
    {
        // Удалим все значения свойств товара
        $this->featuresValuesEntity->deleteProductValue($product->id);
        if (!empty($featuresValues)) {
            foreach ($featuresValues as $featureId => $feature_values) {
                foreach ($feature_values as $k => $valueId) {

                    $value = trim($featuresValuesText[$featureId][$k]);
                    if (!empty($value)) {
                        if (!empty($valueId)) {
                            $this->featuresValuesEntity->update($valueId, ['value' => $value]);
                        } else {
                            /**
                             * Проверим может есть занчение с таким транслитом,
                             * дабы исключить дублирование значений "ТВ приставка" и "TV приставка" и подобных
                             */
                            $valueTranslit = $this->translit->translitAlpha($value);

                            // Ищем значение по транслиту в основной таблице, если мы создаем значение не на основном языке
                            $select = $this->queryFactory->newSelect();
                            $select->from('__features_values')
                                ->cols(['id'])
                                ->where('feature_id=:feature_id')
                                ->where('translit=:translit')
                                ->limit(1)
                                ->bindValues([
                                    'feature_id' => $featureId,
                                    'translit' => $valueTranslit,
                                ]);
                            $this->db->query($select);
                            $valueId = $this->db->result('id');

                            if (empty($valueId) && ($fv = $this->featuresValuesEntity->find(['feature_id' => $featureId, 'translit' => $valueTranslit]))) {
                                $fv = reset($fv);
                                $valueId = $fv->id;
                            }

                            // Если такого значения еще нет, но его запостили тогда добавим
                            if (!$valueId) {
                                $toIndex = $this->featuresEntity->cols(['to_index_new_value'])->get((int)$featureId)->to_index_new_value;
                                $featureValue = new \stdClass();
                                $featureValue->value = $value;
                                $featureValue->feature_id = $featureId;
                                $featureValue->to_index = $toIndex;
                                $valueId = $this->featuresValuesEntity->add($featureValue);
                            }
                        }
                    }

                    if (!empty($valueId)) {
                        $this->featuresValuesEntity->addProductValue($product->id, $valueId);
                    }
                }
            }
        }

        // Новые характеристики
        if (is_array($newFeaturesNames) && is_array($newFeaturesValues)) {
            foreach ($newFeaturesNames as $i => $name) {
                $value = trim($newFeaturesValues[$i]);
                if (!empty($name) && !empty($value)) {
                    $featuresIds = $this->featuresEntity->cols(['id'])->find([
                        'name' => trim($name),
                        'limit' => 1,
                    ]);

                    $featureId = reset($featuresIds);

                    if (empty($featureId)) {
                        $featureId = $this->featuresEntity->add(['name' => trim($name)]);
                    }

                    $this->featuresEntity->addFeatureCategory($featureId, reset($productsCategories)->id);

                    // Добавляем вариант значения свойства
                    $featureValue = new \stdClass();
                    $featureValue->feature_id = $featureId;
                    $featureValue->value = $value;
                    $valueId = $this->featuresValuesEntity->add($featureValue);

                    // Добавляем значения к товару
                    $this->featuresValuesEntity->addProductValue($product->id, $valueId);
                }
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findProductFeaturesValues($product)
    {
        $featuresValues = [];
        if (!empty($product->id)) {
            foreach ($this->featuresValuesEntity->find(['product_id' => $product->id]) as $fv) {
                $featuresValues[$fv->feature_id][] = $fv;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    public function findCategoryFeatures(array $productCategories, array $categoriesTree)
    {
        $features = [];

        $category = reset($productCategories);
        if (is_object($category)) {
            $features = $this->featuresEntity->find(['category_id' => $category->id]);
        }

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function prepareAdd($feature)
    {
        return ExtenderFacade::execute(__METHOD__, $feature, func_get_args());
    }

    public function add($feature)
    {
        $insertId = $this->featuresEntity->add($feature);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($feature)
    {
        return ExtenderFacade::execute(__METHOD__, $feature, func_get_args());
    }

    public function update($id, $feature)
    {
        $this->featuresEntity->update($id, $feature);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getFeature($id)
    {
        $feature = $this->featuresEntity->get((int) $id);
        return ExtenderFacade::execute(__METHOD__, $feature, func_get_args());
    }

    public function updateFeatureCategories($featureId, $featureCategories)
    {
        $this->featuresEntity->updateFeatureCategories((int) $featureId, $featureCategories);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getFeatureCategories($feature)
    {
        $featureCategories = [];
        if (!empty($feature)) {
            $featureCategories = $this->featuresEntity->getFeatureCategories($feature->id);
        } elseif ($category_id = $this->request->get('category_id')) {
            $featureCategories[] = $category_id;
        }

        return ExtenderFacade::execute(__METHOD__, $featureCategories, func_get_args());
    }

    public function buildFeaturesFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['features_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['features_num_admin'])) {
            $filter['limit'] = $_SESSION['features_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        $keyword = $this->request->get('keyword', 'string');
        if (!empty($keyword)) {
        $filter['keyword'] = $keyword;
        }


        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function sortPositions($positions)
    {
        $ids = array_keys($positions);
        sort($positions);
        foreach ($positions as $i=>$position) {
            $this->featuresEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $currentCategoryId = $this->request->get('category_id', 'integer');
        foreach ($ids as $id) {
            // текущие категории
            $featureCategories = $this->featuresEntity->getFeatureCategories($id);

            // В каких категориях оставлять
            $diffCategoriesIds = array_diff($featureCategories, (array)$currentCategoryId); // todo протестить
            if (!empty($currentCategoryId) && !empty($diffCategoriesIds)) {
                $this->featuresEntity->updateFeatureCategories($id, $diffCategoriesIds);
            } else {
                $this->featuresEntity->delete($id);
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function unsetInFilter($ids)
    {
        $this->featuresEntity->update($ids, ['in_filter'=>0]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function setInFilter($ids)
    {
        $this->featuresEntity->update($ids, ['in_filter'=>1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function moveToPage($ids, $targetPage, $filter)
    {
        // Сразу потом откроем эту страницу
        $filter['page'] = $targetPage;

        // До какого свойства перемещать
        $limit = $filter['limit']*($targetPage-1);
        if ($targetPage > $this->request->get('page', 'integer')) {
            $limit += count($ids)-1;
        } else {
            $ids = array_reverse($ids, true);
        }

        $tempFilter = $filter;
        $tempFilter['page'] = $limit+1;
        $tempFilter['limit'] = 1;
        $tmp = $this->featuresEntity->find($tempFilter);
        $targetFeature = array_pop($tmp);
        $targetPosition = $targetFeature->position;

        // Если вылезли за последнее свойство - берем позицию последнего свойства в качестве цели перемещения
        if ($targetPage > $this->request->get('page', 'integer') && !$targetPosition) {
            $select = $this->queryFactory->newSelect();
            $select->from('__features')
                ->cols(['distinct position AS target'])
                ->orderBy(['position DESC'])
                ->limit(1);

            $this->db->query($select);
            $targetPosition = $this->db->result('target');
        }

        foreach ($ids as $id) {
            $initialPosition = $this->featuresEntity->cols(['position'])->get((int)$id)->position;

            $update = $this->queryFactory->newUpdate();
            if ($targetPosition > $initialPosition) {
                $update->table('__features')
                    ->set('position', 'position-1')
                    ->where('position > :initial_position')
                    ->where('position <= :target_position')
                    ->bindValues([
                        'initial_position' => $initialPosition,
                        'target_position' => $targetPosition,
                    ]);
            } else {
                $update->table('__features')
                    ->set('position', 'position+1')
                    ->where('position < :initial_position')
                    ->where('position >= :target_position')
                    ->bindValues([
                        'initial_position' => $initialPosition,
                        'target_position' => $targetPosition,
                    ]);
            }
            $this->db->query($update);

            $this->featuresEntity->update($id, ['position' => $targetPosition]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findFeatures($filter = [], $sort = null)
    {
        if ($sort !== null) {
            $this->featuresEntity->order($sort);
        }
        $features = $this->featuresEntity->order('position')->find($filter);
        foreach ($features as $f) {
            $f->features_categories = $this->featuresEntity->getFeatureCategories($f->id);
        }

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function countPages($filter = [])
    {
        $featuresCount = $this->featuresEntity->count($filter);
        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $featuresCount;
        }

        if ($filter['limit']>0) {
            $pagesCount = ceil($featuresCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        return ExtenderFacade::execute(__METHOD__, $pagesCount, func_get_args());
    }

    public function count($filter = [])
    {
        $featuresCount  = $this->featuresEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $featuresCount, func_get_args());
    }
}