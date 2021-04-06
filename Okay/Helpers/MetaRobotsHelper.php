<?php


namespace Okay\Helpers;


use Exception;

class MetaRobotsHelper
{
    private $catalogPagination;
    private $catalogPageAll;
    private $categoryBrand;
    private $categoryFeatures;
    private $catalogOtherFilter;
    private $catalogFilterPagination;
    
    private $maxBrandFilterDepth;
    private $maxOtherFilterDepth;
    private $maxFeaturesFilterDepth;
    private $maxFeaturesValuesFilterDepth;
    private $maxFilterDepth;
    
    private $features = [];

    public function setParams(
        $catalogPagination,
        $catalogPageAll,
        $categoryBrand,
        $categoryFeatures,
        $catalogOtherFilter,
        $catalogFilterPagination,
        $maxBrandFilterDepth,
        $maxOtherFilterDepth,
        $maxFeaturesFilterDepth,
        $maxFeaturesValuesFilterDepth,
        $maxFilterDepth
    ) {
        $this->catalogPagination = (int)$catalogPagination;
        $this->catalogPageAll = (int)$catalogPageAll;
        $this->categoryBrand = (int)$categoryBrand;
        $this->categoryFeatures = (int)$categoryFeatures;
        $this->catalogOtherFilter = (int)$catalogOtherFilter;
        $this->catalogFilterPagination = (int)$catalogFilterPagination;
        
        $this->maxBrandFilterDepth = (int)$maxBrandFilterDepth;
        $this->maxOtherFilterDepth = (int)$maxOtherFilterDepth;
        $this->maxFeaturesFilterDepth = (int)$maxFeaturesFilterDepth;
        $this->maxFeaturesValuesFilterDepth = (int)$maxFeaturesValuesFilterDepth;
        $this->maxFilterDepth = (int)$maxFilterDepth;
        
    }

    /**
     * @param object[] $features Массив доступных свойств для этой страницы.
     * В качестве свойства должен быть объект свойства содержащий в свойстве features_values массив объектов значений.
     * В качестве значения свойства должен быть объект значения содержащий (свойства объекта) value и to_index
     *
     * Пример данных:
     * [
     *  (object)[
     *      'id' => 1,
     *      'features_values' => [
     *          (object)[
     *              'value' => 'red',
     *              'to_index' => 1,
     *          ],
     *          (object)[
     *              'value' => 'blue',
     *              'to_index' => 0,
     *          ],
     *      ],
     *  ],
     *  (object)[
     *      'id' => 2,
     *      'features_values' => [
     *          (object)[
     *              'value' => 'small',
     *              'to_index' => 1,
     *          ],
     *      ],
     *  ],
     * ]
     *
     * @return $this
     * @throws Exception
     */
    public function setAvailableFeatures(array $features) : self
    {
        if (!empty($features)) {
            $firstItem = reset($features);
            if (is_object($firstItem)) {
                if (!property_exists($firstItem, 'id')) {
                    throw new Exception('Param $features must have id property');
                }
                if (!property_exists($firstItem, 'features_values')) {
                    throw new Exception('Param $features must have features_values property');
                }
                $firstFeatureValue = reset($firstItem->features_values);

                if (!property_exists($firstFeatureValue, 'value')) {
                    throw new Exception('Param $features[]->features_values must have value property');
                }
                if (!property_exists($firstFeatureValue, 'to_index')) {
                    throw new Exception('Param $features[]->features_values must have to_index property');
                }
                foreach ($features as $feature) {
                    foreach ($feature->features_values as $value) {
                        $this->features[$feature->id][$value->value] = $value;
                    }
                }
            }
        }

        return $this;
    }
    
    /**
     * @param string|int $page текущая страница, может быть all
     * @param array $otherFilter одномерный массив, содержащий значения ['discounted', 'featured'...]
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @return int
     * @throws Exception
     *
     * Определение meta robots для категории
     */
    public function getCategoryRobots($page, array $otherFilter, array $featuresFilter, array $brandsFilter) : int
    {
        // Если хоть одно значение свойства отмечено как не идексировать - страница не индексируется
        if (!empty($featuresFilter)) {
            foreach ($featuresFilter as $featureId=>$values) {
                foreach ($values as $value) {
                    if (!isset($this->features[$featureId])) {
                        throw new Exception('Wrong feature id "'.$featureId.'". Need set available features ids via '
                            . self::class
                            . '::setAvailableFeatures() method');
                    } elseif (!isset($this->features[$featureId][$value])) {
                        throw new Exception('Wrong feature value "'.$value.'" for feature id "'.$featureId.'". Need set available feature value via '
                            . self::class
                            . '::setAvailableFeatures() method');
                    }
                    if ($this->features[$featureId][$value]->to_index == false) {
                        return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
                    }
                }
            }
        }
        
        // Подсчитываем общую глубину фильтра
        $filterDepth = 0;
        if (!empty($otherFilter)) {
            $filterDepth++;
        }
        if (!empty($featuresFilter)) {
            $filterDepth += count($featuresFilter);
        }
        if (!empty($brandsFilter)) {
            $filterDepth++;
        }
        
        if ($filterDepth > $this->maxFilterDepth) {
            return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
        }

        if (!empty($page) && (!empty($otherFilter) || !empty($featuresFilter) || !empty($brandsFilter))) {
            return $this->catalogFilterPagination; // no ExtenderFacade
        }
        
        $catalogRobots = $this->getCatalogRobots($page, $otherFilter);
        $categoryRobots = $this->getCategoryRobotsExecutor($page, $featuresFilter, $brandsFilter);
        
        return max($catalogRobots, $categoryRobots); // no ExtenderFacade
    }

    private function getCategoryRobotsExecutor($page, array $featuresFilter, array $brandsFilter) : int
    {
        if (!empty($featuresFilter)) {
            foreach ($featuresFilter as $values) {
                if (count($values) > $this->maxFeaturesValuesFilterDepth) {
                    return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
                }
            }
            if (count($featuresFilter) > $this->maxFeaturesFilterDepth) {
                return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
            }
        }
        
        if (!empty($brandsFilter)) {
            if (count($brandsFilter) > $this->maxBrandFilterDepth) {
                return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
            }
        }

        if (!empty($page)) {
            if ($page == 'all') {
                return $this->catalogPageAll; // no ExtenderFacade
            } else {
                return $this->catalogPagination; // no ExtenderFacade
            }
        }

        if (!empty($featuresFilter)) {
            return $this->categoryFeatures; // no ExtenderFacade
        }

        if (!empty($brandsFilter)) {
            return $this->categoryBrand; // no ExtenderFacade
        }

        return ROBOTS_INDEX_FOLLOW;
    }
    
    /**
     * @param $page
     * @param array $otherFilter
     * @return int
     */
    public function getCatalogRobots($page, array $otherFilter) : int
    {
        if ($this->maxFilterDepth === 0 && !empty($otherFilter)) {
            return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
        }
        
        if (!empty($otherFilter)) {
            if (count($otherFilter) > $this->maxOtherFilterDepth) {
                return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
            }
        }
        
        if (!empty($page) && !empty($otherFilter)) {
            return $this->catalogFilterPagination; // no ExtenderFacade
        }
        
        if (!empty($page)) {
            if ($page == 'all') {
                return $this->catalogPageAll; // no ExtenderFacade
            } else {
                return $this->catalogPagination; // no ExtenderFacade
            }
        }
        
        if (!empty($otherFilter)) {
            return $this->catalogOtherFilter; // no ExtenderFacade
        }
        
        return ROBOTS_INDEX_FOLLOW;
    }
    
}