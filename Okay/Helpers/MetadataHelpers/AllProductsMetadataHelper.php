<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\PagesEntity;

class AllProductsMetadataHelper extends CommonMetadataHelper
{
    
    public function __construct()
    {
        parent::__construct();
        
        if (!$this->design->getVar('keyword')) {
            $entityFactory = $this->SL->getService(EntityFactory::class);
            /** @var PagesEntity $pagesEntity */
            $pagesEntity = $entityFactory->get(PagesEntity::class);
            $this->page = $pagesEntity->get('all-products');
        }
    }

    /**
     * @inheritDoc
     */
    public function getH1Template()
    {
        if ($keyword = $this->design->getVar('keyword')) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $h1 = $translations->getTranslation('general_search') . ' ' . $keyword;
        } else {
            $h1 = parent::getH1Template();
        }
        
        return ExtenderFacade::execute(__METHOD__, $h1, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getDescriptionTemplate()
    {
        if ($keyword = $this->design->getVar('keyword')) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $description = $translations->getTranslation('general_search') . ' ' . $keyword;
        } else {
            $description = parent::getDescriptionTemplate();
        }

        return ExtenderFacade::execute(__METHOD__, $description, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaTitleTemplate()
    {
        if ($keyword = $this->design->getVar('keyword')) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaTitle = $translations->getTranslation('general_search') . ' ' . $keyword;
        } else {
            $metaTitle = parent::getMetaTitleTemplate();
        }

        $isAllPages = $this->design->getVar('is_all_pages');
        $currentPageNum = $this->design->getVar('current_page_num');

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
        if ($keyword = $this->design->getVar('keyword')) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaKeywords = $translations->getTranslation('general_search') . ' ' . $keyword;
        } else {
            $metaKeywords = parent::getMetaKeywordsTemplate();
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaKeywords, func_get_args());
    }

    /**
     * @inheritDoc
     */
    public function getMetaDescriptionTemplate()
    {
        if ($keyword = $this->design->getVar('keyword')) {
            /** @var FrontTranslations $translations */
            $translations = $this->SL->getService(FrontTranslations::class);
            $metaDescription = $translations->getTranslation('general_search') . ' ' . $keyword;
        } else {
            $metaDescription = parent::getMetaDescriptionTemplate();
        }
        
        return ExtenderFacade::execute(__METHOD__, $metaDescription, func_get_args());
    }
    
}