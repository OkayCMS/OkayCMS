<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendCouponsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendCouponsRequest;

class CouponsAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendCouponsRequest $couponsRequest,
        BackendValidateHelper $backendValidateHelper,
        BackendCouponsHelper  $backendCouponsHelper
    ){
        if ($couponsRequest->postNewCode()){
            $coupon = $couponsRequest->postCoupon();
            if($error = $backendValidateHelper->getCouponsValidateError($coupon)) {
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
        $this->response->setContent($this->design->fetch('coupons.tpl'));
    }
    
}
