<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;

class AuthorMetadataHelper extends CommonMetadataHelper
{
    /** @var object */
    private $author;

    /** @var bool */
    private $isAllPages;

    /** @var int */
    private $currentPageNum;

    public function setUp($author, bool $isAllPages = false, int $currentPageNum = 1): void
    {
        $this->author         = $author;
        $this->isAllPages     = $isAllPages;
        $this->currentPageNum = $currentPageNum;
    }

    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        if ($pageH1 = parent::getH1Template()) {
            $h1 = $pageH1;
        } else {
            $h1 = (string)$this->author->name;
        }

        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
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
            $description = (string)$this->author->description;
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate(): string
    {
        if ($pageTitle = parent::getMetaTitleTemplate()) {
            $metaTitle = $pageTitle;
        } else {
            $metaTitle = (string)$this->author->meta_title;
        }

        // Добавим номер страницы к тайтлу
        if ((int)$this->currentPageNum > 1 && $this->isAllPages !== true) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle .= $translations->getTranslation('meta_page') . ' ' . $this->currentPageNum;
        }

        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaKeywordsTemplate(): string
    {
        if ($pageKeywords = parent::getMetaKeywordsTemplate()) {
            $metaKeywords = $pageKeywords;
        } else {
            $metaKeywords = (string)$this->author->meta_keywords;
        }

        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate(): string
    {
        if ($pageMetaDescription = parent::getMetaDescriptionTemplate()) {
            $metaDescription = $pageMetaDescription;
        } else {
            $metaDescription = (string)$this->author->meta_description;
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
            '{$author}' => ($this->author->name ? $this->author->name : ''),
            '{$sitename}' => ($this->settings->get('site_name') ? $this->settings->get('site_name') : ''),
        ];
        
        return $this->parts = ExtenderFacade::execute(__METHOD__, $this->parts, func_get_args());
    }
    
}