<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Core\EntityFactory;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;

class NPDeliveryDataHelper
{
    private NPCostDeliveryDataEntity $deliveryDataEntity;
    private NPCitiesEntity $citiesEntity;
    private NPWarehousesEntity $warehousesEntity;

    public function __construct(EntityFactory $entityFactory)
    {
        $this->deliveryDataEntity = $entityFactory->get(NPCostDeliveryDataEntity::class);
        $this->citiesEntity = $entityFactory->get(NPCitiesEntity::class);
        $this->warehousesEntity = $entityFactory->get(NPWarehousesEntity::class);
    }

    public function getFullDeliveryData(int $orderId): ?object
    {
        if (empty($orderId)) {
            return null;
        }
        if ($npDeliveryData = $this->deliveryDataEntity->getByOrderId($orderId)) {
            $npDeliveryData->city = $this->citiesEntity->findOne([
                'ref' => $npDeliveryData->city_id,
            ]);
            $npDeliveryData->warehouse = $this->warehousesEntity->findOne([
                'ref' => $npDeliveryData->warehouse_id,
            ]);
            return $npDeliveryData;
        }
        return null;
    }
}