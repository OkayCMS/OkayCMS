<?php

declare(strict_types=1);

namespace Modules\OkayCMS\NovaposhtaCost;

use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Core\EntityFactory;
use Okay\Core\Money;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCalcHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCheckoutCostCalculator;
use Okay\Modules\OkayCMS\NovaposhtaCost\VO\NPCalcVO;
use PHPUnit\Framework\TestCase;

final class NPCheckoutCostCalculatorTest extends TestCase
{
    public function testDoorDeliveryPriceUsesDoorSettlementRefEvenWhenWarehouseRefIsPosted(): void
    {
        $calcHelper = $this->createMock(NPCalcHelper::class);
        $calcHelper->expects(self::once())
            ->method('calcPrice')
            ->with('door-ref', false, self::isInstanceOf(NPCalcVO::class), 'WarehouseDoors')
            ->willReturn(120);

        $currencies = $this->createMock(CurrenciesEntity::class);
        $currencies->expects(self::once())
            ->method('findOne')
            ->with(['code' => 'UAH'])
            ->willReturn((object) ['id' => 1]);

        $entityFactory = $this->createMock(EntityFactory::class);
        $entityFactory->expects(self::once())
            ->method('get')
            ->with(CurrenciesEntity::class)
            ->willReturn($currencies);

        $money = $this->createMock(Money::class);
        $money->expects(self::once())
            ->method('convert')
            ->with(120, 1, false, true)
            ->willReturn('120');

        $calculator = new NPCheckoutCostCalculator(
            $entityFactory,
            $this->createStub(BackendOrdersHelper::class),
            $this->settings(),
            $money,
            $calcHelper
        );

        $price = $calculator->calculateSelectedDeliveryPrice(
            (object) ['settings' => ['service_type' => 'WarehouseDoors']],
            (object) ['total_price' => 500],
            [
                'warehouse_city_ref' => 'stale-warehouse-ref',
                'door_settlement_ref' => 'door-ref',
                'redelivery' => '',
            ]
        );

        self::assertSame(120.0, $price);
    }

    private function settings(): Settings
    {
        $settings = $this->createStub(Settings::class);
        $settings->method('get')->willReturn(1);

        return $settings;
    }
}
