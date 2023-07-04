<?php

namespace Okay\Modules\OkayCMS\DeliveryFields\Backend\Requests;

use Okay\Core\Request;

class BackendDeliveryFieldsRequest
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postDeliveryFields(): array
    {
        $postFields = $this->request->post('delivery_fields', null, []);

        $deliveryFields = [];
        foreach ($postFields as $n => $va) {
            foreach ($va as $i => $v) {
                if (empty($deliveryFields[$i])) {
                    $deliveryFields[$i] = new \stdClass();
                    $deliveryFields[$i]->deliveries = [];
                    $deliveryFields[$i]->visible = 0;
                    $deliveryFields[$i]->required = 0;
                }
                $deliveryFields[$i]->$n = $v;
            }
        }

        return $deliveryFields;
    }
}