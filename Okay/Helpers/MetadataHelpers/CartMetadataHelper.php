<?php


namespace Okay\Helpers\MetadataHelpers;


use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;

class CartMetadataHelper extends CommonMetadataHelper
{
    public function getMetaTitle()
    {
        /** @var FrontTranslations $translations */
        $translations = $this->SL->getService(FrontTranslations::class);
        $metaTitle = $this->compileMetadata($translations->getTranslation('cart_title'));
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