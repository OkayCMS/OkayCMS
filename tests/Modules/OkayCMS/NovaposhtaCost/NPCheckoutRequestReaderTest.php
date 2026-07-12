<?php

declare(strict_types=1);

namespace Modules\OkayCMS\NovaposhtaCost;

use Okay\Core\EntityFactory;
use Okay\Core\Modules\Module;
use Okay\Core\Request;
use Okay\Entities\DeliveriesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCheckoutRequestReader;
use PHPUnit\Framework\TestCase;

final class NPCheckoutRequestReaderTest extends TestCase
{
    protected function tearDown(): void
    {
        $_POST = [];
    }

    public function testSelectedNovaPoshtaDeliveryIsDetectedFromDeliveryIdWithoutHiddenFlag(): void
    {
        $_POST = ['delivery_id' => '3'];

        $reader = $this->readerForDelivery((object) [
            'id' => 3,
            'module_id' => 12,
            'settings' => 'a:1:{s:12:"service_type";s:18:"WarehouseWarehouse";}',
        ]);

        $selectedDelivery = $reader->selectedNovaPoshtaDelivery();

        self::assertNotNull($selectedDelivery);
        self::assertFalse($reader->isDoorDelivery($selectedDelivery));
    }

    public function testNamespacedPostDataForSelectedDeliveryWinsOverOtherNovaPoshtaBlocks(): void
    {
        $_POST = [
            'delivery_id' => '3',
            'novaposhta' => [
                3 => [
                    'warehouse_city' => 'Kyiv',
                    'warehouse_city_ref' => 'selected-city-ref',
                    'delivery_warehouse_id' => 'selected-warehouse-ref',
                    'delivery_price' => '120.50',
                    'delivery_term' => '1 day',
                    'redelivery' => '1',
                ],
                4 => [
                    'warehouse_city' => '',
                    'warehouse_city_ref' => '',
                    'delivery_warehouse_id' => '',
                    'redelivery' => '0',
                ],
            ],
        ];

        $reader = $this->readerForDelivery((object) [
            'id' => 3,
            'module_id' => 12,
            'settings' => ['service_type' => 'WarehouseWarehouse'],
        ]);

        $data = $reader->selectedDataForDelivery($reader->selectedNovaPoshtaDelivery());

        self::assertSame('Kyiv', $data['warehouse_city']);
        self::assertSame('selected-city-ref', $data['warehouse_city_ref']);
        self::assertSame('selected-warehouse-ref', $data['delivery_warehouse_id']);
        self::assertSame('120.50', $data['delivery_price']);
        self::assertSame('1', $data['redelivery']);
    }

    public function testFlatPostDataRemainsLegacyFallbackForSingleNovaPoshtaDelivery(): void
    {
        $_POST = [
            'delivery_id' => '3',
            'novaposhta_warehouse_city' => 'Kyiv',
            'novaposhta_warehouse_city_ref' => 'legacy-city-ref',
            'novaposhta_delivery_warehouse_id' => 'legacy-warehouse-ref',
            'novaposhta_redelivery' => '1',
        ];

        $reader = $this->readerForDelivery((object) [
            'id' => 3,
            'module_id' => 12,
            'settings' => ['service_type' => 'WarehouseWarehouse'],
        ]);

        $data = $reader->selectedDataForDelivery($reader->selectedNovaPoshtaDelivery());

        self::assertSame('legacy-city-ref', $data['warehouse_city_ref']);
        self::assertSame('legacy-warehouse-ref', $data['delivery_warehouse_id']);
        self::assertSame('1', $data['redelivery']);
    }

    public function testUnknownNestedAndOverlongPostValuesAreNormalizedBeforeUse(): void
    {
        $_POST = [
            'delivery_id' => '3',
            'novaposhta' => [
                3 => [
                    'warehouse_city' => str_repeat('K', 400),
                    'warehouse_city_ref' => ['nested-ref'],
                    'delivery_warehouse_id' => 'warehouse-ref',
                    'unknown_field' => 'must not survive',
                ],
            ],
        ];

        $reader = $this->readerForDelivery((object) [
            'id' => 3,
            'module_id' => 12,
            'settings' => ['service_type' => 'WarehouseWarehouse'],
        ]);

        $data = $reader->selectedDataForDelivery($reader->selectedNovaPoshtaDelivery());

        self::assertLessThanOrEqual(255, strlen($data['warehouse_city']));
        self::assertSame('', $data['warehouse_city_ref']);
        self::assertSame('warehouse-ref', $data['delivery_warehouse_id']);
        self::assertArrayNotHasKey('unknown_field', $data);
    }

    public function testNonNovaPoshtaDeliveryDoesNotTriggerNovaPoshtaValidation(): void
    {
        $_POST = ['delivery_id' => '7'];

        $reader = $this->readerForDelivery((object) [
            'id' => 7,
            'module_id' => 99,
            'settings' => [],
        ]);

        self::assertNull($reader->selectedNovaPoshtaDelivery());
    }

    public function testDoorSettlementRefIsAcceptedWithoutWarehouseCityCacheLookup(): void
    {
        $_POST = ['delivery_id' => '3'];

        $reader = $this->readerForDelivery((object) [
            'id' => 3,
            'module_id' => 12,
            'settings' => ['service_type' => 'WarehouseDoors'],
        ]);
        $delivery = $reader->selectedNovaPoshtaDelivery();

        self::assertNotNull($delivery);
        self::assertTrue($reader->hasValidLocationRefs($delivery, [
            'door_settlement_ref' => 'settlement-search-ref',
        ]));
    }

    private function readerForDelivery(object $delivery): NPCheckoutRequestReader
    {
        $deliveries = $this->createStub(DeliveriesEntity::class);
        $deliveries->method('get')->willReturn($delivery);
        $settings = $delivery->settings ?? [];
        if (is_string($settings)) {
            $settings = unserialize($settings);
        }
        $deliveries->method('getSettings')->willReturn(is_array($settings) ? $settings : []);

        $entityFactory = $this->createStub(EntityFactory::class);
        $entityFactory->method('get')->willReturn($deliveries);

        $module = $this->createStub(Module::class);
        $module->method('getModuleIdByNamespace')->willReturn(12);

        return new NPCheckoutRequestReader(new Request(), $entityFactory, $module);
    }
}
