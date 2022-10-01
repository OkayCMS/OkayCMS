<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\PagesEntity;

class AllProductsMetadataHelper extends CommonMetadataHelper
{
    /** @var string */
    private $keyword;

    /** @var bool */
    private $isAllPages;

    /** @var int */
    private $currentPageNum;

    public function setUp($keyword = '', bool $isAllPages = false, int $currentPageNum = 1): void
    {
        $this->keyword        = $keyword;
        $this->isAllPages     = $isAllPages;
        $this->currentPageNum = $currentPageNum;
    }

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->keyword) {
            $entityFactory = $this->SL->getService(EntityFactory::class);
            /** @var PagesEntity $pagesEntity */
            $pagesEntity = $entityFactory->get(PagesEntity::class);
            $this->page = $pagesEntity->get('all-products');
        }
    }

    /**
     * @inheritDoc
     */
    public function getH1Template(): string
    {
        if ($this->keyword) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $h1 = $translations->getTranslation('general_search') . ' ' . $this->keyword;
        } else {
            $h1 = parent::getH1Template();
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate(): string
    {
        if ($this->keyword) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $description = $translations->getTranslation('general_search') . ' ' . $this->keyword;
        } else {
            $description = parent::getDescriptionTemplate();
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate(): string
    {
        if ($this->keyword) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle = $translations->getTranslation('general_search') . ' ' . $this->keyword;
        } else {
            $metaTitle = parent::getMetaTitleTemplate();
        }

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
        if ($this->keyword) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaKeywords = $translations->getTranslation('general_search') . ' ' . $this->keyword;
        } else {
            $metaKeywords = parent::getMetaKeywordsTemplate();
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate(): string
    {
        if ($this->keyword) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaDescription = $translations->getTranslation('general_search') . ' ' . $this->keyword;
        } else {
            $metaDescription = parent::getMetaDescriptionTemplate();
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
    
}