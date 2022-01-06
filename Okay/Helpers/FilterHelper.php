<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Core\Settings;
use Okay\Entities\BrandsEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class FilterHelper
{
    /** @var EntityFactory */
    private $entityFactory;

    /** @var Request */
    private $request;

    /** @var Router */
    private $router;

    /** @var Design */
    private $design;

    /** @var Settings */
    private $settings;

    /** @var FrontTranslations */
    private $frontTranslations;


    private $features = [];
    private $featuresByUrl;
    private $featuresUrls;
    private $featuresValuesFilter = [];

    private $maxFilterBrands;
    private $maxFilterFilter;
    private $maxFilterFeaturesValues;
    private $maxFilterFeatures;
    private $maxFilterDepth;

    private $filtersUrl;

    private $currentBrands;
    private $otherFilters = [
        'discounted',
        'featured',
    ];

    private $featureValuesCache = [];

    public function __construct(
        EntityFactory     $entityFactory,
        Settings          $settings,
        Request           $request,
        Router            $router,
        Design            $design,
        FrontTranslations $frontTranslations
    ) {
        $this->entityFactory     = $entityFactory;
        $this->request           = $request;
        $this->router            = $router;
        $this->design            = $design;
        $this->settings          = $settings;
        $this->frontTranslations = $frontTranslations;

        $this->maxFilterBrands         = $settings->get('max_brands_filter_depth');
        $this->maxFilterFilter         = $settings->get('max_other_filter_depth');
        $this->maxFilterFeaturesValues = $settings->get('max_features_values_filter_depth');
        $this->maxFilterFeatures       = $settings->get('max_features_filter_depth');
        $this->maxFilterDepth          = $settings->get('max_filter_depth');
    }

    public function init()
    {
        $this->setFeaturesValuesFilter();
    }

    public function setFiltersUrl(string $filtersUrl): void
    {
        $this->filtersUrl = ExtenderFacade::execute(__METHOD__, $filtersUrl, func_get_args());
    }

    public function getFiltersUrl(): ?string
    {
        $filtersUrl = $this->filtersUrl;

        return ExtenderFacade::execute(__METHOD__, $filtersUrl, func_get_args());
    }

    public function setFeaturesValuesFilter(array $featuresValuesFilter = []): void
    {
        if (!isset($featuresValuesFilter['product_keyword']) && ($keyword = $this->getKeyword()) !== null) {
            $featuresValuesFilter['product_keyword'] = $keyword;
        }

        if (!isset($featuresValuesFilter['feature_id']) && !empty($this->features)) {
            $featuresValuesFilter['feature_id'] = array_map(function (object $feature) {
                return $feature->id;
            }, $this->features);
        }

        $this->featuresValuesFilter = ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    public function getFeaturesValuesFilter(): array
    {
        return ExtenderFacade::execute(__METHOD__, $this->featuresValuesFilter, func_get_args());
    }

    public function setFeatureValue($featureValue)
    {
        if (!isset($this->features[$featureValue->feature_id]->values[$featureValue->id])) {
            $this->features[$featureValue->feature_id]->values[$featureValue->id] = $featureValue;
            $this->features[$featureValue->feature_id]->values_ids[$featureValue->translit] = $featureValue->id;
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод подготавливает фильтр для поиска брендов категории
     */
    public function prepareFilterGetBrands(array $filter = []): array
    {
        $brandsFilter = [
            'visible' => 1,
            'product_visible' => 1
        ];

        if (!empty($filter['category_id'])) {
            $brandsFilter['category_id'] = $filter['category_id'];
        }

        if (!empty($filter['features'])) {
            $brandsFilter['features'] = $filter['features'];
        }

        if (!empty($filter['other_filter'])) {
            $brandsFilter['other_filter'] = $filter['other_filter'];
        }

        if (!empty($filter['price'])) {
            $brandsFilter['price'] = $filter['price'];
        }

        if (!empty($filter['brand_id'])) {
            $brandsFilter['selected_brands'] = $filter['brand_id'];
        }

        if (!empty($filter['keyword'])) {
            $brandsFilter['product_keyword'] = $filter['keyword'];
        }

        return ExtenderFacade::execute(__METHOD__, $brandsFilter, func_get_args());
    }

    /**
     * Возвращает бренды для фильтра
     */
    public function getBrands(array $brandsFilter): array
    {
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $brands = $brandsEntity->mappedBy('id')->find($brandsFilter);
        // Если в фильтре только один бренд и он не выбран, тогда вообще не выводим фильтр по бренду
        if (
            ($firstBrand = reset($brands))
            && $this->settings->get('hide_single_filters')
            && count($brands) <= 1
            && (
                empty($brandsFilter['selected_brands'])
                || !in_array($firstBrand->id, $brandsFilter['selected_brands'])
            )
        ) {
            $brands = [];
        }
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    /**
     * Заполняет два массива featuresByUrl и featuresUrls,
     * но когда будут сделаны кеши для entities, думаю от этого можно будет уйти
     */
    public function setFeatures(array $features): void
    {
        foreach ($features as $feature) {
            $this->features[$feature->id] = $feature;
            $this->featuresByUrl[$feature->url] = $feature;
            $this->featuresUrls[$feature->id] = $feature->url;
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getFeatures(): array
    {
       return ExtenderFacade::execute(__METHOD__, $this->features, func_get_args());
    }

    /**
     * Метод возвращает фильтр, который передадим в FeaturesValuesEntity::find()
     */
    public function prepareFilterGetFeaturesValues(array $productsFilter = [], ?array $featuresValuesFilter = null, ?string $missingProducts = null): array
    {
        if ($featuresValuesFilter === null) {
            $featuresValuesFilter = $this->featuresValuesFilter;
        }

        $featuresValuesFilter['visible'] = 1;

        // Если скрываем из каталога товары не в наличии, значит и в фильтре их значения тоже не нужны будут
        if ($missingProducts === MISSING_PRODUCTS_HIDE) {
            $featuresValuesFilter['in_stock'] = true;
        }

        if (!empty($this->features)) {
            $features_ids = array_keys($this->features);
            if (!empty($features_ids)) {
                $featuresValuesFilter['feature_id'] = $features_ids;
            }
        }

        if (!empty($productsFilter['category_id'])) {
            $featuresValuesFilter['have_products_in_categories'] = $productsFilter['category_id'];
        }

        if (isset($productsFilter['features'])) {
            $featuresValuesFilter['features'] = $productsFilter['features'];
        }

        if (isset($productsFilter['brand_id'])) {
            $featuresValuesFilter['brand_id'] = $productsFilter['brand_id'];
        }

        if (isset($productsFilter['price'])) {
            $featuresValuesFilter['price'] = $productsFilter['price'];
        }

        if (!empty($productsFilter['other_filter'])) {
            $featuresValuesFilter['other_filter'] = $productsFilter['other_filter'];
        }

        if (!empty($productsFilter['keyword'])) {
            $featuresValuesFilter['product_keyword'] = $productsFilter['keyword'];
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    /**
     * Возвращает номер текущей страницы пагинации
     * 
     * @param $filtersUrl
     * @return string|bool
     */
    public function getCurrentPage(string $filtersUrl = null)
    {
        if ($filtersUrl === null && ($filtersUrl = $this->getFiltersUrl()) === null) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $currentPage = '';
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if ($paramName == 'page') {
                $currentPage = (string)$paramValues;
                if ($paramValues != 'all' && (!preg_match('~^[0-9]+$~', $paramValues) || strpos($paramValues, '0') === 0)) {
                    return false;
                }

            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentPage, func_get_args());
    }

    public function getCurrentSort(string $filtersUrl = null)
    {
        if ($filtersUrl === null && ($filtersUrl = $this->getFiltersUrl()) === null) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $currentSort = '';
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if ($paramName == 'sort') {
                $currentSort = (string)$paramValues;
                if (!in_array($currentSort, ['position', 'price', 'price_desc', 'name', 'name_desc', 'rating', 'rating_desc'])) {
                    return ExtenderFacade::execute(__METHOD__, false, func_get_args());;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentSort, func_get_args());
    }

    public function getCurrentOtherFilters(string $filtersUrl = null)
    {
        if ($filtersUrl === null && ($filtersUrl = $this->getFiltersUrl()) === null) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $otherFilter = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if ($paramName == 'filter') {
                foreach (explode('_', $paramValues) as $f) {
                    if (!in_array($f, $otherFilter) && in_array($f, $this->otherFilters)) {
                        $otherFilter[] = $f;
                    } else {
                        return ExtenderFacade::execute(__METHOD__, false, [$filtersUrl]);
                    }
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $otherFilter, func_get_args());
    }

    public function getCurrentBrands(string $filtersUrl = null)
    {
        if ($filtersUrl === null && ($filtersUrl = $this->getFiltersUrl()) === null) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $currentBrands = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $v) {
            if (empty($v)) {
                continue;
            }

            $paramName = explode('-', $v)[0];
            if ($paramName == 'brand') {
                $paramValues = mb_substr($v, strlen($paramName) + 1);

                foreach (explode('_', $paramValues) as $bv) {
                    if (($brand = $this->getBrand((string)$bv)) && !in_array($brand->id, $currentBrands)) {
                        $currentBrands[] = $brand->id;
                    } else {
                        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
                    }
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentBrands, func_get_args());
    }

    public function getCurrentPrices(string $filtersUrl = null)
    {
        if ($filtersUrl === null && ($filtersUrl = $this->getFiltersUrl()) === null) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $currentPrices = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $v) {
            if (empty($v)) {
                continue;
            }

            $paramName = explode('-', $v)[0];
            if ($paramName == 'price') {
                $paramValues = mb_substr($v, strlen($paramName) + 1);

                $prices = explode('_', $paramValues);
                $currentPrices = ['min' => reset($prices), 'max' => end($prices)];
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentPrices, func_get_args());
    }

    private function getNotFeaturesParts()
    {
        return ExtenderFacade::execute(__METHOD__, ['brand', 'filter', 'price', 'page', 'sort'], func_get_args());
    }

    public function getCurrentFeatures(string $filtersUrl = null)
    {
        if ($filtersUrl === null && ($filtersUrl = $this->getFiltersUrl()) === null) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $currentFeatures = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if (!in_array($paramName, $this->getNotFeaturesParts())) {
                if (isset($this->featuresByUrl[$paramName])
                    && ($feature = $this->featuresByUrl[$paramName])
                    && !isset($selectedFeatures[$feature->id])) {
                    $selectedFeatures[$feature->id] = explode('_', $paramValues);
                } else {
                    return ExtenderFacade::execute(__METHOD__, false, func_get_args());
                }
            }
        }

        if (!empty($selectedFeatures)) {
            $valuesIds = [];
            if (!empty($this->features)) {
                // Выше мы определили какие значения каких свойств выбраны, теперь достаем эти значения из базы, чтобы за один раз
                foreach ($this->getFeaturesValues(array_merge_recursive($this->featuresValuesFilter, ['selected_features' => $selectedFeatures])) as $fv) {
                    $valuesIds[$fv->feature_id][$fv->translit] = $fv->id;
                }
            }

            foreach ($selectedFeatures as $featureId => $values) {
                foreach ($values as $value) {
                    if (isset($valuesIds[$featureId][$value])) {
                        $valueId = $valuesIds[$featureId][$value];
                        $currentFeatures[$featureId][$valueId] = $value;
                    } else {
                        // Если не нашли id значения, значит нет такого значения, кидаем 404
                        return ExtenderFacade::execute(__METHOD__, false, func_get_args());
                    }
                }
                // если нет повторяющихся значений свойства - ок, иначе 404
                if (isset($currentFeatures[$featureId]) && count($currentFeatures[$featureId]) == count(array_unique($currentFeatures[$featureId]))) {
                    foreach ($currentFeatures[$featureId] as $paramValue) {
                        if (!in_array($paramValue, array_keys($valuesIds[$featureId]))) {
                            return ExtenderFacade::execute(__METHOD__, false, [$filtersUrl]);
                        }
                    }
                } else {
                    return ExtenderFacade::execute(__METHOD__, false, func_get_args());
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentFeatures, func_get_args());
    }

    /**
     * Метод используется для разбора пользовательских фильтров расширяющих стандартные ЧПУ
     * 
     * @param $paramName
     * @param $paramValues
     * @return array
     */
    public function userGetMetaArray($paramName, $paramValues)
    {
        return ExtenderFacade::execute(__METHOD__, [], func_get_args());
    }
    
    public function getMetaArray($filtersUrl)
    {
        $metaArray = [];
        //определение текущего положения и выставленных параметров
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $k => $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if ($res = $this->userGetMetaArray($paramName, $paramValues)) {
                $metaArray = array_merge($metaArray, $res);
            } else {

                switch ($paramName) {
                    case 'brand':
                    {
                        $paramValues = mb_substr($v, strlen($paramName) + 1);
                        foreach (explode('_', $paramValues) as $bv) {
                            if (($brand = $this->getBrand($bv)) && empty($metaArray['brand'][$brand->id])) {
                                $metaArray['brand'][$brand->id] = $brand->name;
                            }
                        }
                        break;
                    }
                    case 'filter':
                    {
                        foreach (explode('_', $paramValues) as $f) {
                            if (empty($metaArray['filter'][$f])) {
                                $metaArray['filter'][$f] = $this->frontTranslations->getTranslation("features_filter_" . $f);
                            }
                        }
                        break;
                    }
                    case 'page':
                        $metaArray['page'] = $paramValues;
                        break;
                    case 'sort':
                        $metaArray['sort'] = $paramValues;
                        break;
                    default:
                    {
                        if (isset($this->featuresByUrl[$paramName])
                            && ($feature = $this->featuresByUrl[$paramName])
                            && !isset($selectedFeatures[$feature->id])) {

                            $selectedFeatures[$feature->id] = explode('_', $paramValues);
                        }
                    }
                }
            }
        }

        if (!empty($selectedFeatures)) {
            $selectedFeaturesValues = [];
            if (!empty($this->features)) {
                // Выше мы определили какие значения каких свойств выбраны, теперь достаем эти значения из базы, чтобы за один раз
                foreach ($this->getFeaturesValues(array_merge_recursive($this->featuresValuesFilter, ['selected_features' => $selectedFeatures])) as $fv) {
                    $selectedFeaturesValues[$fv->feature_id][$fv->id] = $fv;
                }
            }

            foreach ($selectedFeatures as $featureId => $values) {
                if (isset($selectedFeaturesValues[$featureId])) {
                    foreach ($selectedFeaturesValues[$featureId] as $fv) {
                        if (in_array($fv->translit, $values, true)) {
                            $metaArray['features_values'][$featureId][$fv->id] = $fv->value;
                        }
                    }
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $metaArray, func_get_args());
    }
    
    public function changeLangUrls($filtersUrl)
    {

        if ($languages = (array)$this->design->getVar('languages')) {
            /** @var FeaturesValuesEntity $featuresValuesEntity */
            $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
            
            $routeParams = $this->router->getCurrentRouteRequiredParams();
    
            $currentFeatures = $this->getCurrentFeatures($filtersUrl);
            // Достаем выбранные значения свойств для других языков
            $langValuesFilter = [];
            foreach ($currentFeatures as $featureId=>$values) {
                $langValuesFilter[$featureId] = array_keys($values);
            }
            $langValues = $featuresValuesEntity->getFeaturesValuesAllLang($langValuesFilter);
            
            //  Заменяем url языка с учетом ЧПУ
            foreach ($languages as $l) {
                $furl = ['sort'=>null];
                $featuresAltLang = [];
                // Для каждого значения, выбираем все его варианты на других языках
                foreach ($currentFeatures as $featureId=>$values) {
                    if (isset($this->featuresUrls[$featureId])) {
                        foreach (array_keys($values) as $fvId) {
                            if (isset($langValues[$l->id][$featureId][$fvId])) {
                                $translit = $langValues[$l->id][$featureId][$fvId]->translit;
                                $featureUrl = $this->featuresUrls[$featureId];
                                $furl[$featureUrl][$fvId] = $translit;
                                $featuresAltLang[$featureId][$fvId] = $translit;
                            }
                        }
                    }
                }

                $baseUrl = $this->router->generateUrl($this->router->getCurrentRouteName(), $routeParams, true, $l->id);
                $chpuUrl = $this->filterChpuUrl($furl, $featuresAltLang);
                $chpuUrl = trim($chpuUrl, '/');

                if (!empty($chpuUrl)) {
                    $baseUrl = trim($baseUrl, '/');
                }

                $l->url = $baseUrl . (!empty($chpuUrl) ? '/' . $chpuUrl : '');
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод возвращает ассоциативный массив из одного элемента, ключ которого - название параметра фильтра
     * Например для фильтра по модели УРЛ будет catalog/category/model-s нужно вернуть массив ['model' => 's']
     *
     * @param $paramName
     * @param $paramValues
     * @return array
     */
    private function filterChpuUrlParseUrl($paramName, $paramValues)
    {
        return ExtenderFacade::execute(__METHOD__, [], func_get_args());
    }

    /**
     * Метод парсит параметры, переданные из смарти ф-ции {furl}, нужно вернуть ассоциативный массив, на базе которого 
     * в методе filterChpuUrlBuildUrl можно будет построить урл
     * 
     * @param $paramName
     * @param $paramValues
     * @param $resultArray
     * @return array
     */
    private function filterChpuUrlParseParams($paramName, $paramValues, &$resultArray)
    {
        return ExtenderFacade::execute(__METHOD__, [], [$paramName, $paramValues, &$resultArray]);
    }

    /**
     * Метод предназначен для построения ЧПУ урла из модулей
     *
     * @param $resultArray
     * @param $filterParamsCount
     * @param $seoHideFilter
     * @return string
     */
    private function filterChpuUrlBuildUrl($resultArray, &$filterParamsCount, &$seoHideFilter)
    {
        return ExtenderFacade::execute(__METHOD__, '', [$resultArray, &$filterParamsCount, &$seoHideFilter]);
    }

    // из-за особенностей смарти, при использовании этого метода из плагина, нужно отдельно передавать
    // экземпляр Smarty, чтобы отрабатывал assign
    public function filterChpuUrl($params, $featuresAltLang = [], $smarty = null)
    {
        $resultArray = ['brand'=>[],'features'=>[], 'filter'=>[], 'sort'=>null,'page'=>null, 'price'=>[]];
        $uriArray = $this->parseFilterUrl($this->filtersUrl);
        if (($currentFeaturesValues = $this->getCurrentFeatures($this->filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $resultArray = $this->getCurrentUrlParams($uriArray, $currentFeaturesValues, $resultArray);
        $resultArray = $this->getNewUrlParams($this->features, $params, $resultArray);
        $resultString = $this->filterChpuUrlBuild($resultArray, $smarty);

        return ExtenderFacade::execute(__METHOD__, $resultString, func_get_args());
    }

    /**
     * Определяем, что у нас уже есть в строке
     *
     * @param $uriArray
     * @param $currentFeaturesValues
     * @param $resultArray
     * @return array|mixed
     */
    private function getCurrentUrlParams($uriArray, $currentFeaturesValues, $resultArray)
    {
        if (!empty($this->filtersUrl)) {
            foreach ($uriArray as $k => $v) {
                
                list($paramName, $paramValues) = explode('-', $v);

                if ($parsedUrl = $this->filterChpuUrlParseUrl($paramName, $paramValues)) {
                    $resultArray = array_merge($resultArray, $parsedUrl);
                } else {
                    switch ($paramName) {
                        case 'brand':
                            $paramValues = mb_substr($v, strlen($paramName) + 1);
                            $resultArray['brand'] = explode('_', $paramValues);
                            break;
                        case 'filter':
                            $resultArray['filter'] = explode('_', $paramValues);
                            break;
                        case 'price':
                            $prices = explode('_', $paramValues);
                            $resultArray['price'] = [
                                'min' => reset($prices),
                                'max' => end($prices)
                            ];
                            break;
                        case 'sort':
                            $resultArray['sort'] = strval($paramValues);
                            break;
                        case 'page':
                            $resultArray['page'] = $paramValues;
                            break;
                        default:
                            // Ключем массива должно быть id значения
                            if (!empty($this->featuresUrls)) {
                                $paramValuesArray = [];
                                $featureId = array_search($paramName, $this->featuresUrls);
                                foreach (explode('_', $paramValues) as $valueTranslit) {
                                    if ($valueId = array_search($valueTranslit, $currentFeaturesValues[$featureId])) {
                                        if (isset($featuresAltLang[$featureId][$valueId])) {
                                            $valueTranslit = $featuresAltLang[$featureId][$valueId];
                                        }
                                        $paramValuesArray[$valueId] = $valueTranslit;
                                    }
                                }
                                $resultArray['features'][$paramName] = $paramValuesArray;
                            }
                    }
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $resultArray, func_get_args());
    }

    /**
     * Определяем переданные параметры для ссылки
     *
     * @param $features
     * @param $params
     * @param $resultArray
     * @return array
     */
    private function getNewUrlParams($features, $params, $resultArray)
    {
        foreach($params as $paramName=>$paramValues) {
            if ($parsedParams = $this->filterChpuUrlParseParams($paramName, $paramValues, $resultArray)) {
                $resultArray = array_merge($resultArray, $parsedParams);
            } else {
                switch ($paramName) {
                    case 'brand':
                        if (is_null($paramValues)) {
                            unset($resultArray['brand']);
                        } elseif (in_array($paramValues, $resultArray['brand'])) {
                            unset($resultArray['brand'][array_search($paramValues, $resultArray['brand'])]);
                        } else {
                            $resultArray['brand'][] = $paramValues;
                        }
                        break;
                    case 'filter':
                        if (is_null($paramValues)) {
                            unset($resultArray['filter']);
                        } elseif (in_array($paramValues, $resultArray['filter'])) {
                            unset($resultArray['filter'][array_search($paramValues, $resultArray['filter'])]);
                        } else {
                            $resultArray['filter'][] = $paramValues;
                        }
                        if (empty($resultArray['filter'])) {
                            unset($resultArray['filter']);
                        }
                        break;
                    case 'price':
                        $resultArray['price'] = $paramValues;
                        break;
                    case 'sort':
                        $resultArray['sort'] = strval($paramValues);
                        break;
                    case 'page':
                        $resultArray['page'] = $paramValues;
                        break;
                    default:
                        if (is_null($paramValues)) {
                            unset($resultArray['features'][$paramName]);
                        } elseif (!empty($resultArray['features']) && in_array($paramName, array_keys($resultArray['features']), true) && in_array($paramValues, $resultArray['features'][$paramName], true)) {
                            unset($resultArray['features'][$paramName][array_search($paramValues, $resultArray['features'][$paramName])]);
                        } else {
                            if (!empty($this->featuresUrls)) {
                                $featureId = array_search($paramName, $this->featuresUrls);

                                if (!empty($features[$featureId]->values)) {
                                    $paramValues = (array)$paramValues;
                                    foreach ($paramValues as $valueTranslit) {
                                        if (!empty($valueId = $features[$featureId]->values_ids[$valueTranslit])) {
                                            $resultArray['features'][$paramName][$valueId] = $valueTranslit;
                                        }
                                    }
                                }
                            }
                        }
                        if (empty($resultArray['features'][$paramName])) {
                            unset($resultArray['features'][$paramName]);
                        }
                        break;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $resultArray, func_get_args());
    }

    /**
     * @param $resultArray
     * @param $smarty
     * @return string
     */
    private function filterChpuUrlBuild($resultArray, $smarty)
    {
        $resultString = '';

        $filter_params_count = 0;
        $seoHideFilter = false;
        if (!empty($resultArray['brand'])) {
            if (count($resultArray['brand']) > $this->maxFilterBrands) {
                $seoHideFilter = true;
            }
            $filter_params_count ++;
            $brandsString = $this->sortBrands($resultArray['brand']); // - это с сортировкой по брендам
            if (!empty($brandsString)) {
                $resultString .= '/brand-' . implode("_", $brandsString);
            }
        }
        foreach ($resultArray['features'] as $k=>$v) {
            if (count($resultArray['features'][$k]) > $this->maxFilterFeaturesValues || count($resultArray['features']) > $this->maxFilterFeatures) {
                $seoHideFilter = true;
            }
        }
        if (!empty($resultArray['filter'])) {
            if (count($resultArray['filter']) > $this->maxFilterFilter) {
                $seoHideFilter = true;
            }
            $filter_params_count ++;
            $resultString .= '/filter-' . implode("_", $resultArray['filter']);
        }

        $resultString .= $this->filterChpuUrlBuildUrl($resultArray, $filter_params_count, $seoHideFilter);

        if (!empty($resultArray['features'])) {
            $filter_params_count ++;
            $resultString .= $this->sortFeatures($resultArray['features']);
        }

        if ($filter_params_count > $this->maxFilterDepth) {
            $seoHideFilter = true;
        }

        if (!empty($resultArray['price'])) {
            $resultString .= '/price-' . $resultArray['price']['min'] . '_' . $resultArray['price']['max'];
        }

        if (!empty($resultArray['sort'])) {
            $resultString .= '/sort-' . $resultArray['sort'];
        }

        if ($resultArray['page'] > 1 || $resultArray['page'] == 'all') {
            $resultString .= '/page-' . $resultArray['page'];
        }

        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $resultString .= '?keyword='.htmlspecialchars(strip_tags($keyword));
        }
        if ($smarty !== null) {
            /** @var \Smarty_Internal_Template $smarty */
            $smarty->assign('seo_hide_filter', $seoHideFilter);
        }
        $this->design->assign('seo_hide_filter', $seoHideFilter);

        return ExtenderFacade::execute(__METHOD__, $resultString, func_get_args());
    }

    public function parseFilterUrl($filtersUrl)
    {
        return explode('/', $filtersUrl);
    }

    private function getBrand($url)
    {
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);

        $url = (string)$url;

        if (isset($this->currentBrands[$url])) {
            return ExtenderFacade::execute(__METHOD__, $this->currentBrands[$url]);
        }
        $brand = $brandsEntity->get($url);

        $this->currentBrands[$url] = $brand;
        return ExtenderFacade::execute(__METHOD__, $this->currentBrands[$url], func_get_args());
    }

    public function getFeaturesValues(array $filter)
    {
        array_multisort($filter);
        $cacheKey = serialize($filter);

        if (!empty($this->featureValuesCache[$cacheKey])) {
            return ExtenderFacade::execute(__METHOD__, $this->featureValuesCache[$cacheKey], func_get_args());
        }

        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);

        $featuresValuesEntity->addHighPriority('category_id');
        $featuresValues = $featuresValuesEntity->find($filter);

        $this->featureValuesCache[$cacheKey] = $featuresValues;

        return ExtenderFacade::execute(__METHOD__, $featuresValues);
    }
    
    private function sortBrands($brandsUrls = []) // todo проверить
    {
        if (empty($brandsUrls)) {
            return ExtenderFacade::execute(__METHOD__, false);
        }
        
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $sortedBrandsUrls = $brandsEntity->cols(['url'])
            ->order('position')
            ->find(['url' => $brandsUrls]);

        if (empty($sortedBrandsUrls)) {
            return ExtenderFacade::execute(__METHOD__, false);
        }

        return ExtenderFacade::execute(__METHOD__, $sortedBrandsUrls, func_get_args());
    }

    private function sortFeatures($features = [])
    {
        if (empty($features)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        $resultString = '';
        foreach ($this->featuresUrls as $furl) {
            if (in_array($furl, array_keys($features), true)) {
                $resultString .= '/'.$furl.'-'.implode('_', $features[$furl]);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $resultString, func_get_args());
    }

    public function isFilterPage(array $filter): bool
    {
        $result = !empty($filter['price'])
                || !empty($filter['features'])
                || !empty($filter['other_filter'])
                || !empty($filter['brand_id']);

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function generateCacheKey($key): void
    {
        if (($keyword = $this->getKeyword()) !== null) {
            $key .= "-{$keyword}";
        }

        $filterCacheKey = $key . '-' . $this->filterChpuUrl([
                'page' => null,
                'sort' => null
            ]);
        $this->design->assign('filterCacheKey', $filterCacheKey, true);
        $this->design->assignJsVar('filterCacheKey', $filterCacheKey);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getKeyword(): ?string
    {
        $keyword = $this->request->get('keyword', null, null, false);
        if ($keyword = strip_tags($keyword)) {
            $result = $keyword;
        } else {
            $result = null;
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
}