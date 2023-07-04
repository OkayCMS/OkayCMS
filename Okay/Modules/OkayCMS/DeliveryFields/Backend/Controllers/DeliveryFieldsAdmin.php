<?php


namespace Okay\Modules\OkayCMS\DeliveryFields\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Entities\DeliveriesEntity;
use Okay\Modules\OkayCMS\DeliveryFields\Backend\Helpers\BackendDeliveryFieldsHelper;
use Okay\Modules\OkayCMS\DeliveryFields\Backend\Requests\BackendDeliveryFieldsRequest;
use Okay\Modules\OkayCMS\DeliveryFields\Helpers\DeliveryFieldsHelper;

class DeliveryFieldsAdmin extends IndexAdmin
{
    public function fetch(
        DeliveriesEntity $deliveriesEntity,
        BackendDeliveryFieldsRequest $backendRequest,
        BackendDeliveryFieldsHelper $backendHelper,
        DeliveryFieldsHelper $deliveryFieldsHelper
    ) {
        $this->design->assign('deliveries', $deliveriesEntity->find());

        if ($this->request->isPost()) {
            $deliveryFields = $backendRequest->postDeliveryFields();
            $backendHelper->updateDeliveryFields($deliveryFields);
            $this->design->assign('message_success', 'saved');
        }

        $deliveryFields = $deliveryFieldsHelper->findDeliveryFields();
        $this->design->assign('deliveryFields', $deliveryFields);
        $lastDeliveryFieldIndex = 0;
        if (!empty($deliveryFields)) {
            $lastDeliveryFieldIndex = max(array_keys($deliveryFields)) + 1;
        }
        $this->design->assign('lastDeliveryFieldIndex', $lastDeliveryFieldIndex);

        $this->response->setContent($this->design->fetch('delivery_fields.tpl'));
    }
}