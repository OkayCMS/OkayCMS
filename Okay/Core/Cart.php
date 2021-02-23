<?php


namespace Okay\Core;


use Okay\Core\Classes\Discount;
use Okay\Core\Classes\Purchase;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\UserCartItemsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\CouponsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\UsersEntity;
use Okay\Helpers\MainHelper;
use Okay\Helpers\DiscountsHelper;
use Okay\Helpers\ProductsHelper;
use Okay\Helpers\MoneyHelper;

class Cart
{
    /** @var Settings */
    private $settings;

    /** @var ProductsHelper */
    private $productsHelper;

    /** @var MoneyHelper */
    private $moneyHelper;

    /** @var Discounts */
    private $discountsCore;

    /** @var DiscountsHelper */
    private $discountsHelper;

    /** @var EntityFactory */
    private $entityFactory;

    /** @var MainHelper */
    private $mainHelper;


    /** @var ProductsEntity */
    private $productsEntity;

    /** @var VariantsEntity */
    private $variantsEntity;

    /** @var CouponsEntity */
    private $couponsEntity;

    /** @var ImagesEntity */
    private $imagesEntity;

    /** @var UsersEntity */
    private $usersEntity;

    /** @var UserCartItemsEntity */
    private $userCartItemsEntity;


    /** @var array
     * Cart purchases
     */
    public $purchases = [];

    /**
     * @var int
     * Price before all discounts
     */
    public $basic_total_price = 0;

    /**
     * @var int
     * Price after purchase discounts, but before cart discounts
     */
    //TODO Discount undiscounted_total_price без доставки, хотя total_price с доставкой
    public $undiscounted_total_price = 0;

    /**
     * @var int
     * Price after all discounts
     */
    public $total_price = 0;

    /**
     * @var int
     * Amount of purchased items
     */
    public $total_products  = 0;

    /**
     * @var array
     * All available discounts of the cart
     */
    public $availableDiscounts = [];

    /**
     * @var array
     * All applied discounts of the cart
     */
    public $discounts = [];

    /**
     * @var bool
     * Whether the cart is currently empty
     */
    public $isEmpty = true;

    public function __construct(
        EntityFactory   $entityFactory,
        Settings        $settings,
        ProductsHelper  $productsHelper,
        MoneyHelper     $moneyHelper,
        MainHelper      $mainHelper,
        Discounts       $discountsCore,
        DiscountsHelper $discountsHelper
    ) {
        $this->settings        = $settings;
        $this->productsHelper  = $productsHelper;
        $this->entityFactory   = $entityFactory;
        $this->productsHelper  = $productsHelper;
        $this->moneyHelper     = $moneyHelper;
        $this->mainHelper      = $mainHelper;
        $this->discountsCore   = $discountsCore;
        $this->discountsHelper = $discountsHelper;

        $this->productsEntity      = $entityFactory->get(ProductsEntity::class);
        $this->variantsEntity      = $entityFactory->get(VariantsEntity::class);
        $this->couponsEntity       = $entityFactory->get(CouponsEntity::class);
        $this->imagesEntity        = $entityFactory->get(ImagesEntity::class);
        $this->usersEntity         = $entityFactory->get(UsersEntity::class);
        $this->userCartItemsEntity = $entityFactory->get(UserCartItemsEntity::class);
    }

    public function init()
    {
        if (empty($_SESSION['user_id'])) {
            if (isset($_SESSION['shopping_cart'])) {
                $this->getPurchases($_SESSION['shopping_cart']);
            } else {
                $this->getPurchases([]);
            }
        }
    }

    /**
     * Get purchases and set them into cart
     *
     * @param array $purchasesVariants
     * @return mixed|void|null
     * @throws \Exception
     */
    public function getPurchases(array $purchasesVariants)
    {
        $purchases = [];
        if (!empty($purchasesVariants)) {
            $variants = $this->variantsEntity->mappedBy('id')->find(['id' => $this->getVariantsIdsByCart($purchasesVariants)]);
            if (!empty($variants)) {
                $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);
                $products = $this->getProductsByVariants($variants);
                $products = $this->productsHelper->attachImages($products);
                $items = $this->buildItemsByVariants($variants, $purchasesVariants);
                foreach($items as $variantId=>$item) {
                    if (!empty($products[$item->variant->product_id])) {
                        $purchase = new Purchase();
                        $purchase->setProduct($products[$item->variant->product_id]);
                        $purchase->setVariant($item->variant);
                        $purchase->setAmount($item->amount);

                        $purchases[] = $purchase;
                    }
                }
            }
        }
        $this->purchases = ExtenderFacade::execute(__METHOD__, $purchases, func_get_args());
        $this->updateTotals();
    }

    public function get()
    {
        return ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * Add item into the session-cart and the database-cart
     *
     * @param string|int $variantId
     * @param int $amount
     */
    public function addItem($variantId, $amount = 1)
    {
        if (!isset($_SESSION['shopping_cart'][$variantId])) {
            $variant = $this->variantsEntity->get(intval($variantId));
            if (!empty($variant) && ($variant->stock > 0 || $this->settings->get('is_preorder'))) {
                $amount = max(1, $amount);
                $amount = min($amount, ($variant->stock > 0 ? $variant->stock : min($this->settings->get('max_order_amount'), $amount)));
                $_SESSION['shopping_cart'][$variantId] = intval($amount);
                $this->addPurchase($variantId, $amount);
                if ($user = $this->mainHelper->getCurrentUser()) {
                    $this->userCartItemsEntity->updateAmount($user->id, $variantId, $amount);
                }
            }
        } else {
            $amount = max(1, $amount + $_SESSION['shopping_cart'][$variantId]);
            $this->updateItem($variantId, $amount);
        }

        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * Update item in the session-cart and the database-cart
     *
     * @param string|int $variantId
     * @param int $amount
     * @throws \Exception
     */
    public function updateItem($variantId, $amount = 1)
    {
        if (isset($_SESSION['shopping_cart'][$variantId])) {
            $variant = $this->variantsEntity->get(intval($variantId));
            if (!empty($variant) && ($variant->stock > 0 || $this->settings->get('is_preorder'))) {
                $amount = max(1, $amount);
                $amount = min($amount, ($variant->stock > 0 ? $variant->stock : min($this->settings->get('max_order_amount'), $amount)));
                $_SESSION['shopping_cart'][$variantId] = intval($amount);
                $this->updatePurchase($variantId, $amount);
                if ($user = $this->mainHelper->getCurrentUser()) {
                    $this->userCartItemsEntity->updateAmount($user->id, $variantId, $amount);
                }
            }
        } else {
            $this->addItem($variantId, $amount);
        }
    }

    /**
     * Delete item from the session-cart and the database-cart
     *
     * @param $variantId
     * @throws \Exception
     */
    public function deleteItem($variantId)
    {
        unset($_SESSION['shopping_cart'][$variantId]);
        if ($user = $this->mainHelper->getCurrentUser()) {
            $this->userCartItemsEntity->deleteByVariantId($user->id, $variantId);
        }
        $this->deletePurchase($variantId);

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Clear the session-cart, the database-cart and the cart
     */
    public function clear()
    {
        if ($user = $this->mainHelper->getCurrentUser()) {
            $this->userCartItemsEntity->deleteByVariantId($user->id, array_keys($_SESSION['shopping_cart']));
        }

        unset($_SESSION['shopping_cart']);
        unset($_SESSION['coupon_code']);
        $this->purchases = [];
        $this->updateTotals();
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * Add purchase into the cart
     *
     * @param string|int $variantId
     * @param int $amount
     */
    private function addPurchase($variantId, $amount = 1)
    {
        $variant = $this->variantsEntity->findOne(['id' => $variantId]);
        $variant = $this->moneyHelper->convertVariantPriceToMainCurrency($variant);
        if (empty($variant)) {
            ExtenderFacade::execute(__METHOD__, false, func_get_args());
        } else {
            $product = $this->productsEntity->findOne(['id' => $variant->product_id]);
            $product = $this->productsHelper->attachImages([$product->id => $product])[$product->id];

            $purchase = new Purchase();
            $purchase->setProduct($product);
            $purchase->setVariant($variant);
            $purchase->setAmount($amount);
            $this->isEmpty = false;

            $purchase = ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
            $this->purchases[] = $purchase;
            $this->updateTotals();
        }
    }

    /**
     * Update purchase in the cart
     *
     * @param string|int $variantId
     * @param int $amount
     */
    private function updatePurchase($variantId, $amount)
    {
        foreach ($this->purchases as &$purchase) {
            if ($purchase->variant->id == $variantId) {
                $purchase->setAmount($amount);
                $purchase = ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
                $this->updateTotals();
                return;
            }
        }
        ExtenderFacade::execute(__METHOD__, false, func_get_args());
    }

    /**
     * Delete purchase from the cart
     *
     * @param string|int $variantId
     */
    private function deletePurchase($variantId)
    {
        foreach ($this->purchases as $i => $purchase) {
            if ($purchase->variant->id == $variantId) {
                ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
                unset($this->purchases[$i]);
                $this->updateTotals();
                return;
            }
        }
        ExtenderFacade::execute(__METHOD__, false, func_get_args());
    }

    /**
     * Add coupon code in the session
     *
     * @param string $couponCode
     */
    public function applyCoupon($couponCode)
    {
        $coupon = $this->couponsEntity->get((string) $couponCode);
        if($coupon && $coupon->valid) {
            $_SESSION['coupon_code'] = $coupon->code;
        } else {
            unset($_SESSION['coupon_code']);
        }
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
        $this->updateTotals();
    }

    /**
     * Update cart's total fields
     */
    public function updateTotals()
    {
        $this->basic_total_price = 0;
        $this->total_products = 0;
        $this->isEmpty = true;
        if (!empty($this->purchases)) {
            foreach ($this->purchases as $purchase) {
                $this->basic_total_price += $purchase->meta->undiscounted_total_price;
                $this->total_products    += $purchase->amount;
            }
            $this->isEmpty = false;
        }
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
        $this->attachDiscounts();
        $this->applyDiscounts();
    }

    /**
     * Attach discounts to cart
     * Modules should add their own purchase and cart discounts by extending this method
     */
    private function attachDiscounts()
    {
        $this->availableDiscounts = [];
        if (!$this->isEmpty) {
            $this->attachCouponDiscount();
            $this->attachUserDiscount();
        }
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    private function attachCouponDiscount()
    {
        if (!empty($_SESSION['coupon_code'])) {
            $coupon = $this->couponsEntity->get($_SESSION['coupon_code']);
            if ($coupon && $coupon->valid) {
                if ($this->basic_total_price >= $coupon->min_order_price) {
                    $discount = new Discount();
                    $discount->sign = 'ok_coup';
                    $discount->langParts['coupon'] = $coupon->code;
                    if ($coupon->type == 'absolute') {
                        $discount->type = 'absolute';
                        $discount->value = $coupon->value;
                    } else {
                        $discount->type = 'percent';
                        $discount->value = $coupon->value;
                    }
                    $discount = ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
                    $this->availableDiscounts['ok_coup'] = $discount;
                }
            } else {
                unset($_SESSION['coupon_code']);
            }
        }
    }

    private function attachUserDiscount()
    {
        if (isset($_SESSION['user_id']) && ($user = $this->usersEntity->get(intval($_SESSION['user_id']))) && $user->discount) {
            $discount = new Discount();
            $discount->sign = 'ok_gr';
            $discount->type = 'percent';
            $discount->value = $user->discount;
            $discount->langParts['user_group'] = $user->group_name;
            $discount = ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
            $this->availableDiscounts['ok_gr'] = $discount;
        }
    }

    /**
     * Apply and calculate all registered and available discounts in cart and purchases
     */
    private function applyDiscounts()
    {
        if (!$this->isEmpty) {
            $this->applyPurchasesDiscounts();
            $this->discounts = [];
            $this->total_price = $this->undiscounted_total_price;
            $sets = $this->discountsHelper->getCartSets();
            if (!empty($this->availableDiscounts) && !empty($sets)) {
                foreach ($sets as $set) {
                    if ($signs = $this->discountsHelper->parseSet($set)) {
                        if (empty($signs) || !isset($signs['cart']) || (isset($signs['cart']) && !$this->checkAvailableDiscounts($signs['cart']))) {
                            continue;
                        }
                        foreach ($this->purchases as $purchase) {
                            /** @var $purchase Purchase */
                            if (isset($signs['purchase']) && !$purchase->checkAvailableDiscounts($signs['purchase'])) {
                                continue 2;
                            }
                        }
                        $discounts = $this->discountsHelper->prepareDiscounts($signs['cart'], $this->availableDiscounts);
                        list($this->discounts, $this->total_price) = $this->discountsHelper->calculateDiscounts($discounts, $this->undiscounted_total_price);
                        break;
                    }
                }
            }
        }
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    private function applyPurchasesDiscounts() {
        $this->undiscounted_total_price = 0;
        foreach ($this->purchases as $purchase) {
            /** @var $purchase Purchase */
            $purchase->applyDiscounts($this);
            $this->undiscounted_total_price += $purchase->meta->total_price;
        }
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * Checks the availability of a discount for each registered sign.
     *
     * @param array $signs
     * @return bool
     */
    public function checkAvailableDiscounts($signs)
    {
        $valid = false;
        if (!empty($signs)) {
            foreach ($signs as $sign) {
                if (isset($this->availableDiscounts[$sign->sign])) {
                    $valid = true;
                } else if (!$sign->partial) {
                    $valid = false;
                    break;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $valid, func_get_args());
    }

    /**
     * @param array $sessionCart
     * @return array
     */
    private function getVariantsIdsByCart(array $sessionCart)
    {
        return ExtenderFacade::execute(__METHOD__, array_keys($sessionCart), func_get_args());
    }

    /**
     * @param array $variants
     * @return array
     * @throws \Exception
     */
    private function getProductsByVariants(array $variants)
    {
        $productsIds = $this->getProductsIdsByVariants($variants);
        $products = $this->productsEntity->mappedBy('id')->find([
            'id'    => $productsIds,
            'limit' => count($productsIds)
        ]);

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    /**
     * @param array $variants
     * @param array $purchasesVariants
     * @return array
     */
    private function buildItemsByVariants(array $variants, array $purchasesVariants)
    {
        $items = [];
        if (empty($variants)) {
            return $items;
        }

        foreach ($purchasesVariants as $variantId => $amount) {
            if (isset($variants[$variantId])) {
                $item = new \stdClass;
                $item->variant = $variants[$variantId];
                $item->amount = $amount;

                $items[$variantId] = $item;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $items, func_get_args());
    }

    /**
     * @param array $variants
     * @return array
     */
    private function getProductsIdsByVariants(array $variants)
    {
        $productsIds = [];
        foreach($variants as $variant) {
            $productsIds[] = $variant->product_id;
        }

        return ExtenderFacade::execute(__METHOD__, $productsIds, func_get_args());
    }
}