<?php


namespace Okay\Modules\OkayCMS\WayForPay\Controllers;


use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Controllers\AbstractController;
use Psr\Log\LoggerInterface;

class CallbackController extends AbstractController
{
    public function payOrder(
        Money $money,
        Notify $notify,
        OrdersEntity $ordersEntity,
        PaymentsEntity $paymentsEntity,
        LoggerInterface $logger
    ) {
        $keysForSignature = [
            'merchantAccount',
            'orderReference',
            'amount',
            'currency',
            'authCode',
            'cardPan',
            'transactionStatus',
            'reasonCode'
        ];

        $this->response->setContentType(RESPONSE_TEXT);
        
        $data = json_decode(file_get_contents("php://input"));
        if (empty($data->orderReference)) {
            $this->response->setContent("Wrong data")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }
        $orderId = $data->orderReference;

        $order = $ordersEntity->get((int) $orderId);
        if (empty($order)) {
            $logger->warning("WayForPay notice: 'Order not found'. Order №{$orderId}");
            $this->response->setContent("Order not found")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        $method = $paymentsEntity->get((int) $order->payment_method_id);
        if (empty($method)) {
            $logger->warning("WayForPay notice: 'Invalid payment method'. Order №{$orderId}");
            $this->response->setContent("Invalid payment method")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        $amount = !empty($data->amount) ? $data->amount : null;
        $w4pAmount = round($amount, 2);
        $orderAmount = round($money->convert($order->total_price, $method->currency_id, false), 2);
        if ($orderAmount != $w4pAmount) {
            $logger->warning("WayForPay notice: 'Invalid total order price'. Order №{$orderId}");
            $this->response->setContent("Invalid total order price")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        $settings = unserialize($method->settings);

        $sign = array();
        foreach ($keysForSignature as $dataKey) {
            if (array_key_exists($dataKey, $data)) {
                $sign [] = $data->$dataKey;
            }
        }

        $sign = implode(';', $sign);
        $sign = hash_hmac('md5', $sign, $settings['wayforpay_secretkey']);
        if (!empty($data->merchantSignature) && $data->merchantSignature != $sign) {
            $logger->warning("WayForPay notice: 'Invalid merchant signature'. Order №{$orderId}");
            $this->response->setContent("Invalid merchant signature")->setStatusCode(400);
            $this->response->sendContent();
            exit;
        }

        $responseToGateway = [
            'orderReference' => $data->orderReference,
            'status'         => 'accept',
            'time'           => time()
        ];

        $sign = array();
        foreach ($responseToGateway as $dataKey => $dataValue) {
            $sign [] = $dataValue;
        }

        $sign = implode(';', $sign);
        $sign = hash_hmac('md5', $sign, $settings['wayforpay_secretkey']);
        $responseToGateway['signature'] = $sign;

        if (!empty($data->transactionStatus) &&  $data->transactionStatus == 'Approved' && !$order->paid) {
            $ordersEntity->update((int) $order->id, ['paid' => 1]);
            $ordersEntity->close((int) $order->id);
            $notify->emailOrderUser((int) $order->id);
            $notify->emailOrderAdmin((int) $order->id);
        }

        $this->response->setContent(json_encode($responseToGateway), RESPONSE_JSON);
    }
}