<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BlogCategoryMetadataHelper extends CommonMetadataHelper
{
 
    /**
     * @inheritDoc
     */
    public function getH1Template()
    {
        $category = $this->design->getVar('category');

        $categoryH1 = !empty($category->name_h1) ? $category->name_h1 : $category->name;
        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = $categoryH1;
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate()
    {
        $category = $this->design->getVar('category');
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');

        if ((int)$currentPageNum > 1 || $isAllPages === true) {
            $description = '';
        } elseif ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } else {
            $description = $category->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate()
    {
        $category = $this->design->getVar('category');
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');
        
        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = $category->meta_title;
        }

        // Добавим номер страницы к тайтлу
        if ((int)$currentPageNum > 1 && $isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $currentPageNum;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }
    
    public function getMetaKeywordsTemplate()
    {
        $category = $this->design->getVar('category');
        
        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = $category->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate()
    {
        $category = $this->design->getVar('category');
        
        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = $category->meta_description;
        }

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
    
    /**
     * @inheritDoc
     */
    protected function getParts()
    {

        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }
        
        $category = $this->design->getVar('category');
        
        $this->parts = [
            '{$category}' => ($category->name ? $category->name : ''),
            '{$category_h1}' => ($category->name_h1 ? $category->name_h1 : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];

        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }
    
}