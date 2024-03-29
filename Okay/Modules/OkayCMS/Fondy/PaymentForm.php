<?php


namespace Okay\Modules\OkayCMS\Fondy;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\Fondy\Helpers\FondyHelper;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{
    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Money
     */
    private $money;

    public function __construct(EntityFactory $entityFactory, Money $money)
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->money         = $money;
    }
    
    public function checkoutForm($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        $order = $ordersEntity->get((int)$orderId);
        $payment_method = $paymentsEntity->get((int)$order->payment_method_id);

        $payment_currency = $currenciesEntity->get(intval($payment_method->currency_id));
        $settings = $paymentsEntity->getPaymentSettings($payment_method->id);

        $price = round($this->money->convert($order->total_price, $payment_method->currency_id, false), 2);

        // описание заказа
        // order description
        $desc = 'Заказ номер: '.$order->id;

        // Способ оплаты
        $paymode = $settings['fondy_paymode'];
        
        $resultUrl = Router::generateUrl('OkayCMS_Fondy_callback', [], true);
        //$returnUrl = Router::generateUrl('order', ['url' => $order->url], true);

        $currency = $payment_currency->code;
        if ($currency == 'RUR')
            $currency = 'RUB';
        if ($settings['lang']=='') {
            $settings['lang'] ='ru';
        }

        $formData = [
            'order_id' => $orderId . FondyHelper::ORDER_SEPARATOR . time(),
            'merchant_id' => $settings['fondy_merchantid'],
            'order_desc' => $desc,
            'amount' => $price * 100,
            'currency' => $currency,
            'server_callback_url' => $resultUrl,
            'response_url' => $resultUrl,
            'lang' =>  $settings['lang'],
            'sender_email' => $order->email
        ];
        
        if ($paymode == 'Y') {
            $formData['preauth'] = 'Y';
        }
        
        $formData['signature'] = FondyHelper::getSignature($formData, $settings['fondy_secret']);
        
        $this->design->assign('fondy_settings', $settings);
        $this->design->assign('form_data', $formData);

        return $this->design->fetch('form.tpl');
    }
    
}