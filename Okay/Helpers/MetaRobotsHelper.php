<?php


namespace Okay\Helpers;


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
     * @param string|int $page текущая страница, может быть all
     * @param array $otherFilter одномерный массив, содержащий значения ['discounted', 'featured'...]
     * @param array $featuresFilter
     * @param array $brandsFilter
     * @return int
     *
     * Определение meta robots для категории
     */
    public function getCategoryRobots($page, array $otherFilter, array $featuresFilter, array $brandsFilter) : int
    {

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