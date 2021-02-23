<?php


namespace Okay\Modules\OkayCMS\YooKassa\Controllers;

use Okay\Core\Config;
use Okay\Core\Router;
use Okay\Core\EntityFactory;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\OrdersEntity;
use YooKassa\Request\Payments\CreatePaymentRequest;
use YooKassa\Model\Payment;
use YooKassa\Client;
use Okay\Core\Settings;
use Okay\Controllers\AbstractController;

class RequestController extends AbstractController
{
    const DEFAULT_TAX_RATE_ID = 1;

    const YOOMONEY_MODULE_VERSION = '1.5.2';

    public function sendPaymentRequest(Settings $settings, EntityFactory $entityFactory, Config $config)
    {
        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity  = $entityFactory->get(PurchasesEntity::class);

        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $entityFactory->get(DeliveriesEntity::class);

        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $entityFactory->get(OrdersEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $entityFactory->get(PaymentsEntity::class);

        $amount          = $this->request->post('amount');
        $orderId         = $this->request->post('order_id');
        $paymentType     = $this->request->post('payment_type');
        $order           = $ordersEntity->get((int) $orderId);
        $idTax           = $this->getIdTax($settings);
        $paymentSettings = (object) $paymentsEntity->getPaymentSettings((int) $order->payment_method_id);

        $apiClient = new Client();
        $apiClient->setAuth($paymentSettings->yandex_api_shopid, $paymentSettings->yandex_api_password);
        $builder = CreatePaymentRequest::builder();
        $builder->setAmount($amount);
        $builder->setPaymentMethodData($paymentType);
        $builder->setCapture(true);
        $builder->setDescription($this->createDescription($order, $settings));
        $builder->setConfirmation([
                'type'       => \YooKassa\Model\ConfirmationType::REDIRECT,
                'return_url' => Router::generateUrl('OkayCMS.YooKassa.Callback', [], true)."/?action=return&order_id={$orderId}",
            ]);
        $builder->setMetadata([
            'cms_name'       => 'yoo_okay',
            'module_version' => self::YOOMONEY_MODULE_VERSION,
            'order_id'       => $order->id,
        ]);

        if ($paymentSettings->ya_kassa_api_send_check) {
            $purchases = $purchasesEntity->find(['order_id' => (int) $order->id]);
            $builder->setReceiptEmail($order->email);

            foreach ($purchases as $purchase) {
                $builder->addReceiptItem(
                    $purchase->product_name,
                    $purchase->price,
                    $purchase->amount,
                    $idTax,
                    $paymentSettings->ya_kassa_api_payment_mode,
                    $paymentSettings->ya_kassa_api_payment_subject
                );
            }

            if ($order->delivery_id && $order->delivery_price > 0) {
                $delivery = $deliveriesEntity->get((int) $order->delivery_id);
                $builder->addReceiptShipping(
                    $delivery->name,
                    $order->delivery_price,
                    $idTax,
                    $paymentSettings->ya_kassa_api_payment_mode,
                    $paymentSettings->ya_kassa_api_payment_subject
                );
            }
        }

        $paymentRequest = $builder->build();
        $idUniqueKey    = base64_encode($order->id.microtime());
        $response       = $apiClient->createPayment($paymentRequest, $idUniqueKey);

        if (!empty($response)) {
            $ordersEntity->update($order->id, ['payment_details' => $response->getId()]);
            $confirmationUrl = $response->confirmation->confirmationUrl;
            $this->response->redirectTo($confirmationUrl);
        }
    }

    private function getIdTax(Settings $settings)
    {
        $yaKassaApiTax = $settings->get('ya_kassa_api_tax');

        if (!empty($yaKassaApiTax)) {
            return $yaKassaApiTax;
        }

        return self::DEFAULT_TAX_RATE_ID;
    }

    private function createDescription($orderInfo, Settings $settings)
    {
        $yandexDescriptionTemplate = $settings->get('yandex_description_template');
        $descriptionTemplate = !empty($yandexDescriptionTemplate) ? $yandexDescriptionTemplate : 'Оплата заказа №%id%';

        $replace = [];
        foreach ($orderInfo as $key => $value) {
            if (is_scalar($value)) {
                $replace['%'.$key.'%'] = $value;
            }
        }

        $description = strtr($descriptionTemplate, $replace);
        return (string) mb_substr($description, 0, Payment::MAX_LENGTH_DESCRIPTION);
    }
}