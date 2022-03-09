<?php

namespace Okay\Controllers;

use Okay\Core\Router;
use Okay\Core\Routes\RouteFactory;
use Okay\Entities\BrandsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\CanonicalHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\CategoriesHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetadataHelpers\CategoryMetadataHelper;
use Okay\Helpers\MetaRobotsHelper;
use Okay\Helpers\ProductsHelper;

class CategoryController extends AbstractController
{
    public function render(
        BrandsEntity           $brandsEntity,
        CategoriesEntity       $categoriesEntity,
        CatalogHelper          $catalogHelper,
        ProductsHelper         $productsHelper,
        FilterHelper           $filterHelper,
        ProductsEntity         $productsEntity,
        CategoryMetadataHelper $categoryMetadataHelper,
        CanonicalHelper        $canonicalHelper,
        MetaRobotsHelper       $metaRobotsHelper,
        RouteFactory           $routeFactory,
        CategoriesHelper       $categoriesHelper,
                               $url,
                               $filtersUrl = ''
    ) {
        $categoryRoute = $routeFactory->create('category');
        $this->design->assign('url', $categoryRoute->generateSlugUrl($url), true);
        $this->design->assign('filtersUrl', !empty($filtersUrl) ? '/'.$filtersUrl : '', true);
        $this->design->assign('ajax_filter_route', 'category_features', true);

        $category = $categoriesEntity->get((string)$url);

        if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
            return false;
        }

        //метод можно расширять и отменить дальнейшую логику работы контроллера
        if (($setCategory = $categoriesHelper->setCatalogCategory($category)) !== null) {
            return $setCategory;
        }

        $catalogFeatures = $categoriesHelper->getCatalogFeatures($category);

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);
        $filterHelper->setFeaturesValuesFilter(['category_id' => $category->children]);

        if (($productsFilter = $categoriesHelper->getProductsFilter($category, $filtersUrl)) === null) {
            return false;
        }
        
        if (($currentPage = $filterHelper->getCurrentPage($filtersUrl)) === false) {
            return false;
        }

        $filterHelper->generateCacheKey("category-{$category->id}");

        $filterHelper->changeLangUrls($filtersUrl);

        $productsSort = $catalogHelper->getProductsSort($filtersUrl);

        $this->design->assign('category', $category);
        $this->design->assign('sort', $productsSort);

        $catalogBrands = $brandsEntity->mappedBy('id')->find([
            'category_id' => $category->children,
            'visible' => 1,
            'product_visible' => 1,
        ]);
         
        $metaArray = $filterHelper->getMetaArray($filtersUrl);

        // Если в строке есть параметры которые не должны быть в фильтре, либо параметры с другой категории, бросаем 404
        if (!empty($metaArray['features_values'])
            && array_intersect_key($metaArray['features_values'], $catalogFeatures) !== $metaArray['features_values']
            || !empty($metaArray['brand'])
            && array_intersect_key($metaArray['brand'], $catalogBrands) !== $metaArray['brand']
        ) {
            return false;
        }

        $isFilterPage = $categoriesHelper->isFilterPage($productsFilter);
        $this->design->assign('is_filter_page', $isFilterPage);

        if (!$this->settings->get('deferred_load_features') || $this->request->get('ajax','boolean')) {
            $categoriesHelper->assignFilterProcedure(
                $productsFilter,
                $catalogFeatures,
                $category
            );
        } else {
            // если включена отложенная загрузка фильтров, установим отдельно возможные значения свойств
            $baseFeaturesValues = $catalogHelper->getBaseFeaturesValues(null, $this->settings->get('missing_products'));

            if (!empty($baseFeaturesValues)) {
                foreach ($baseFeaturesValues as $values) {
                    foreach ($values as $value) {
                        if (isset($catalogFeatures[$value->feature_id])) {
                            $catalogFeatures[$value->feature_id]->features_values[$value->id] = $value;
                        }
                    }
                }
            }
            foreach ($catalogFeatures as $k => $feature) {
                if (!property_exists($feature, 'features_values') || empty($feature->features_values)) {
                    unset($catalogFeatures[$k]);
                }
            }
            
            $metaRobotsHelper->setAvailableFeatures($catalogFeatures);
        }
        
        if (!$catalogHelper->paginate(
            $this->settings->get('products_num'),
            $currentPage,
            $productsFilter,
            $this->design
        )) {
            return false;
        }

        // Товары
        $products = $productsHelper->getList($productsFilter, $productsSort);
        $products = $productsHelper->attachDescriptionByTemplate($products);
        $this->design->assign('products', $products);
        
        if ($this->request->get('ajax','boolean')) {
            $this->design->assign('ajax', 1);
            $result = $catalogHelper->getAjaxFilterData();
            $this->response->setContent(json_encode($result), RESPONSE_JSON);
            return true;
        }

        //lastModify
        $lastModify = $productsEntity->cols(['last_modify'])
            ->order('last_modify_desc')
            ->find([
                'category_id' => $productsFilter['category_id'],
                'limit' => 1,
            ]);
        $lastModify[] = $category->last_modify;
        if ($this->page) {
            $lastModify[] = $this->page->last_modify;
        }
        $this->response->setHeaderLastModify(max($lastModify));
        //lastModify END

        if (isset($productsFilter['keyword'])) {
            $this->design->assign('noindex_nofollow', true);
        } else {
            switch ($metaRobotsHelper->getCatalogRobots(
                $currentPage,
                $productsFilter['other_filter'] ?? [],
                $metaArray['features_values'] ?? [],
                $productsFilter['brand_id'] ?? [])
            ) {
                case ROBOTS_NOINDEX_FOLLOW:
                    $this->design->assign('noindex_follow', true);
                    break;
                case ROBOTS_NOINDEX_NOFOLLOW:
                    $this->design->assign('noindex_nofollow', true);
                    break;
            }
        }

        if (!empty($metaArray['features_values'])) {
            $canonicalFeaturesValues = array_combine(
                array_map(function ($featureId) use ($catalogFeatures) {
                    return $catalogFeatures[$featureId]->url;
                }, array_keys($metaArray['features_values'])),
                $metaArray['features_values']
            );
        } else {
            $canonicalFeaturesValues = [];
        }

        $canonicalData = $canonicalHelper->getCatalogCanonicalData(
            $currentPage,
            $productsFilter['other_filter'] ?? [],
            $canonicalFeaturesValues,
            $productsFilter['brand_id'] ?? []
        );

        if ($canonicalData) {
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

        $categoryMetadataHelper->setUp(
            $category,
            $isFilterPage,
            $this->design->getVar('is_all_pages'),
            $this->design->getVar('current_page_num'),
            $productsFilter['features'] ?? [],
            $metaArray,
            $productsFilter['keyword'] ?? null
        );
        $this->setMetadataHelper($categoryMetadataHelper);

        $this->response->setContent('products.tpl');
    }

    public function getFilter(
        CategoriesEntity $categoriesEntity,
        FilterHelper     $filterHelper,
        CategoriesHelper $categoriesHelper,
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

        $catalogFeatures = $categoriesHelper->getCatalogFeatures($category);

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);
        $filterHelper->setFeaturesValuesFilter(['category_id' => $category->children]);

        if (($productsFilter = $categoriesHelper->getProductsFilter($category, $filtersUrl)) === null) {
            return false;
        }

        $this->design->assign('is_filter_page', $categoriesHelper->isFilterPage($productsFilter));

        $categoriesHelper->assignFilterProcedure(
            $productsFilter,
            $catalogFeatures,
            $category
        );

        $this->design->assign('furlRoute', 'category');
        $this->design->assign('category', $category);

        $response = [
            'features' => $this->design->fetch('features.tpl', true),
            'selected_features' => $this->design->fetch('selected_features.tpl', true),
        ];

        $this->response->setContent(json_encode($response), RESPONSE_JSON);
    }
}