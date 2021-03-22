<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Exception;
use Okay\Entities\BrandsEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesEntity;

class FurlFilterParserHelper
{
    private $sorts = [];
    private $pages = [];
    private $otherFilters = [];
    private $brandsUrls = [];
    private $featuresUrls = [];
    
    private $filtersUrl;
    private $isSetFiltersUrl;
    
    private $filterHelper;
    private $entityFactory;

    public function __construct(FilterHelper $filterHelper, EntityFactory $entityFactory)
    {
        $this->filterHelper = $filterHelper;
        $this->entityFactory = $entityFactory;
    }
    
    /**
     * @param $filtersUrl
     * @return $this
     */
    public function setFilterUrl($filtersUrl) : self
    {
        $this->isSetFiltersUrl = true;
        $this->filtersUrl = trim($filtersUrl, '/');
        return $this;
    }

    /**
     * Метод возвращает доступные варианты сортировок
     * 
     * @return array
     */
    public function getSortTypes() : array
    {
        return ExtenderFacade::execute(__METHOD__, ['position', 'price', 'price_desc', 'name', 'name_desc', 'rating', 'rating_desc'], func_get_args());
    }

    /**
     * Метод возвращает доступные "дополнительные" фильтры
     * 
     * @return array
     */
    public function getOtherFiltersTypes() : array
    {
        return ExtenderFacade::execute(__METHOD__, ['discounted', 'featured'], func_get_args());
    }

    /**
     * Метод возвращает части фильтров, для валидации структуры урла
     * Может понадобиться если модуль будет добавлять кастомный фильтр
     * 
     * @return array
     * @throws Exception
     */
    public function getUrlParts() : array // todo нужны тесты
    {
        $parts = [];
        if ($currentPage = $this->getCurrentPage()) {
            $parts['{$page}'] = [
                'key' => 'page',
                'value' => $currentPage,
            ];
        }
        if ($currentSort = $this->getCurrentSort()) {
            $parts['{$sort}'] = [
                'key' => 'sort',
                'value' => $currentSort,
            ];
        }
        if ($currentOtherFilters = $this->getCurrentOtherFilters()) {
            $parts['{$otherFilter}'] = [
                'key' => 'filter',
                'value' => $currentOtherFilters,
            ];
        }
        if ($currentBrandsUrls = $this->getCurrentBrandsUrls()) {
            $parts['{$brand}'] = [
                'key' => 'brand',
                'value' => $currentBrandsUrls,
            ];
        }
        if ($currentFeaturesUrls = $this->getCurrentFeaturesUrls()) {
            $features = [];
            foreach ($currentFeaturesUrls as $featureUrl => $valuesTranslit) {
                $features[$featureUrl] = [
                    'key' => $featureUrl,
                    'value' => $valuesTranslit,
                ];
            }
            
            $parts['{$features}'] = $features;
        }
        
        return ExtenderFacade::execute(__METHOD__, $parts, func_get_args());
    }
    
    /**
     * Возвращает номер текущей страницы пагинации
     *
     * @return string|false
     * @throws Exception
     */
    public function getCurrentPage()
    {
        if (!$this->isSetFiltersUrl) {
            throw new Exception('Param filtersUrl is not set. Must be installed via setFilterUrl() method');
        }
        
        if (isset($this->pages[$this->filtersUrl])) {
            return $this->pages[$this->filtersUrl]; // no ExtenderFacade
        }

        $result = '';
        
        if (preg_match('~(?:/|\b)(?:page-([0-9]+|all))(?:/|\b)~', $this->filtersUrl, $matches)) {
            $result = $matches[1];
            
            // Если номер страницы начинается с 0 - бросаем 404
            if (strpos($result, '0') === 0) {
                $result = false;
            }
        }
        $this->pages[$this->filtersUrl] = $result;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * Метод возвращает текущую сортировку
     * 
     * @return string|false
     * @throws Exception
     */
    public function getCurrentSort()
    {
        if (!$this->isSetFiltersUrl) {
            throw new Exception('Param filtersUrl is not set. Must be installed via setFilterUrl() method');
        }
        
        if (isset($this->sorts[$this->filtersUrl])) {
            return $this->sorts[$this->filtersUrl]; // no ExtenderFacade
        }
        
        $result = '';
        if (preg_match('~(?:/|\b)(?:sort-([\w]+))(?:/|\b)~u', $this->filtersUrl, $matches)) {

            $result = $matches[1];
            if (!in_array($result, $this->getSortTypes())) {
                $result = false;
            }
        }
        $this->sorts[$this->filtersUrl] = $result;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * Метод возвращает текущие "дополнительные" фильтры
     * 
     * @return array|false
     * @throws Exception
     */
    public function getCurrentOtherFilters()
    {
        if (!$this->isSetFiltersUrl) {
            throw new Exception('Param filtersUrl is not set. Must be installed via setFilterUrl() method');
        }
        
        if (isset($this->otherFilters[$this->filtersUrl])) {
            return $this->otherFilters[$this->filtersUrl]; // no ExtenderFacade
        }

        $result = [];

        if (preg_match('~(?:/|\b)(?:filter-((?:[\w]+_?)+))(?:/|\b)~u', $this->filtersUrl, $matches)) {
            
            $currentFilters = explode('_', $matches[1]);
            $otherFiltersTypes = $this->getOtherFiltersTypes();

            $result = array_values(array_intersect($otherFiltersTypes, $currentFilters));
            
            // Если есть значения, которых быть не может
            if (array_diff($currentFilters, $otherFiltersTypes)) {
                $result = false;
            } elseif (count($currentFilters) != count($result)) { // Если есть дубликаты значений
                $result = false;
            }
        }

        $this->otherFilters[$this->filtersUrl] = $result;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * @return array|false
     * @throws Exception
     */
    public function getCurrentBrandsUrls()
    {
        if (!$this->isSetFiltersUrl) {
            throw new Exception('Param filtersUrl is not set. Must be installed via setFilterUrl() method');
        }

        if (isset($this->brandsUrls[$this->filtersUrl])) {
            return $this->brandsUrls[$this->filtersUrl]; // no ExtenderFacade
        }

        $result = [];
        
        if (preg_match('~(?:/|\b)(?:brand-((?:[\w]+_?)+))(?:/|\b)~u', $this->filtersUrl, $matches)) {
            
            $currentBrands = explode('_', $matches[1]);

            /** @var BrandsEntity $brandsEntity */
            $brandsEntity = $this->entityFactory->get(BrandsEntity::class);
            // todo Здесь возможно нужно доставать только бренды этой категории
            if ($brandsUrls = $brandsEntity->col('url')->find(['url' => $currentBrands])) {

                // Если есть бренды, которых быть не может
                if (array_diff($currentBrands, $brandsUrls)) {
                    $result = false;
                } elseif (count($currentBrands) != count($brandsUrls)) { // Если есть дубликаты значений
                    $result = false;
                } else {
                    // Сортируем бренды в порядке как они в базе были
                    $result = array_values(array_intersect($brandsUrls, $currentBrands));
                }
            } else {
                $result = false;
            }
        }
        
        $this->brandsUrls[$this->filtersUrl] = $result;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
    /**
     * @return array|false
     * @throws Exception
     */
    public function getCurrentFeaturesUrls()
    {
        if (!$this->isSetFiltersUrl) {
            throw new Exception('Param filtersUrl is not set. Must be installed via setFilterUrl() method');
        }

        if (isset($this->featuresUrls[$this->filtersUrl])) {
            return $this->featuresUrls[$this->filtersUrl]; // no ExtenderFacade
        }

        $result = [];
        $currentFeaturesUrls = [];
        $currentFeaturesValuesUrls = [];
        foreach (explode('/', $this->filtersUrl) as $featureUrl => $v) {
            if (empty($v)) {
                continue;
            }
            
            if (strpos($v, '-') === false) {
                $this->featuresUrls[$this->filtersUrl] = false;
                return ExtenderFacade::execute(__METHOD__, $result, func_get_args()); 
            }
            list($featureUrl, $valuesString) = explode('-', $v, 2);
            
            if (in_array($featureUrl, $this->filterHelper->getNotFeaturesParts())) {
                continue;
            }
            $currentFeaturesUrls[] = $featureUrl;
            $currentFeaturesValuesUrls[$featureUrl] = explode('_', $valuesString);
        }

        if (empty($currentFeaturesUrls)) {
            $this->featuresUrls[$this->filtersUrl] = $result;
            return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
        }
        
        /** @var FeaturesEntity $featuresEntity */
        $featuresEntity = $this->entityFactory->get(FeaturesEntity::class);

        /** @var FeaturesValuesEntity $featuresValuesEntity */
        $featuresValuesEntity = $this->entityFactory->get(FeaturesValuesEntity::class);
        
        // todo Здесь возможно нужно доставать только свойства этой категории
        if ($featuresUrls = $featuresEntity->col('url')->find(['url' => $currentFeaturesUrls])) {
            // Если есть бренды, которых быть не может
            if (array_diff($currentFeaturesUrls, $featuresUrls)) {
                $result = false;
            } elseif (count($currentFeaturesUrls) != count($featuresUrls)) {// Если есть дубликаты свойств
                $result = false;
            } 
            
            if ($result !== false) {
                $fvList = $featuresValuesEntity->cols([
                    'translit',
                    'f.url as feature_url',
                    ])->find([
                        'selected_features' => $currentFeaturesValuesUrls,
                    ]);

                $featuresValues = [];
                foreach ($featuresUrls as $featureUrl) {
                    $featuresValues[$featureUrl] = [];
                }
                
                foreach ($fvList as $featureValue) {
                    $featuresValues[$featureValue->feature_url][] = $featureValue->translit;
                }
                foreach ($currentFeaturesValuesUrls as $featureUrl => $values) {
                    if (array_diff($values, $featuresValues[$featureUrl])) {
                        $result = false;
                        break;
                    }
                }
                if ($result !== false) {
                    $result = $featuresValues;
                }
            }
        } else {
            $result = false;
        }
        
        $this->featuresUrls[$this->filtersUrl] = $result;
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * Метод валидирует урл фильтра на предмет правильной последовательности переданных параметров фильтра
     * Возвращает true если урл валидный или код ошибки невалидности урла.
     * 
     * Ошибки описаны в константах:
     * FILTER_ERROR_WRONG_PARAMS
     * FILTER_ERROR_MISCOUNTING_POSITION_OF_PARTS
     * FILTER_ERROR_MISCOUNTING_POSITION_OF_VALUES
     * 
     * @return bool|int
     * @throws Exception
     */
    public function validateUrl()
    {
        $urlParts = [];
        foreach ($this->getUrlParts() as $partKey => $part) {
            if (empty($part)) {
                continue;
            }
            if ($partKey == '{$features}') {
                $featuresParts = [];
                foreach ($part as $featurePart) {
                    $featuresParts[] = $featurePart['key'] . '-' . implode('_', (array)$featurePart['value']);
                }
                $urlParts[$partKey] = implode('/', $featuresParts);
            } else {
                $urlParts[$partKey] = $part['key'] . '-' . implode('_', (array)$part['value']);
            }
        }
        
        $actualString = strtr($this->filterHelper->getUrlPattern(), $urlParts);
        $actualString = preg_replace('~{\$[^$]*}~', '', $actualString);
        $actualString = trim(preg_replace('~/{2,}~', '/', $actualString), '/');
        
        if ($actualString == $this->filtersUrl) {
            return true;
        }
        
        $actualParts = explode('/', $actualString);
        $currentParts = explode('/', $this->filtersUrl);
        
        if (count($actualParts) != count($currentParts)) {
            return FILTER_ERROR_WRONG_PARAMS;
        }
        
        // Проходимся по элементам фильтра
        foreach ($actualParts as $k => $actualPart) {
            if (!isset($currentParts[$k])) {
                return FILTER_ERROR_WRONG_PARAMS;
            }
            list($actualParamName, $actualValues) = explode('-', $actualPart, 2);
            list($currentParamName, $currentValues) = explode('-', $currentParts[$k], 2);
            
            // Если на конкретной позиции название параметра не совпадает с ожидаемым - позиции элементов фильтра перепутаны
            if ($actualParamName != $currentParamName) {
                return FILTER_ERROR_MISCOUNTING_POSITION_OF_PARTS;
            }
            
            // Если строки разные - перепутаны местами значения, на предмет ложных значений валидировал метод 
            // возвращающий свою часть фильтра (getCurrentFeaturesUrls, getCurrentBrandsUrls, getCurrentSort etc)
            if ($actualValues != $currentValues) {
                return FILTER_ERROR_MISCOUNTING_POSITION_OF_VALUES;
            }
        }
        return true;
    }
}