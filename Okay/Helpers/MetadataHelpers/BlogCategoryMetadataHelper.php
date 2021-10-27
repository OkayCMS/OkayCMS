<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BlogCategoryMetadataHelper extends CommonMetadataHelper
{
    /** @var object */
    private $category;

    /** @var bool */
    private $isAllPages;

    /** @var int */
    private $currentPageNum;

    public function setUp($category, bool $isAllPages = false, int $currentPageNum = 1): void
    {
        $this->category       = $category;
        $this->isAllPages     = $isAllPages;
        $this->currentPageNum = $currentPageNum;
    }

    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        $categoryH1 = !empty($this->category->name_h1) ? $this->category->name_h1 : $this->category->name;
        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = (string)$categoryH1;
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationTemplate(): string
    {
        if ((int)$this->currentPageNum > 1 || $this->isAllPages === true) {
            $annotation = '';
        } elseif ($pageAnnotation = parent::getAnnotationTemplate()) {
            $annotation = $pageAnnotation;
        } else {
            $annotation = (string)$this->category->annotation;
        }

        return ExtenderFacade::execute(__METHOD__, $annotation, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate(): string
    {
        if ((int)$this->currentPageNum > 1 || $this->isAllPages === true) {
            $description = '';
        } elseif ($pageDescription = parent::getDescriptionTemplate()) {
            $description = $pageDescription;
        } else {
            $description = (string)$this->category->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }
    
    public function getMetaTitleTemplate(): string
    {
        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = (string)$this->category->meta_title;
        }

        // Добавим номер страницы к тайтлу
        if ((int)$this->currentPageNum > 1 && $this->isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $this->currentPageNum;
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }
    
    public function getMetaKeywordsTemplate(): string
    {
        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = (string)$this->category->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }
    
    public function getMetaDescriptionTemplate(): string
    {
        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = (string)$this->category->meta_description;
        }

        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
    
    /**
     * @inheritDoc
     */
    protected function getParts(): array
    {

        if (!empty($this->parts)) {
            return $this->parts; // no ExtenderFacade
        }

        $this->parts = [
            '{$category}' => ($this->category->name ? $this->category->name : ''),
            '{$category_h1}' => ($this->category->name_h1 ? $this->category->name_h1 : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];

        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }
    
}