<?php

namespace Okay\Modules\OkayCMS\DeliveryFields\Helpers;

use Okay\Core\EntityFactory;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsEntity;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsValuesEntity;

/**
 * @phpstan-type DeliveryFieldRow object{visible?: int|string|bool|null, value?: mixed, deliveries?: list<int|string>}&\stdClass
 * @phpstan-type FieldDeliveryRow object{field_id: int|string, delivery_id: int|string}&\stdClass
 * @phpstan-type FieldValueRow object{field_id: int|string, value: mixed}&\stdClass
 */
class DeliveryFieldsHelper
{
    private DeliveryFieldsEntity $deliveryFieldsEntity;
    private EntityFactory $entityFactory;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->deliveryFieldsEntity = $entityFactory->get(DeliveryFieldsEntity::class);
        $this->entityFactory = $entityFactory;
    }

    /**
     * @param array<string, mixed> $filter
     * @return array<int|string, DeliveryFieldRow>
     * @throws \Exception
     *
     * Пошук полів з додаванням інформації, яке поле яким способам доставки належить.
     */
    public function findDeliveryFields(array $filter = []): array
    {
        $deliveryFields = $this->deliveryFieldsEntity->mappedBy('id')->find($filter);
        if ($deliveryFields) {
            foreach ($this->deliveryFieldsEntity->getFieldsDeliveries(array_keys($deliveryFields)) as $fieldDelivery) {
                /** @var FieldDeliveryRow $fieldDelivery */
                $deliveryFields[$fieldDelivery->field_id]->deliveries[] = $fieldDelivery->delivery_id;
            }
        }

        return $deliveryFields;
    }

    /**
     * @param int $orderId
     * @param int $deliveryId
     * @return array<int|string, DeliveryFieldRow>
     * @throws \Exception
     *
     * Пошук полів для способів доставки зі значеннями для конкретного замовлення (фронт).
     */
    public function getOrderDeliveryFields(int $orderId, int $deliveryId): array
    {
        $deliveryFieldsValuesEntity = $this->entityFactory->get(DeliveryFieldsValuesEntity::class);

        $deliveryFields = $this->deliveryFieldsEntity->mappedBy('id')->find([
            'delivery_id' => $deliveryId,
        ]);
        if (!empty($deliveryFields)) {
            $fieldValues = $deliveryFieldsValuesEntity->find([
                'field_id' => array_keys($deliveryFields),
                'order_id' => $orderId,
            ]);
            foreach ($fieldValues as $fieldValue) {
                /** @var FieldValueRow $fieldValue */
                $deliveryFields[$fieldValue->field_id]->value = $fieldValue->value;
            }
            foreach ($deliveryFields as $key => $deliveryField) {
                if (!$deliveryField->visible && empty($deliveryField->value)) {
                    unset($deliveryFields[$key]);
                }
            }
        }
        return $deliveryFields;
    }
}
