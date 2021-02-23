<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Phone;
use Okay\Core\UserReferer\UserReferer;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\UsersEntity;
use Okay\Entities\VariantsEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class OrdersHelper
{
    /** @var EntityFactory */
    private $entityFactory;

    /** @var ProductsHelper */
    private $productsHelper;

    /** @var MoneyHelper */
    private $moneyHelper;


    /** @var DiscountsEntity */
    private $discountsEntity;

    /** @var DiscountsHelper */
    private $discountsHelper;

    public function __construct(
        EntityFactory   $entityFactory,
        ProductsHelper  $productsHelper,
        MoneyHelper     $moneyHelper,
        DiscountsHelper $discountsHelper
    ) {
        $this->entityFactory   = $entityFactory;
        $this->productsHelper  = $productsHelper;
        $this->moneyHelper     = $moneyHelper;
        $this->discountsHelper = $discountsHelper;

        $this->discountsEntity = $entityFactory->get(DiscountsEntity::class);
    }

    /**
     * @param $order
     * Метод вызывается после оформления заказа, перед отправкой пользователя на страницу заказа и очисткой корзины.
     * Нужен чтобы модули могли расширять эту процедуру 
     */
    public function finalCreateOrderProcedure($order)
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function getOrderPaymentMethodsList($order)
    {
        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);
        
        $paymentMethods = $paymentsEntity->find([
            'delivery_id' => $order->delivery_id,
            'enabled' => 1,
        ]);

        return ExtenderFacade::execute(__METHOD__, $paymentMethods, func_get_args());
    }
    
    public function getOrderPurchasesList($orderId)
    {
        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entityFactory->get(PurchasesEntity::class);
        
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entityFactory->get(VariantsEntity::class);
        
        $purchases = $purchasesEntity->mappedBy('id')->find(['order_id'=>intval($orderId)]);
        if (!$purchases) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $productsIds = [];
        $variantsIds = [];
        foreach ($purchases as $purchase) {
            $productsIds[] = $purchase->product_id;
            $variantsIds[] = $purchase->variant_id;
        }

        $products = $productsEntity->mappedBy('id')->find(['id'=>$productsIds, 'limit' => count($productsIds)]);
        $products = $this->productsHelper->attachVariants($products, ['id'=>$variantsIds]);
        $products = $this->productsHelper->attachMainImages($products);
        $variants = $variantsEntity->mappedBy('id')->find(['id'=>$variantsIds]);
        $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);

        $discounts = $this->discountsEntity->find([
            'entity' => 'purchase',
            'entity_id' => array_keys($purchases)
        ]);

        $sortedDiscounts = [];
        if (!empty($discounts)) {
            foreach ($discounts as $discount) {
                $sortedDiscounts[$discount->entity_id][] = $discount;
            }
        }

        foreach ($purchases as $purchase) {
            if (!empty($products[$purchase->product_id])) {
                $purchase->product = $products[$purchase->product_id];
            }
            if (!empty($variants[$purchase->variant_id])) {
                $purchase->variant = $variants[$purchase->variant_id];
            }
            if (isset($sortedDiscounts[$purchase->id])) {
                list($purchase->discounts) = $this->discountsHelper->calculateDiscounts($this->discountsHelper->buildFromDB($sortedDiscounts[$purchase->id]), $purchase->undiscounted_price);
            }
        }

        return ExtenderFacade::execute(__METHOD__, $purchases, func_get_args());
    }

    public function prepareAdd($order)
    {
        if (!empty($order->phone)) {
            $order->phone = Phone::toSave($order->phone);
        }

        // Добавим источник, с которого пришел пользователь
        if ($referer = UserReferer::getUserReferer()) {
            $order->referer_channel = $referer['medium'];
            $order->referer_source = $referer['source'];
        }
        
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    public function add($order)
    {
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        
        $result = $ordersEntity->add($order);
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function attachUserIfLogin($order, $user)
    {
        if (!empty($user->id)) {
            $order->user_id = $user->id;
            
            // Если у пользователя телефон пустой, но в заказе указан, добавим этот телефон пользователю
            if (empty($user->phone && !empty($order->phone))) {
                /** @var UsersEntity $usersEntity */
                $usersEntity = $this->entityFactory->get(UsersEntity::class);
                $usersEntity->update($user->id, ['phone' => $order->phone]);
            }
            
        }

        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    public function getDiscounts($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        $discountsDB = $this->discountsEntity->order('position')->find([
            'entity' => 'order',
            'entity_id' => $orderId
        ]);
        $order = $ordersEntity->findOne(['id' => $orderId]);
        list($discounts) = $this->discountsHelper->calculateDiscounts($this->discountsHelper->buildFromDB($discountsDB), $order->undiscounted_total_price);

        return ExtenderFacade::execute(__METHOD__, $discounts, func_get_args());
    }
}