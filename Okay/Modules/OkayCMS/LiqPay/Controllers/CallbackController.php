<?php


namespace Okay\Modules\OkayCMS\LiqPay\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Psr\Log\LoggerInterface;

class CallbackController extends AbstractController
{
    public function payOrder(
        OrdersEntity $ordersEntity,
        PaymentsEntity $paymentsEntity,
        CurrenciesEntity $currenciesEntity,
        Money $money,
        Notify $notify,
        LoggerInterface $logger
    ) {

        $this->response->setContentType(RESPONSE_TEXT);

        $signature      = $this->request->post('signature');
        $data           = $this->request->post('data');

        $payment_data = json_decode(base64_decode($data));

        $orderId = intval(substr($payment_data->order_id, 0, strpos($payment_data->order_id, '-')));
        $currency = $payment_data->currency;
        $status = $payment_data->status;
        $type = $payment_data->type;
        $amount = $payment_data->amount;


        if ($status !== 'success') {
            $logger->warning("LiqPay notice: 'bad status'. Order №{$orderId}");
            $this->response->setContent("bad status")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        if ($type !== 'buy') {
            $logger->warning("LiqPay notice: 'bad type'. Order №{$orderId}");
            $this->response->setContent("bad type")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        // Выберем заказ из базы
        $order = $ordersEntity->findOne(['id' => intval($orderId)]);
        if (empty($order)) {
            $logger->warning("LiqPay notice: 'Order not found'. Order №{$orderId}");
            die('Оплачиваемый заказ не найден');
        }

        // Выбираем из базы соответствующий метод оплаты
        $method = $paymentsEntity->get(intval($order->payment_method_id));
        if (empty($method)) {
            $logger->warning("LiqPay notice: 'Method Not Allowed'. Order №{$orderId}");
            $this->response->setContent("Method Not Allowed")->setStatusCode(405);
            $this->response->sendContent();
            exit;
        }

        $settings = $paymentsEntity->getPaymentSettings($method->id);
        $payment_currency = $currenciesEntity->get(intval($method->currency_id));

        // Валюта должна совпадать
        if ($currency !== $payment_currency->code) {
            $logger->warning("LiqPay notice: 'bad currency'. Order №{$orderId}");
            $this->response->setContent("bad currency")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        $mySignature = base64_encode(sha1($settings['liq_pay_private_key'] . $data . $settings['liq_pay_private_key'],1));

        if ($mySignature !== $signature) {
            $logger->warning("LiqPay notice: 'bad sign {$signature}'. Order №{$orderId}");
            $this->response->setContent("bad sign {$signature}")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        // Нельзя оплатить уже оплаченный заказ  
        if ($order->paid) {
            $logger->warning("LiqPay notice: 'order already paid'. Order №{$orderId}");
            $this->response->setContent("order already paid")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        if ($amount != round($money->convert($order->total_price, $method->currency_id, false), 2) || $amount<=0) {
            $logger->warning("LiqPay notice: 'incorrect price'. Order №{$orderId}");
            $this->response->setContent("incorrect price")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        // Установим статус оплачен
        $ordersEntity->update(intval($order->id), ['paid'=>1]);

        // Отправим уведомление на email
        $notify->emailOrderUser(intval($order->id));
        $notify->emailOrderAdmin(intval($order->id));

        // Спишем товары  
        $ordersEntity->close(intval($order->id));

    }
}
