<?php

/* https://www.liqpay.ua/uk/doc/api/internet_acquiring/checkout?tab=1 */

namespace Okay\Modules\OkayCMS\LiqPay;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{
    /* EntityFactory $entityFactory */
    private $entityFactory;

    /* Money $money */
    private $money;

    /* FrontTranslations $frontTranslations */
    private $frontTranslations;

    /* Settings $settings */
    private $settings;

    public function __construct(
        EntityFactory     $entityFactory,
        Money             $money,
        FrontTranslations $frontTranslations,
        Settings $settings
    ){
        parent::__construct();
        $this->entityFactory     = $entityFactory;
        $this->money             = $money;
        $this->frontTranslations = $frontTranslations;
        $this->settings          = $settings;
    }

    /**
     * @inheritDoc
     */
    public function checkoutForm($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entityFactory->get(PurchasesEntity::class);

        $order = $ordersEntity->get((int)$orderId);
        $liqPayOrderId = $order->id . "-" . rand(100000, 999999);
        $paymentMethod = $paymentsEntity->get($order->payment_method_id);
        $paymentCurrency = $currenciesEntity->get(intval($paymentMethod->currency_id));
        $settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);

        $price = $this->money->convert($order->total_price, $paymentMethod->currency_id, false, false, 2);

        // опис замовлення
        $desc = $this->frontTranslations->getTranslation('okaycms__liq_pay__order_description') . ' №'.$order->id;

        $resultUrl = Router::generateUrl('order', ['url' => $order->url], true);
        $serverUrl = Router::generateUrl('OkayCMS_LiqPay_callback', [], true);

        $privateKey  = $settings['liq_pay_private_key'];
        $publicKey   = $settings['liq_pay_public_key'];
        $paymentType = $settings['pay_types'];

        //  дані для фіскалізації
        $purchases = $purchasesEntity->find(['order_id' => $order->id]);

        $items = [];

        $taxs [] = [ "name"=> "Без ПДВ 0%", "letter"=> "А", "prc"=> 0, "type"=> 0];

        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $amount     = (int) $purchase->amount;
                $itemPrice  = round($this->money->convert((float) $purchase->price, $paymentMethod->currency_id, false, false, 2), 2);
                $cost       = round($this->money->convert(($itemPrice * $amount), $paymentMethod->currency_id, false, false, 2), 2);

                $items[] = [
                    "amount"        => $amount,
                    "price"         => $itemPrice,
                    "cost"          => $cost,
                    "categoryname"  => "Дитячий одяг",
                    "name"          => $purchase->product_name,
                    "unitname"      => "Штука",
                    "vndcode"       => $purchase->sku,
                    "taxs"          => $taxs
                ];
            };
        };

        $rro_info['items'] = $items;

        if (!empty($orderEmailAdmin = $this->settings->get('order_email'))
            && !empty($sendAdmin = $this->settings->get('liq_pay_pay_send_receipts_admin_after_fiscalization'))
        ){
            $deliveryEmails[] = $orderEmailAdmin;
        }
        if (!empty($order->email)
            && !empty($sendRecepient = $this->settings->get('liq_pay_pay_send_receipts_client_after_fiscalization'))
        ){
            $deliveryEmails[] = $order->email;
        }
        if (!empty($deliveryEmails)){
            $rro_info['delivery_emails'] = array_values($deliveryEmails);
        }

        $data_array = [
            'version'      => 3,
            'public_key'   => $publicKey,
            'private_key'  => $privateKey,
            'action'       => 'pay',
            'amount'       => $price,
            'currency'     => $paymentCurrency->code,
            'description'  => $desc,
            'order_id'     => $liqPayOrderId,
            'result_url'   => $resultUrl,
            'server_url'   => $serverUrl,
            'rro_info'     => $rro_info,
        ];

        if(!empty($paymentType) && $paymentType !== 'default') {
            $data_array['paytypes'] = $paymentType;
        }


        $data = base64_encode(json_encode($data_array));
        $sign = base64_encode(sha1($privateKey.$data.$privateKey,1));

        $this->design->assign('data', $data);
        $this->design->assign('sign', $sign);

        return $this->design->fetch('form.tpl');
    }
}