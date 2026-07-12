<?php

namespace Okay\Controllers;

use Okay\Core\Router;
use Okay\Core\Response;
use Okay\Core\WishList;
use Okay\Helpers\ValidateHelper;
use Okay\Helpers\WishListHelper;

class WishListController extends AbstractController
{
    public function render()
    {
        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('wishlist', [], true));
        $this->response->setContent('wishlist.tpl');
    }

    /**
     * @param WishList $wishList
     * @param WishListHelper $wishListHelper
     */
    public function ajaxUpdate(
        WishList $wishList,
        WishListHelper $wishListHelper,
        ValidateHelper $validateHelper
    ) {

        if (!$this->request->isPost()) {
            return $this->rejectWishListMutation(405, 'method_not_allowed');
        }

        if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
            return $this->rejectWishListMutation(403, $error);
        }

        $productId = $this->request->post('id', 'integer');
        $action = $this->request->post('action');
        if ($action === 'delete') {
            $wishList->deleteItem($productId);
        } else {
            $wishList->addItem($productId);
        }

        $result = $wishListHelper->getAjaxWishListResult();

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    private function rejectWishListMutation(int $statusCode, string $error): Response
    {
        $this->response->setStatusCode($statusCode);
        return $this->response->setContent(json_encode([
            'result' => 0,
            'error' => $error,
        ]), RESPONSE_JSON);
    }
}
