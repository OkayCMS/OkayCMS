<?php


namespace Okay\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class CartRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postOrder()
    {
        $order = new \stdClass;
        $order->payment_method_id = $this->request->post('payment_method_id', 'integer');
        $order->delivery_id = $this->request->post('delivery_id', 'integer');
        $order->name        = $this->request->post('name');
        $order->last_name   = $this->request->post('last_name');
        $order->email       = $this->request->post('email');
        $order->address     = $this->request->post('address');
        $order->phone       = $this->request->post('phone');
        $order->comment     = $this->request->post('comment');
        $order->ip          = $_SERVER['REMOTE_ADDR'];

        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    public function postCoupon()
    {
        $couponCode = trim($this->request->post('coupon_code', 'string'));
        return ExtenderFacade::execute(__METHOD__, $couponCode, func_get_args());
    }
}