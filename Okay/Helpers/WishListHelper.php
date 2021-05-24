<?php


namespace Okay\Helpers;



use Okay\Core\Design;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\WishList;
use Psr\Log\LoggerInterface;

class WishListHelper
{
    /** @var Design  */
    private $design;
    
    /** @var LoggerInterface  */
    private $logger;
    
    /** @var WishList */
    private $wishList;

    /** @var FrontTemplateConfig */
    private $frontTemplateConfig;

    public function __construct(
        Design $design,
        WishList $wishList,
        FrontTemplateConfig $frontTemplateConfig,
        LoggerInterface $logger 
    ) {
        $this->design = $design;
        $this->wishList = $wishList;
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->logger = $logger;
    }
    
    public function getAjaxWishListResult()
    {
        $this->design->assign('wishlist', $this->wishList->get());
        
        $result = [];
        
        if (is_file('design/' . $this->frontTemplateConfig->getTheme() . '/html/wishlist_informer.tpl')) {
            $result['wishlist_informer'] = $this->design->fetch('wishlist_informer.tpl');
        } else {
            $this->logger->error('File "design/' . $this->frontTemplateConfig->getTheme() . '/html/wishlist_informer.tpl" not found');
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
}