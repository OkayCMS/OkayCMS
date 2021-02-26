<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\FeaturesAliasesValuesEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\FeaturesValuesAliasesValuesEntity;
use Okay\Entities\SEOFilterPatternsEntity;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MetaRobotsHelper;

class BrandMetadataHelper extends CommonMetadataHelper
{
 
    private $metaArray = [];
    private $metaDelimiter = ', ';
    private $autoMeta;
    private $metaRobots;

    /**
     * @inheritDoc
     */
    public function getH1Template()
    {
        $brand = $this->design->getVar('brand');
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } elseif (!empty($filterAutoMeta->h1)) {
            $h1 = $brand->name . ' ' . $filterAutoMeta->h1;
        } else {
            $h1 = $brand->name;
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate()
    {
        $brand = $this->design->getVar('brand');
        $isFilterPage = $this->design->getVar('is_filter_page');
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ((int)$currentPageNum > 1 || $isAllPages === true) {
            $description = '';
        } elseif ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        /*} elseif (!empty($filterAutoMeta->description)) {
            $description = $filterAutoMeta->description;*/
        } elseif ($isFilterPage === false) {
            $description = $brand->description;
        } else {
            $description = '';
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate()
    {
        $brand = $this->design->getVar('brand');
        $filterAutoMeta = $this->getFilterAutoMeta();
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');

        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } elseif (!empty($filterAutoMeta->meta_title)) {
            $metaTitle = $brand->meta_title . ' ' . $filterAutoMeta->meta_title;
        } else {
            $metaTitle = $brand->meta_title;
        }

        // Добавим номер страницы к тайтлу
        if ((int)$currentPageNum > 1 && $isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $currentPageNum;
        }

        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywordsTemplate()
    {
        $brand = $this->design->getVar('brand');
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } elseif (!empty($filterAutoMeta->meta_keywords)) {
            $metaKeywords = $brand->meta_keywords . ' ' . $filterAutoMeta->meta_keywords;
        } else {
            $metaKeywords = $brand->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate()
    {
        $brand = $this->design->getVar('brand');
        $filterAutoMeta = $this->getFilterAutoMeta();

        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } elseif (!empty($filterAutoMeta->meta_description)) {
            $metaDescription = $brand->meta_description . ' ' . $filterAutoMeta->meta_description;
        } else {
            $metaDescription = $brand->meta_description;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }

    private function getFilterAutoMeta()
    {

        if (empty($this->metaRobots)) {
            /** @var MetaRobotsHelper $metaRobotsHelper */
            $metaRobotsHelper = $this->SL->getService(MetaRobotsHelper::class);

            $metaArray = $this->getMetaArray();

            $currentPage = isset($metaArray['page']) ? $metaArray['page'] : null;
            $currentOtherFilters = isset($metaArray['filter']) ? $metaArray['filter'] : [];

            $this->metaRobots = $metaRobotsHelper->getCatalogRobots($currentPage, $currentOtherFilters);
        }

        if ($this->metaRobots == ROBOTS_NOINDEX_FOLLOW || $this->metaRobots == ROBOTS_NOINDEX_NOFOLLOW) {
            return false;
        }
        
        if (empty($this->autoMeta)) {
            
            $autoMeta = [
                'h1' => '',
                'meta_title' => '',
                'meta_keywords' => '',
                'meta_description' => '',
                'description' => '',
            ];

            $metaArray = $this->getMetaArray();
            if (!empty($metaArray)) {
                foreach ($metaArray as $type => $_meta_array) {
                    switch ($type) {
                        case 'brand': // no break
                        case 'filter':
                        {
                            $autoMeta['h1'] = $autoMeta['meta_title'] = $autoMeta['meta_keywords'] = $autoMeta['meta_description'] = $autoMeta['description'] = implode($this->metaDelimiter, $_meta_array);
                            break;
                        }
                    }
                }
            }
            $this->autoMeta = (object)$autoMeta;
        }

        return $this->autoMeta;
    }
    
    /**
     * @inheritDoc
     */
    protected function getParts()
    {
        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }

        $brand = $this->design->getVar('brand');
        
        $this->parts = [
            '{$brand}' => ($brand->name ? $brand->name : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];
        
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }

    private function getMetaArray()
    {
        if (empty($this->metaArray)) {
            /** @var FilterHelper $filterHelper */
            $filterHelper = $this->SL->getService(FilterHelper::class);
            $this->metaArray = $filterHelper->getMetaArray();
        }
        return $this->metaArray;
    }
    
}