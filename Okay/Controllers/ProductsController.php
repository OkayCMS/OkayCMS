<?php


namespace Okay\Controllers;


use Okay\Core\Image;
use Okay\Core\Money;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Entities\ProductsEntity;
use Okay\Helpers\CanonicalHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetadataHelpers\AllProductsMetadataHelper;
use Okay\Helpers\MetaRobotsHelper;
use Okay\Helpers\ProductsHelper;

class ProductsController extends AbstractController
{
    public function render(
        CatalogHelper             $catalogHelper,
        ProductsHelper            $productsHelper,
        ProductsEntity            $productsEntity,
        FilterHelper              $filterHelper,
        AllProductsMetadataHelper $allProductsMetadataHelper,
        CanonicalHelper           $canonicalHelper,
        MetaRobotsHelper          $metaRobotsHelper,
                                  $filtersUrl = ''
    ) {
        $this->design->assign('filtersUrl', !empty($filtersUrl) ? '/'.$filtersUrl : '', true);
        $this->design->assign('ajax_filter_route', 'products_features', true);

        $catalogFeatures = $productsHelper->getCatalogFeatures();

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);

        if (($productsFilter = $productsHelper->getProductsFilter($filtersUrl)) === null) {
            return false;
        }

        if (($currentPage = $filterHelper->getCurrentPage($filtersUrl)) === false) {
            return false;
        }

        $filterHelper->generateCacheKey("products");

        $filterHelper->changeLangUrls($filtersUrl);

        $productsSort = $catalogHelper->getProductsSort($filtersUrl);

        $this->design->assign('sort', $productsSort);

        $metaArray = $filterHelper->getMetaArray($filtersUrl);

        // Если в строке есть параметры которые не должны быть в фильтре, либо параметры с другой категории, бросаем 404
        if (!empty($metaArray['features_values'])
            && array_intersect_key($metaArray['features_values'], $catalogFeatures) !== $metaArray['features_values']
        ) {
            return false;
        }

        $isFilterPage = $productsHelper->isFilterPage($productsFilter);
        $this->design->assign('is_filter_page', $isFilterPage);

        if (!$this->settings->get('deferred_load_features') || $this->request->get('ajax','boolean')) {
            $productsHelper->assignFilterProcedure(
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
        
        // Если нашелся только один товар, перенаправим сразу на него
        if (!empty($productsFilter['keyword']) && count($products) == 1) {
            $product = reset($products);
            Response::redirectTo(Router::generateUrl('product', [
                'url' => $product->url,
            ], true));
        }
        
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
            ->find(['limit' => 1]);
        if ($this->page) {
            $lastModify[] = $this->page->last_modify;
        }
        $this->response->setHeaderLastModify(max($lastModify));
        //lastModify END

        $relPrevNext = $this->design->fetch('products_rel_prev_next.tpl');
        $this->design->assign('rel_prev_next', $relPrevNext);

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
            $canonical = Router::generateUrl('products', [], true);
            $chpuUrl = $filterHelper->filterChpuUrl($canonicalData);
            $chpuUrl = ltrim($chpuUrl, '/');
            if (!empty($chpuUrl)) {
                $canonical = rtrim($canonical, '/') . '/' . $chpuUrl;
            }

            $this->design->assign('canonical', $canonical);
        }

        $allProductsMetadataHelper->setUp(
            $productsFilter['keyword'] ?? '',
            $this->design->getVar('is_all_pages'),
            $this->design->getVar('current_page_num')
        );

        $this->setMetadataHelper($allProductsMetadataHelper);
        
        $this->response->setContent('products.tpl');
    }
    
    public function ajaxSearch(ProductsHelper $productsHelper, Image $image, Money $money, Router $router)
    {

        $filter['keyword'] = $this->request->get('query', null, null, false);
        $filter['keyword'] = strip_tags($filter['keyword']);
        $filter['visible'] = true;
        $filter['limit'] = 10;

        $products = $productsHelper->getList($filter, 'name');

        $suggestions = [];
        if (!empty($products)) {
            foreach ($products as $product) {
                $suggestion = new \stdClass();
                if (isset($product->image)) {
                    $product->image = $image->getResizeModifier($product->image->filename, 35, 35);
                }

                $product->url = $router->generateUrl('product', ['url' => $product->url]);

                $suggestion->price = $money->convert($product->variant->price);
                $suggestion->currency = $this->currency->sign;
                $suggestion->value = $product->name;
                $suggestion->data = $product;
                $suggestions[] = $suggestion;
            }
        }

        $res = new \stdClass;
        $res->query = $filter['keyword'];
        $res->suggestions = $suggestions;

        $this->response->setContent(json_encode($res), RESPONSE_JSON);
    }

    public function getFilter(
        FilterHelper   $filterHelper,
        ProductsHelper $productsHelper,
                       $filtersUrl = ''
    ) {
        // Если ленивая отложенная загрузка фильтра отключена, этот метод должен давать 404
        if (!$this->settings->get('deferred_load_features')) {
            return false;
        }

        $catalogFeatures = $productsHelper->getCatalogFeatures();

        $filterHelper->setFiltersUrl($filtersUrl);
        $filterHelper->setFeatures($catalogFeatures);

        if (($productsFilter = $productsHelper->getProductsFilter($filtersUrl)) === null) {
            return false;
        }

        $this->design->assign('is_filter_page', $productsHelper->isFilterPage($productsFilter));

        $productsHelper->assignFilterProcedure(
            $productsFilter,
            $catalogFeatures
        );

        $this->design->assign('furlRoute', 'products');

        $response = [
            'features' => $this->design->fetch('features.tpl', true),
            'selected_features' => $this->design->fetch('selected_features.tpl', true),
        ];

        $this->response->setContent(json_encode($response), RESPONSE_JSON);
    }
}
