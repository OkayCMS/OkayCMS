<?php

namespace Okay\Core\Classes;

use Okay\Core\Cart;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\ServiceLocator;
use Okay\Helpers\DiscountsHelper;

/**
 * @phpstan-type PurchaseProductRow object{id: int|string, name: string}&\stdClass
 * @phpstan-type PurchaseVariantRow object{id: int|string, name: string|null, price: int|float|string, sku: string|null, units: string|null}&\stdClass
 * @phpstan-type PurchaseMeta object{undiscounted_total_price?: int|float, total_price?: int|float}&\stdClass
 * @phpstan-type DiscountSignRow object{sign: string, partial?: bool}&\stdClass
 */
class Purchase
{
    /**
     * @var DiscountsHelper
     */
    public $discountsHelper;


    /**
     * @var PurchaseProductRow
     * Purchased product
     */
    public $product;

    /**
     * @var string|int
     * Id of purchased product
     * Will be deprecated in future releases
     */
    public $product_id;

    /**
     * @var string
     * Name of purchased product
     */
    public $product_name;

    /**
     * @var PurchaseVariantRow
     * Purchased variant
     */
    public $variant;

    /**
     * @var string|int
     * Id of purchased variant
     * Will be deprecated in future releases
     */
    public $variant_id;

    /**
     * @var string
     * Name of purchased variant
     */
    public $variant_name;

    /**
     * @var integer
     * Amount of purchased items
     */
    public $amount;

    /**
     * @var string|int|float|null
     * Price before all discounts
     */
    public $undiscounted_price;

    /**
     * @var string|float
     * Price of purchased item
     */
    public $price;

    /**
     * @var string
     * Sku of purchased item
     */
    public $sku;

    /**
     * @var string
     * Measurement units of the purchased item
     */
    public $units;

    /**
     * @var array<string, Discount>
     * All available discounts of the purchase
     */
    public $availableDiscounts = [];

    /**
     * @var array<int, Discount>
     * All applied discounts of the purchase
     */
    public $discounts = [];

    /**
     * @var PurchaseMeta
     * Purchase metadata
     */
    public $meta;

    public function __construct()
    {
        $meta = new \stdClass();
        /** @var PurchaseMeta $meta */
        $this->meta = $meta;
        $SL = ServiceLocator::getInstance();
        $this->discountsHelper = $SL->getService(DiscountsHelper::class);
    }

    /**
     * @param PurchaseProductRow $product
     */
    public function setProduct($product)
    {
        $this->product      = $product;
        $this->product_id   = $product->id;
        $this->product_name = $product->name;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * @param PurchaseVariantRow $variant
     */
    public function setVariant($variant)
    {
        $this->variant            = $variant;
        $this->variant_id         = $variant->id;
        $this->variant_name       = $variant->name ?? '';
        $this->undiscounted_price = $variant->price;
        $this->sku                = $variant->sku ?? '';
        $this->units              = $variant->units ?? '';
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
        $this->updateTotals();
    }

    /**
     * @param string|int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = (int) $amount;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
        $this->updateTotals();
    }

    /**
     * Update purchase's total fields
     */
    public function updateTotals()
    {
        $undiscountedPrice = 0;
        if (isset($this->amount) && isset($this->undiscounted_price)) {
            $undiscountedPrice = $this->amount * (float) $this->undiscounted_price;
        }
        $undiscountedPrice = ($undiscountedPrice < 0) ? $undiscountedPrice = 0 : $undiscountedPrice;
        $this->meta->undiscounted_total_price = $undiscountedPrice;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * This method cannot be executed without cart
     *
     * @param Cart $cart
     */
    public function applyDiscounts($cart)
    {
        $this->price = $this->undiscounted_price ?? 0;
        $this->meta->total_price = $this->meta->undiscounted_total_price;
        $sets = $this->discountsHelper->getPurchaseSets();
        $this->discounts = [];
        if (!empty($this->availableDiscounts) && !empty($sets)) {
            foreach ($sets as $set) {
                if ($signs = $this->discountsHelper->parseSet($set)) {
                    if (
                        empty($signs) ||
                        !isset($signs['purchase']) ||
                        (isset($signs['cart']) && !$cart->checkAvailableDiscounts($signs['cart'])) ||
                        !$this->checkAvailableDiscounts($signs['purchase'])
                    ) {
                        continue;
                    }
                    $discounts = $this->discountsHelper->prepareDiscounts($signs['purchase'], $this->availableDiscounts);
                    list($this->discounts, $this->price) = $this->discountsHelper->calculateDiscounts($discounts, (float) $this->undiscounted_price);
                    $this->meta->total_price = $this->price * $this->amount;
                    break;
                }
            }
        }

        $this->collectAppliedTotalDiscount($cart);

        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * Сollects all discounts applied to purchases to the cart
     * @param Cart $cart
     * @return null
     */
    public function collectAppliedTotalDiscount($cart)
    {
        foreach ($this->discounts as $discount) {
            if (!isset($cart->total_purchases_discounts[$discount->sign])) {
                $discountForTotal = (object)(array)$discount;
                $discountForTotal->absoluteDiscount = (float) $discountForTotal->absoluteDiscount * $this->amount;
                $discountForTotal->priceBeforeDiscount = (float) $discountForTotal->priceBeforeDiscount * $this->amount;
                $discountForTotal->priceAfterDiscount = (float) $discountForTotal->priceAfterDiscount * $this->amount;
                $discountForTotal->percentDiscount = round($discountForTotal->absoluteDiscount / $discountForTotal->priceBeforeDiscount * 100, 2);
                /** @var object{absoluteDiscount: int|float, priceBeforeDiscount: int|float, priceAfterDiscount: int|float, percentDiscount: int|float}&\stdClass $discountForTotal */
                $cart->total_purchases_discounts[$discount->sign] = $discountForTotal;
            } else {
                $cart->total_purchases_discounts[$discount->sign]->absoluteDiscount += (float) $discount->absoluteDiscount * $this->amount;
                $cart->total_purchases_discounts[$discount->sign]->priceBeforeDiscount += (float) $discount->priceBeforeDiscount * $this->amount;
                $cart->total_purchases_discounts[$discount->sign]->priceAfterDiscount += (float) $discount->priceAfterDiscount * $this->amount;
                $cart->total_purchases_discounts[$discount->sign]->percentDiscount = round($cart->total_purchases_discounts[$discount->sign]->absoluteDiscount / $cart->total_purchases_discounts[$discount->sign]->priceBeforeDiscount * 100, 2);
            }
        }
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
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
     * @param string|int $orderId
     * @return object{id?: string|int|null}&\stdClass
     */
    public function getForDB($orderId)
    {
        $purchase = (object) [
            'product_id'         => $this->product_id,
            'product_name'       => $this->product_name,
            'variant_id'         => $this->variant_id,
            'amount'             => $this->amount,
            'undiscounted_price' => $this->undiscounted_price,
            'price'              => $this->price,
            'sku'                => $this->sku,
            'units'              => $this->units,
            'order_id'           => $orderId
        ];

        return ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }
}
