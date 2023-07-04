<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Helpers;

use Okay\Core\EntityFactory;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPDeliveryTypesEntity;

class NPBackendHelper
{
    private NPDeliveryTypesEntity $deliveryTypesEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->deliveryTypesEntity = $entityFactory->get(NPDeliveryTypesEntity::class);
    }

    public function updateDeliveryTypes(array $deliveryTypes)
    {
        $typesIds = [];

        foreach ($deliveryTypes as $deliveryType) {
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