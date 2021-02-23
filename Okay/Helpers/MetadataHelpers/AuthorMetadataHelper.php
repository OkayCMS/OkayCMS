<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;

class AuthorMetadataHelper extends CommonMetadataHelper
{

    /**
     * @inheritDoc
     */
    public function getH1Template()
    {
        $author = $this->design->getVar('author');

        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = $author->name;
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate()
    {
        $author = $this->design->getVar('author');
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');

        if ((int)$currentPageNum > 1 || $isAllPages === true) {
            $description = '';
        } elseif ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } else {
            $description = $author->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate()
    {
        $author = $this->design->getVar('author');
        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');

        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = $author->meta_title;
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
        $author = $this->design->getVar('author');

        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = $author->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate()
    {
        $author = $this->design->getVar('author');

        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = $author->meta_description;
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

        $author = $this->design->getVar('author');
        
        $this->parts = [
            '{$author}' => ($author->name ? $author->name : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];
        
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }
    
}