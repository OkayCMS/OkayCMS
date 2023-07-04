<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Requests;

use Okay\Core\Request;

class NPBackendRequest
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postDeliveryTypes(): array
    {
        $postFields = $this->request->post('delivery_types');

        $deliveryTypes = [];
        foreach ($postFields as $n=>$va) {
            foreach ($va as $i=>$v) {
                if (empty($deliveryTypes[$i])) {
                    $deliveryTypes[$i] = new \stdClass();
                    $deliveryTypes[$i]->warehouses_type_refs = [];
                }
                $deliveryTypes[$i]->$n = $v;
            }
        }

        return $deliveryTypes;
    }
}