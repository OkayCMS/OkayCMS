<?php


namespace Okay\Helpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Money as MoneyCore;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class CatalogHelper
{
    /** @var MoneyCore */
    private $money;

    /** @var EntityFactory */
    private $entityFactory;

    /** @var Settings */
    private $settings;

    /** @var Request */
    private $request;

    /** @var FilterHelper */
    private $filterHelper;

    /** @var MetaRobotsHelper */
    private $metaRobotsHelper;

    /** @var Design */
    private $design;


    /** @var FeaturesEntity */
    private $featuresEntity;


    /** @var string[] */
    private $otherFilters = [
        'discounted',
        'featured',
    ];

    public function __construct(
        EntityFactory    $entityFactory,
        MoneyCore        $money,
        Settings         $settings,
        Request          $request,
        FilterHelper     $filterHelper,
        MetaRobotsHelper $metaRobotsHelper,
        Design           $design
    )
    {
        $this->entityFactory    = $entityFactory;
        $this->money            = $money;
        $this->settings         = $settings;
        $this->request          = $request;
        $this->filterHelper     = $filterHelper;
        $this->metaRobotsHelper = $metaRobotsHelper;
        $this->design           = $design;

        $this->featuresEntity = $entityFactory->get(FeaturesEntity::class);
    }
    
    public function assignCatalogDataProcedure(
        array  $productsFilter,
        array  $catalogFeatures,
        object $catalogPrices,
        array  $catalogCategories,
        ?array $catalogBrands = null,
        ?int   $featuresLimit = null
    ): void {
        if ($catalogBrands === null) {
            $brandsFilter = $this->filterHelper->prepareFilterGetBrands($productsFilter);
            $catalogBrands = $this->filterHelper->getBrands($brandsFilter);
        }

        $otherFiltersFilter = $this->getOtherFiltersFilter($productsFilter);

        if (!empty($catalogFeatures)) {
            /**
             * Получаем значения свойств для категории, чтобы на страницах фильтров убрать фильтры
             * у которых изначально был только один вариант выбора
             */
            $featuresIds = array_map(function (object $feature) {
                return $feature->id;
            }, $catalogFeatures);
            $baseFeaturesValues = $this->getBaseFeaturesValues(['feature_id' => $featuresIds], $this->settings->get('missing_products'));

            // Дополняем массив $catalogFilterFeatures значениями, которые в данный момент выбраны
            // и были изначально, но их фильтрация (по бренду или цене) отсекла.
            if (!empty($baseFeaturesValues)) {
                foreach ($baseFeaturesValues as $values) {
                    foreach ($values as $value) {
                        if (isset($productsFilter['features'][$value->feature_id][$value->id]) && isset($catalogFeatures[$value->feature_id])) {
                            $catalogFeatures[$value->feature_id]->features_values[$value->id] = $value;
                        }
                    }
                }
            }

            // Достаём значения свойств текущей категории
            $featuresValuesFilter = $this->filterHelper->prepareFilterGetFeaturesValues($productsFilter, $this->settings->get('missing_products'));
            foreach ($this->filterHelper->getFeaturesValues($featuresValuesFilter) as $featureValue) {
                if (isset($catalogFeatures[$featureValue->feature_id])) {
                    $this->filterHelper->setFeatureValue($featureValue);
                    $catalogFeatures[$featureValue->feature_id]->features_values[$featureValue->id] = $featureValue;
                }
            }

            $unusedFeatures = 0;
            foreach ($catalogFeatures as $i => $feature) {
                // Если хоть одно значение свойства выбрано, его убирать нельзя
                if (empty($productsFilter['features'][$feature->id])) {
                    // На странице фильтра убираем свойства у которых вообще нет значений (отфильтровались)
                    // или они изначально имели только один вариант выбора
                    if (
                        ($featuresLimit !== null && $unusedFeatures >= $featuresLimit)
                        || !isset($baseFeaturesValues[$feature->id])
                        || ($this->settings->get('hide_single_filters')
                            && ((count($baseFeaturesValues[$feature->id]) <= 1)
                                || !isset($feature->features_values)
                                || count($feature->features_values) <= 1))
                    ) {
                        unset($catalogFeatures[$i]);
                    } else {
                        $unusedFeatures++;
                    }
                }
            }
        }

        // Установим возможные значения свойств
        $this->metaRobotsHelper->setAvailableFeatures($catalogFeatures);

        $this->design->assign('catalog_categories', $catalogCategories);
        $this->design->assign('catalog_brands', $catalogBrands);
        $this->design->assign('catalog_other_filters', $this->getOtherFilters($otherFiltersFilter));
        $this->design->assign('catalog_features', $catalogFeatures);
        $this->design->assign('catalog_prices', $catalogPrices);

        $this->design->assign('selected_catalog_features', $productsFilter['features'] ?? []);
        $this->design->assign('selected_catalog_brands_ids', $productsFilter['brand_id'] ?? []);
        $this->design->assign('selected_catalog_other_filters', $productsFilter['other_filter'] ?? []);
    }
    
    public function getPriceFilter($catalogType, $objectId = null)
    {
        $resultPrice = [];
        $priceFilter = $this->getPriceFromStorage($catalogType, $objectId);
        
        $currentPrices = [];
        if ($this->request->get('p')) {
            $currentPrices = $this->request->get('p');
            if (isset($currentPrices['min'])) {
                $currentPrices['min'] = $this->money->convert($currentPrices['min'], null, false, true);
            }

            if (isset($currentPrices['max'])) {
                $currentPrices['max'] = $this->money->convert($currentPrices['max'], null, false, true);
            }
        }

        if (isset($currentPrices['min']) && isset($currentPrices['max']) && $currentPrices['max'] !== '' && $currentPrices['min'] !== '' && $currentPrices['min'] !== null) {
            $resultPrice = $currentPrices;
        }

        if (empty($resultPrice) && $priceFilter['price_range']['min'] !== '' && $priceFilter['price_range']['max'] !== '' && $priceFilter['price_range']['min'] !== null) {
            $resultPrice = $priceFilter['price_range'];
        }
        
        return ExtenderFacade::execute(__METHOD__, $resultPrice, func_get_args());
    }
    
    public function getPrices(array &$filter, $catalogType, $objectId = null): object
    {
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        $priceFilter = $this->getPriceFromStorage($catalogType, $objectId);

        $prices = [];
        if ($this->request->get('p')) {
            $prices['current'] = $this->request->get('p');

            if (isset($prices['current']['min'])) {
                $prices['current']['min'] = $this->money->convert($prices['current']['min'], null, false, true);
            }

            if (isset($prices['current']['max'])) {
                $prices['current']['max'] = $this->money->convert($prices['current']['max'], null, false, true);
            }
        }
        if (isset($prices['current']['min']) && isset($prices['current']['max']) && $prices['current']['max'] !== '' && $prices['current']['min'] !== '' && $prices['current']['min'] !== null) {
            $filterPrice = $prices['current'];
        } else {
            unset($prices['current']);
        }
        
        // Если прилетела фильтрация по цене, запомним её
        if (!empty($filterPrice)) {
            $priceFilter['price_range'] = $filterPrice;
            // Если в куках есть сохраненный фильтр по цене, применяем его
        } elseif ($priceFilter['price_range']['min'] !== '' && $priceFilter['price_range']['max'] !== '' && $priceFilter['price_range']['min'] !== null) {
            $prices['current'] = $priceFilter['price_range'];
        }

        if (!empty($filter['price']['min'])) {
            $filter['price']['min'] = round($this->money->convert($filter['price']['min'], null, false));
        }

        if (!empty($filter['price']['max'])) {
            $filter['price']['max'] = round($this->money->convert($filter['price']['max'], null, false));
        }

        if (isset($prices['current'])) {
            $prices['current'] = (object)$prices['current'];
        }
        $prices = (object)$prices;
        
        $rangeFilter = $filter;
        unset($rangeFilter['price']);
        $prices->range = $productsEntity->getPriceRange($rangeFilter);

        if (isset($prices->current->min)) {
            $prices->current->min = round($this->money->convert($prices->current->min, null, false));
        }
        if (isset($prices->current->max)) {
            $prices->current->max = round($this->money->convert($prices->current->max, null, false));
        }
        
        // Вдруг вылезли за диапазон доступного...
        if (isset($prices->current->min) && $prices->range->min !== '') {
            if ($prices->current->min < $prices->range->min) {
                $prices->current->min = $filter['price']['min'] = $prices->range->min;
            }
            if ($prices->current->min > $prices->range->max) {
                $prices->current->min = $filter['price']['min'] = $prices->range->max;
            }
        }
        if (isset($prices->current->max) && $prices->range->max !== '') {
            if ($prices->current->max > $prices->range->max) {
                $prices->current->max = $filter['price']['max'] = $prices->range->max;
            }
            if ($prices->current->max < $prices->range->min) {
                $prices->current->max = $filter['price']['max'] = $prices->range->min;
            }
        }

        // Сохраняем фильтр в куки
        setcookie("price_filter", json_encode($priceFilter), time()+3600*24*1, "/");

        return ExtenderFacade::execute(__METHOD__, $prices, func_get_args());
    }
    
    public function getOtherFiltersFilter(array $filter)
    {

        if (!empty($filter['price']) && $filter['price']['min'] != '' && $filter['price']['max'] != '') {
            if (isset($filter['price']['min'])) {
                $filter['price']['min'] = round($this->money->convert($filter['price']['min'], null, false));
            }

            if (isset($filter['price']['max'])) {
                $filter['price']['max'] = round($this->money->convert($filter['price']['max'], null, false));
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
    
    public function getOtherFilters(array $filter)
    {
        $SL = ServiceLocator::getInstance();
        /** @var FrontTranslations $translations */
        $translations = $SL->getService(FrontTranslations::class);
        
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        $otherFilters = [];
        foreach ($this->otherFilters as $f) {
            $label = 'features_filter_'.$f;
            $item = (object)[
                'url' => $f,
                'name' => $translations->{$label},
                'translation' => $label,
            ];
            if (empty($filter['other_filter']) || !in_array($f, $filter['other_filter'])) {
                $tmFilter = $filter;
                $tmFilter['other_filter'] = [$f];
                $cnt = $productsEntity->count($tmFilter);
                if ($cnt > 0) {
                    $otherFilters[] = $item;
                }
            } else {
                $otherFilters[] = $item;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $otherFilters, func_get_args());
    }

    /**
     * Метод возвращает данные для Ajax ответа при фильтрации
     * 
     * @param Design $design
     * @return object
     */
    public function getAjaxFilterData(Design $design)
    {
        $result = new \stdClass;
        $result->products_content = $design->fetch('products_content.tpl');
        $result->products_pagination = $design->fetch('chpu_pagination.tpl');
        $result->products_sort = $design->fetch('products_sort.tpl');
        $result->features = $design->fetch('features.tpl');
        $result->selected_features = $design->fetch('selected_features.tpl');
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
    public function paginate($itemsPerPage, $currentPage, array &$filter, Design $design)
    {

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        if ($this->settings->get('missing_products') === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }
        
        // Вычисляем количество страниц
        $productsCount = $productsEntity->count($filter);

        // Показать все страницы сразу
        $allPages = false;
        if ($currentPage == 'all') {
            $allPages = true;
            $itemsPerPage = $productsCount;
        }

        // Если не задана, то равна 1
        $currentPage = max(1, (int)$currentPage);
        $design->assign('current_page_num', $currentPage);
        $design->assign('is_all_pages', $allPages);
        
        $pagesNum = !empty($itemsPerPage) ? ceil($productsCount/$itemsPerPage) : 0;
        $design->assign('total_pages_num', $pagesNum);
        $design->assign('total_products_num', $productsCount);

        $filter['page'] = $currentPage;
        $filter['limit'] = $itemsPerPage;

        $result = true;
        if ($allPages === false && $currentPage > 1 && $currentPage > $pagesNum) {
            $result = false;
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    private function getPriceFromStorage($catalogType, $objectId = null)
    {
        $priceFilter = $this->resetPriceFilter();
        if (isset($_COOKIE['price_filter'])) {
            $priceFilter = json_decode($_COOKIE['price_filter'], true);
        }

        // Когда перешли на другой тип каталога, забываем диапазон цен
        if ($priceFilter['catalog_type'] != $catalogType) {
            $priceFilter = $this->resetPriceFilter();
            $priceFilter['catalog_type'] = $catalogType;
        }

        if ($priceFilter['catalog_type'] !== null) {
            switch ($catalogType) {
                case 'category':
                    if ($priceFilter['category_id'] != $objectId) {
                        $priceFilter = $this->resetPriceFilter();
                        $priceFilter['category_id'] = $objectId;
                        $priceFilter['catalog_type'] = $catalogType;
                    }
                    break;
                case 'brand':
                    if ($priceFilter['brand_id'] != $objectId) {
                        $priceFilter = $this->resetPriceFilter();
                        $priceFilter['brand_id'] = $objectId;
                        $priceFilter['catalog_type'] = $catalogType;
                    }
                    break;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $priceFilter, func_get_args());
    }
    
    private function resetPriceFilter() {
        return [
            'category_id'   => null,
            'brand_id'      => null,
            'catalog_type'  => null,
            'price_range'   => [
                'min' => null,
                'max' => null,
            ]
        ];
    }

    /**
     * Метод возвращает базовые значения свойств (без учёта фильтрации)
     * Используется на странице фильтра, и нужно, чтобы определить у фильтра один вариант значения (который нужно скрыть)
     * или изначально было много значений, тогда такой фильтр остаётся
     */
    public function getBaseFeaturesValues(array $filter, ?string $missingProducts = null): array
    {
        // Если скрываем из каталога товары не в наличии, значит и в фильтре их значения тоже не нужны будут
        if ($missingProducts === MISSING_PRODUCTS_HIDE) {
            $filter['in_stock'] = true;
        }

        if (($keyword = $this->filterHelper->getKeyword()) !== null) {
            $filter['product_keyword'] = $keyword;
        }

        if (!empty($this->features)) {
            $featuresIds = array_keys($this->features);
            if (!empty($featuresIds)) {
                $filter['feature_id'] = $featuresIds;
            }
        }

        /**
         * Получаем значения свойств для категории, чтобы на страницах фильтров убрать фильтры
         * у которых изначально был только один вариант выбора
         */
        $baseFeaturesValues = [];
        foreach ($this->filterHelper->getFeaturesValues($filter) as $fv) {
            $baseFeaturesValues[$fv->feature_id][$fv->id] = $fv;
        }

        return ExtenderFacade::execute(__METHOD__, $baseFeaturesValues, func_get_args());
    }

    public function getProductsFilter(string $filtersUrl = null, array $filter = []): ?array
    {
        if (($currentFeatures = $this->filterHelper->getCurrentFeatures($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } else {
            foreach ($this->filterHelper->getFeatures() as $feature) {
                if (isset($currentFeatures[$feature->id])) {
                    $filter['features'][$feature->id] = $currentFeatures[$feature->id];
                }
            }
        }

        if (($currentBrandsIds = $this->filterHelper->getCurrentBrands($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } else if (!empty($currentBrandsIds)) {
            $filter['brand_id'] = $currentBrandsIds;
        }

        if (($currentOtherFilters = $this->filterHelper->getCurrentOtherFilters($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        } else if (!empty($currentOtherFilters)) {
            $filter['other_filter'] = $currentOtherFilters;
        }

        $filter['visible'] = 1;

        $keyword = $this->request->get('keyword', null, null, false);
        if ($keyword = strip_tags($keyword)) {
            $filter['keyword'] = $keyword;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getCatalogFeaturesFilter(): array
    {
        $filter = [
            'in_filter' => 1,
            'visible' => 1,
        ];

        if (($keyword = $this->filterHelper->getKeyword()) !== null) {
            $filter['product_keyword'] = $keyword;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getCatalogFeatures(?array $filter = null): array
    {
        if ($filter === null) {
            $filter = $this->getCatalogFeaturesFilter();
        }

        $features = $this->featuresEntity->mappedBy('id')->find($filter);

        return ExtenderFacade::execute(__METHOD__, $features, func_get_args());
    }

    public function getProductsSort(?string $filtersUrl = null)
    {
        if (($currentSort = $this->filterHelper->getCurrentSort($filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        // Сортировка товаров, сохраняем в сессии, чтобы текущая сортировка оставалась для всего сайта
        if (!empty($currentSort)) {
            $_SESSION['sort'] = $currentSort;
        }
        if (!empty($_SESSION['sort'])) {
            $sortProducts = $_SESSION['sort'];
        } else {
            $sortProducts = 'position';
        }

        return ExtenderFacade::execute(__METHOD__, $sortProducts, func_get_args());
    }
}