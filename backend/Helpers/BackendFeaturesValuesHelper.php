<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Languages;
use Okay\Core\Request;
use Okay\Core\Settings;
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

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(
        EntityFactory $entityFactory,
        QueryFactory  $queryFactory,
        Translit      $translit,
        Database      $db,
        Request       $request,
        Languages     $languages,
        Settings      $settings
    ) {
        $this->featuresValuesEntity = $entityFactory->get(FeaturesValuesEntity::class);
        $this->featuresEntity       = $entityFactory->get(FeaturesEntity::class);
        $this->queryFactory         = $queryFactory;
        $this->translit             = $translit;
        $this->db                   = $db;
        $this->request              = $request;
        $this->languages            = $languages;
        $this->settings             = $settings;
    }

    public function moveToPage($ids, $page, $feature, $featuresValuesFilter)
    {
        $featuresValuesFilter['page'] = $page;
        $offset = $featuresValuesFilter['limit'] * ($page-1);

        if (empty($ids) || !$offset) {
            return ExtenderFacade::execute(__METHOD__, $offset, func_get_args());
        }

        $targetFVs = $this->featuresValuesEntity->cols(['id', 'feature_id', 'position'])->find(['id'=>$ids]);

        $shiftCounter = 0;
        foreach ($targetFVs as $targetFV) {
            $select = $this->queryFactory->newSelect();
            $shiftFV = $select->from(FeaturesValuesEntity::getTable().' fv')
                ->cols(['feature_value_id', 'lfv.position as position'])
                ->leftJoin(FeaturesValuesEntity::getLangTable().' lfv', 'fv.feature_id = :feature_id AND fv.id = lfv.feature_value_id AND lfv.lang_id = :lang_id')
                ->where('feature_id = :feature_id')

                ->bindValues([
                    'feature_id' => $feature->id,
                    'lang_id' => $this->languages->getLangId()
                ])
                ->orderBy(['lfv.position ASC'])
                ->limit(1)
                ->offset($offset + $shiftCounter)
                ->result();
             $shiftCounter++;

            if ($shiftFV) {
                $positionUpdateFrom = $targetFV->position;
                $positionUpdateTo = $shiftFV->position;

                $update = $this->queryFactory->newSqlQuery();
                $update->setStatement('
                    UPDATE '.FeaturesValuesEntity::getLangTable().' lfv
                    JOIN (SELECT lfv.feature_value_id as feature_value_id, lfv.position as position
                            FROM '.FeaturesValuesEntity::getLangTable().' lfv
                            LEFT JOIN '.FeaturesValuesEntity::getTable().' fv ON lfv.feature_value_id = fv.id
                            WHERE fv.feature_id = :feature_id 
                              AND lfv.lang_id = :lang_id 
                              AND lfv.position > :positionFrom
                              AND lfv.position <= :positionTo
                            ORDER BY lfv.position
                          ) as tp ON lfv.feature_value_id = tp.feature_value_id
                    SET lfv.position = lfv.position-1
                    WHERE lfv.lang_id = :lang_id2;'
                )->bindValues([
                    'feature_id' => $feature->id,
                    'lang_id' => $this->languages->getLangId(),
                    'lang_id2' => $this->languages->getLangId(),
                    'positionFrom' => $positionUpdateFrom,
                    'positionTo' => $positionUpdateTo,
                ])->execute();

                $this->featuresValuesEntity->update($targetFV->id, ['position'=>$shiftFV->position]);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $offset, func_get_args());
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

        $count = 0;
        foreach($this->featuresValuesEntity->find($featuresValuesFilter) as $fv) {
            if (isset($featuresValues[$fv->translit])) {
                $featuresValues[$fv->translit.'repeated'.++$count] = $fv;
            } else {
                $featuresValues[$fv->translit] = $fv;
            }
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

    public function sortFeatureValuePositions($feature, $featuresValues, $featuresValuesFilter)
    {
        $featureValuesIds = [];

        $isPageAll = $featuresValuesFilter['page'] == 'all';
        $position = $isPageAll ? 1 : $featuresValuesFilter['limit'] * ($featuresValuesFilter['page']-1) +1;

        $addedIds = [];
        foreach($featuresValues as $fv) {
            if (!$fv->to_index) {
                $fv->to_index = 0;
            }
            if ($fv->value !== '' && $fv->value !== null) {
                $translit = !empty(trim($fv->translit ?? '')) ? $fv->translit : $fv->value;
                $fv->feature_id = $feature->id;
                $fv->position = $position;

                if (!empty($fv->id)) {
                    $fv->translit = $this->generateUniqueTranslitForValue($feature->id, $translit, $fv->id);
                    $this->featuresValuesEntity->update($fv->id, $fv);
                } else {
                    $fv->translit = $this->generateUniqueTranslitForValue($feature->id, $translit, null);
                    unset($fv->id);

                    $fv->id = $this->featuresValuesEntity->add($fv);
                    $addedIds[] = $fv->id;
                }
                $featureValuesIds[] = $fv->id;
                $position++;
            }
        }

        $this->updateValuesPositions($feature->id, $this->languages->getLangId(), $position-1, $addedIds);
        $this->sortAllFeatureValuesTranslationsProcedure($feature->id, $addedIds);

        ExtenderFacade::execute(__METHOD__, $featureValuesIds, func_get_args());
    }

    public function updateValuesPositions($featureId, $langId, $position = 0, $addedFeatureValuesIds = [], $doShiftCount = 0)
    {
        if ($doShiftCount || !empty($addedFeatureValuesIds)) {
            $addedStr = !empty($addedFeatureValuesIds) ? 'AND lfv.feature_value_id not in (:added_value_ids)' : '';

            $this->queryFactory->newSqlQuery()
                ->setStatement('UPDATE '.FeaturesValuesEntity::getLangTable().' lfv
                LEFT JOIN '.FeaturesValuesEntity::getTable().' fv ON lfv.feature_value_id = fv.id
                SET lfv.position = (lfv.position + :shift)
                WHERE fv.feature_id = :feature_id AND lfv.lang_id = :lang_id AND lfv.position > :position '.$addedStr.';
            ')->bindValues([
                    'feature_id' => $featureId,
                    'lang_id' => $langId,
                    'position' => $position,
                    'shift' => $doShiftCount ?: count($addedFeatureValuesIds ?? []),
                    'added_value_ids' => $addedFeatureValuesIds,
                ])->execute();
        }

        $this->queryFactory->newSqlQuery()->setStatement('
                SET @rownum := 0;
                CREATE TEMPORARY TABLE __temp_positions AS
                SELECT feature_value_id, @rownum := @rownum + 1 AS new_position
                FROM (SELECT lfv.feature_value_id as feature_value_id
                        FROM '.FeaturesValuesEntity::getLangTable().' lfv
                        LEFT JOIN '.FeaturesValuesEntity::getTable().' fv ON lfv.feature_value_id = fv.id
                        WHERE fv.feature_id = :feature_id 
                          AND lfv.lang_id = :lang_id 
                        ORDER BY lfv.position
                      ) as t;
                
                UPDATE '.FeaturesValuesEntity::getLangTable().' lfv
                    JOIN temp_positions tp ON lfv.feature_value_id = tp.feature_value_id
                    SET lfv.position = tp.new_position;
                    WHERE lfv.lang_id = :lang_id2;
                DROP TEMPORARY TABLE __temp_positions;
            ')->bindValues([
                'feature_id' => $featureId,
                'lang_id' => $langId,
                'lang_id2' => $langId,
            ])->execute();
    }

    public function generateUniqueTranslitForValue($featureId, $translit, $valueId = null)
    {
        while ($this->getUniqueTranslitForLangTable($featureId, $translit, $valueId)) {
            if(preg_match('/(.+)rptd([0-9]+)$/', $translit, $parts)) {
                $translit = $parts[1].'rptd'.($parts[2]+1);
            } else {
                $translit = $translit.'rptd2';
            }

        }

        return $translit;
    }

    public function getUniqueTranslitForLangTable($featureId, $translit, $valueId = null)
    {
        if ($valueId) {
            $selectFromLangTable = $this->featuresValuesEntity->cols(['id'])
                ->getSelect(['feature_id'=>$featureId, 'translit'=>Translit::translitAlpha($translit), 'limit'=>1])
                ->where('id != :value_id')
                ->bindValues(['value_id'=>$valueId]);
        } else {
            $selectFromLangTable = $this->queryFactory->newSelect()->cols(['id'])->from(FeaturesValuesEntity::getTable().' fv')
                ->innerJoin(FeaturesValuesEntity::getLangTable().' lfv', 'fv.id = lfv.feature_value_id')
                ->where('lfv.translit = :translit')
                ->where('fv.feature_id = :feature_id')
                ->bindValues([
                    'translit' => Translit::translitAlpha($translit),
                    'feature_id' => $featureId
                ])->limit(1);
        }

        return $selectFromLangTable->result('id');
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
        foreach($this->featuresValuesEntity->cols(['id', 'value'])->noLimit()->find(['feature_id' => $feature->id]) as $fv) {
            $featuresValues[$fv->id] = $fv->value;
        }

        asort($featuresValues, SORT_NATURAL);

        $featureValueIds = array_keys($featuresValues);
        foreach($featureValueIds as $position => $id) {
            $this->featuresValuesEntity->update($id, ['position'=>$position+1]);
        }

        $this->sortAllFeatureValuesTranslationsProcedure($feature->id);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function checkValuesDuplicates()
    {
        return $this->getQueryForValueDuplicates()->results();
    }

    public function checkValuesDuplicatesCount()
    {
        return $this->getQueryForValueDuplicates()->cols(['COUNT(*) as total_count'])->result('total_count');
    }
    
    protected function getQueryForValueDuplicates(): QueryFactory\Select
    {
        $query = $this->queryFactory->newSelect();

        $query->cols([
            'l.label AS lang',
            'lfv_inner.lang_id',
            'lfv_inner.feature_value_id',
            'fv_inner.feature_id',
            'lf.name AS feature_name',
            'lfv_inner.translit',
            'lfv_inner.value'
        ]);

        $query->from('__lang_features_values AS lfv_inner')
            ->innerJoin('__features_values AS fv_inner', 'lfv_inner.feature_value_id = fv_inner.id')
            ->leftJoin('__languages AS l', 'l.id = lfv_inner.lang_id')
            ->leftJoin('__lang_features AS lf','lf.feature_id = fv_inner.feature_id AND lf.lang_id = lfv_inner.lang_id');

        $subQuery = $this->queryFactory->newSelect();
        $subQuery->cols([
            'lfv_inner.lang_id',
            'fv_inner.feature_id',
            'lfv_inner.translit'])
            ->from('__lang_features_values as lfv_inner')
            ->innerJoin('__features_values fv_inner','lfv_inner.feature_value_id = fv_inner.id')
        ->groupBy(['lfv_inner.lang_id, fv_inner.feature_id, lfv_inner.translit'])
        ->having('COUNT(*) > 1');

        $query->where(' (lfv_inner.lang_id, fv_inner.feature_id, lfv_inner.translit) IN (?)', $subQuery);

        $query->orderBy(['lang_id', 'feature_id', 'translit', 'feature_value_id', 'translit']);

        return $query;
    }

    public function resolveDuplicateFeatureValues()
    {
        $this->queryFactory->newSqlQuery()->setStatement('START TRANSACTION')->execute();
        $this->queryFactory->newSqlQuery()->setStatement("
         CREATE TEMPORARY TABLE temp_duplicates AS
            SELECT
                lfv_inner.lang_id as lang_id,
                fv_inner.feature_id as feature_id,
                lfv_inner.translit as translit,
                lfv_inner.feature_value_id as feature_value_id
            FROM __lang_features_values lfv_inner
            JOIN __features_values fv_inner ON lfv_inner.feature_value_id = fv_inner.id
            WHERE (lfv_inner.lang_id, fv_inner.feature_id, lfv_inner.translit) 
              IN (SELECT lfv_inner.lang_id, fv_inner.feature_id, lfv_inner.translit
                FROM __lang_features_values lfv_inner
                JOIN __features_values fv_inner ON lfv_inner.feature_value_id = fv_inner.id
                GROUP BY lfv_inner.lang_id, fv_inner.feature_id, lfv_inner.translit
                HAVING COUNT(*) > 1
              );
        ")->execute();

        $this->queryFactory->newSqlQuery()->setStatement("
        UPDATE __lang_features_values lfv
        JOIN (SELECT
                lang_id,
                translit,
                feature_value_id,
                @row_number := IF(@prev_translit = translit COLLATE utf8mb4_unicode_ci AND @prev_lang_id = lang_id, @row_number + 1, 1) AS repeat_number,
                @prev_translit := translit COLLATE utf8mb4_unicode_ci,
                @prev_lang_id := lang_id
                FROM temp_duplicates
                CROSS JOIN (SELECT @row_number := 0, @prev_translit := '', @prev_lang_id := '') AS vars
                ORDER BY
                    lang_id, translit, feature_value_id
        ) AS numbered_duplicates ON lfv.lang_id = numbered_duplicates.lang_id AND  lfv.feature_value_id = numbered_duplicates.feature_value_id
        SET lfv.translit = CONCAT(lfv.translit, 'rptd', numbered_duplicates.repeat_number)
        WHERE numbered_duplicates.repeat_number > 1;")->execute();

        $this->queryFactory->newSqlQuery()->setStatement('COMMIT')->execute();
        $this->queryFactory->newSqlQuery()->setStatement('DROP TEMPORARY TABLE IF EXISTS temp_duplicates;')->execute();

        return !$this->checkValuesDuplicatesCount();
    }

    /**
     * @param $featureId
     * @param $addedValueIds
     */
    public function sortAllFeatureValuesTranslationsProcedure($featureId, $addedValueIds = [])
    {
        if ($this->settings->get('sort_feature_values_individually_each_lang') != 1) {
            $this->sortAllFeatureValuesTranslationsByLang($featureId, $this->languages->getLangId());
        } elseif(!empty($addedValueIds)) {
            $this->sortAddedFeatureValuesPosition($featureId, $addedValueIds);
        }
    }

    /**
     * @param int $featureId
     * @param int|null $langId
     */
    public function sortAllFeatureValuesTranslationsByLang($featureId, $langId=null)
    {
        if (!empty($featureId = (int)$featureId)) {

            if (empty($langId)) {
                $langId = $this->languages->getLangId();
            }

            $update = $this->queryFactory->newSqlQuery();
            $update->setStatement(
                'UPDATE `__lang_features_values` origin_lfv
                INNER JOIN `__features_values` fv ON origin_lfv.feature_value_id = fv.id
                LEFT JOIN `__lang_features_values` sub ON origin_lfv.feature_value_id = sub.feature_value_id AND sub.lang_id = :lang_id
                SET origin_lfv.`position`= sub.position WHERE fv.`feature_id` = :feature_id')
                ->bindValues(['feature_id'=>$featureId, 'lang_id'=>$langId])
                ->execute();
        }
    }

    /**
     * @param int $featureId
     * @param int[] $addedValueIds
     * @param int|null $langId
     */
    public function sortAddedFeatureValuesPosition($featureId, $addedValueIds, $langId=null)
    {
        if (!empty($featureId = (int)$featureId)
            && !empty($addedValueIds = (array)$addedValueIds)) {

            if (empty($langId)) {
                $langId = $this->languages->getLangId();
            }

            $update = $this->queryFactory->newSqlQuery();
            $update->setStatement(
                'UPDATE `__lang_features_values` lfv
                INNER JOIN `__features_values` fv ON lfv.feature_value_id = fv.id
                SET lfv.`position`= lfv.feature_value_id  
                WHERE fv.`feature_id` = :feature_id AND lfv.feature_value_id in (:value_ids) AND lfv.lang_id != :lang_id;'
            )->bindValues([
                'feature_id'=>$featureId,
                'lang_id'=>$langId,
                'value_ids'=>$addedValueIds
            ])->execute();
        }
    }
}