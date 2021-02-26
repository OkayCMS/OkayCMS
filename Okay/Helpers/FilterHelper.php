<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Money;
use Okay\Core\Request;
use Okay\Core\Router;
use Okay\Core\Settings;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\BrandsEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;
use Okay\Entities\TranslationsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class FilterHelper
{

    private $entityFactory;
    private $request;
    private $router;
    private $design;
    private $settings;
    private $money;
    
    private $categoryFeatures = [];
    private $categoryFeaturesByUrl;
    private $featuresUrls;

    private $maxFilterBrands;
    private $maxFilterFilter;
    private $maxFilterFeaturesValues;
    private $maxFilterFeatures;
    private $maxFilterDepth;

    private $category;
    private $language;
    private $filtersUrl;

    private $currentBrands;
    private $otherFilters = [
        'discounted',
        'featured',
    ];

    private $featureValuesCache = [];

    public function __construct(
        EntityFactory $entityFactory,
        Settings $settings,
        Languages $languages,
        Request $request,
        Router $router,
        Design $design,
        Money $money
    ) {
        $this->entityFactory = $entityFactory;
        $this->request = $request;
        $this->router = $router;
        $this->design = $design;
        $this->settings = $settings;
        $this->money = $money;

        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $entityFactory->get(LanguagesEntity::class);
        $this->language = $languagesEntity->get($languages->getLangId());

        $this->maxFilterBrands = $settings->get('max_brands_filter_depth');
        $this->maxFilterFilter = $settings->get('max_other_filter_depth');
        $this->maxFilterFeaturesValues = $settings->get('max_features_values_filter_depth');
        $this->maxFilterFeatures = $settings->get('max_features_filter_depth');
        $this->maxFilterDepth = $settings->get('max_filter_depth');
    }

    public function getBrandProductsFilter(array $filter = [])
    {
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getCategoryProductsFilter(array $filter = [])
    {
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getDiscountedProductsFilter(array $filter = [])
    {
        $filter['discounted'] = true;
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getFeaturedProductsFilter(array $filter = [])
    {
        $filter['featured'] = true;
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getSearchProductsFilter(array $filter = [], $keyword = null)
    {
        if ($keyword !== null) {
            $filter['keyword'] = $keyword;
        }
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }
    
    public function setCategory($category)
    {
        $this->category = $category;
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function setFiltersUrl($filtersUrl)
    {
        $this->filtersUrl = $filtersUrl;
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getFiltersUrl()
    {
        return $this->filtersUrl; // No ExtenderFacade
    }

    public function setCategoryFeatureValue($featureValue)
    {
        if ($this->categoryFeatures === null) {
            $this->getCategoryFeatures();
        }
        
        if (!isset($this->categoryFeatures[$featureValue->feature_id]->values[$featureValue->id])) {
            $this->categoryFeatures[$featureValue->feature_id]->values[$featureValue->id] = $featureValue;
            $this->categoryFeatures[$featureValue->feature_id]->values_ids[$featureValue->translit] = $featureValue->id;
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод подготавлявает фильтр для поиска брендов категории
     * 
     * @param $category
     * @param array $filter
     * @return array
     */
    public function prepareFilterGetCategoryBrands($category, array $filter)
    {
        $brandsFilter = [
            'category_id'   => $category->children,
            'visible'       => 1,
            'product_visible' => 1
        ];

        if (!empty($filter['features'])) {
            $brandsFilter['features'] = $filter['features'];
        }

        if (!empty($filter['other_filter'])) {
            $brandsFilter['other_filter'] = $filter['other_filter'];
        }

        if (!empty($filter['price']) && $filter['price']['min'] != '' && $filter['price']['max'] != '') {
            if (isset($filter['price']['min'])) {
                $brandsFilter['price']['min'] = round($this->money->convert($filter['price']['min'], null, false));
            }

            if (isset($filter['price']['max'])) {
                $brandsFilter['price']['max'] = round($this->money->convert($filter['price']['max'], null, false));
            }
        }

        // В выборку указываем выбранные бренды, чтобы достать еще и все выбранные бренды, чтобы их можно было отменить
        if (!empty($currentBrandsIds)) {
            $brandsFilter['selected_brands'] = $currentBrandsIds;// todo проверить, корректно ли работает
        }

        return ExtenderFacade::execute(__METHOD__, $brandsFilter, func_get_args());
    }

    /**
     * Возвращает бренды для текущей категории, для фильтра
     * 
     * @param $brandsFilter
     * @param $currentBrandsIds
     * @return array
     * @throws \Exception
     */
    public function getCategoryBrands($brandsFilter, $currentBrandsIds)
    {
        /** @var BrandsEntity $brandsEntity */
        $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
        $brands = $brandsEntity->mappedBy('id')->find($brandsFilter);
        // Если в фильтре только один бренд и он не выбран, тогда вообще не выводим фильтр по бренду
        if (($firstBrand = reset($brands)) 
            && $this->settings->get('hide_single_filters') 
            && count($brands) <= 1 
            && !in_array($firstBrand->id, $currentBrandsIds)
        ) {
            $brands = [];
        }
        return ExtenderFacade::execute(__METHOD__, $brands, func_get_args());
    }

    /**
     * Метод возвращает свойства текущей категории
     * Также он заполняет два массива categoryFeaturesByUrl и featuresUrls,
     * но когда будут сделаны кеши для entities, думаю от этого можно будет уйти
     * 
     * @return array
     * @throws \Exception
     */
    public function getCategoryFeatures()
    {
        if (!empty($this->categoryFeatures)) {
            return $this->categoryFeatures;
        }
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->entityFactory->get(FeaturesEntity::class);

        if (!empty($this->category) && empty($this->categoryFeatures)) {
            foreach ($featuresEntity->find(['category_id' => $this->category->id, 'in_filter' => 1]) as $feature) {
                $this->categoryFeatures[$feature->id] = $feature;
                $this->categoryFeaturesByUrl[$feature->url] = $feature;
                $this->featuresUrls[$feature->id] = $feature->url;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $this->categoryFeatures, func_get_args());
    }

    /**
     * Метод возвращает базовые значения свойств категоии (без учёта фильтрации)
     * Используется на странице фильтра, и нужно чтобы определить у фильтра один вириант значения (который нужно скрыть)
     * или изначально было много значени, тогда такой фильтр остаётся
     * 
     * @param $category
     * @param $missingProducts
     * @return array
     * @throws \Exception
     */
    public function getCategoryBaseFeaturesValues($category, $missingProducts)
    {

        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
        
        $featuresValuesFilter['visible'] = 1;

        // Если скрываем из каталога товары не в наличии, значит и в фильтре их значения тоже не нужны будут
        if ($missingProducts === MISSING_PRODUCTS_HIDE) {
            $featuresValuesFilter['in_stock'] = true;
        }

        if (!empty($this->categoryFeatures)) {
            $features_ids = array_keys($this->categoryFeatures);
            if (!empty($features_ids)) {
                $featuresValuesFilter['feature_id'] = $features_ids;
            }
        }
        $featuresValuesFilter['category_id'] = $category->children;

        /**
         * Получаем значения свойств для категории, чтобы на страницах фильтров убрать фильтры
         * у которых изначально был только один вариант выбора
         */
        $baseFeaturesValues = [];
        foreach ($featuresValuesEntity->find($featuresValuesFilter) as $fv) {
            $baseFeaturesValues[$fv->feature_id][$fv->id] = $fv;
        }
        
        return ExtenderFacade::execute(__METHOD__, $baseFeaturesValues, func_get_args());
    }

    /**
     * Метод возвращает фильтр, который передадим в FeaturesValuesEntity::find()
     * 
     * @param $category
     * @param $missingProducts
     * @param array $filter
     * @return array
     */
    public function prepareFilterGetFeaturesValues($category, $missingProducts, array $filter = [])
    {
        $featuresValuesFilter['visible'] = 1;

        // Если скрываем из каталога товары не в наличии, значит и в фильтре их значения тоже не нужны будут
        if ($missingProducts === MISSING_PRODUCTS_HIDE) {
            $featuresValuesFilter['in_stock'] = true;
        }

        if (!empty($this->categoryFeatures)) {
            $features_ids = array_keys($this->categoryFeatures);
            if (!empty($features_ids)) {
                $featuresValuesFilter['feature_id'] = $features_ids;
            }
        }
        $featuresValuesFilter['category_id'] = $category->children;

        if (isset($filter['features'])) {
            $featuresValuesFilter['features'] = $filter['features'];
        }

        if (isset($filter['brand_id'])) {
            $featuresValuesFilter['brand_id'] = $filter['brand_id'];
        }

        if (!empty($filter['other_filter'])) {
            $featuresValuesFilter['other_filter'] = $filter['other_filter'];
        }

        if (!empty($filter['price']) && $filter['price']['min'] != '' && $filter['price']['max'] != '') {

            if (isset($filter['price']['min'])) {
                $featuresValuesFilter['price']['min'] = round($this->money->convert($filter['price']['min'], null, false));
            }

            if (isset($filter['price']['max'])) {
                $featuresValuesFilter['price']['max'] = round($this->money->convert($filter['price']['max'], null, false));
            }
            
        }

        return ExtenderFacade::execute(__METHOD__, $featuresValuesFilter, func_get_args());
    }

    /**
     * Метод возвращает текущие свойства для фильтра
     * 
     * @param array $featuresValuesFilter
     * @return array
     * @throws \Exception
     */
    public function getCategoryFeaturesValues(array $featuresValuesFilter = [])
    {
        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
        
        $featuresValuesEntity->addHighPriority('category_id');
        $featuresValues = $featuresValuesEntity->find($featuresValuesFilter);
        return ExtenderFacade::execute(__METHOD__, $featuresValues, func_get_args());
    }

    /**
     * Возвращает номер текущей страницы пагинации
     * 
     * @param $filtersUrl
     * @return string|bool
     */
    public function getCurrentPage($filtersUrl)
    {
        $currentPage = '';
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $k => $v) {
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

    public function getCurrentSort($filtersUrl)
    {
        $currentSort = '';
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $k => $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if ($paramName == 'sort') {
                $currentSort = (string)$paramValues;
                if (!in_array($currentSort, ['position', 'price', 'price_desc', 'name', 'name_desc', 'rating', 'rating_desc'])) {
                    return false;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currentSort, func_get_args());
    }

    public function getCurrentOtherFilters($filtersUrl)
    {
        $otherFilter = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $k => $v) {
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

    public function getCurrentBrands($filtersUrl)
    {
        $currentBrands = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $k => $v) {
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

    private function getNotFeaturesParts()
    {
        return ExtenderFacade::execute(__METHOD__, ['brand', 'filter', 'page', 'sort'], func_get_args());
    }
    
    public function getCurrentCategoryFeatures($filtersUrl) // todo возвращать только в конце
    {
        if ($this->categoryFeatures === null) {
            $this->getCategoryFeatures();
        }
        
        $currentFeatures = [];
        $uriArray = $this->parseFilterUrl($filtersUrl);
        foreach ($uriArray as $k => $v) {
            if (empty($v)) {
                continue;
            }
            @list($paramName, $paramValues) = explode('-', $v);

            if (!in_array($paramName, $this->getNotFeaturesParts())) {
                if (isset($this->categoryFeaturesByUrl[$paramName])
                    && ($feature = $this->categoryFeaturesByUrl[$paramName])
                    && !isset($selectedFeatures[$feature->id])) {
                    $selectedFeatures[$feature->id] = explode('_', $paramValues);
                } else {
                    return ExtenderFacade::execute(__METHOD__, false, func_get_args());
                }
            }
        }

        if (!empty($selectedFeatures)) {
            $valuesIds = [];
            if (!empty($this->categoryFeatures)) {
                // Выше мы определили какие значения каких свойств выбраны, теперь достаем эти значения из базы, чтобы за один раз
                foreach ($this->getFeaturesValues(['selected_features' => $selectedFeatures, 'category_id' => $this->category->children]) as $fv) {
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
    
    public function getMetaArray()
    {
        /** @var TranslationsEntity $translationsEntity */
        $translationsEntity = $this->entityFactory->get(TranslationsEntity::class);
        $translations = $translationsEntity->find(['lang' => $this->language->label]); // todo здесь должен быть FrontTranslations
        
        if ($this->categoryFeatures === null) {
            $this->getCategoryFeatures();
        }
        
        $metaArray = [];
        //определение текущего положения и выставленных параметров
        $uriArray = $this->parseFilterUrl($this->filtersUrl);
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
                                $metaArray['filter'][$f] = $translations->{"features_filter_" . $f};
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
                        if (isset($this->categoryFeaturesByUrl[$paramName])
                            && ($feature = $this->categoryFeaturesByUrl[$paramName])
                            && !isset($selectedFeatures[$feature->id])) {

                            $selectedFeatures[$feature->id] = explode('_', $paramValues);
                        }
                    }
                }
            }
        }

        if (!empty($selectedFeatures)) {
            $selectedFeaturesValues = [];
            if (!empty($this->categoryFeatures)) {
                // Выше мы определили какие значения каких свойств выбраны, теперь достаем эти значения из базы, чтобы за один раз
                foreach ($this->getFeaturesValues(['selected_features' => $selectedFeatures, 'category_id' => $this->category->children]) as $fv) {
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
    
            $currentCategoryFeatures = $this->getCurrentCategoryFeatures($filtersUrl);
            // Достаем выбранные значения свойств для других языков
            $langValuesFilter = [];
            foreach ($currentCategoryFeatures as $featureId=>$values) {
                $langValuesFilter[$featureId] = array_keys($values);
            }
            $langValues = $featuresValuesEntity->getFeaturesValuesAllLang($langValuesFilter);
            
            //  Заменяем url языка с учетом ЧПУ
            foreach ($languages as $l) {
                $furl = ['sort'=>null];
                $featuresAltLang = [];
                // Для каждого значения, выбираем все его варианты на других языках
                foreach ($currentCategoryFeatures as $featureId=>$values) {
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
        if (is_array($params) && is_array(reset($params))) {
            $params = reset($params);
        }

        $resultArray = ['brand'=>[],'features'=>[], 'filter'=>[], 'sort'=>null,'page'=>null];
        $uriArray = $this->parseFilterUrl($this->filtersUrl);
        if (($currentFeaturesValues = $this->getCurrentCategoryFeatures($this->filtersUrl)) === false) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        $categoryFeatures = $this->getCategoryFeatures();

        $resultArray = $this->getCurrentUrlParams($uriArray, $currentFeaturesValues, $resultArray);
        $resultArray = $this->getNewUrlParams($categoryFeatures, $params, $resultArray);
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
                        {
                            $paramValues = mb_substr($v, strlen($paramName) + 1);
                            $resultArray['brand'] = explode('_', $paramValues);
                            break;
                        }
                        case 'filter':
                        {
                            $resultArray['filter'] = explode('_', $paramValues);
                            break;
                        }
                        case 'sort':
                        {
                            $resultArray['sort'] = strval($paramValues);
                            break;
                        }
                        case 'page':
                        {
                            $resultArray['page'] = $paramValues;
                            break;
                        }
                        default:
                        {
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
        }

        return ExtenderFacade::execute(__METHOD__, $resultArray, func_get_args());
    }

    /**
     * Определяем переданные параметры для ссылки
     *
     * @param $categoryFeatures
     * @param $params
     * @param $resultArray
     * @return array
     */
    private function getNewUrlParams($categoryFeatures, $params, $resultArray)
    {
        foreach($params as $paramName=>$paramValues) {
            if ($parsedParams = $this->filterChpuUrlParseParams($paramName, $paramValues, $resultArray)) {
                $resultArray = array_merge($resultArray, $parsedParams);
            } else {
                switch ($paramName) {
                    case 'brand':
                    {
                        if (is_null($paramValues)) {
                            unset($resultArray['brand']);
                        } elseif (in_array($paramValues, $resultArray['brand'])) {
                            unset($resultArray['brand'][array_search($paramValues, $resultArray['brand'])]);
                        } else {
                            $resultArray['brand'][] = $paramValues;
                        }
                        break;
                    }
                    case 'filter':
                    {
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
                    }
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

                                if (!empty($categoryFeatures[$featureId]->values)) {
                                    $paramValues = (array)$paramValues;
                                    foreach ($paramValues as $valueTranslit) {
                                        if (!empty($valueId = $categoryFeatures[$featureId]->values_ids[$valueTranslit])) {
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

    private function getFeaturesValues(array $filter)
    {
        array_multisort($filter);
        $cacheKey = serialize($filter);

        if (!empty($this->featureValuesCache[$cacheKey])) {
            return ExtenderFacade::execute(__METHOD__, $this->featureValuesCache[$cacheKey], func_get_args());
        }

        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
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

}