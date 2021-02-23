<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendPaymentsRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postPayment()
    {
        $paymentMethod = new \stdClass();
        $paymentMethod->id          = $this->request->post('id', 'integer');
        $paymentMethod->enabled     = $this->request->post('enabled', 'boolean');
        $paymentMethod->auto_submit = $this->request->post('auto_submit', 'boolean');
        $paymentMethod->name        = $this->request->post('name');
        $paymentMethod->currency_id = $this->request->post('currency_id');
        $paymentMethod->description = $this->request->post('description');
        $paymentMethod->module      = $this->request->post('module');

        return ExtenderFacade::execute(__METHOD__, $paymentMethod, func_get_args());
    }

    public function postPaymentDeliveries()
    {
        $paymentDeliveries = $this->request->post('payment_deliveries', null, []);
        return ExtenderFacade::execute(__METHOD__, $paymentDeliveries, func_get_args());
    }

    public function postSettings()
    {
        $paymentSettings = $this->request->post('payment_settings', null, []);
        return ExtenderFacade::execute(__METHOD__, $paymentSettings, func_get_args());
    }

    public function postDeleteImage()
    {
        $deleteImage = $this->request->post('delete_image');
        return ExtenderFacade::execute(__METHOD__, $deleteImage, func_get_args());
    }

    public function fileImage()
    {
        $image = $this->request->files('image');
        return ExtenderFacade::execute(__METHOD__, $image, func_get_args());
    }

    public function postCheck()
    {
        $check = (array) $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }
}