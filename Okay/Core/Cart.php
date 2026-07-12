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

/**
 * @phpstan-type CartProductRow object{id: int|string, url: string, slug_url: string, name: string, main_image_id?: int|string|null, brand_id?: int|string|null, main_category_id?: int|string|null, visible?: mixed, image?: object{product_id: int|string}&\stdClass, images?: list<object{product_id: int|string}&\stdClass>}&\stdClass
 * @phpstan-type CartVariantRow object{id: int|string, product_id: int|string, price: int|float|string, currency_id: int|string|null, compare_price?: int|float|string|null, name: string|null, sku: string|null, units: string|null, stock: int|float|string|null, stock_raw?: int|null, available_to_order?: bool, order_amount_limit?: int|null}&\stdClass
 * @phpstan-type CartItemRow object{variant: CartVariantRow, amount: int}&\stdClass
 * @phpstan-type DiscountSignRow object{sign: string, partial?: bool}&\stdClass
 */
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


    /** @var Purchase[]
     */
    public array $purchases = [];

    /**
     * @var int|float
     * Price before all discounts
     */
    public $basic_total_price = 0;

    /**
     * @var int|float
     * Price after purchase discounts, but before cart discounts
     */
    //TODO Discount undiscounted_total_price без доставки, хотя total_price с доставкой
    public $undiscounted_total_price = 0;

    /**
     * @var int|float
     * Price after all discounts
     */
    public $total_price = 0;

    /**
     * @var int
     * Amount of purchased items
     */
    public $total_products  = 0;

    /**
     * @var array<string, Discount>
     * All available discounts of the cart
     */
    public $availableDiscounts = [];

    /**
     * @var array<int, Discount>
     * All applied discounts of the cart
     */
    public $discounts = [];

    /**
     * @var bool
     * Whether the cart is currently empty
     */
    public $isEmpty = true;

    /**
     * @var array<string, object{absoluteDiscount: int|float, priceBeforeDiscount: int|float, priceAfterDiscount: int|float, percentDiscount: int|float}&\stdClass>
     * Total sum for all applied discounts for purchases
     */
    public $total_purchases_discounts = [];

    /**
     * @var array<int, object{id?: string|int|null}&\stdClass>
     * Purchases prepared for database insertion
     */
    public array $purchasesToDB = [];

    /**
     * @var array<int, object{id?: string|int|null}&\stdClass>
     * Discounts prepared for database insertion
     */
    public array $discountsToDB = [];

    /**
     * @var array<int, array<int|string, array<string, string>>>
     * Language-specific discounts prepared for database insertion
     */
    public array $langDiscountsToDB = [];

    public function __construct(
        EntityFactory $entityFactory,
        Settings $settings,
        ProductsHelper $productsHelper,
        MoneyHelper $moneyHelper,
        MainHelper $mainHelper,
        Discounts $discountsCore,
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
            if (!empty($_COOKIE['shopping_cart']) && is_array($items = json_decode($_COOKIE['shopping_cart'], true))) {
                foreach ($items as $key => $item) {
                    if (!empty($_SESSION['shopping_cart'][$key])) {
                        $_SESSION['shopping_cart'][$key] = max($item, $_SESSION['shopping_cart'][$key]);
                    } else {
                        $_SESSION['shopping_cart'][$key] = $item;
                    }
                }
                if (!empty($items)) {
                    $this->saveShoppingCart($_SESSION['shopping_cart'] ?? []);
                }
            }

            if (isset($_SESSION['shopping_cart'])) {
                $this->getPurchases($_SESSION['shopping_cart']);
            } else {
                $this->getPurchases([]);
            }
        }
    }

    /**
     * We save the data of the selected products in a cookie
     *
     * @param array<string|int, int|float> $items
     */
    public function saveShoppingCart(array $items)
    {
        if (!empty($items)) {
            $encodedItems = json_encode($items);
            if ($encodedItems === false) {
                return ExtenderFacade::execute(__METHOD__, $this, func_get_args());
            }
            $_COOKIE['shopping_cart'] = $encodedItems;
            $this->setShoppingCartCookie($encodedItems, time() + 30 * 24 * 3600);
        } elseif (empty($items)) {
            //  And delete the cookie variable when we empty the trash
            $this->deleteShoppingCartCookie();
        }

        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * Get purchases and set them into cart
     *
     * @param array<string|int, int|float> $purchasesVariants
     * @return mixed|void|null
     * @throws \Exception
     */
    public function getPurchases(array $purchasesVariants)
    {
        $purchases = [];
        if (!empty($purchasesVariants)) {
            $variants = $this->variantsEntity->mappedBy('id')->find(['id' => $this->getVariantsIdsByCart($purchasesVariants)]);
            if (!empty($variants)) {
                /** @var array<int|string, CartVariantRow> $variants */
                $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);
                /** @var array<int|string, CartVariantRow> $variants */
                $products = $this->getProductsByVariants($variants);
                $products = $this->productsHelper->attachImages($products);
                /** @var array<int|string, CartProductRow> $products */
                $items = $this->buildItemsByVariants($variants, $purchasesVariants);
                foreach ($items as $variantId => $item) {
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

    public function get(): self
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
            if (!empty($variant) && $this->isVariantOrderable($variant)) {
                /** @var CartVariantRow $variant */
                $amount = max(1, $amount);
                $amount = $this->limitAmountByVariantAvailability($amount, $variant);
                $_SESSION['shopping_cart'][$variantId] = intval($amount);
                if (!empty($_SESSION['shopping_cart'])) {
                    $this->saveShoppingCart($_SESSION['shopping_cart']);
                }
                $this->addPurchase($variantId, $amount);
                if ($user = $this->mainHelper->getCurrentUser()) {
                    $this->userCartItemsEntity->updateAmount($user->id, $variantId, $amount);
                }
            }
        } else {
            $amount = (int) max(1, $amount + $_SESSION['shopping_cart'][$variantId]);
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
            if (!empty($variant) && $this->isVariantOrderable($variant)) {
                /** @var CartVariantRow $variant */
                $amount = max(1, $amount);
                $amount = $this->limitAmountByVariantAvailability($amount, $variant);
                $_SESSION['shopping_cart'][$variantId] = intval($amount);
                if (!empty($_SESSION['shopping_cart'])) {
                    $this->saveShoppingCart($_SESSION['shopping_cart']);
                }
                $this->updatePurchase($variantId, $amount);
                if ($user = $this->mainHelper->getCurrentUser()) {
                    $this->userCartItemsEntity->updateAmount($user->id, $variantId, $amount);
                }
            }
        } else {
            $this->addItem($variantId, $amount);
        }

        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * @param CartVariantRow $variant
     */
    private function isVariantOrderable($variant): bool
    {
        if (property_exists($variant, 'available_to_order')) {
            return (bool) $variant->available_to_order;
        }

        return $variant->stock > 0 || (bool) $this->settings->get('is_preorder');
    }

    /**
     * @param CartVariantRow $variant
     */
    private function limitAmountByVariantAvailability(int $amount, $variant): int
    {
        if (property_exists($variant, 'order_amount_limit') && $variant->order_amount_limit !== null) {
            return (int) min($amount, (int) $variant->order_amount_limit);
        }

        if (!property_exists($variant, 'order_amount_limit')) {
            return (int) min(
                $amount,
                ($variant->stock > 0 ? $variant->stock : min($this->settings->get('max_order_amount'), $amount))
            );
        }

        return $amount;
    }

    /**
     * Delete item from the session-cart and the database-cart
     *
     * @param string|int $variantId
     * @throws \Exception
     */
    public function deleteItem($variantId)
    {
        unset($_SESSION['shopping_cart'][$variantId]);

        $this->saveShoppingCart($_SESSION['shopping_cart'] ?? []);

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

        //  delete the cookie variable when we empty the trash
        $this->deleteShoppingCartCookie();

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
            /** @var CartVariantRow $variant */
            $product = $this->productsEntity->findOne(['id' => $variant->product_id]);
            if ($product === false) {
                ExtenderFacade::execute(__METHOD__, false, func_get_args());

                return;
            }
            /** @var CartProductRow $productRow */
            $productRow = $product;
            $products = $this->productsHelper->attachImages([$productRow->id => $productRow]);
            /** @var array<int|string, CartProductRow> $products */
            $product = $products[$productRow->id];

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

    private function setShoppingCartCookie(string $value, int $expires): void
    {
        setcookie('shopping_cart', $value, [
            'expires' => $expires,
            'path' => '/',
            'secure' => $this->isHttpsRequest(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function deleteShoppingCartCookie(): void
    {
        if (isset($_COOKIE['shopping_cart'])) {
            unset($_COOKIE['shopping_cart']);
        }

        $this->setShoppingCartCookie('', time() - 3600);
    }

    private function isHttpsRequest(): bool
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
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
        if ($coupon && $coupon->valid) {
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
            $this->total_purchases_discounts = [];
            $this->applyPurchasesDiscounts();
            $this->discounts = [];
            $this->total_price = $this->undiscounted_total_price;
            $sets = $this->discountsHelper->getCartSets();
            if (!empty($this->availableDiscounts) && !empty($sets)) {
                foreach ($sets as $set) {
                    if ($signs = $this->discountsHelper->parseSet($set)) {
                        if (isset($signs['purchase'])) {
                            foreach ($this->purchases as $purchase) {
                                /** @var $purchase Purchase */
                                if ($purchase->checkAvailableDiscounts($signs['purchase'])) {
                                    break 2;
                                }
                            }
                        }

                        if (
                            empty($signs) ||
                            !isset($signs['cart']) ||
                            !$this->checkAvailableDiscounts($signs['cart'])
                        ) {
                            continue;
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

    private function applyPurchasesDiscounts()
    {
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
     * @param list<DiscountSignRow> $signs
     * @return bool
     */
    public function checkAvailableDiscounts($signs)
    {
        $valid = false;
        if (!empty($signs)) {
            foreach ($signs as $sign) {
                if (isset($this->availableDiscounts[$sign->sign])) {
                    $valid = true;
                } elseif (empty($sign->partial)) {
                    $valid = false;
                    break;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $valid, func_get_args());
    }

    /**
     * @param array<string|int, int|float> $sessionCart
     * @return array<int, string|int>
     */
    private function getVariantsIdsByCart(array $sessionCart)
    {
        return ExtenderFacade::execute(__METHOD__, array_keys($sessionCart), func_get_args());
    }

    /**
     * @param array<string|int, CartVariantRow> $variants
     * @return array<string|int, CartProductRow>
     * @throws \Exception
     */
    private function getProductsByVariants(array $variants)
    {
        $productsIds = $this->getProductsIdsByVariants($variants);
        $products = $this->productsEntity->mappedBy('id')->find([
            'id'    => $productsIds,
            'limit' => count($productsIds)
        ]);
        /** @var array<string|int, CartProductRow> $products */

        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }

    /**
     * @param array<string|int, CartVariantRow> $variants
     * @param array<string|int, int|float> $purchasesVariants
     * @return array<string|int, CartItemRow>
     */
    private function buildItemsByVariants(array $variants, array $purchasesVariants)
    {
        $items = [];
        if (empty($variants)) {
            return $items;
        }

        foreach ($purchasesVariants as $variantId => $amount) {
            if (isset($variants[$variantId])) {
                $item = new \stdClass();
                $item->variant = $variants[$variantId];
                $item->amount = (int) $amount;

                $items[$variantId] = $item;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $items, func_get_args());
    }

    /**
     * @param array<string|int, CartVariantRow> $variants
     * @return array<int, string|int>
     */
    private function getProductsIdsByVariants(array $variants)
    {
        $productsIds = [];
        foreach ($variants as $variant) {
            $productsIds[] = $variant->product_id;
        }

        return ExtenderFacade::execute(__METHOD__, $productsIds, func_get_args());
    }
}
