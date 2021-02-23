<?php


namespace Okay\Helpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Money as MoneyCore;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Entities\TranslationsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class CatalogHelper
{

    private $money;
    private $entityFactory;
    private $settings;
    private $request;
    private $otherFilters = [
        'discounted',
        'featured',
    ];

    public function __construct(EntityFactory $entityFactory, MoneyCore $money, Settings $settings, Request $request)
    {
        $this->entityFactory = $entityFactory;
        $this->money = $money;
        $this->settings = $settings;
        $this->request = $request;
    }
    
    public function assignCategoryFilterProcedure(
        $category,
        array $filter,
        array $currentBrandsIds,
        array $categoryBrands,
        array $categoryFeatures,
        array $currentFeatures,
        $isFilterPage,
        $catalogType
    ) {
        
        $SL = ServiceLocator::getInstance();

        /** @var FilterHelper $filterHelper */
        $filterHelper = $SL->getService(FilterHelper::class);

        /** @var Design $design */
        $design = $SL->getService(Design::class);
        
        $brandsFilter = $filterHelper->prepareFilterGetCategoryBrands($category, $filter);
        if ($brands = $filterHelper->getCategoryBrands($brandsFilter, $currentBrandsIds)) {
            $category->brands = $brands;
        }

        // Дополняем список брендов, теми, которые выбраны в данный момент, но их фильтрация отсекла
        if ($isFilterPage === true && !empty($this->categoryBrands) && !empty($currentBrandsIds)) {
            foreach ($currentBrandsIds as $brandId) {
                if (isset($categoryBrands[$brandId]) && !isset($category->brands[$brandId])) {
                    $category->brands[$brandId] = $this->categoryBrands[$brandId];
                }
            }
        }

        /**
         * Получаем значения свойств для категории, чтобы на страницах фильтров убрать фильтры
         * у которых изначально был только один вариант выбора
         */
        $baseFeaturesValues = [];
        if ($isFilterPage === true) {
            $baseFeaturesValues = $filterHelper->getCategoryBaseFeaturesValues($category, $this->settings->get('missing_products'));

            // Дополняем массив categoryFeatures значениями, которые в данный момент выбраны
            // и были изначально, но их фильтрация (по бренду или цене) отсекла.
            if (!empty($baseFeaturesValues)) {
                foreach ($baseFeaturesValues as $values) {
                    foreach ($values as $value) {
                        if (isset($currentFeatures[$value->feature_id][$value->id]) && isset($categoryFeatures[$value->feature_id])) {
                            $categoryFeatures[$value->feature_id]->features_values[$value->id] = $value;
                        }
                    }
                }
            }
        }

        if (!empty($categoryFeatures)) {

            // Достаём значения свойств текущей категории
            $featuresValuesFilter = $filterHelper->prepareFilterGetFeaturesValues($category, $this->settings->get('missing_products'), $filter);
            foreach ($filterHelper->getCategoryFeaturesValues($featuresValuesFilter) as $featureValue) {
                if (isset($categoryFeatures[$featureValue->feature_id])) {
                    $filterHelper->setCategoryFeatureValue($featureValue);
                    $categoryFeatures[$featureValue->feature_id]->features_values[$featureValue->id] = $featureValue;
                }
            }

            foreach ($categoryFeatures as $i => $feature) {
                // Если хоть одно значение свойства выбрано, его убирать нельзя
                if (empty($currentFeatures[$feature->id])) {
                    // На странице фильтра убираем свойства у которых вообще нет значений (отфильтровались)
                    // или они изначально имели только один вариант выбора
                    if ($isFilterPage === true) {
                        if (!isset($baseFeaturesValues[$feature->id])
                            || ($this->settings->get('hide_single_filters') && (count($baseFeaturesValues[$feature->id]) <= 1)
                                || !isset($feature->features_values)
                                || count($feature->features_values) == 0)) {

                            unset($categoryFeatures[$i]);
                        }
                        // Иначе убираем свойства у которых только один вариант выбора
                    } elseif (!isset($feature->features_values) || ($this->settings->get('hide_single_filters') && count($feature->features_values) <= 1)) {
                        unset($categoryFeatures[$i]);
                    }
                }
            }
        }
        $design->assign('features', $categoryFeatures);

        $otherFiltersFilter = $this->getOtherFiltersFilter($filter);
        $design->assign('other_filters', $this->getOtherFilters($otherFiltersFilter));

        $prices = $this->getPrices($filter, $catalogType, $category->id);
        $design->assign('prices', $prices);
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
    
    public function getPrices(array &$filter, $catalogType, $objectId = null)
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
    
}