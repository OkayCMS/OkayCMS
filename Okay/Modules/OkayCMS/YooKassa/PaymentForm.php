<?php

namespace Okay\Modules\OkayCMS\YooKassa;

define('YAMONEY_MODULE_VERSION', '1.0.10');

use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Modules\AbstractModule;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\FeaturesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Core\EntityFactory;
use Okay\Core\Settings;
use Okay\Core\Response;
use Okay\Core\Request;
use Okay\Core\Money;

class PaymentForm extends AbstractModule
{
    const INSTALLMENTS_MIN_AMOUNT = 3000;

    private $deliveriesEntity;
    private $purchasesEntity;
    private $paymentsEntity;
    private $featuresEntity;
    private $ordersEntity;
    private $settings;
    private $response;
    private $request;
    private $money;

    public function __construct(
        EntityFactory $entityFactory,
        Response      $response,
        Request       $request,
        Money         $money,
        Settings      $settings
    ){
        parent::__construct();
        $this->deliveriesEntity = $entityFactory->get(DeliveriesEntity::class);
        $this->purchasesEntity  = $entityFactory->get(PurchasesEntity::class);
        $this->paymentsEntity   = $entityFactory->get(PaymentsEntity::class);
        $this->featuresEntity   = $entityFactory->get(FeaturesEntity::class);
        $this->ordersEntity     = $entityFactory->get(OrdersEntity::class);
        $this->settings         = $settings;
        $this->response         = $response;
        $this->request          = $request;
        $this->money            = $money;
    }

    public function checkoutForm($orderId)
    {
        $order            = $this->ordersEntity->get((int) $orderId);
        $paymentMethod    = $this->paymentsEntity->get((int) $order->payment_method_id);
        $paymentSettings  = (object) $this->paymentsEntity->getPaymentSettings($paymentMethod->id);
        $amount           = round($this->money->convert($order->total_price, $paymentMethod->currency_id, false), 2);
        $paymentType      = ($paymentSettings->yandex_api_paymode == 'site') ? $paymentSettings->yandex_api_paymenttype : '';

        $this->design->assign('button_text',      'Перейти к оплате');
        $this->design->assign('payment_settings', $paymentSettings);
        $this->design->assign('amount',           $amount);
        $this->design->assign('payment_type',     $paymentType);
        return $this->design->fetch('form.tpl');
    }
}
