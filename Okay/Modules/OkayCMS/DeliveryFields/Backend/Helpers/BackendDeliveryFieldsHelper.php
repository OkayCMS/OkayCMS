<?php

namespace Okay\Modules\OkayCMS\DeliveryFields\Backend\Helpers;

use Okay\Core\EntityFactory;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsEntity;

class BackendDeliveryFieldsHelper
{
    private DeliveryFieldsEntity $deliveryFieldsEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->deliveryFieldsEntity = $entityFactory->get(DeliveryFieldsEntity::class);
    }

    /**
     * @param array $deliveryFields
     * @return void
     *
     * Оновлюємо список полів для способів доставки.
     */
    public function updateDeliveryFields(array $deliveryFields)
    {
        $fieldIds = [];
        foreach ($deliveryFields as $deliveryField) {
            $fieldDeliveries = $deliveryField->deliveries;
            unset($deliveryField->deliveries);
            if (!empty($deliveryField->id)) {
                $this->deliveryFieldsEntity->update($deliveryField->id, $deliveryField);
            } else {
                $deliveryField->id = $this->deliveryFieldsEntity->add($deliveryField);
            }
            if (!empty($deliveryField->id)) {
                $fieldId = (int)$deliveryField->id;
                $fieldIds[] = $fieldId;

                // Оновлюємо інформацію про варіанти доставки для поля
                $this->deliveryFieldsEntity->deleteFieldDeliveries($fieldId);
                foreach ($fieldDeliveries as $fieldDeliveryId) {
                    $this->deliveryFieldsEntity->addFieldDelivery($fieldId, (int)$fieldDeliveryId);
                }
            }
        }

        // Видаляємо непередані поля
        $currentDeliveryFields = $this->deliveryFieldsEntity->find();
        foreach ($currentDeliveryFields as $currentDeliveryField) {
            if (!in_array($currentDeliveryField->id, $fieldIds)) {
                $this->deliveryFieldsEntity->delete($currentDeliveryField->id);
            }
        }

        // Сортуємо поля
        asort($fieldIds);
        $i = 0;
        foreach ($fieldIds as $fieldId) {
            $this->deliveryFieldsEntity->update($fieldIds[$i], ['position' => $fieldId]);
            $i++;
        }
    }
}