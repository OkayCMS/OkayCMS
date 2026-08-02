<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Helpers;

use Okay\Core\EntityFactory;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPDeliveryTypesEntity;

/**
 * @phpstan-type DeliveryTypeRow object{id?: int|string|null, position?: int|string|null, warehouses_type_refs?: list<string>, name?: string}&\stdClass
 */
class NPBackendHelper
{
    private NPDeliveryTypesEntity $deliveryTypesEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->deliveryTypesEntity = $entityFactory->get(NPDeliveryTypesEntity::class);
    }

    /**
     * @param array<int|string, object> $deliveryTypes
     */
    public function updateDeliveryTypes(array $deliveryTypes)
    {
        $typesIds = [];

        foreach ($deliveryTypes as $deliveryType) {
            /** @var DeliveryTypeRow $deliveryType */
            if (!empty($deliveryType->id)) {
                $this->deliveryTypesEntity->update($deliveryType->id, $deliveryType);
            } else {
                $deliveryType->id = $this->deliveryTypesEntity->add($deliveryType);
            }
            if (!empty($deliveryType->id)) {
                $typesIds[] = $deliveryType->id;
            }
        }

        // Видаляємо непередані типи доставки
        $currentDeliveryTypes = $this->deliveryTypesEntity->find();
        /** @var list<DeliveryTypeRow> $currentDeliveryTypes */
        foreach ($currentDeliveryTypes as $currentDeliveryType) {
            if (!in_array($currentDeliveryType->id, $typesIds)) {
                $this->deliveryTypesEntity->delete($currentDeliveryType->id);
            }
        }

        // Сортуємо типи доставки
        asort($typesIds);
        $i = 0;
        foreach ($typesIds as $typesId) {
            $this->deliveryTypesEntity->update($typesIds[$i], ['position' => $typesId]);
            $i++;
        }
    }
}
