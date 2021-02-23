<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;

class OrderMetadataHelper extends CommonMetadataHelper
{
    public function getMetaTitle()
    {
        $order = $this->design->getVar('order');
        /** @var FrontTranslations $translations */
        $translations = $this->SL->getService(FrontTranslations::class);
        $metaTitle = $this->compileMetadata($translations->getTranslation('order_title')) . ' ' . $order->id;
        return ExtenderFacade::execute(__METHOD__, $metaTitle, func_get_args());
    }
    
    public function getMetaKeywords()
    {
        return ExtenderFacade::execute(__METHOD__, '', func_get_args());
    }
    
    public function getMetaDescription()
    {
        return ExtenderFacade::execute(__METHOD__, '', func_get_args());
    }
    
}