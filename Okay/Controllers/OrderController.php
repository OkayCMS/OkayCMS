<?php


namespace Okay\Controllers;


use Okay\Entities\CouponsEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Helpers\MetadataHelpers\OrderMetadataHelper;
use Okay\Helpers\OrdersHelper;

class OrderController extends AbstractController
{
    
    public function render(
        OrdersEntity        $ordersEntity,
        CouponsEntity       $couponsEntity,
        PaymentsEntity      $paymentsEntity,
        DeliveriesEntity    $deliveriesEntity,
        OrderStatusEntity   $orderStatusEntity,
        CurrenciesEntity    $currenciesEntity,
        OrdersHelper        $ordersHelper,
        OrderMetadataHelper $orderMetadataHelper,
        $url
    ) {
        $order = $ordersEntity->get((string)$url);

        if (empty($order)) {
            return false;
        }

        $purchases = $ordersHelper->getOrderPurchasesList(intval($order->id));
        if (!$purchases) {
            return false;
        }

        if (!empty($order->coupon_code)) {
            $order->coupon = $couponsEntity->get((string)$order->coupon_code);
            if ($order->coupon && $order->coupon->valid && $order->total_price >= $order->coupon->min_order_price) {
                if ($order->coupon->type == 'absolute') {
                    // Абсолютная скидка не более суммы заказа
                    $order->coupon->coupon_percent = round(100 - ($order->total_price * 100) / ($order->total_price + $order->coupon->value), 2);
                } else {
                    $order->coupon->coupon_percent = $order->coupon->value;
                }
            }
        }

        $this->design->assign('order', $order);
        $this->setMetadataHelper($orderMetadataHelper);
        
        /*Выбор другого способа оплаты*/
        if ($this->request->method('post')) {
            if ($paymentMethodId = $this->request->post('payment_method_id', 'integer')) {
                $ordersEntity->update($order->id, ['payment_method_id'=>$paymentMethodId]);
                $order = $ordersEntity->get((int)$order->id);
            } elseif ($this->request->post('reset_payment_method')) {
                $ordersEntity->update($order->id, ['payment_method_id'=>null]);
                $order = $ordersEntity->get((int)$order->id);
            }
        }
        
        // Способ доставки
        $delivery = $deliveriesEntity->get((int)$order->delivery_id);
        $this->design->assign('delivery', $delivery);
        $orderStatuses = $orderStatusEntity->get(intval($order->status_id));
        $this->design->assign('order_status', $orderStatuses);
        $this->design->assign('purchases', $purchases);
        
        // Способ оплаты
        if (!empty($order->payment_method_id)) {
            $payment_method = $paymentsEntity->get((int)$order->payment_method_id);
            $this->design->assign('payment_method', $payment_method);
        }
        
        // Варианты оплаты
        $paymentMethods = $ordersHelper->getOrderPaymentMethodsList($order);
        $this->design->assign('payment_methods', $paymentMethods);
        
        // Все валюты
        $this->design->assign('all_currencies', $currenciesEntity->mappedBy('id')->find());

        // Скидки
        $discounts = $ordersHelper->getDiscounts($order->id);
        $this->design->assign('discounts', $discounts);

        $this->design->assign('noindex_nofollow', true);
        
        // Выводим заказ
        $this->response->setContent('order.tpl');
    }
    
}
