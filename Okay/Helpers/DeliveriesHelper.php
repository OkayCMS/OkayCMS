<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Interfaces\DeliveryInterface;
use Okay\Core\Modules\Module;
use Okay\Core\Money;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\ModulesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Psr\Log\LoggerInterface;

class DeliveriesHelper
{

    private $entityFactory;
    private $module;
    private $logger;

    public function __construct(EntityFactory $entityFactory, Module $module, LoggerInterface $logger)
    {
        $this->entityFactory = $entityFactory;
        $this->module = $module;
        $this->logger = $logger;
    }

    /**
     * @param $delivery
     * @param $order
     * @return array
     * 
     * Метод подготавливает данные для записи стоимости доставки в заказ
     * 
     */
    public function prepareDeliveryPriceInfo($delivery, $order)
    {
        if (empty($delivery)) {
            return ExtenderFacade::execute(__METHOD__, [], func_get_args());
        }

        if ($delivery->free_from > $order->total_price) {
            $deliveryPriceInfo = [
                'delivery_price'    => $delivery->price,
                'separate_delivery' => $delivery->separate_payment,
            ];
        } else {
            $deliveryPriceInfo = [
                'delivery_price'    => 0,
                'separate_delivery' => $delivery->separate_payment,
            ];
        }

        return ExtenderFacade::execute(__METHOD__, $deliveryPriceInfo, func_get_args());
    }

    /**
     * @var $cart
     * @var $paymentMethods
     * @return array
     * @throws \Exception
     *
     * Метод возвращает способы доставки для корзины
     */
    public function getCartDeliveriesList($cart, $paymentMethods)
    {
        $SL = ServiceLocator::getInstance();
        
        /** @var FrontTranslations $frontTranslations */
        $frontTranslations = $SL->getService(FrontTranslations::class);
        
        /** @var EntityFactory $entityFactory */
        $entityFactory = $SL->getService(EntityFactory::class);
        
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $entityFactory->get(CurrenciesEntity::class);
        $currency = $currenciesEntity->get((int)$_SESSION['currency_id']);
        
        /** @var Money $money */
        $money = $SL->getService(Money::class);
        
        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
        $deliveries = $deliveriesEntity->mappedBy('id')->find(['enabled'=>1]);
        
        foreach ($deliveries as $delivery) {

            $delivery->is_free_delivery = false;
            if ($cart->total_price > $delivery->free_from) {
                $delivery->is_free_delivery = true;
            }
            
            // Добавим текст стоимости доставки
            $delivery->delivery_price_text = '';
            if ($cart->total_price < $delivery->free_from && $delivery->price>0) {
                $delivery->delivery_price_text = $money->convert($delivery->price) . ' ' . $currency->sign;
                if ($delivery->separate_payment) {
                    $delivery->delivery_price_text .= ', ' . $frontTranslations->getTranslation('cart_paid_separate');
                }
            } elseif ($cart->total_price > $delivery->free_from) {
                $delivery->delivery_price_text = $frontTranslations->getTranslation('cart_free');
            }
            $delivery->total_price_with_delivery = $cart->total_price;
            if (!$delivery->separate_payment && $cart->total_price < $delivery->free_from) {
                $delivery->total_price_with_delivery += $delivery->price;
            }
            
            // Сортируем массив id способов оплаты для доставки в соответствии с позициями способов оплаты.
            // Также откинем не активные способы полаты
            $delivery->payment_methods_ids = array_intersect(array_keys($paymentMethods), $deliveriesEntity->getDeliveryPayments($delivery->id));
            
            if (!empty($delivery->settings) && is_string($delivery->settings)) {
                $delivery->settings = unserialize($delivery->settings);
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $deliveries, func_get_args());
    }
    
    public function getActiveDeliveryMethod($deliveries, $user)
    {
        $SL = ServiceLocator::getInstance();

        /** @var Request $request */
        $request = $SL->getService(Request::class);
        
        // Передаём на фронт активный способ доставки
        if (!empty($user->preferred_delivery_id) && isset($deliveries[$user->preferred_delivery_id])) {
            $activeDelivery = $deliveries[$user->preferred_delivery_id];
        } elseif (($deliveryId = $request->post('delivery_id', 'integer')) && isset($deliveries[$deliveryId])) {
            $activeDelivery = $deliveries[$deliveryId];
        } else {
            $activeDelivery = reset($deliveries);
        }

        return ExtenderFacade::execute(__METHOD__, $activeDelivery, func_get_args());
    }

    public function updateDeliveryPriceInfo($deliveryPriceInfo, $order)
    {
        if (!empty($deliveryPriceInfo)) {
            $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
            $ordersEntity->update($order->id, $deliveryPriceInfo);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    private function isSeparateDelivery($delivery)
    {
        return !empty($delivery->separate_payment);
    }
}