<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendCouponsRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function postCoupon()
    {
        $coupon         = new \stdClass();
        $coupon->id     = $this->request->post('new_id', 'integer');
        $coupon->code   = $this->request->post('new_code', 'string');
        $coupon->value  = $this->request->post('new_value', 'float');
        $coupon->type   = $this->request->post('new_type', 'string');
        $coupon->single = $this->request->post('new_single', 'float');
        $coupon->min_order_price = $this->request->post('new_min_order_price', 'float');

        $expired = $this->request->post('new_expire');
        if (!empty($expired)) {
            $coupon->expire = date('Y-m-d', strtotime($expired));
        } else {
            $coupon->expire = null;
        }

        return ExtenderFacade::execute(__METHOD__, $coupon, func_get_args());
    }

    public function postNewCode()
    {
        $newCode = $this->request->post('new_code');
        return ExtenderFacade::execute(__METHOD__, $newCode, func_get_args());
    }

    public function postCheck()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $check = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }
}