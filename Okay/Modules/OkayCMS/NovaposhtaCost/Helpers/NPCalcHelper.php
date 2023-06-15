<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Money;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\VO\NPCalcVO;
use Psr\Log\LoggerInterface;

class NPCalcHelper
{
    private EntityFactory $entityFactory;
    private NPApiHelper $apiHelper;
    private Settings $settings;
    private Money $money;

    public function __construct(
        EntityFactory $entityFactory,
        NPApiHelper   $apiHelper,
        Settings      $settings,
        Money         $money
    ) {
        $this->entityFactory = $entityFactory;
        $this->apiHelper = $apiHelper;
        $this->settings = $settings;
        $this->money = $money;
    }

    /**
     * Калькулятор стоимости доставки Новой Почты
     * @param string $cityRef id города Новой Почты
     * @param bool $redelivery наложенный платеж
     * @param NPCalcVO $calcVO - данные о заказе
     * @param string $serviceType - тип доставки (до двери, до склада...)
     * @return int|null
     * @throws \Exception
     */
    public function calcPrice(
        string $cityRef,
        bool $redelivery,
        NPCalcVO $calcVO,
        string $serviceType
    ): ?int
    {
        if (empty($cityRef)) {
            return false;
        }

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        if (!$npCurrency = $currenciesEntity->findOne(['code' => 'UAH'])) {
            $npCurrency = $currenciesEntity->getMainCurrency();
        }

        $methodProperties = [
            'CitySender' => $this->settings->get('newpost_city'),
            'CityRecipient' => $cityRef,
            'Weight' => $calcVO->getTotalWeight(),
            'ServiceType' => $serviceType,
        ];

        if ($this->settings->get('newpost_use_volume')){
            $methodProperties = array_merge($methodProperties, [
                'VolumeGeneral' => $calcVO->getTotalVolume()
            ]);
        }

        /* Если в настройках выбрано "оценочная стоимость" */
        if ($this->settings->get('newpost_use_assessed_value')) {
            $cost = $this->money->convert($calcVO->getTotalPrice(), $npCurrency->id, false);
            $methodProperties = array_merge($methodProperties, [
                'Cost' => max(1, round($cost))
            ]);
        }

        /* Если выбрали наложенный платеж */
        if ($redelivery) {
            $redeliveryAmount = $this->money->convert($calcVO->getTotalPrice(), $npCurrency->id, false);

            $methodProperties = array_merge($methodProperties, [
                'RedeliveryCalculate' => [
                    'CargoType' => 'Money',
                    'Amount' => round($redeliveryAmount),
                ],
            ]);
        }

        $request = [
            'modelName' => 'InternetDocument',
            'calledMethod' => 'getDocumentPrice',
            'methodProperties' => $methodProperties
        ];

        $response = $this->apiHelper->request($request);

        if (!empty($response->success)) {
            return (int)($response->data[0]->Cost + ($response->data[0]->CostRedelivery ?? 0));
        }

        return null;
    }

    /**
     * Калькулятор срока доставки
     * @param string $cityRef id города Новой Почты
     * @param string $serviceType - тип доставки (до двери, до склада...)
     * @return int|null
     */
    public function calcTerm(string $cityRef, string $serviceType): ?int
    {
        if (empty($cityRef)) {
            return false;
        }

        $request = [
            'modelName' => 'InternetDocument',
            'calledMethod' => 'getDocumentDeliveryDate',
            'methodProperties' => [
                'CitySender' => $this->settings->get('newpost_city'),
                'CityRecipient' => $cityRef,
                'ServiceType' => $serviceType,
            ],
        ];

        $response = $this->apiHelper->request($request);
        if (!empty($response->success)) {
            $term = strtotime($response->data[0]->DeliveryDate->date);

            //От НП приходит дата доставки, рассчитываем сколько это дней от сегодня
            return (int)ceil(($term - time()) / 86400);
        }

        return null;
    }
}