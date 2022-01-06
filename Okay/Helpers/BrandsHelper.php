<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;

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

    /** @var Design */
    private $design;


    /** @var CategoriesEntity */
    private $categoriesEntity;

    /** @var BrandsEntity */
    private $brandsEntity;
    
    public function __construct(
        EntityFactory $entityFactory,
        CatalogHelper $catalogHelper,
        Settings      $settings,
        FilterHelper  $filterHelper,
        Design        $design
    ) {
        $this->entityFactory = $entityFactory;
        $this->catalogHelper = $catalogHelper;
        $this->settings      = $settings;
        $this->filterHelper  = $filterHelper;
        $this->design        = $design;

        $this->categoriesEntity = $entityFactory->get(CategoriesEntity::class);
        $this->brandsEntity     = $entityFactory->get(BrandsEntity::class);
    }

    public function assignBrandFilterProcedure(
        array  $productsFilter,
        array  $catalogFeatures,
        object $brand
    ): void {
        $catalogCategories = $this->categoriesEntity->find(['brand_id' => $brand->id]);

        $this->catalogHelper->assignCatalogDataProcedure(
            $productsFilter,
            $catalogFeatures,
            $catalogCategories,
            []
        );

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function assignBrandsFilterProcedure(
        array  $productsFilter,
        array  $catalogFeatures
    ): void {
        $this->catalogHelper->assignCatalogDataProcedure(
            $productsFilter,
            $catalogFeatures,
            [],
            [],
            (int) $this->settings->get('features_max_count_products')
        );

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function isFilterPage(array $filter): bool
    {
        unset($filter['brand_id']);

        return ExtenderFacade::execute(__METHOD__, $this->filterHelper->isFilterPage($filter), func_get_args());
    }

    public function getBrandsFilter(array $productsFilter = [])
    {
        $brandsFilter = $this->filterHelper->prepareFilterGetBrands($productsFilter);

        return ExtenderFacade::execute(__METHOD__, $brandsFilter, func_get_args());
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

    public function getCatalogFeatures(?object $brand = null): array
    {
        $filter = $this->catalogHelper->getCatalogFeaturesFilter();

        $filter['brand'] = true;
        if ($brand) {
            $filter['brand_id'] = $brand->id;
        }

        $features = $this->catalogHelper->getCatalogFeatures($filter);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function getProductsFilter(?object $brand = null, ?string $filtersUrl = null, array $filter = []): ?array
    {
        if (($filter = $this->catalogHelper->getProductsFilter($filtersUrl, $filter)) === null) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        if (!empty($filter['brand_id'])) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        if ($brand) {
            $filter['brand_id'] = [$brand->id];
        }


        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getBrandsAjaxFilterData()
    {
        $result = new \stdClass;
        $result->products_content = $this->design->fetch('brands_content.tpl');
        $result->products_pagination = '';
        $result->products_sort = '';
        $result->features = $this->design->fetch('features.tpl');
        $result->selected_features = $this->design->fetch('selected_features.tpl');

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function paginateBrands($itemsPerPage, $currentPage, array &$filter, Design $design)
    {
        // Вычисляем количество страниц
        $brandsCount = $this->brandsEntity->count($filter);

        // Показать все страницы сразу
        $allPages = false;
        if ($currentPage == 'all') {
            $allPages = true;
            $itemsPerPage = $brandsCount;
        }

        // Если не задана, то равна 1
        $currentPage = max(1, (int)$currentPage);
        $design->assign('current_page_num', $currentPage);
        $design->assign('is_all_pages', $allPages);

        $pagesNum = !empty($itemsPerPage) ? ceil($brandsCount/$itemsPerPage) : 0;
        $design->assign('total_pages_num', $pagesNum);

        $filter['page'] = $currentPage;
        $filter['limit'] = $itemsPerPage;

        $result = true;
        if ($allPages === false && $currentPage > 1 && $currentPage > $pagesNum) {
            $result = false;
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
}