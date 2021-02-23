<?php


namespace Okay\Modules\OkayCMS\WayForPay;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Core\Languages;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\LanguagesEntity;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var Money
     */
    private $money;


    public function __construct(EntityFactory $entityFactory, Languages $languages, Money $money)
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->languages     = $languages;
        $this->money         = $money;
    }

    /**
     * @inheritDoc
     */
    public function checkoutForm($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entityFactory->get(PurchasesEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $this->entityFactory->get(LanguagesEntity::class);

        $order = $ordersEntity->get((int)$orderId);
        $purchases = $purchasesEntity->find(['order_id' => (int)$orderId]);
        $paymentMethod = $paymentsEntity->get($order->payment_method_id);
        $settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);

        $this->design->assign('merchantAccount', $settings['wayforpay_merchant']);
        $this->design->assign('orderReference', $order->id);
        $this->design->assign('orderDate', strtotime($order->date));
        $this->design->assign('merchantAuthType', 'simpleSignature');
        $this->design->assign('merchantDomainName', $_SERVER['HTTP_HOST']);

        $price           = round($this->money->convert($order->total_price, $paymentMethod->currency_id, false), 2);
        $paymentCurrency = $currenciesEntity->get(intval($paymentMethod->currency_id));
        $this->design->assign('amount', $price);
        $this->design->assign('currency', $paymentCurrency->code);

        $this->design->assign('productName', $this->getPurchaseNames($purchases));
        $this->design->assign('productPrice', $this->getPurchasePrices($purchases, $paymentMethod->currency_id));
        $this->design->assign('productCount', $this->getPurchaseCount($purchases));
        $this->design->assign('returnUrl', Router::generateUrl('order', ['url' => $order->url], true));
        $this->design->assign('serviceUrl', Router::generateUrl('OkayCMS_WayForPay_callback', [], true));
        $this->design->assign('merchantTransactionSecureType', 'AUTO');
        $this->design->assign('merchantSignature', $this->generateHash($settings));

        list($firstName, $lastName) = $this->separateFullNameOnFirstNameAndLastName($order->name);
        $this->design->assign('clientFirstName', $firstName);
        $this->design->assign('clientLastName', $lastName);
        $this->design->assign('clientEmail', $order->email);
        $this->design->assign('clientPhone', $this->formatPhone($order->phone));
        //$this->design->assign('clientCity', $order->location);
        $this->design->assign('clientAddress', $order->address);

        $currentLangId    = (int) $this->languages->getLangId();
        $currentLangLabel = $languagesEntity->get($currentLangId)->label;
        $this->design->assign('language', $currentLangLabel);

        return $this->design->fetch('form.tpl');
    }

    private function separateFullNameOnFirstNameAndLastName($fullName)
    {
        $parts = explode(' ', $fullName);
        $firstName = isset($parts[0]) ? $parts[0] : '';
        $lastName  = isset($parts[1]) ? $parts[1] : '';
        return [$firstName, $lastName];
    }

    private function getPurchaseNames($purchases)
    {
        $purchasesNames = [];

        foreach($purchases as $purchase) {
            $purchasesNames[] = $purchase->product_name.' '.$purchase->variant_name;
        }

        return $purchasesNames;
    }

    private function getPurchasePrices($purchases, $currencyId)
    {
        $purchasesPrices = [];

        foreach($purchases as $purchase) {
            $purchasesPrices[] = round($this->money->convert($purchase->price, $currencyId, false), 2);
        }

        return $purchasesPrices;
    }

    private function getPurchaseCount($purchases)
    {
        $purchasesCount = [];

        foreach($purchases as $purchase) {
            $purchasesCount[] = $purchase->amount;
        }

        return $purchasesCount;
    }

    private function generateHash($settings)
    {
        $keysForSignature = [
            'merchantAccount',
            'merchantDomainName',
            'orderReference',
            'orderDate',
            'amount',
            'currency',
            'productName',
            'productCount',
            'productPrice'
        ];

        $hash = [];
        foreach ($keysForSignature as $dataKey) {
            $variableDataKey = $this->design->getVar($dataKey);
            if (empty($variableDataKey)) {
                continue;
            }

            if (is_array($variableDataKey)) {
                foreach ($variableDataKey as $v) {
                    $hash[] = $v;
                }
                continue;
            }

            $hash[] = $variableDataKey;
        }
        $hash = implode(';', $hash);
        return hash_hmac('md5', $hash, $settings['wayforpay_secretkey']);
    }

    private function formatPhone($phone)
    {
        $phone = str_replace(['+', ' ', '(', ')'], ['','','',''], $phone);

        if(strlen($phone) == 10){
            return '38'.$phone;
        }

        if(strlen($phone) == 11){
            return '3'.$phone;
        }

        return $phone;
    }
}