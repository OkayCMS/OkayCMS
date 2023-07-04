<?php 


namespace Okay\Modules\OkayCMS\DeliveryFields\Extenders;


use Okay\Core\Design;
use Okay\Core\Request;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsEntity;
use Okay\Modules\OkayCMS\DeliveryFields\Helpers\DeliveryFieldsHelper;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsValuesEntity;

class BackendExtender implements ExtensionInterface
{
    private DeliveryFieldsEntity $deliveryFieldsEntity;
    private DeliveryFieldsValuesEntity $deliveryFieldsValuesEntity;
    private Request $request;
    private Design $design;
    private DeliveryFieldsHelper $deliveryFieldsHelper;

    public function __construct(
        EntityFactory $entityFactory,
        Request $request,
        Design $design,
        DeliveryFieldsHelper $deliveryFieldsHelper
    ) {
        $this->deliveryFieldsEntity = $entityFactory->get(DeliveryFieldsEntity::class);
        $this->deliveryFieldsValuesEntity = $entityFactory->get(DeliveryFieldsValuesEntity::class);
        $this->request = $request;
        $this->design = $design;
        $this->deliveryFieldsHelper = $deliveryFieldsHelper;
    }

    public function extendUpdateOrder($order)
    {
        if (empty($order->delivery_id)) {
            return;
        }

        $deliveryFields = $this->request->post('delivery_fields', null, []);
        $deliveryFieldsValuesIds = $this->request->post('delivery_fields_values_ids', null, []);

        foreach ($deliveryFields as $fieldId => $value) {
            if ($deliveryFieldsValuesIds[$fieldId] ?? null) {
                if (empty($value)) {
                    $this->deliveryFieldsValuesEntity->delete($deliveryFieldsValuesIds[$fieldId]);
                } else {
                    $this->deliveryFieldsValuesEntity->update($deliveryFieldsValuesIds[$fieldId], [
                        'value' => $value,
                    ]);
                }
            } elseif (!empty($value)) {
                $this->deliveryFieldsValuesEntity->add([
                    'order_id' => $order->id,
                    'field_id' => $fieldId,
                    'value'    => $value,
                ]);
            }
        }
    }

    public function extendFindOrderDelivery($delivery, $order): void
    {
        if (empty($order) || empty($order->delivery_id)) {
            return;
        }
        $fields = $this->deliveryFieldsHelper->findDeliveryFields();
        $deliveryFieldsValues = $this->deliveryFieldsValuesEntity->find([
            'order_id' => (int)$order->id,
        ]);

        foreach ($deliveryFieldsValues as $fieldValue) {
            if (!isset($fields[$fieldValue->field_id])) {
                continue;
            }
            $fields[$fieldValue->field_id]->value = $fieldValue->value;
            $fields[$fieldValue->field_id]->value_id = $fieldValue->id;
        }
        $this->design->assign('deliveryFields', $fields);
        $this->design->assign('deliveryFieldsValues', $deliveryFieldsValues);
    }

    public function extendDeleteDelivery($result, array $deliveriesIds)
    {
        foreach ($deliveriesIds as $deliveriesId) {
            $this->deliveryFieldsEntity->deleteFieldDelivery((int)$deliveriesId);
        }
    }
}