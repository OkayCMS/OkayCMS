<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Core\Routes\RouteFactory;
use Okay\Helpers\BrandsHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetadataHelpers\AllBrandsMetadataHelper;

class BrandsController extends AbstractController
{
    /*Отображение страницы всех брендов*/
    public function render(
        BrandsHelper            $brandsHelper,
        FilterHelper            $filterHelper,
        AllBrandsMetadataHelper $allBrandsMetadataHelper,
        RouteFactory            $routeFactory,
        CatalogHelper           $catalogHelper,
                                $filtersUrl = ''
    ) {
        $allBrandsRouteParams = $routeFactory->create('brands')->generateRouteParams();
        $this->design->assign('url', $allBrandsRouteParams->getSlug(), true);
        $this->design->assign('filtersUrl', !empty($filtersUrl) ? $filtersUrl : '');
        $this->design->assign('filtersUrl', !empty($filtersUrl) ? $filtersUrl : '', true);
        $this->design->assign('ajax_filter_route', 'brands_features', true);

        $catalogFeatures = $brandsHelper->getCatalogFeatures();

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);
        $filterHelper->setFeaturesValuesFilter(['brand' => true]);

        if (($productsFilter = $brandsHelper->getProductsFilter(null, $filtersUrl)) === null) {
            return false;
        }

        if (($currentPage = $filterHelper->getCurrentPage($filtersUrl)) === false) {
            return false;
        }

        $filterHelper->changeLangUrls($filtersUrl);

        $metaArray = $filterHelper->getMetaArray($filtersUrl);

        // Если в строке есть параметры которые не должны быть в фильтре, либо параметры с другой категории, бросаем 404
        if (!empty($metaArray['features_values'])
            && array_intersect_key($metaArray['features_values'], $catalogFeatures) !== $metaArray['features_values']
        ) {
            return false;
        }

        $isFilterPage = $brandsHelper->isFilterPage($productsFilter);
        $this->design->assign('is_filter_page', $isFilterPage);

        if (!$this->settings->get('deferred_load_features') || $this->request->get('ajax','boolean')) {
            $brandsHelper->assignBrandsFilterProcedure(
                $productsFilter,
                $catalogFeatures
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
        }

        $brandsFilter = $brandsHelper->getBrandsFilter($productsFilter);

        if (!$brandsHelper->paginateBrands(
            $this->settings->get('products_num'),
            $currentPage,
            $brandsFilter,
            $this->design
        )) {
            return false;
        }

        $brands = $brandsHelper->getList(
            $brandsFilter,
            $brandsHelper->getCurrentSort()
        );

        $this->design->assign('brands', $brands);

        if ($this->request->get('ajax','boolean')) {
            $this->design->assign('ajax', 1);
            $result = $brandsHelper->getBrandsAjaxFilterData();
            $this->response->setContent(json_encode($result), RESPONSE_JSON);
            return true;
        }

        $this->design->assign('canonical', Router::generateUrl('brands', [], true));

        if ($isFilterPage) {
            $this->design->assign('noindex_nofollow', true);
        }

        $allBrandsMetadataHelper->setUp(
            $productsFilter['keyword'] ?? null
        );

        $this->setMetadataHelper($allBrandsMetadataHelper);

        $this->response->setContent('brands.tpl');
    }

    public function getFilter(
        FilterHelper $filterHelper,
        BrandsHelper $brandsHelper,
                     $filtersUrl = ''
    ) {
        // Если ленивая отложенная загрузка фильтра отключена, этот метод должен давать 404
        if (!$this->settings->get('deferred_load_features')) {
            return false;
        }

        $catalogFeatures = $brandsHelper->getCatalogFeatures();

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);
        $filterHelper->setFeaturesValuesFilter(['brand' => true]);

        if (($productsFilter = $brandsHelper->getProductsFilter(null, $filtersUrl)) === null) {
            return false;
        }

        $this->design->assign('is_filter_page', $brandsHelper->isFilterPage($productsFilter));

        $brandsHelper->assignBrandsFilterProcedure(
            $productsFilter,
            $catalogFeatures
        );

        $this->design->assign('furlRoute', 'brands');

        $response = [
            'features' => $this->design->fetch('features.tpl', true),
            'selected_features' => $this->design->fetch('selected_features.tpl', true),
        ];

        $this->response->setContent(json_encode($response), RESPONSE_JSON);
    }
}
