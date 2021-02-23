<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Request;
use Okay\Core\Translit;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\EntityFactory;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendFeaturesValuesHelper
{
    /**
     * @var FeaturesValuesEntity
     */
    private $featuresValuesEntity;

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

    public function moveToPage($ids, $page, $feature, $featuresValuesFilter)
    {
        $featuresValuesFilter['page'] = $page;

        $check = $this->request->post('check');
        $select = $this->queryFactory->newSelect();
        $select->from('__features_values')
            ->cols(['id'])
            ->where('feature_id = :feature_id')
            ->where('id NOT IN (:id)')
            ->bindValues([
                'feature_id' => $feature->id,
                'id' => (array)$check,
            ])
            ->orderBy(['position ASC']);
        $this->db->query($select);

        $ids = $this->db->results('id');

        // вычисляем после какого значения вставить то, которое меремещали
        $offset = $featuresValuesFilter['limit'] * ($page)-1;
        $featureValuesIds = [];

        // Собираем общий массив id значений, и в нужное место добавим значение которое перемещали
        // По сути иммитация если выбрали page=all и мереместили приблизительно в нужное место значение
        foreach ($ids as $k=>$id) {
            if ($k == $offset) {
                $featureValuesIds = array_merge($featureValuesIds, $check);
                unset($check);
            }
            $featureValuesIds[] = $id;
        }

        if (!empty($check)) {
            $featureValuesIds = array_merge($featureValuesIds, $check);
        }

        asort($featureValuesIds);
        $i = 0;

        foreach ($featureValuesIds as $featuresValueId) {
            $this->featuresValuesEntity->update($featureValuesIds[$i], ['position'=>$featuresValueId]);
            $i++;
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    public function countPages($featuresValuesFilter, $feature)
    {
        $obj = new \ArrayObject($featuresValuesFilter);
        $copyFeaturesValuesFilter = $obj->getArrayCopy();

        unset($copyFeaturesValuesFilter['limit']);
        unset($copyFeaturesValuesFilter['page']);

        $featuresCount = $this->featuresValuesEntity->count($copyFeaturesValuesFilter);
        $pageCount = ceil($featuresCount/$featuresValuesFilter['limit']);
        return ExtenderFacade::execute(__METHOD__, $pageCount, func_get_args());
    }

    public function count($featuresValuesFilter)
    {
        $obj = new \ArrayObject($featuresValuesFilter);
        $copyFeaturesValuesFilter = $obj->getArrayCopy();

        unset($copyFeaturesValuesFilter['limit']);
        unset($copyFeaturesValuesFilter['page']);

        $featuresValuesCount = $this->featuresValuesEntity->count($copyFeaturesValuesFilter);
        return ExtenderFacade::execute(__METHOD__, $featuresValuesCount, func_get_args());
    }

    public function getProductsCountsByValues($featuresValuesFilter, $featuresValues)
    {
        $featureValuesIds = [];
        foreach ($featuresValues as $fv) {
            $featureValuesIds[] = $fv->id;
        }

        $productsCounts = $this->featuresValuesEntity->countProductsByValueId($featureValuesIds);
        return ExtenderFacade::execute(__METHOD__, $productsCounts, func_get_args());
    }

    public function makePagination($featuresValuesFilter)
    {
        $featuresValuesFilter['page'] = max(1, $this->request->get('page', 'integer'));
        $featureValuesCount = $this->featuresValuesEntity->count($featuresValuesFilter);

        // Показать все страницы сразу
        if($this->request->get('page') == 'all') {
            $featuresValuesFilter['limit'] = $featureValuesCount;
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    public function findFeaturesValues($featuresValuesFilter)
    {
        $featuresValues = [];

        foreach($this->featuresValuesEntity->find($featuresValuesFilter) as $fv) {
            $featuresValues[$fv->translit] = $fv;
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    public function showAllValuesIfSelected($featuresValuesFilter)
    {
        $feature_values_count = $this->featuresValuesEntity->count($featuresValuesFilter);

        // Показать все страницы сразу
        if($this->request->get('page') == 'all') {
            $featuresValuesFilter['limit'] = $feature_values_count;
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    public function buildValuesFilter($feature)
    {
        $featuresValuesFilter = ['feature_id'=>$feature->id];

        if ($featuresValuesFilter['limit'] = $this->request->get('limit', 'integer')) {
            $featuresValuesFilter['limit'] = max(5, $featuresValuesFilter['limit']);
            $featuresValuesFilter['limit'] = min(100, $featuresValuesFilter['limit']);
            $_SESSION['features_values_num_admin'] = $featuresValuesFilter['limit'];
        } elseif (!empty($_SESSION['features_values_num_admin'])) {
            $featuresValuesFilter['limit'] = $_SESSION['features_values_num_admin'];
        } else {
            $featuresValuesFilter['limit'] = 25;
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    public function unionValues($unionMainValueId, $unionSecondValueId)
    {
        $unionMainValue   = $this->featuresValuesEntity->get((int) $unionMainValueId);
        $unionSecondValue = $this->featuresValuesEntity->get((int) $unionSecondValueId);

        if ($unionMainValue && $unionSecondValue && $unionMainValue->id != $unionSecondValue->id) {
            // Получим id товаров для которых уже есть занчение, которое мы объединяем
            $select = $this->queryFactory->newSelect();
            $productsIds = $select->from('__products_features_values')
                ->cols(['product_id'])
                ->where('value_id=:value_id')
                ->bindValue('value_id', $unionMainValue->id)
                ->results('product_id');

            foreach ($productsIds as $productId) {
                $sql = $this->queryFactory->newSqlQuery();
                $sql->setStatement("REPLACE INTO `__products_features_values` SET `product_id`=:product_id, `value_id`=:value_id")
                    ->bindValue('product_id', $productId)
                    ->bindValue('value_id', $unionSecondValue->id)
                    ->execute();
            }

            $this->featuresValuesEntity->delete($unionMainValue->id);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortFeatureValuePositions($feature, $featuresValues)
    {
        $featureValuesIds = [];
        foreach($featuresValues as $fv) {
            if (!$fv->to_index) {
                $fv->to_index = 0;
            }
            // TODO Обработка ошибок не уникального тринслита или генерить уникальный
            if ($fv->value !== '' && $fv->value !== null) {
                
                $fv->feature_id = $feature->id;
                if (!empty($fv->id)) {
                    $this->featuresValuesEntity->update($fv->id, $fv);
                } else {
                    unset($fv->id);
                    $fv->id = $this->featuresValuesEntity->add($fv);
                }
                $featureValuesIds[] = $fv->id;
            }
        }

        asort($featureValuesIds);
        $i = 0;
        foreach($featureValuesIds as $featureValueId) {
            $this->featuresValuesEntity->update($featureValuesIds[$i], ['position'=>$featureValueId]);
            $i++;
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function deleteSelectedValues($valuesToDelete, $featuresValues)
    {
        foreach ($featuresValues  as $k=>$fv) {
            if (in_array($fv->id, $valuesToDelete)) {
                unset($featuresValues[$k]);
                $this->featuresValuesEntity->delete($fv->id);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    public function toIndexAllValues($feature)
    {
        $update = $this->queryFactory->newUpdate();
        $update->table('__features_values')
            ->col('to_index', 1)
            ->where('feature_id=:feature_id')
            ->bindValue('feature_id', $feature->id)
            ->execute();

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortFeatureValuePositionsAlphabet($feature)
    {
        $featuresValues = [];
        foreach($this->featuresValuesEntity->cols(['id', 'value'])->find(['feature_id' => $feature->id]) as $fv) {
            $featuresValues[$fv->id] = $fv->value;
        }

        asort($featuresValues, SORT_NATURAL);
        $i = 0;
        $featureValueIds = array_keys($featuresValues);
        foreach($featureValueIds as $featureValueId) {
            $this->featuresValuesEntity->update($featureValueId, ['position'=>$i++]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}