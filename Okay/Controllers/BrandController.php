<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Entities\BrandsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\CanonicalHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetadataHelpers\BrandMetadataHelper;
use Okay\Helpers\MetaRobotsHelper;
use Okay\Helpers\ProductsHelper;

class BrandController extends AbstractController
{

    private $catalogType = 'brand';

    /*Отображение страницы бренда*/
    public function render(
        BrandsEntity $brandsEntity,
        CategoriesEntity $categoriesEntity,
        CatalogHelper $catalogHelper,
        ProductsHelper $productsHelper,
        ProductsEntity $productsEntity,
        FilterHelper $filterHelper,
        BrandMetadataHelper $brandMetadataHelper,
        CanonicalHelper $canonicalHelper,
        MetaRobotsHelper $metaRobotsHelper,
        $url,
        $filtersUrl = ''
    ) {

        $isFilterPage = false;
        $filterHelper->setFiltersUrl($filtersUrl);

        $this->setMetadataHelper($brandMetadataHelper);
        
        $sortProducts = null;
        $filter['visible'] = 1;

        $brand = $brandsEntity->get((string)$url);
        if (empty($brand) || (!$brand->visible && empty($_SESSION['admin']))) {
            return false;
        }

        // Если нашли фильтр по бренду, кидаем 404
        if (($currentBrandsIds = $filterHelper->getCurrentBrands($filtersUrl)) === false || !empty($currentBrandsIds)) {
            return false;
        }

        // Если нашли фильтр по свойствам, кидаем 404
        if (($currentFeatures = $filterHelper->getCurrentCategoryFeatures($filtersUrl)) === false || !empty($currentFeatures)) {
            return false;
        }
        
        if (($currentOtherFilters = $filterHelper->getCurrentOtherFilters($filtersUrl)) === false) {
            return false;
        }

        if (($currentPage = $filterHelper->getCurrentPage($filtersUrl)) === false) {
            return false;
        }
        
        if (($currentSort = $filterHelper->getCurrentSort($filtersUrl)) === false) {
            return false;
        }

        if (!empty($currentOtherFilters)) {
            $filter['other_filter'] = $currentOtherFilters;
            $this->design->assign('selected_other_filters', $currentOtherFilters);
        }

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
        
        $filter['price'] = $catalogHelper->getPriceFilter($this->catalogType, $brand->id);
        
        $brand->categories = $categoriesEntity->find(['brand_id'=>$brand->id, 'category_visible'=>1]);
        $this->design->assign('brand', $brand);
        $filter['brand_id'] = $brand->id;
        
        $this->design->assign('other_filters', $catalogHelper->getOtherFilters($filter));

        if ((!empty($filter['price']) && $filter['price']['min'] !== '' && $filter['price']['max'] !== '' && $filter['price']['min'] !== null) || !empty($filter['other_filter'])) {
            $isFilterPage = true;
        }
        $this->design->assign('is_filter_page', $isFilterPage);
        
        $prices = $catalogHelper->getPrices($filter, $this->catalogType, $brand->id);
        $this->design->assign('prices', $prices);

        $filter = $filterHelper->getBrandProductsFilter($filter);

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
                'brand_id' => $filter['brand_id'],
                'limit' => 1,
            ]);
        $lastModify[] = $brand->last_modify;
        if ($this->page) {
            $lastModify[] = $this->page->last_modify;
        }
        $this->response->setHeaderLastModify(max($lastModify));
        //lastModify END

        switch ($metaRobotsHelper->getCatalogRobots($currentPage, $currentOtherFilters)) {
            case ROBOTS_NOINDEX_FOLLOW:
                $this->design->assign('noindex_follow', true);
                break;
            case ROBOTS_NOINDEX_NOFOLLOW:
                $this->design->assign('noindex_nofollow', true);
                break;
        }

        if ($canonicalData = $canonicalHelper->getCatalogCanonicalData($currentPage, $currentOtherFilters)) {
            $canonical = Router::generateUrl('brand', ['url' => $brand->url], true);
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

}
