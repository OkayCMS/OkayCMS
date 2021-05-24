<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Core\WishList;
use Okay\Helpers\WishListHelper;

class WishListController extends AbstractController
{
    
    public function render()
    {
        $this->design->assign('canonical', Router::generateUrl('wishlist', [], true));
        $this->response->setContent('wishlist.tpl');
    }

    /**
     * @param WishList $wishList
     * @param WishListHelper $wishListHelper
     */
    public function ajaxUpdate(
        WishList $wishList,
        WishListHelper $wishListHelper
    ) {

        $productId = $this->request->get('id', 'integer');
        $action = $this->request->get('action');
        if ($action == 'delete') {
            $wishList->deleteItem($productId);
        } else {
            $wishList->addItem($productId);
        }

        $result = $wishListHelper->getAjaxWishListResult();
        
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
}
