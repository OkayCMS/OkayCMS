<?php

namespace Okay\Modules\OkayCMS\YooKassa;

if (!date_default_timezone_get()) {
    date_default_timezone_set('Europe/Moscow');
}

use YooKassa\Client;
use YooKassa\Model\Notification\NotificationSucceeded;
use YooKassa\Model\Notification\NotificationWaitingForCapture;
use YooKassa\Model\NotificationEventType;
use YooKassa\Model\PaymentStatus;
use YooKassa\Request\Payments\Payment\CreateCaptureRequest;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Notify;
use Okay\Core\Database;
use Okay\Core\QueryFactory;
use Okay\Core\EntityFactory;

class YooMoneyCallbackHandler
{
    private $entityFactory;
    private $paymentsEntity;
    private $ordersEntity;
    private $request;
    private $response;
    private $notify;
    private $db;
    private $queryFactory;

    public function __construct(
        EntityFactory  $entityFactory,
        Request        $request,
        Response       $response,
        Notify         $notify,
        Database       $db,
        QueryFactory   $queryFactory
    ){
        $this->paymentsEntity = $entityFactory->get(PaymentsEntity::class);
        $this->ordersEntity   = $entityFactory->get(OrdersEntity::class);
        $this->response       = $response;
        $this->request        = $request;
        $this->notify         = $notify;
        $this->db             = $db;
        $this->queryFactory   = $queryFactory;
        $this->entityFactory  = $entityFactory;
    }

    public function processReturnUrl()
    {
        $orderId       = $this->request->get('order_id');
        $order         = $this->ordersEntity->get((int) $orderId);
        $paymentMethod = $this->paymentsEntity->get((int) $order->payment_method_id);
        $settings      = $this->paymentsEntity->getPaymentSettings($paymentMethod->id);
        $apiClient     = $this->getApiClient($settings['yandex_api_shopid'], $settings['yandex_api_password']);
        $paymentId     = $this->getPaymentId($orderId);

        $paymentInfo = $apiClient->getPaymentInfo((string) $paymentId);
        if ($paymentInfo->status == PaymentStatus::WAITING_FOR_CAPTURE) {
            $captureResult = $this->capturePayment($apiClient, $paymentInfo);
            if ($captureResult->status == PaymentStatus::SUCCEEDED) {
                $this->completePayment($order, $paymentId);
            }
        } elseif ($paymentInfo->status == PaymentStatus::SUCCEEDED) {
            $this->completePayment($order, $paymentId);
        }

        $return_url = $this->request->getRootUrl().'/order/'.$order->url;
        $this->response->redirectTo($return_url);
    }

    public function processNotification()
    {
        $body           = @file_get_contents('php://input');
        $callbackParams = json_decode($body, true);

        if (json_last_error()) {
            $this->response->addHeader("HTTP/1.1 400 Bad Request");
            $this->response->addHeader("Status: 400 Bad Request");
            $this->response->sendHeaders();
            return;
        }

        $notificationModel = ($callbackParams['event'] === NotificationEventType::PAYMENT_SUCCEEDED)
            ? new NotificationSucceeded($callbackParams)
            : new NotificationWaitingForCapture($callbackParams);

        $payment       = $notificationModel->getObject();
        $orderId       = (int)$payment->getMetadata()->offsetGet('order_id');
        $order         = $this->ordersEntity->get((int) $orderId);
        $paymentMethod = $this->paymentsEntity->get((int) $order->payment_method_id);
        $settings      = $this->paymentsEntity->getPaymentSettings($paymentMethod->id);
        $apiClient     = $this->getApiClient($settings['yandex_api_shopid'], $settings['yandex_api_password']);
        $logger        = new YooMoneyLogger($settings['ya_kassa_debug']);

        $logger->info('Notification: '.$body);
        $apiClient->setLogger($logger);
        $paymentId = $payment->getId();

        if (empty($order)) {
            $logger->error('Order not found. OrderId: '.$orderId);
            $this->response->addHeader("HTTP/1.1 404 Not Found");
            $this->response->addHeader("Status: 404 Not Found");
            $this->response->sendHeaders();
            return;
        }

        $paymentInfo = $apiClient->getPaymentInfo($payment->getId());
        if (empty($paymentInfo)) {
            $logger->error('Empty payment info. OrderId: '.$orderId);
            $this->response->addHeader("HTTP/1.1 404 Not Found");
            $this->response->addHeader("Status: 404 Not Found");
            $this->response->sendHeaders();
            return;
        }

        if (PaymentStatus::WAITING_FOR_CAPTURE === $paymentInfo->status) {
            $captureResult = $this->capturePayment($apiClient, $paymentInfo);
            $logger->info('Capture payment #'.$paymentId.' orderId: '.$orderId);

            if (PaymentStatus::SUCCEEDED === $captureResult->status) {
                $this->completePayment($order, $paymentId);
                $logger->info('Complete payment #'.$paymentId.' orderId: '.$orderId);
            } else {
                $logger->info('Capture order fail. OrderId: '.$orderId);
            }

            $this->response->addHeader("HTTP/1.1 200 OK");
            $this->response->addHeader("Status: 200 OK");
            $this->response->sendHeaders();
            return;
        }

        if (PaymentStatus::PENDING === $paymentInfo->status) {
            $logger->info('Pending payment. OrderId: '.$orderId.' paymentId: '.$paymentId);
            $this->response->addHeader("HTTP/1.1 400 Bad Request");
            $this->response->addHeader("Status: 400 Bad Request");
            $this->response->sendHeaders();
            return;
        }

        if (PaymentStatus::SUCCEEDED === $paymentInfo->status) {
            $this->completePayment($order, $paymentId);
            $logger->info('Complete payment #'.$paymentId.' orderId: '.$orderId);
            $this->response->addHeader("HTTP/1.1 200 OK");
            $this->response->addHeader("Status: 200 OK");
            $this->response->sendHeaders();
            return;
        }

        if (PaymentStatus::CANCELED === $paymentInfo->status) {
            $logger->info('Cancel order. OrderId: '.$orderId);
            $this->response->addHeader("HTTP/1.1 200 OK");
            $this->response->addHeader("Status: 200 OK");
            $this->response->sendHeaders();
            return;
        }
    }

    protected function capturePayment($apiClient, $payment)
    {
        $captureRequest = CreateCaptureRequest::builder();
        $captureRequest->setAmount($payment->getAmount());
        $captureRequest->build();
        return $apiClient->capturePayment($captureRequest, $payment->id);
    }

    protected function getApiClient($shopId, $shopPassword)
    {
        $apiClient = new Client();
        $apiClient->setAuth($shopId, $shopPassword);
        return $apiClient;
    }

    private function getPaymentId($orderId)
    {
        $select = $this->queryFactory->newSelect();
        $select->cols(['payment_details'])
            ->from('__orders')
            ->where('id=:id')
            ->bindValue('id', (int)$orderId);
        $this->db->query($select);
        return $this->db->result('payment_details');
    }

    private function completePayment($order, $paymentId)
    {
        $this->ordersEntity->update($order->id, [
            'paid'    => 1,
            'comment' => " Номер транзакции в Яндекс.Кассе: {$paymentId}. Сумма: {$order->total_price}",
        ]);

        $this->notify->emailOrderAdmin((int)$order->id);
    }
}