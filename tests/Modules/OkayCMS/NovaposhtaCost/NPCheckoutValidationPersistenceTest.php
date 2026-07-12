<?php

declare(strict_types=1);

namespace Modules\OkayCMS\NovaposhtaCost;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\FrontExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCheckoutCostCalculator;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCheckoutRequestReader;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPDeliveryDataHelper;
use PHPUnit\Framework\TestCase;

final class NPCheckoutValidationPersistenceTest extends TestCase
{
    public function testSelectedWarehouseDeliveryWithoutCityIsBlocked(): void
    {
        $delivery = $this->delivery(['service_type' => 'WarehouseWarehouse']);
        $reader = $this->createStub(NPCheckoutRequestReader::class);
        $reader->method('selectedNovaPoshtaDelivery')->willReturn($delivery);
        $reader->method('selectedDataForDelivery')->willReturn([
            'warehouse_city' => '',
            'warehouse_city_ref' => '',
            'delivery_warehouse_id' => '',
        ]);

        $extender = $this->extender($reader);

        self::assertSame('city error', $extender->getCartValidateError(null));
    }

    public function testSelectedWarehouseDeliveryPersistsCityAndWarehouseData(): void
    {
        $delivery = $this->delivery(['service_type' => 'WarehouseWarehouse']);
        $reader = $this->createStub(NPCheckoutRequestReader::class);
        $reader->method('selectedNovaPoshtaDelivery')->willReturn($delivery);
        $reader->method('selectedDataForDelivery')->willReturn([
            'warehouse_city' => 'Kyiv',
            'warehouse_city_ref' => 'city-ref',
            'warehouse_city_name' => 'Kyiv city',
            'delivery_warehouse_id' => 'warehouse-ref',
            'delivery_term' => '1',
            'redelivery' => '0',
        ]);
        $reader->method('hasValidLocationRefs')->willReturn(true);
        $reader->method('isDoorDelivery')->willReturn(false);

        $deliveryData = $this->createMock(NPCostDeliveryDataEntity::class);
        $deliveryData->expects(self::once())->method('add')->with(self::callback(
            static fn (object $row): bool => $row->order_id === 42
                && $row->city_id === 'city-ref'
                && $row->city_name === 'Kyiv city'
                && $row->warehouse_id === 'warehouse-ref'
                && !isset($row->street)
                && !isset($row->house)
        ))->willReturn(777);

        $result = $this->extender($reader, null, $deliveryData)->setCartDeliveryDataProcedure(null, (object) ['id' => 42]);

        self::assertSame(777, $result[0]);
    }

    public function testSelectedDoorDeliveryPersistsAddressData(): void
    {
        $delivery = $this->delivery(['service_type' => 'WarehouseDoors']);
        $reader = $this->createStub(NPCheckoutRequestReader::class);
        $reader->method('selectedNovaPoshtaDelivery')->willReturn($delivery);
        $reader->method('selectedDataForDelivery')->willReturn([
            'door_city' => 'Kyiv',
            'door_settlement_ref' => 'settlement-ref',
            'city_name' => 'Kyiv city',
            'area_name' => 'Kyiv area',
            'region_name' => 'Kyiv region',
            'street' => 'Fallback street',
            'street_name' => 'Selected street',
            'house' => '10',
            'apartment' => '5',
            'delivery_term' => '1',
            'redelivery' => '1',
        ]);
        $reader->method('hasValidLocationRefs')->willReturn(true);
        $reader->method('isDoorDelivery')->willReturn(true);

        $deliveryData = $this->createMock(NPCostDeliveryDataEntity::class);
        $deliveryData->expects(self::once())->method('add')->with(self::callback(
            static fn (object $row): bool => $row->city_id === 'settlement-ref'
                && $row->city_name === 'Kyiv city'
                && $row->street === 'Selected street'
                && $row->house === '10'
                && $row->apartment === '5'
                && !isset($row->warehouse_id)
        ))->willReturn(778);

        $result = $this->extender($reader, null, $deliveryData)->setCartDeliveryDataProcedure(null, (object) ['id' => 43]);

        self::assertSame(778, $result[0]);
    }

    public function testNonNovaPoshtaDeliveryDoesNotValidateOrPersistNovaPoshtaData(): void
    {
        $reader = $this->createMock(NPCheckoutRequestReader::class);
        $reader->method('selectedNovaPoshtaDelivery')->willReturn(null);
        $reader->expects(self::never())->method('selectedDataForDelivery');

        $entityFactory = $this->createMock(EntityFactory::class);
        $entityFactory->expects(self::never())->method('get');

        $extender = $this->extender($reader, null, null, $entityFactory);

        self::assertNull($extender->getCartValidateError(null));
        self::assertNull($extender->setCartDeliveryDataProcedure(null, (object) ['id' => 44]));
    }

    public function testPostedDeliveryPriceIsNotTrustedAsOrderDeliveryPrice(): void
    {
        $delivery = $this->delivery(['service_type' => 'WarehouseWarehouse']);
        $reader = $this->createStub(NPCheckoutRequestReader::class);
        $reader->method('selectedNovaPoshtaDelivery')->willReturn($delivery);
        $reader->method('selectedDataForDelivery')->willReturn([
            'warehouse_city_ref' => 'city-ref',
            'delivery_price' => '999.99',
            'redelivery' => '0',
        ]);

        $calculator = $this->createMock(NPCheckoutCostCalculator::class);
        $calculator->expects(self::once())
            ->method('calculateSelectedDeliveryPrice')
            ->with($delivery, self::callback('is_object'), self::callback('is_array'))
            ->willReturn(null);

        $result = $this->extender($reader, $calculator)->setCartDeliveryPrice(
            [],
            (object) ['id' => 3, 'paid' => true, 'free_from' => 1000],
            (object) ['total_price' => 100]
        );

        self::assertArrayNotHasKey('delivery_price', $result);
    }

    /**
     * @param array<string, string> $settings
     */
    private function delivery(array $settings): object
    {
        return (object) [
            'id' => 3,
            'paid' => true,
            'free_from' => 1000,
            'settings' => $settings,
        ];
    }

    private function extender(
        NPCheckoutRequestReader $reader,
        ?NPCheckoutCostCalculator $calculator = null,
        ?NPCostDeliveryDataEntity $deliveryData = null,
        ?EntityFactory $entityFactory = null
    ): FrontExtender {
        $translations = $this->createStub(FrontTranslations::class);
        $translations->method('getTranslation')->willReturnMap([
            ['np_cart_error_city', 'city error'],
            ['np_cart_error_street', 'street error'],
            ['np_cart_error_house', 'house error'],
            ['np_cart_error_warehouse', 'warehouse error'],
        ]);

        if ($entityFactory === null && $deliveryData !== null) {
            $entityFactory = $this->createMock(EntityFactory::class);
            $entityFactory->expects(self::once())->method('get')->with(NPCostDeliveryDataEntity::class)->willReturn($deliveryData);
        } else {
            $entityFactory ??= $this->createStub(EntityFactory::class);
        }

        return new FrontExtender(
            $entityFactory,
            $translations,
            $this->createStub(Design::class),
            $this->createStub(NPDeliveryDataHelper::class),
            $reader,
            $calculator ?? $this->createStub(NPCheckoutCostCalculator::class)
        );
    }
}
