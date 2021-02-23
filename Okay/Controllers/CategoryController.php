<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Entities\BrandsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\CanonicalHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetadataHelpers\CategoryMetadataHelper;
use Okay\Helpers\MetaRobotsHelper;
use Okay\Helpers\ProductsHelper;

class CategoryController extends AbstractController
{

    private $catalogType = 'category';

    /*Отображение каталога*/
    public function render(
        BrandsEntity $brandsEntity,
        CategoriesEntity $categoriesEntity,
        CatalogHelper $catalogHelper,
        ProductsHelper $productsHelper,
        FilterHelper $filterHelper,
        ProductsEntity $productsEntity,
        CategoryMetadataHelper $categoryMetadataHelper,
        CanonicalHelper $canonicalHelper,
        MetaRobotsHelper $metaRobotsHelper,
        $url,
        $filtersUrl = ''
    ) {
        $isFilterPage = false;
        $filter['visible'] = 1;
        $sortProducts = null;

        $this->design->assign('url', $url, true);
        $this->design->assign('filtersUrl', !empty($filtersUrl) ? '/'.$filtersUrl : '', true);

        $filterHelper->setFiltersUrl($filtersUrl);
        
        $this->setMetadataHelper($categoryMetadataHelper);
        
        $category = $categoriesEntity->get((string)$url);
        if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
            return false;
        }
        $this->design->assign('category', $category);
        $filter['category_id'] = $category->children;

        $filterHelper->setCategory($category);
        $categoryFeatures = $filterHelper->getCategoryFeatures();

        // Генерируем ключ кэша, для текущей страницы фильтров
        $filterCacheKey = $category->id . '-' . $filterHelper->filterChpuUrl([
            'page' => null,
            'sort' => null
        ]);
        $this->design->assign('filterCacheKey', $filterCacheKey, true);
        $this->design->assignJsVar('filterCacheKey', $filterCacheKey);
        
        if (($currentBrandsIds = $filterHelper->getCurrentBrands($filtersUrl)) === false) {
            return false;
        }
        
        if (($currentOtherFilters = $filterHelper->getCurrentOtherFilters($filtersUrl)) === false) {
            return false;
        }
        
        if (($currentPage = $filterHelper->getCurrentPage($filtersUrl)) === false) {
            return false;
        }
        
        if (($currentFeatures = $filterHelper->getCurrentCategoryFeatures($filtersUrl)) === false) {
            return false;
        }
        
        if (($currentSort = $filterHelper->getCurrentSort($filtersUrl)) === false) {
            return false;
        }

        $filterHelper->changeLangUrls($filtersUrl);

        // Если задан бренд, выберем его из базы
        if (!empty($currentBrandsIds)) {
            $filter['brand_id'] = $currentBrandsIds;
            $this->design->assign('selected_brands_ids', $currentBrandsIds);
        }

        if (!empty($currentOtherFilters)) {
            $filter['other_filter'] = $currentOtherFilters;
            $this->design->assign('selected_other_filters', $currentOtherFilters);
        }

        $filter['price'] = $catalogHelper->getPriceFilter($this->catalogType, $category->id);

        // Сортировка товаров, сохраняем в сесси, чтобы текущая сортировка оставалась для всего сайта
        if (!empty($currentSort)) {
            $_SESSION['sort'] = $currentSort;
        }
        if (!empty($_SESSION['sort'])) {
            $sortProducts = $_SESSION['sort'];
        } else {
            $sortProducts = 'position';
        }
        $this->design->assign('sort', $sortProducts);

        // Свойства товаров
        if (!empty($categoryFeatures)) {
            foreach ($categoryFeatures as $feature) {
                if (isset($currentFeatures[$feature->id])) {
                    $filter['features'][$feature->id] = $currentFeatures[$feature->id];
                }
            }
        }

        // Выбираем бренды, они нужны нам в шаблоне
        $brandsFilter = [
            'category_id' => $category->children,
            'visible' => 1,
            'product_visible' => 1,
        ];
        $categoryBrands = $brandsEntity->mappedBy('id')->find($brandsFilter);
         
        $metaArray = $filterHelper->getMetaArray();
        // Если в строке есть параметры которые не должны быть в фильтре, либо параметры с другой категории, бросаем 404
        if (!empty($metaArray['features_values']) && array_intersect_key($metaArray['features_values'], $categoryFeatures) !== $metaArray['features_values'] ||
            !empty($metaArray['brand']) && array_intersect_key($metaArray['brand'], $categoryBrands) !== $metaArray['brand']) {
            return false;
        }

        if ((!empty($filter['price']) && $filter['price']['min'] !== '' && $filter['price']['max'] !== '' && $filter['price']['min'] !== null)
            || !empty($filter['features'])
            || !empty($filter['other_filter'])
            || !empty($filter['brand_id'])
        ) {
            $isFilterPage = true;
        }
        $this->design->assign('is_filter_page', $isFilterPage);

        if (!$this->settings->get('deferred_load_features') || $this->request->get('ajax','boolean')) {
            $catalogHelper->assignCategoryFilterProcedure(
                $category,
                $filter,
                $currentBrandsIds,
                $categoryBrands,
                $categoryFeatures,
                $currentFeatures,
                $isFilterPage,
                $this->catalogType
            );
        }
        
        $this->design->assign('selected_filters', $currentFeatures);

        $filter = $filterHelper->getCategoryProductsFilter($filter);
        if ($filter === false) {
            return false;
        }
        
        $paginate = $catalogHelper->paginate(
            $this->settings->get('products_num'),
            $currentPage,
            $filter,
            $this->design
        );
        
        if (!$paginate) {
            return false;
        }

        // Товары
        $products = $productsHelper->getList($filter, $sortProducts);
        $this->design->assign('products', $products);
        
        if ($this->request->get('ajax','boolean')) {
            $this->design->assign('ajax', 1);
            $result = $catalogHelper->getAjaxFilterData($this->design);
            $this->response->setContent(json_encode($result), RESPONSE_JSON);
            return true;
        }

        //lastModify
        $lastModify = $productsEntity->cols(['last_modify'])
            ->order('last_modify_desc')
            ->find([
                'category_id' => $filter['category_id'],
                'limit' => 1,
            ]);
        $lastModify[] = $category->last_modify;
        if ($this->page) {
            $lastModify[] = $this->page->last_modify;
        }
        $this->response->setHeaderLastModify(max($lastModify));
        //lastModify END
        
        $filterFeatures = [];
        foreach ($currentFeatures as $featureId => $values) {
            if (isset($categoryFeatures[$featureId])) {
                $filterFeatures[$categoryFeatures[$featureId]->url] = $values;
            }
        }
        switch ($metaRobotsHelper->getCategoryRobots($currentPage, $currentOtherFilters, $filterFeatures, $currentBrandsIds)) {
            case ROBOTS_NOINDEX_FOLLOW:
                $this->design->assign('noindex_follow', true);
                break;
            case ROBOTS_NOINDEX_NOFOLLOW:
                $this->design->assign('noindex_nofollow', true);
                break;
        }
        
        if ($canonicalData = $canonicalHelper->getCategoryCanonicalData($currentPage, $currentOtherFilters, $filterFeatures, $currentBrandsIds)) {
            $canonical = Router::generateUrl('category', ['url' => $category->url], true);
            $chpuUrl = $filterHelper->filterChpuUrl($canonicalData);
            $chpuUrl = ltrim($chpuUrl, '/');
            if (!empty($chpuUrl)) {
                $canonical = rtrim($canonical, '/') . '/' . $chpuUrl;
            }
            
            $this->design->assign('canonical', $canonical);
        }
        
        $relPrevNext = $this->design->fetch('products_rel_prev_next.tpl');
        $this->design->assign('rel_prev_next', $relPrevNext);

        $this->response->setContent('products.tpl');
    }

    public function getFilter(
        BrandsEntity $brandsEntity,
        CatalogHelper $catalogHelper,
        CategoriesEntity $categoriesEntity,
        FilterHelper $filterHelper,
        $url,
        $filtersUrl = ''
    ) {

        // Если ленивая отложенная загрузка фильтра отключена, этот метод должен давать 404
        if (!$this->settings->get('deferred_load_features')) {
            return false;
        }

        $isFilterPage = false;
        $filter['visible'] = 1;

        $filterHelper->setFiltersUrl($filtersUrl);

        $category = $categoriesEntity->get((string)$url);

        if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
            return false;
        }

        $filterHelper->setCategory($category);

        $categoryFeatures = $filterHelper->getCategoryFeatures();

        if (($currentBrandsIds = $filterHelper->getCurrentBrands($filtersUrl)) === false) {
            return false;
        }

        if (($currentOtherFilters = $filterHelper->getCurrentOtherFilters($filtersUrl)) === false) {
            return false;
        }

        if (($currentFeatures = $filterHelper->getCurrentCategoryFeatures($filtersUrl)) === false) {
            return false;
        }

        $filter['category_id'] = $category->children;

        // Если задан бренд, выберем его из базы
        if (!empty($currentBrandsIds)) {
            $filter['brand_id'] = $currentBrandsIds;
            $this->design->assign('selected_brands_ids', $currentBrandsIds);
        }

        if (!empty($currentOtherFilters)) {
            $filter['other_filter'] = $currentOtherFilters;
            $this->design->assign('selected_other_filters', $currentOtherFilters);
        }

        $filter['price'] = $catalogHelper->getPriceFilter($this->catalogType, $category->id);

        // Свойства товаров
        if (!empty($categoryFeatures)) {
            foreach ($categoryFeatures as $feature) {
                if (isset($currentFeatures[$feature->id])) {
                    $filter['features'][$feature->id] = $currentFeatures[$feature->id];
                }
            }
        }

        // Выбираем бренды, они нужны нам в шаблоне
        $brandsFilter = [
            'category_id' => $category->children,
            'visible' => 1,
            'product_visible' => 1,
        ];
        $categoryBrands = $brandsEntity->mappedBy('id')->find($brandsFilter);

        if ((!empty($filter['price']) && $filter['price']['min'] !== '' && $filter['price']['max'] !== '' && $filter['price']['min'] !== null)
            || !empty($filter['features'])
            || !empty($filter['other_filter'])
            || !empty($filter['brand_id'])
        ) {
            $isFilterPage = true;
        }
        $this->design->assign('is_filter_page', $isFilterPage);

        $catalogHelper->assignCategoryFilterProcedure(
            $category,
            $filter,
            $currentBrandsIds,
            $categoryBrands,
            $categoryFeatures,
            $currentFeatures,
            $isFilterPage,
            $this->catalogType
        );

        $this->design->assign('selected_filters', $currentFeatures);
        
        $otherFiltersFilter = $catalogHelper->getOtherFiltersFilter($filter);
        $this->design->assign('other_filters', $catalogHelper->getOtherFilters($otherFiltersFilter));

        $prices = $catalogHelper->getPrices($filter, $this->catalogType, $category->id);
        $this->design->assign('prices', $prices);

        $this->design->assign('furlRoute', 'category');
        $this->design->assign('category', $category);

        $response = [
            'features' => $this->design->fetch('features.tpl', true),
            'selected_features' => $this->design->fetch('selected_features.tpl', true),
        ];

        $this->response->setContent(json_encode($response), RESPONSE_JSON);

    }
}
