<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Controllers;


use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Controllers\AbstractController;
use Okay\Core\Cart;
use Okay\Core\Money;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCalcHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\VO\NPCalcVO;
use Psr\Log\LoggerInterface;

class NovaposhtaCostController extends AbstractController
{
    public function calc(
        Cart $cart,
        Money $money,
        CurrenciesEntity $currenciesEntity,
        DeliveriesEntity $deliveriesEntity,
        OrdersEntity $ordersEntity,
        LoggerInterface $logger,
        BackendOrdersHelper $backendOrdersHelper,
        Settings $settings,
        NPCalcHelper $calcHelper
    ) {
        $this->design->assignJsVar('np_delivery_module_id', 1);
        $cityRef = $this->request->get('city');
        $deliveryId = $this->request->get('delivery_id', 'integer');
        $redelivery = $this->request->get('redelivery', 'boolean');
        
        $orderId = $this->request->get('order_id', 'integer');
        $currencyId = $this->request->get('currency', 'integer', $_SESSION['currency_id']);

        if ($orderId) {
            if (!$order = $ordersEntity->get($orderId)) {
                $this->response->setContent(json_encode(['error' => 'order not found']), RESPONSE_JSON);
                return;
            }

            $totalPrice = $order->total_price;
            if ($order->separate_delivery == 0) {
                $totalPrice -= $order->delivery_price;
            }
            $calcVO = new NPCalcVO(
                (int)$totalPrice,
                (float)$settings->get('newpost_weight'),
                (float)$settings->get('newpost_volume')
            );
            foreach ($backendOrdersHelper->findOrderPurchases($order) as $purchase) {
                $calcVO->addPurchaseWeight((float)$purchase->variant->weight, $purchase->amount);
                $calcVO->addPurchaseVolume((float)$purchase->variant->volume, $purchase->amount);
            }
        } else {
            $calcVO = new NPCalcVO(
                $cart->total_price,
                (float)$settings->get('newpost_weight'),
                (float)$settings->get('newpost_volume')
            );
            foreach ($cart->purchases as $purchase) {
                $calcVO->addPurchaseWeight((float)$purchase->variant->weight, $purchase->amount);
                $calcVO->addPurchaseVolume((float)$purchase->variant->volume, $purchase->amount);
            }
        }

        if (!$delivery = $deliveriesEntity->get($deliveryId)) {
            $this->response->setContent(json_encode(['error' => 'delivery not found']), RESPONSE_JSON);
            return;
        }
        $deliverySettings = $deliveriesEntity->getSettings($deliveryId);
        $serviceType = $deliverySettings['service_type'];

        $result['term_response']['success'] = false;
        $result['price_response']['success'] = false;

        if ($deliveryPrice = $calcHelper->calcPrice($cityRef, $redelivery, $calcVO, $serviceType)) {
            $currency = $currenciesEntity->get($currencyId);

            if ($npCurrency = $currenciesEntity->findOne(['code' => 'UAH'])) {
                $result['price_response']['success'] = true;
                // Переводим цену в валюту по умолчанию для сайта
                $result['price_response']['price'] = (float)$money->convert($deliveryPrice, $npCurrency->id, false, true);

                // Изменим стоимость доставки, на ту, что просчитала Новая почта
                $result['price_response']['cart_total_price'] = $cart->get()->total_price;
                if (!$delivery->separate_payment && $cart->get()->total_price < $delivery->free_from) {
                    $result['price_response']['cart_total_price'] += $deliveryPrice;
                }

                $result['price_response']['price_formatted'] = $money->convert($deliveryPrice, $currency->id) . ' ' . $currency->sign;
            } else {
                $logger->warning('Novaposhta cost need create currency with code UAH');
            }

            $result['price_response']['delivery_id'] = $deliveryId;
        }

        if ($deliveryDays = $calcHelper->calcTerm($cityRef, $serviceType)) {
            $result['term_response']['success'] = true;
            $result['term_response']['term'] = $deliveryDays;
        }

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
}
