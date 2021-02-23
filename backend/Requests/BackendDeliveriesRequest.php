<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendDeliveriesRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postDelivery()
    {
        $delivery = new \stdClass();
        $delivery->id                        = $this->request->post('id', 'integer');
        $delivery->enabled                   = $this->request->post('enabled', 'boolean');
        $delivery->name                      = $this->request->post('name');
        $delivery->description               = $this->request->post('description');
        $delivery->price                     = $this->request->post('price');
        $delivery->free_from                 = $this->request->post('free_from');
        $delivery->paid                      = $this->request->post('delivery_type') === 'paid' ? 1 : 0;
        $delivery->separate_payment          = $this->request->post('separate_payment','boolean');
        $delivery->module_id                 = $this->request->post('module_id', 'integer');
        $delivery->hide_front_delivery_price = $this->request->post('hide_front_delivery_price', 'integer');

        return ExtenderFacade::execute(__METHOD__, $delivery, func_get_args());
    }

    public function postDeliveryPayments()
    {
        $deliveryPayments = $this->request->post('delivery_payments', null, []);
        return ExtenderFacade::execute(__METHOD__, $deliveryPayments, func_get_args());
    }

    public function postSettings()
    {
        $deliverySettings = $this->request->post('delivery_settings', null, []);
        return ExtenderFacade::execute(__METHOD__, $deliverySettings, func_get_args());
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