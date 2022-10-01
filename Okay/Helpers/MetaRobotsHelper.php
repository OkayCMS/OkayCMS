<?php


namespace Okay\Helpers;


use Exception;

class MetaRobotsHelper
{
    private $catalogPagination;
    private $catalogPageAll;
    private $catalogBrand;
    private $catalogFeatures;
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
        $catalogBrand,
        $catalogFeatures,
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
        $this->catalogBrand = (int)$catalogBrand;
        $this->catalogFeatures = (int)$catalogFeatures;
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
                foreach ($features as $feature) {
                    if (!property_exists($feature, 'id')) {
                        throw new Exception('Param $features must have id property');
                    }
                    if (!property_exists($feature, 'features_values')) {
                        throw new Exception('Param $features must have features_values property');
                    }
                    foreach ($feature->features_values as $value) {

                        if (!property_exists($value, 'value')) {
                            throw new Exception('Param $features[]->features_values must have value property');
                        }
                        if (!property_exists($value, 'to_index')) {
                            throw new Exception('Param $features[]->features_values must have to_index property');
                        }
                        
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
    public function getCatalogRobots($page, array $otherFilter, array $featuresFilter, array $brandsFilter) : int
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
            $paginationCatalogRobots = $this->catalogFilterPagination; // no ExtenderFacade
        } else if (!empty($page)) {
            if ($page == 'all') {
                $paginationCatalogRobots = $this->catalogPageAll; // no ExtenderFacade
            } else {
                $paginationCatalogRobots = $this->catalogPagination; // no ExtenderFacade
            }
        } else {
            $paginationCatalogRobots = 0;
        }
        
        $baseCatalogRobots = $this->getBaseCatalogRobots($otherFilter);
        $catalogRobots = $this->getCatalogRobotsExecutor($featuresFilter, $brandsFilter);
        
        return max($baseCatalogRobots, $catalogRobots, $paginationCatalogRobots); // no ExtenderFacade
    }

    private function getCatalogRobotsExecutor(array $featuresFilter, array $brandsFilter) : int
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

        if (!empty($featuresFilter)) {
            return $this->catalogFeatures; // no ExtenderFacade
        }

        if (!empty($brandsFilter)) {
            return $this->catalogBrand; // no ExtenderFacade
        }

        return ROBOTS_INDEX_FOLLOW;
    }

    /**
     * @param array $otherFilter
     * @return int
     */
    private function getBaseCatalogRobots(array $otherFilter) : int
    {
        if ($this->maxFilterDepth === 0 && !empty($otherFilter)) {
            return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
        }

        if (!empty($otherFilter)) {
            if (count($otherFilter) > $this->maxOtherFilterDepth) {
                return ROBOTS_NOINDEX_NOFOLLOW; // no ExtenderFacade
            }
        }

        if (!empty($otherFilter)) {
            return $this->catalogOtherFilter; // no ExtenderFacade
        }
        
        return ROBOTS_INDEX_FOLLOW;
    }
}