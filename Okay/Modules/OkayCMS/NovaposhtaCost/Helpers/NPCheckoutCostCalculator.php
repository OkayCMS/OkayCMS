<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Core\EntityFactory;
use Okay\Core\Money;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\VO\NPCalcVO;

/**
 * @phpstan-type CurrencyRow object{id: int|string}&\stdClass
 * @phpstan-type PurchaseRow object{amount: int|string, variant: object{weight?: int|float|string|null, volume?: int|float|string|null}}&\stdClass
 */
class NPCheckoutCostCalculator
{
    private const DOOR_SERVICE_TYPES = [
        'DoorsDoors' => true,
        'WarehouseDoors' => true,
    ];

    private EntityFactory $entityFactory;
    private BackendOrdersHelper $backendOrdersHelper;
    private Settings $settings;
    private Money $money;
    private NPCalcHelper $calcHelper;

    public function __construct(
        EntityFactory $entityFactory,
        BackendOrdersHelper $backendOrdersHelper,
        Settings $settings,
        Money $money,
        NPCalcHelper $calcHelper
    ) {
        $this->entityFactory = $entityFactory;
        $this->backendOrdersHelper = $backendOrdersHelper;
        $this->settings = $settings;
        $this->money = $money;
        $this->calcHelper = $calcHelper;
    }

    /**
     * @param array<string, string> $selectedData
     */
    public function calculateSelectedDeliveryPrice(object $delivery, object $order, array $selectedData): ?float
    {
        $serviceType = (string)($delivery->settings['service_type'] ?? '');
        $cityRef = isset(self::DOOR_SERVICE_TYPES[$serviceType])
            ? $selectedData['door_settlement_ref']
            : $selectedData['warehouse_city_ref'];
        if ($cityRef === '') {
            return null;
        }

        if ($serviceType === '') {
            return null;
        }

        try {
            $deliveryPrice = $this->calcHelper->calcPrice(
                $cityRef,
                (bool)$selectedData['redelivery'],
                $this->buildCalcVO($order),
                $serviceType
            );
            if ($deliveryPrice === null) {
                return null;
            }

            /** @var CurrenciesEntity $currenciesEntity */
            $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
            /** @var CurrencyRow|false $npCurrency */
            $npCurrency = $currenciesEntity->findOne(['code' => 'UAH']);
            if (!$npCurrency) {
                return null;
            }

            return (float)$this->money->convert($deliveryPrice, $npCurrency->id, false, true);
        } catch (\Throwable) {
            return null;
        }
    }

    private function buildCalcVO(object $order): NPCalcVO
    {
        $totalPrice = (float)($order->total_price ?? 0);
        if (!empty($order->separate_delivery) && (int)$order->separate_delivery === 0) {
            $totalPrice -= (float)($order->delivery_price ?? 0);
        }

        $calcVO = new NPCalcVO(
            $totalPrice,
            (float)$this->settings->get('newpost_weight'),
            (float)$this->settings->get('newpost_volume')
        );

        foreach ($this->findPurchases($order) as $purchase) {
            /** @var PurchaseRow $purchase */
            $variant = $purchase->variant ?? null;
            if (!is_object($variant)) {
                continue;
            }

            $calcVO->addPurchaseWeight((float)($variant->weight ?? 0), (int)$purchase->amount);
            $calcVO->addPurchaseVolume((float)($variant->volume ?? 0), (int)$purchase->amount);
        }

        return $calcVO;
    }

    /**
     * @return iterable<int, object>
     */
    private function findPurchases(object $order): iterable
    {
        if (!empty($order->purchases) && is_iterable($order->purchases)) {
            return $order->purchases;
        }

        if (empty($order->id)) {
            return [];
        }

        return $this->backendOrdersHelper->findOrderPurchases($order);
    }
}
