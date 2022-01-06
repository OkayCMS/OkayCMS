<?php

namespace Okay\Controllers;

use Okay\Core\Router;
use Okay\Core\Routes\RouteFactory;
use Okay\Entities\BrandsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Helpers\BrandsHelper;
use Okay\Helpers\CanonicalHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetadataHelpers\BrandMetadataHelper;
use Okay\Helpers\MetaRobotsHelper;
use Okay\Helpers\ProductsHelper;

class BrandController extends AbstractController
{
    /*Отображение страницы бренда*/
    public function render(
        BrandsEntity        $brandsEntity,
        CatalogHelper       $catalogHelper,
        ProductsHelper      $productsHelper,
        ProductsEntity      $productsEntity,
        FilterHelper        $filterHelper,
        BrandMetadataHelper $brandMetadataHelper,
        CanonicalHelper     $canonicalHelper,
        MetaRobotsHelper    $metaRobotsHelper,
        BrandsHelper        $brandsHelper,
        RouteFactory        $routeFactory,
                            $url,
                            $filtersUrl = ''
    ) {
        $brandRoute = $routeFactory->create('brand');
        $this->design->assign('url', $brandRoute->generateSlugUrl($url), true);
        $this->design->assign('filtersUrl', !empty($filtersUrl) ? '/'.$filtersUrl : '', true);
        $this->design->assign('ajax_filter_route', 'brand_features', true);

        $brand = $brandsEntity->get((string)$url);

        if (empty($brand) || (!$brand->visible && empty($_SESSION['admin']))) {
            return false;
        }

        //метод можно расширять и отменить либо переопределить дальнейшую логику работы контроллера
        if (($setBrand = $brandsHelper->setBrand($brand)) !== null) {
            return $setBrand;
        }

        $catalogFeatures = $brandsHelper->getCatalogFeatures($brand);

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);
        $filterHelper->setFeaturesValuesFilter(['brand_id' => $brand->id]);

        if (($productsFilter = $brandsHelper->getProductsFilter($brand, $filtersUrl)) === null) {
            return false;
        }

        if (($currentPage = $filterHelper->getCurrentPage($filtersUrl)) === false) {
            return false;
        }

        $filterHelper->generateCacheKey("brand-{$brand->id}");

        $filterHelper->changeLangUrls($filtersUrl);

        $productsSort = $catalogHelper->getProductsSort($filtersUrl);

        $this->design->assign('brand', $brand);
        $this->design->assign('sort', $productsSort);

        $metaArray = $filterHelper->getMetaArray($filtersUrl);

        // Если в строке есть параметры которые не должны быть в фильтре, либо параметры с другой категории, бросаем 404
        if (!empty($metaArray['features_values'])
            && array_intersect_key($metaArray['features_values'], $catalogFeatures) !== $metaArray['features_values']
            || !empty($metaArray['brand'])
            && array_intersect_key($metaArray['brand'], [$brand]) !== $metaArray['brand']
        ) {
            return false;
        }

        $isFilterPage = $brandsHelper->isFilterPage($productsFilter);
        $this->design->assign('is_filter_page', $isFilterPage);

        if (!$this->settings->get('deferred_load_features') || $this->request->get('ajax','boolean')) {
            $brandsHelper->assignBrandFilterProcedure(
                $productsFilter,
                $catalogFeatures,
                $brand
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
                'brand_id' => $productsFilter['brand_id'],
                'limit' => 1,
            ]);
        $lastModify[] = $brand->last_modify;
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
                [])
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

        $brandMetadataHelper->setUp(
            $brand,
            $isFilterPage,
            $this->design->getVar('is_all_pages'),
            $this->design->getVar('current_page_num'),
            $filterHelper->getMetaArray($filtersUrl),
            $productsFilter['keyword'] ?? null

        );
        $this->setMetadataHelper($brandMetadataHelper);
        
        $this->response->setContent('products.tpl');
    }

    public function getFilter(
        BrandsEntity $brandsEntity,
        FilterHelper $filterHelper,
        BrandsHelper $brandsHelper,
                     $url,
                     $filtersUrl = ''
    ) {
        // Если ленивая отложенная загрузка фильтра отключена, этот метод должен давать 404
        if (!$this->settings->get('deferred_load_features')) {
            return false;
        }

        $brand = $brandsEntity->get((string)$url);

        if (empty($brand) || (!$brand->visible && empty($_SESSION['admin']))) {
            return false;
        }

        $catalogFeatures = $brandsHelper->getCatalogFeatures($brand);

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);
        $filterHelper->setFeaturesValuesFilter(['brand_id' => $brand->id]);

        if (($productsFilter = $brandsHelper->getProductsFilter($brand, $filtersUrl)) === null) {
            return false;
        }

        $this->design->assign('is_filter_page', $brandsHelper->isFilterPage($productsFilter));

        $brandsHelper->assignBrandFilterProcedure(
            $productsFilter,
            $catalogFeatures,
            $brand
        );

        $this->design->assign('furlRoute', 'brand');
        $this->design->assign('brand', $brand);

        $response = [
            'features' => $this->design->fetch('features.tpl', true),
            'selected_features' => $this->design->fetch('selected_features.tpl', true),
        ];

        $this->response->setContent(json_encode($response), RESPONSE_JSON);
    }
}