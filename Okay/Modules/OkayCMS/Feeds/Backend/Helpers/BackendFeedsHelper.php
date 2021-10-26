<?php

namespace Okay\Modules\OkayCMS\Feeds\Backend\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Helpers\ProductsHelper;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\BackendPresetAdapterFactory;
use Okay\Modules\OkayCMS\Feeds\Entities\ConditionsEntity;
use Okay\Modules\OkayCMS\Feeds\Entities\FeedsEntity;

class BackendFeedsHelper
{
    /** @var ProductsHelper */
    private $productsHelper;

    /** @var BackendPresetAdapterFactory */
    private $presetAdapterFactory;

    /** @var QueryFactory */
    private $queryFactory;

    /** @var Request */
    private $request;


    /** @var FeedsEntity */
    private $feedsEntity;

    /** @var ConditionsEntity */
    private $conditionsEntity;

    /** @var CategoriesEntity */
    private $categoriesEntity;

    /** @var BrandsEntity */
    private $brandsEntity;

    /** @var FeaturesEntity */
    private $featuresEntity;

    /** @var FeaturesValuesEntity */
    private $featuresValuesEntity;

    public function __construct(
        EntityFactory               $entityFactory,
        ProductsHelper              $productsHelper,
        BackendPresetAdapterFactory $backendPresetAdapterFactory,
        QueryFactory                $queryFactory,
        Request                     $request
    ) {
        $this->productsHelper       = $productsHelper;
        $this->presetAdapterFactory = $backendPresetAdapterFactory;
        $this->queryFactory         = $queryFactory;
        $this->request              = $request;

        $this->feedsEntity           = $entityFactory->get(FeedsEntity::class);
        $this->conditionsEntity      = $entityFactory->get(ConditionsEntity::class);
        $this->categoriesEntity      = $entityFactory->get(CategoriesEntity::class);
        $this->brandsEntity          = $entityFactory->get(BrandsEntity::class);
        $this->featuresEntity        = $entityFactory->get(FeaturesEntity::class);
        $this->featuresValuesEntity  = $entityFactory->get(FeaturesValuesEntity::class);
    }

    public function getValidateError(object $feed): string
    {
        $error = '';
        if (($f = $this->feedsEntity->findOne(['url' => $feed->url])) && $f->id != $feed->id) {
            $error = 'url_exists';
        } elseif (empty($feed->name)) {
            $error = 'empty_name';
        } elseif (substr($feed->url, -1) == '-' || substr($feed->url, 0, 1) == '-') {
            $error = 'url_wrong';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function prepareAdd(object $feed): object
    {
        return ExtenderFacade::execute(__METHOD__, $feed, func_get_args());
    }

    public function add(object $feed)
    {
        $insertedId = $this->feedsEntity->add($feed);
        return ExtenderFacade::execute(__METHOD__, $insertedId, func_get_args());
    }

    public function prepareUpdate(object $feed): object
    {
        return ExtenderFacade::execute(__METHOD__, $feed, func_get_args());
    }

    public function update($id, object $feed): bool
    {
        $result = $this->feedsEntity->update($id, $feed);
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function addConditions($feedId, array $conditions): void
    {
        foreach ($conditions as $conditionType => $typeConditions) {
            foreach ($typeConditions as $condition) {
                $conditionId = $this->conditionsEntity->add([
                    'feed_id' => $feedId,
                    'entity' => $condition['entity'],
                    'type' => $conditionType,
                    'all_entities' => $condition['all_entities'] ?? 0
                ]);

                if (!empty($condition['entities'])) {
                    $this->conditionsEntity->updateConditionEntities($conditionId, $condition['entities']);
                }
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateConditions($feedId, array $conditions): void
    {
        foreach ($conditions as $conditionId => $condition) {
            $this->conditionsEntity->updateConditionEntities($conditionId, $condition['entities'] ?? []);
            $this->conditionsEntity->update($conditionId, ['all_entities' => $condition['all_entities'] ?? 0]);
        }

        $query = $this->queryFactory->newSelect()
            ->from(ConditionsEntity::getTable())
            ->cols(['id'])
            ->where('feed_id = :feed_id')
            ->bindValues(['feed_id' => $feedId]);

        if (!empty($conditions)) {
            $query->where('id NOT IN (:ids)')
                ->bindValues(['ids' => array_keys($conditions)]);
        }

        $idsToDelete = $query->results('id');

        $this->conditionsEntity->delete($idsToDelete);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateCategorySettings($feedId, $entityId, $newSettings): void
    {
        $settings = $this->feedsEntity->cols(['categories_settings'])->findOne(['id' => $feedId]);
        $settings[$entityId] = $newSettings;
        $this->feedsEntity->update($feedId, ['categories_settings' => $settings]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateFeatureSettings($feedId, $entityId, array $newSettings): void
    {
        $settings = $this->feedsEntity->cols(['features_settings'])->findOne(['id' => $feedId]);
        $settings[$entityId] = $newSettings;
        $this->feedsEntity->update($feedId, ['features_settings' => $settings]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getCategories(): array
    {
        $categories = $this->categoriesEntity->getCategoriesTree();

        return ExtenderFacade::execute(__METHOD__, $categories, func_get_args());
    }

    public function getBrands(): array
    {
        $limit = $this->brandsEntity->count();
        $brands = $this->brandsEntity->find(['limit' => $limit]);

        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    public function getFeatures(): array
    {
        $limit = $this->featuresEntity->count();
        $features = $this->featuresEntity->order('name')->find(['limit' => $limit]);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function getFeatureValues($featureId): array
    {
        $limit = $this->featuresValuesEntity->count();
        $features = $this->featuresValuesEntity->cols(['id', 'value'])->order('value_asc')->find(['limit' => $limit, 'feature_id' => $featureId]);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function getFeed($id)
    {
        $feed = $this->feedsEntity->findOne(['id' => $id]);

        return ExtenderFacade::execute(__METHOD__, $feed, func_get_args());
    }

    public function getConditions($feedId): array
    {
        $conditions = $this->conditionsEntity->find(['feed_id' => $feedId]);

        foreach ($conditions as $condition) {
            if ($condition->entity === 'feature_value') {
                if (empty($condition->entity_ids)) {
                    $condition->all_entities = [];
                } else {
                    $featureId = $this->featuresValuesEntity->cols(['feature_id'])->findOne(['id' => reset($condition->entity_ids)]);
                    $condition->all_entities = $this->featuresValuesEntity->order('value')->find(['feature_id' => $featureId]);
                }
            } elseif ($condition->entity === 'product') {
                $condition->entities = empty($condition->entity_ids) ? [] : $this->productsHelper->getList(['id' => $condition->entity_ids]);
            }
        }

        $conditions = array_reduce($conditions, function($result, $condition) {
            $result[$condition->type.'s'][] = $condition;
            return $result;
        }, ['inclusions' => [], 'exclusions' => []]);

        return ExtenderFacade::execute(__METHOD__, $conditions, func_get_args());
    }

    public function sortPositions(array $positions): void
    {
        $ids = array_keys($positions);
        sort($positions);
        foreach ($positions as $i=>$position) {
            $this->feedsEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter(): array
    {
        // Пагинация
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['okay_cms__feeds__feeds_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['okay_cms__feeds__feeds_num_admin'])) {
            $filter['limit'] = $_SESSION['okay_cms__feeds__feeds_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        // Бренды
        $presetName = $this->request->get('preset');
        if($presetName) {
            $filter['preset'] = $presetName;
        }

        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $this->feedsEntity->count($filter);
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function disable(array $ids): void
    {
        $this->feedsEntity->update($ids, ['enabled' => 0]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable(array $ids): void
    {
        $this->feedsEntity->update($ids, ['enabled' => 1]);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete(array $ids): void
    {
        $this->feedsEntity->delete($ids);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function duplicate(array $ids): void
    {
        foreach($ids as $id) {
            $this->feedsEntity->duplicate((int)$id);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function loadSettings(string $presetName, array $settings)
    {
        $adapter = $this->presetAdapterFactory->get($presetName);
        $settings = $adapter->loadSettings($settings);

        return ExtenderFacade::execute(__METHOD__, $settings, func_get_args());
    }

    public function fetchSettingsTemplate(string $presetName): string
    {
        $adapter = $this->presetAdapterFactory->get($presetName);
        $settingsTemplate = $adapter->fetchSettingsTemplate();

        return ExtenderFacade::execute(__METHOD__, $settingsTemplate, func_get_args());
    }

    public function fetchSettingsTemplates(): array
    {
        $settingsTemplates = [];
        foreach ($this->presetAdapterFactory->getPresets() as $presetName => $preset) {
            $adapter = $this->presetAdapterFactory->get($presetName);
            $settingsTemplates[$presetName] = $adapter->fetchSettingsTemplate();
        }

        return ExtenderFacade::execute(__METHOD__, $settingsTemplates, func_get_args());
    }

    public function getPresets(): array
    {
        $presets = $this->presetAdapterFactory->getPresets();

        return ExtenderFacade::execute(__METHOD__, $presets, func_get_args());
    }

    public function registerSettingsBlocks(string $presetName): void
    {
        $adapter = $this->presetAdapterFactory->get($presetName);
        $adapter->registerCategorySettingsBlock();
        $adapter->registerFeatureSettingsBlock();
    }
}