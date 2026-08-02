<?php

namespace Okay\Modules\OkayCMS\DeliveryFields\Extenders;

use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Modules\OkayCMS\DeliveryFields\Helpers\DeliveryFieldsHelper;

/**
 * @phpstan-type DeliveryRow object{delivery_fields?: list<object>}&\stdClass
 * @phpstan-type DeliveryFieldRow object{deliveries: array<int|string, int|string>}&\stdClass
 */
class DeliveryFieldsExtender implements ExtensionInterface
{
    private DeliveryFieldsHelper $deliveryFieldsHelper;

    public function __construct(DeliveryFieldsHelper $deliveryFieldsHelper)
    {
        $this->deliveryFieldsHelper = $deliveryFieldsHelper;
    }

    /**
     * @param $deliveries
     * @return mixed
     * @throws \Exception
     *
     * Передаємо в кошик активні поля для можливих способів доставки.
     */
    public function extendGetCartDeliveriesList($deliveries)
    {
        /** @var array<int|string, DeliveryRow> $deliveries */
        $deliveryFields = $this->deliveryFieldsHelper->findDeliveryFields([
            'delivery_id' => array_keys($deliveries),
            'visible' => true,
        ]);

        /** @var array<int|string, DeliveryFieldRow> $deliveryFields */
        foreach ($deliveryFields as $deliveryField) {
            foreach ($deliveryField->deliveries as $deliveryId) {
                $deliveries[$deliveryId]->delivery_fields[] = $deliveryField;
            }
        }

        return $deliveries;
    }
}
