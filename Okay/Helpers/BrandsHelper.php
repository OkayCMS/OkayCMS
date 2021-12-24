<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;

class BrandsHelper implements GetListInterface
{
    /** @var EntityFactory */
    private $entityFactory;

    /** @var CatalogHelper */
    private $catalogHelper;

    /** @var Settings */
    private $settings;

    /** @var FilterHelper */
    private $filterHelper;


    /** @var FeaturesEntity */
    private $featuresEntity;

    /** @var CategoriesEntity */
    private $categoriesEntity;
    
    public function __construct(
        EntityFactory $entityFactory,
        CatalogHelper $catalogHelper,
        Settings      $settings,
        FilterHelper  $filterHelper
    ) {
        $this->entityFactory = $entityFactory;
        $this->catalogHelper = $catalogHelper;
        $this->settings      = $settings;
        $this->filterHelper  = $filterHelper;

        $this->featuresEntity   = $entityFactory->get(FeaturesEntity::class);
        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
    }

    public function assignFilterProcedure(
        array  $productsFilter,
        array  $catalogFeatures,
        object $brand
    ): void {
        $catalogCategories = $this->categoriesEntity->find(['brand_id' => $brand->id]);

        $this->catalogHelper->assignCatalogDataProcedure(
            $productsFilter,
            $catalogFeatures,
            $this->catalogHelper->getPrices($productsFilter, 'brand', $brand->id),
            $catalogCategories,
            []
        );

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getCatalogBaseFeaturesValues(array $featuresIds, object $brand): array
    {
        $featuresValues = $this->catalogHelper->getBaseFeaturesValues([
            'brand_id' => $brand->id,
            'feature_id' => $featuresIds
        ], $this->settings->get('missing_products'));

        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    public function isFilterPage(array $filter): bool
    {
        unset($filter['brand_id']);

        return ExtenderFacade::execute(__METHOD__, $this->filterHelper->isFilterPage($filter), func_get_args());
    }

    public function getBrandsFilter(array $filter = [])
    {
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
    
    public function getCurrentSort()
    {
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {

        if ($excludedFields === null) {
            $excludedFields = $this->getExcludeFields();
        }
        
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);

        // Исключаем колонки, которые нам не нужны
        if (is_array($excludedFields) && !empty($excludedFields)) {
            $brandsEntity->cols(BrandsEntity::getDifferentFields($excludedFields));
        }
        
        if ($sortName !== null) {
            $brandsEntity->order($sortName, $this->getOrderBrandsAdditionalData());
        }
        $brands = $brandsEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    public function getExcludeFields()
    {
        $excludedFields = [
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
        return ExtenderFacade::execute(__METHOD__, $excludedFields, func_get_args());
    }
    
    // Данный метод остаётся для обратной совместимости, но объявлен как deprecated, и будет удалён в будущих версиях
    public function getBrandsList($filter = [], $sort = null)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use getList', E_USER_DEPRECATED);
        $brands = $this->getList($filter, $sort, false);
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    private function getOrderBrandsAdditionalData()
    {
        $orderAdditionalData = [];
        return ExtenderFacade::execute(__METHOD__, $orderAdditionalData, func_get_args());
    }

    /**
     * Метод проверяет доступность бренда для показа в контроллере
     * можно переопределить логику работы контроллера и отменить дальнейшие действия
     * для этого после реализации другой логики необходимо вернуть true из экстендера
     *
     * @param object $brand
     * @return object
     */
    public function setBrand($brand)
    {
        if (empty($brand) || (!$brand->visible && empty($_SESSION['admin']))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getCatalogFeatures(object $brand): array
    {
        $features = $this->featuresEntity->mappedBy('id')->find([
            'brand_id' => $brand->id,
            'in_filter' => 1,
            'visible' => 1,
        ]);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function getProductsFilter(object $brand, ?string $filtersUrl = null, array $filter = []): ?array
    {
        if (($filter = $this->catalogHelper->getProductsFilter($filtersUrl, $filter)) === null) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        if (!empty($filter['brand_id'])) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        $filter['brand_id'] = [$brand->id];
        $filter['price'] = $this->catalogHelper->getPriceFilter('brand', $brand->id);

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
}