<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Core\WishList;

class WishListController extends AbstractController
{
    
    public function render()
    {
        $this->design->assign('canonical', Router::generateUrl('wishlist', [], true));
        $this->response->setContent('wishlist.tpl');
    }
    
    public function ajaxUpdate(WishList $wishList)
    {

        $productId = $this->request->get('id', 'integer');
        $action = $this->request->get('action');
        if ($action == 'delete') {
            $wishList->deleteItem($productId);
        } else {
            $wishList->addItem($productId);
        }

        $this->design->assign('wishlist', $wishList->get());

        $result = $this->design->fetch('wishlist_informer.tpl');        
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
}
