<?php


namespace Okay\Helpers;


use Okay\Core\Cart;
use Okay\Core\Classes\Discount;
use Okay\Core\Classes\Purchase;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Money;
use Okay\Core\Phone;
use Okay\Core\Router;
use Okay\Core\SmartyPlugins\Plugins\CheckoutPaymentForm;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;
use Psr\Log\LoggerInterface;

class CartHelper
{
    /** @var EntityFactory */
    private $entityFactory;

    /** @var Money */
    private $moneyCore;
    
    /** @var FrontTemplateConfig */
    private $frontTemplateConfig;
    
    /** @var LoggerInterface */
    private $logger;
    
    /** @var Design */
    private $design;

    /** @var Cart */
    private $cart;

    /** @var Languages */
    private $languagesCore;

    /** @var DiscountsHelper */
    private $discountsHelper;

    /** @var CheckoutPaymentForm */
    private $checkoutPaymentForm;

    public function __construct(
        EntityFactory       $entityFactory,
        Money               $moneyCore,
        FrontTemplateConfig $frontTemplateConfig,
        LoggerInterface     $logger,
        Design              $design,
        CheckoutPaymentForm $checkoutPaymentForm,
        Cart                $cart,
        Languages           $languagesCore,
        DiscountsHelper     $discountsHelper
    ) {
        $this->entityFactory       = $entityFactory;
        $this->moneyCore           = $moneyCore;
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->logger              = $logger;
        $this->design              = $design;
        $this->cart                = $cart;
        $this->languagesCore       = $languagesCore;
        $this->discountsHelper     = $discountsHelper;
        $this->checkoutPaymentForm = $checkoutPaymentForm;
    }

    public function getDefaultCartData($user)
    {
        $defaultData = [];
        if (!empty($user->id)) {

            /** @var OrdersEntity $ordersEntity */
            $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
            
            $lastOrder = $ordersEntity->findOne(['user_id'=>$user->id]);
            if ($lastOrder) {
                $defaultData['name'] = $lastOrder->name;
                $defaultData['last_name'] = $lastOrder->last_name;
                $defaultData['email'] = $lastOrder->email;
                $defaultData['phone'] = Phone::format($lastOrder->phone);
                $defaultData['address'] = $lastOrder->address;
            } else {
                $defaultData['name'] = $user->name;
                $defaultData['last_name'] = $user->last_name;
                $defaultData['email'] = $user->email;
                $defaultData['phone'] = Phone::format($user->phone);
                $defaultData['address'] = $user->address;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $defaultData, func_get_args());
    }

    public function getAjaxCartResult($cart, $currency, $paymentMethods, $deliveries, $action, $variantId, $amount = 0)
    {
        $this->design->assign('cart', $cart);

        $result = [];
        if ($cart->isEmpty === false) {
            $result['result'] = 1;
        } else {
            $result['result'] = 0;
            $result['content']       = $this->design->fetch('cart.tpl');
        }
            
        $result['deliveries_data'] = $deliveries;
        $result['payment_methods_data'] = $paymentMethods;

        if (is_file('design/' . $this->frontTemplateConfig->getTheme() . '/html/cart_coupon.tpl')) {
            $result['cart_coupon'] = $this->design->fetch('cart_coupon.tpl');
        } else {
            $this->logger->error('File "design/' . $this->frontTemplateConfig->getTheme() . '/html/cart_coupon.tpl" not found');
        }

        if (is_file('design/' . $this->frontTemplateConfig->getTheme() . '/html/cart_purchases.tpl')) {
            $result['cart_purchases'] = $this->design->fetch('cart_purchases.tpl');
        } else {
            $this->logger->error('File "design/' . $this->frontTemplateConfig->getTheme() . '/html/cart_purchases.tpl" not found');
        }

        if (is_file('design/' . $this->frontTemplateConfig->getTheme() . '/html/pop_up_cart.tpl')) {
            $result['pop_up_cart'] = $this->design->fetch('pop_up_cart.tpl');
        } else {
            $this->logger->error('File "design/' . $this->frontTemplateConfig->getTheme() . '/html/pop_up_cart.tpl" not found');
        }

        $result['cart_deliveries'] = 'DEPRECATED DATA';
        $result['currency_sign']   = $currency->sign;
        $result['total_price']     = $this->moneyCore->convert($cart->total_price, $currency->id);
        $result['total_products']  = $cart->total_products;

        if (is_file('design/' . $this->frontTemplateConfig->getTheme() . '/html/cart_informer.tpl')) {
            $result['cart_informer'] = $this->design->fetch('cart_informer.tpl');
        } else {
            $this->logger->error('File "design/' . $this->frontTemplateConfig->getTheme() . '/html/cart_informer.tpl" not found');
        }
        
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * @param Cart $cart
     * @param string|int $orderId
     * @return Cart
     */
    public function prepareCart($cart, $orderId)
    {
        $cart->purchasesToDB = [];
        /** @var Purchase $purchase */
        foreach ($cart->purchases as $i => $purchase)
            $cart->purchasesToDB[$i] = $purchase->getForDB($orderId);

        return ExtenderFacade::execute(__METHOD__, $cart, func_get_args());
    }


    /**
     * @param Cart $cart
     * @param string|int $orderId
     * @return Cart
     * @throws \Exception
     */
    public function cartToOrder($cart, $orderId)
    {
        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entityFactory->get(PurchasesEntity::class);
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        foreach($cart->purchasesToDB as $purchaseDB) {
            $purchaseDB->id = $purchasesEntity->add($purchaseDB);
        }
        $ordersEntity->update($orderId, [
            'total_price' => $cart->total_price,
        ]);

        return ExtenderFacade::execute(__METHOD__, $cart, func_get_args());
    }

    /**
     * @param Cart $cart
     * @return Cart
     */
    public function prepareDiscounts($cart, $orderId)
    {
        $cart->discountsToDB = [];
        $cart->langDiscountsToDB = [];
        /** @var Discount $discount */
        foreach ($cart->discounts as $discount) {
            list($discountToDB, $langDiscountToDB) = $this->discountsHelper->prepareForDB($discount, 'order', $orderId);
            $cart->discountsToDB[] = $discountToDB;
            $cart->langDiscountsToDB[] = $langDiscountToDB;
        }
        foreach ($cart->purchases as $i => $purchase) {
            foreach ($purchase->discounts as $discount) {
                list($discountToDB, $langDiscountToDB) = $this->discountsHelper->prepareForDB($discount, 'purchase', $cart->purchasesToDB[$i]->id);
                $cart->discountsToDB[] = $discountToDB;
                $cart->langDiscountsToDB[] = $langDiscountToDB;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $cart, func_get_args());
    }

    /**
     * @param Cart $cart
     * @throws \Exception
     */
    public function discountsToDB($cart)
    {
        /** @var DiscountsEntity $discountsEntity */
        $discountsEntity = $this->entityFactory->get(DiscountsEntity::class);
        $mainLanguage = $this->languagesCore->getMainLanguage();
        foreach ($cart->discountsToDB as $i => $discountDB) {
            $discountDB->id = $discountsEntity->add($discountDB);
            foreach ($cart->langDiscountsToDB[$i] as $langId => $langDiscountToDB) {
                $this->languagesCore->setLangId($langId);
                $discountsEntity->update($discountDB->id, $langDiscountToDB);
            }
        }
        $this->languagesCore->setLangId($mainLanguage->id);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getAjaxOrderContent($order)
    {
        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        $paymentMethod = $paymentsEntity->findOne(['id' => $order->payment_method_id]);
        if ($paymentMethod->auto_submit) {
            $paymentForm = $this->checkoutPaymentForm->run([
                'order_id' => $order->id,
                'module' => $paymentMethod->module
            ]);
            $content = [
                'auto_submit' => true,
                'url' => Router::generateUrl('order', ['url' => $order->url], true),
                'form' => $paymentForm
            ];
        } else {
            $content = [
                'auto_submit' => false,
                'url' => Router::generateUrl('order', ['url' => $order->url], true)
            ];
        }

        return ExtenderFacade::execute(__METHOD__, $content, func_get_args());
    }
}