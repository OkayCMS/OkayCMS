<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Modules\Module;
use Okay\Core\Request;
use Okay\Entities\DeliveriesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;

class NPCheckoutRequestReader
{
    private const DOOR_SERVICE_TYPES = [
        'DoorsDoors' => true,
        'WarehouseDoors' => true,
    ];

    private const FIELD_LENGTHS = [
        'door_delivery' => 8,
        'door_city' => 255,
        'door_settlement_ref' => 64,
        'city_name' => 255,
        'area_name' => 255,
        'region_name' => 255,
        'street' => 255,
        'street_name' => 255,
        'house' => 64,
        'apartment' => 64,
        'warehouse_city' => 255,
        'warehouse_city_ref' => 64,
        'warehouse_city_name' => 255,
        'delivery_warehouse_id' => 64,
        'delivery_price' => 32,
        'delivery_term' => 64,
        'redelivery' => 8,
    ];

    private Request $request;
    private EntityFactory $entityFactory;
    private Module $module;

    public function __construct(Request $request, EntityFactory $entityFactory, Module $module)
    {
        $this->request = $request;
        $this->entityFactory = $entityFactory;
        $this->module = $module;
    }

    /**
     * @return (object{id: int|string, module_id: int|string, settings: array<string, mixed>}&\stdClass)|null
     */
    public function selectedNovaPoshtaDelivery(): ?object
    {
        $deliveryId = $this->request->post('delivery_id', 'integer');
        if ($deliveryId <= 0) {
            return null;
        }

        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
        $delivery = $deliveriesEntity->get($deliveryId);
        if (empty($delivery) || empty($delivery->module_id)) {
            return null;
        }
        /** @var object{id: int|string, module_id: int|string, settings?: array<string, mixed>}&\stdClass $delivery */

        $moduleId = $this->module->getModuleIdByNamespace(self::class);
        if ((int)$delivery->module_id !== (int)$moduleId) {
            return null;
        }

        $settings = $deliveriesEntity->getSettings($deliveryId);
        $delivery->settings = is_array($settings) ? $settings : [];

        /** @var object{id: int|string, module_id: int|string, settings: array<string, mixed>}&\stdClass $delivery */
        return $delivery;
    }

    /**
     * @param object{settings: array<string, mixed>} $delivery
     */
    public function isDoorDelivery(object $delivery): bool
    {
        $serviceType = (string)($delivery->settings['service_type'] ?? '');

        return isset(self::DOOR_SERVICE_TYPES[$serviceType]);
    }

    /**
     * @param object{id: int|string} $delivery
     * @return array<string, string>
     */
    public function selectedDataForDelivery(object $delivery): array
    {
        $deliveryId = (int)$delivery->id;
        $data = $this->legacyFlatData();

        $scoped = $this->request->post('novaposhta');
        if (is_array($scoped) && isset($scoped[$deliveryId]) && is_array($scoped[$deliveryId])) {
            $data = array_replace($data, $scoped[$deliveryId]);
        }

        return $this->normalizeData($data);
    }

    /**
     * @param object{settings: array<string, mixed>} $delivery
     * @param array<string, mixed> $data
     */
    public function hasValidLocationRefs(object $delivery, array $data): bool
    {
        if ($this->isDoorDelivery($delivery)) {
            return (string)($data['door_settlement_ref'] ?? '') !== '';
        }

        return $this->cityRefExists((string)($data['warehouse_city_ref'] ?? ''))
            && $this->warehouseRefExists(
                (string)($data['warehouse_city_ref'] ?? ''),
                (string)($data['delivery_warehouse_id'] ?? '')
            );
    }

    /**
     * @return array<string, mixed>
     */
    private function legacyFlatData(): array
    {
        return [
            'door_delivery' => $this->request->post('novaposhta_door_delivery'),
            'door_city' => $this->request->post('novaposhta_door_city'),
            'door_settlement_ref' => $this->request->post('novaposhta_door_settlement_ref'),
            'city_name' => $this->request->post('novaposhta_city_name'),
            'area_name' => $this->request->post('novaposhta_area_name'),
            'region_name' => $this->request->post('novaposhta_region_name'),
            'street' => $this->request->post('novaposhta_street'),
            'street_name' => $this->request->post('novaposhta_street_name'),
            'house' => $this->request->post('novaposhta_house'),
            'apartment' => $this->request->post('novaposhta_apartment'),
            'warehouse_city' => $this->request->post('novaposhta_warehouse_city'),
            'warehouse_city_ref' => $this->request->post('novaposhta_warehouse_city_ref'),
            'warehouse_city_name' => $this->request->post('novaposhta_warehouse_city_name'),
            'delivery_warehouse_id' => $this->request->post('novaposhta_delivery_warehouse_id'),
            'delivery_price' => $this->request->post('novaposhta_delivery_price'),
            'delivery_term' => $this->request->post('novaposhta_delivery_term'),
            'redelivery' => $this->request->post('novaposhta_redelivery'),
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return array<string, string>
     */
    private function normalizeData(array $data): array
    {
        $normalized = [];
        foreach (self::FIELD_LENGTHS as $field => $maxLength) {
            $normalized[$field] = $this->normalizeString($data[$field] ?? '', $maxLength);
        }

        return $normalized;
    }

    private function normalizeString(mixed $value, int $maxLength): string
    {
        if (!is_scalar($value)) {
            return '';
        }

        return mb_substr(trim(strip_tags((string)$value)), 0, $maxLength);
    }

    private function cityRefExists(string $cityRef): bool
    {
        if ($cityRef === '') {
            return false;
        }

        return (bool)$this->entityFactory->get(NPCitiesEntity::class)->findOne(['ref' => $cityRef]);
    }

    private function warehouseRefExists(string $cityRef, string $warehouseRef): bool
    {
        if ($cityRef === '' || $warehouseRef === '') {
            return false;
        }

        return (bool)$this->entityFactory->get(NPWarehousesEntity::class)->findOne([
            'city_ref' => $cityRef,
            'ref' => $warehouseRef,
        ]);
    }
}
