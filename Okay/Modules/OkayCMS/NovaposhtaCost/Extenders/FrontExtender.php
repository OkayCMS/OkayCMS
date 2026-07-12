<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Extenders;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Modules\Module;
use Okay\Core\Router;
use Okay\Core\ServiceLocator;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCheckoutCostCalculator;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCheckoutRequestReader;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPDeliveryDataHelper;

/**
 * @phpstan-type UserRow object{id: int|string}&\stdClass
 * @phpstan-type OrderRow object{id: int|string}&\stdClass
 * @phpstan-type DeliveryDataRow object{city_id?: int|string|null, warehouse_id?: int|string|null, city_name?: string|null, area_name?: string|null, region_name?: string|null, street?: string|null, house?: string|null, apartment?: string|null}&\stdClass
 */
class FrontExtender implements ExtensionInterface
{
    private EntityFactory $entityFactory;
    private FrontTranslations $frontTranslations;
    private Design $design;
    private NPDeliveryDataHelper $deliveryDataHelper;
    private NPCheckoutRequestReader $checkoutRequestReader;
    private NPCheckoutCostCalculator $checkoutCostCalculator;

    public function __construct(
        EntityFactory $entityFactory,
        FrontTranslations $frontTranslations,
        Design $design,
        NPDeliveryDataHelper $deliveryDataHelper,
        NPCheckoutRequestReader $checkoutRequestReader,
        NPCheckoutCostCalculator $checkoutCostCalculator
    ) {
        $this->entityFactory     = $entityFactory;
        $this->frontTranslations = $frontTranslations;
        $this->design            = $design;
        $this->deliveryDataHelper = $deliveryDataHelper;
        $this->checkoutRequestReader = $checkoutRequestReader;
        $this->checkoutCostCalculator = $checkoutCostCalculator;
    }

    /**
     * @param $deliveries
     * @param $cart
     * @return array<int|string, object>
     * @throws \Exception
     *
     * Метод проходится по способам доставки, и подменяет текст стоимости доставки.
     *
     */
    public function getCartDeliveriesList($deliveries, $cart)
    {
        $SL = ServiceLocator::getInstance();

        /** @var FrontTranslations $frontTranslations */
        $frontTranslations = $SL->getService(FrontTranslations::class);

        /** @var Module $module */
        $module = $SL->getService(Module::class);

        /** @var Design $design */
        $design = $SL->getService(Design::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        $redeliveryPaymentsIds = $paymentsEntity->cols(['id'])->find(['novaposhta_cost__cash_on_delivery' => 1]);
        foreach ($redeliveryPaymentsIds as $k => $id) {
            $redeliveryPaymentsIds[$k] = (int)$id;
        }
        $design->assignJsVar('np_redelivery_payments_ids', $redeliveryPaymentsIds);
        $design->assign('np_redelivery_payments_ids', $redeliveryPaymentsIds);

        $npModuleId = $module->getModuleIdByNamespace(__CLASS__);
        $design->assignJsVar('np_delivery_module_id', $npModuleId);
        $design->assign('np_delivery_module_id', $npModuleId);

        foreach ($deliveries as $delivery) {
            if ($delivery->module_id == $npModuleId) {
                $delivery->delivery_price_text = $frontTranslations->getTranslation('np_need_select_city');
            }
        }
        return ExtenderFacade::execute(__METHOD__, $deliveries, func_get_args());
    }

    /**
     * @param array<string, mixed> $defaultData
     * @param $user
     * @return array<string, mixed>
     * @throws \Exception
     *
     * Если у пользователя был ранее заказ, и он был на Новую почту, заполним данными
     */
    public function getDefaultCartData($defaultData, $user)
    {

        /** @var UserRow|null $user */
        if (!empty($user->id)) {
            /** @var OrdersEntity $ordersEntity */
            $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

            /** @var NPCostDeliveryDataEntity $npDeliveryDataEntity */
            $npDeliveryDataEntity = $this->entityFactory->get(NPCostDeliveryDataEntity::class);

            /** @var NPCitiesEntity $npCitiesEntity */
            $npCitiesEntity = $this->entityFactory->get(NPCitiesEntity::class);

            $lastOrder = $ordersEntity->findOne(['user_id' => $user->id]);
            /** @var OrderRow|false $lastOrder */
            if ($lastOrder && ($npDeliveryData = $npDeliveryDataEntity->getByOrderId($lastOrder->id))) {
                /** @var OrderRow $lastOrder */
                /** @var DeliveryDataRow $npDeliveryData */
                if (!empty($npDeliveryData->warehouse_id)) {
                    $defaultData['novaposhta_warehouse_city_ref'] = $npDeliveryData->city_id;
                    $defaultData['novaposhta_delivery_warehouse_id'] = $npDeliveryData->warehouse_id;

                    if (!empty($npDeliveryData->city_id) && empty($npDeliveryData->city_name)) {
                        $npDeliveryData->city_name = $npCitiesEntity->col('name')->findOne(['ref' => $npDeliveryData->city_id]);
                    }

                    $defaultData['novaposhta_warehouse_city'] = $npDeliveryData->city_name;
                    $defaultData['novaposhta_warehouse_city_name'] = $npDeliveryData->city_name;
                } else {
                    $defaultData['novaposhta_door_settlement_ref'] = $npDeliveryData->city_id;
                    $defaultData['novaposhta_door_city'] = $npDeliveryData->city_name;
                    $defaultData['novaposhta_city_name'] = $npDeliveryData->city_name;
                    $defaultData['novaposhta_area_name'] = $npDeliveryData->area_name;
                    $defaultData['novaposhta_region_name'] = $npDeliveryData->region_name;
                    $defaultData['novaposhta_street'] = $defaultData['novaposhta_street_name'] = $npDeliveryData->street;
                    $defaultData['novaposhta_house'] = $npDeliveryData->house;
                    $defaultData['novaposhta_apartment'] = $npDeliveryData->apartment;
                }
            }
        }

        $route_name = Router::getCurrentRouteName();
        if (!empty($route_name) && ($route_name === 'cart')) {
            $np_cart_calculate = $this->frontTranslations->getTranslation('np_cart_calculate');
            if (empty($np_cart_calculate)) {
                $np_cart_calculate = 'Вычисляем...';
            }
            $this->design->assignJsVar('np_cart_calculate', $np_cart_calculate);
        }

        return ExtenderFacade::execute(__METHOD__, $defaultData, func_get_args());
    }

    /**
     * @param $result
     * @param $delivery
     * @param $order
     * @return mixed
     *
     * Обновляем стоимость доставки, которая пришла из API.
     * Важно не забывать что экстендеры работают всегда, и важно проверить что выбран именно наш способ доставки
     */
    public function setCartDeliveryPrice($result, $delivery, $order)
    {
        $selectedDelivery = $this->checkoutRequestReader->selectedNovaPoshtaDelivery();
        if (
            $selectedDelivery !== null &&
            (int)$selectedDelivery->id === (int)$delivery->id &&
            $delivery->paid &&
            $delivery->free_from > $order->total_price
        ) {
            $selectedData = $this->checkoutRequestReader->selectedDataForDelivery($selectedDelivery);
            $deliveryPrice = $this->checkoutCostCalculator->calculateSelectedDeliveryPrice(
                $selectedDelivery,
                $order,
                $selectedData
            );
            if ($deliveryPrice !== null) {
                $result['delivery_price'] = $deliveryPrice;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * @param $in
     * @param $order
     * @throws \Exception
     * Добавляем данные по доставке, для этого заказа
     */
    public function setCartDeliveryDataProcedure($in, $order)
    {
        $selectedDelivery = $this->checkoutRequestReader->selectedNovaPoshtaDelivery();
        if ($selectedDelivery === null) {
            return null;
        }

        $selectedData = $this->checkoutRequestReader->selectedDataForDelivery($selectedDelivery);
        if (!$this->checkoutRequestReader->hasValidLocationRefs($selectedDelivery, $selectedData)) {
            return null;
        }

        /** @var NPCostDeliveryDataEntity $npDeliveryDataEntity */
        $npDeliveryDataEntity = $this->entityFactory->get(NPCostDeliveryDataEntity::class);
        $deliveryData = new \stdClass();
        $deliveryData->delivery_term = $selectedData['delivery_term'];
        $deliveryData->redelivery = $selectedData['redelivery'];
        $deliveryData->order_id = $order->id;

        if ($this->checkoutRequestReader->isDoorDelivery($selectedDelivery)) {
            $deliveryData->city_id = $selectedData['door_settlement_ref'];
            $deliveryData->city_name = $selectedData['city_name'] ?: $selectedData['door_city'];
            $deliveryData->area_name = $selectedData['area_name'];
            $deliveryData->region_name = $selectedData['region_name'];
            $deliveryData->street = $selectedData['street_name'] ?: $selectedData['street'];
            $deliveryData->house = $selectedData['house'];
            $deliveryData->apartment = $selectedData['apartment'];
        } else {
            $deliveryData->city_id = $selectedData['warehouse_city_ref'];
            $deliveryData->city_name = $selectedData['warehouse_city_name'] ?: $selectedData['warehouse_city'];
            $deliveryData->warehouse_id = $selectedData['delivery_warehouse_id'];
        }

        $addId = $npDeliveryDataEntity->add($deliveryData);

        return ExtenderFacade::execute(__METHOD__, [$addId, $deliveryData], func_get_args());
    }

    /**
     * @param $error
     * @param $order
     * @return null|string
     */
    public function getCartValidateError($error)
    {
        if ($error !== null) {
            return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
        }

        $selectedDelivery = $this->checkoutRequestReader->selectedNovaPoshtaDelivery();
        if ($selectedDelivery === null) {
            return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
        }

        $selectedData = $this->checkoutRequestReader->selectedDataForDelivery($selectedDelivery);
        if ($this->checkoutRequestReader->isDoorDelivery($selectedDelivery)) {
            if (
                empty($selectedData['door_city'])
                || empty($selectedData['door_settlement_ref'])
            ) {
                $error = $this->frontTranslations->getTranslation('np_cart_error_city');
            } elseif (empty($selectedData['street']) && empty($selectedData['street_name'])) {
                $error = $this->frontTranslations->getTranslation('np_cart_error_street');
            } elseif (empty($selectedData['house'])) {
                $error = $this->frontTranslations->getTranslation('np_cart_error_house');
            } elseif (!$this->checkoutRequestReader->hasValidLocationRefs($selectedDelivery, $selectedData)) {
                $error = $this->frontTranslations->getTranslation('np_cart_error_city');
            }
        } elseif (
            empty($selectedData['warehouse_city'])
            || empty($selectedData['warehouse_city_ref'])
        ) {
            $error = $this->frontTranslations->getTranslation('np_cart_error_city');
        } elseif (empty($selectedData['delivery_warehouse_id'])) {
            $error = $this->frontTranslations->getTranslation('np_cart_error_warehouse');
        } elseif (!$this->checkoutRequestReader->hasValidLocationRefs($selectedDelivery, $selectedData)) {
            $error = $this->frontTranslations->getTranslation('np_cart_error_warehouse');
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getDeliveryDataProcedure($paymentMethods, $order)
    {
        if (!empty($order->id)) {
            $this->design->assign(
                'novaposhta_delivery_data',
                $this->deliveryDataHelper->getFullDeliveryData((int)$order->id)
            );
        }
    }
}
