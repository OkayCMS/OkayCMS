<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendCouponsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendCouponsRequest;
use Okay\Helpers\DiscountsHelper;

class CouponsAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendCouponsRequest $couponsRequest,
        BackendValidateHelper $backendValidateHelper,
        BackendCouponsHelper  $backendCouponsHelper,
        DiscountsHelper       $discountsHelper
    ){
        if ($couponsRequest->postNewCode()){
            $coupon = $couponsRequest->postCoupon();
            if($error = $backendValidateHelper->getCouponsValidateError($coupon)) {
                $this->design->assign('message_error', $error);
            } else {
                $coupon     = $backendCouponsHelper->prepareAdd($coupon);
                $coupon->id = $backendCouponsHelper->add($coupon);
                $this->design->assign('message_success', 'added');
            }
        }

        if ($this->request->method('post')) {
            $ids = $couponsRequest->postCheck();
            switch ($couponsRequest->postAction()) {
                case 'delete': {
                    $backendCouponsHelper->delete($ids);
                    break;
                }
            }
        }

        $filter         = $backendCouponsHelper->buildFilter();
        $couponsCount   = $backendCouponsHelper->count($filter);
        $pagesCount     = ceil($couponsCount/$filter['limit']);
        $filter['page'] = min($filter['page'], $pagesCount);
        $coupons        = $backendCouponsHelper->findCoupons($filter);

        if (isset($filter['keyword'])) {
            $this->design->assign('keyword', $filter['keyword']);
        }

        $this->design->assign('coupons_count', $couponsCount);
        $this->design->assign('pages_count',   $pagesCount);
        $this->design->assign('current_page',  $filter['page']);
        $this->design->assign('coupons',       $coupons);

        if (!$this->isCouponSignInCartSets($discountsHelper)) {
            $this->design->assign('coupon_sign_missing', true);
        }

        $this->response->setContent($this->design->fetch('coupons.tpl'));
    }

    /**
     * Без знака $ok_coup в наборах для корзины купоны никогда не применятся
     */
    private function isCouponSignInCartSets(DiscountsHelper $discountsHelper)
    {
        foreach ((array) $discountsHelper->getCartSets() as $set) {
            $signs = $discountsHelper->parseSet($set);
            if (empty($signs['cart'])) {
                continue;
            }

            foreach ($signs['cart'] as $sign) {
                if ($sign->sign === 'ok_coup') {
                    return true;
                }
            }
        }

        return false;
    }
    
}
