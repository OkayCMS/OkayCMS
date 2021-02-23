<?php


namespace Okay\Core\Classes;


use Okay\Core\Cart;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\ServiceLocator;
use Okay\Helpers\DiscountsHelper;

class Purchase
{
    /**
     * @var DiscountsHelper
     */
    public $discountsHelper;


    /**
     * @var object
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
     * @var object
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
     * @var integer
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
     * @var array
     * All available discounts of the purchase
     */
    public $availableDiscounts = [];

    /**
     * @var array
     * All applied discounts of the purchase
     */
    public $discounts = [];

    /**
     * @var object
     * Purchase metadata
     */
    public $meta;

    public function __construct(
    ) {
        $this->meta = new \stdClass();
        $SL = ServiceLocator::getInstance();
        $this->discountsHelper = $SL->getService(DiscountsHelper::class);
    }

    /**
     * @param object $product
     */
    public function setProduct($product)
    {
        $this->product      = $product;
        $this->product_id   = $product->id;
        $this->product_name = $product->name;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
    }

    /**
     * @param object $variant
     */
    public function setVariant($variant)
    {
        $this->variant            = $variant;
        $this->variant_id         = $variant->id;
        $this->variant_name       = $variant->name;
        $this->undiscounted_price = $variant->price;
        $this->sku                = $variant->sku;
        $this->units              = $variant->units;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
        $this->updateTotals();
    }

    /**
     * @param string|int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        ExtenderFacade::execute(__METHOD__, $this, func_get_args());
        $this->updateTotals();
    }

    /**
     * Update purchase's total fields
     */
    public function updateTotals()
    {
        $undiscountedPrice = 0;
        if (isset($this->amount) && isset($this->undiscounted_price))
            $undiscountedPrice = $this->amount * $this->undiscounted_price;
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
        $this->price = $this->undiscounted_price;
        $this->meta->total_price = $this->meta->undiscounted_total_price;
        $sets = $this->discountsHelper->getPurchaseSets();
        if (!empty($this->availableDiscounts) && !empty($sets)) {
            foreach ($sets as $set) {
                if ($signs = $this->discountsHelper->parseSet($set)) {
                    if (empty($signs) ||
                        (isset($signs['cart']) && !$cart->checkAvailableDiscounts($signs['cart'])) ||
                        (isset($signs['purchase']) && !$this->checkAvailableDiscounts($signs['purchase']))) {
                        continue;
                    }
                    $discounts = $this->discountsHelper->prepareDiscounts($signs['purchase'], $this->availableDiscounts);
                    list($this->discounts, $this->price) = $this->discountsHelper->calculateDiscounts($discounts, $this->undiscounted_price);
                    $this->meta->total_price = $this->price * $this->amount;
                    break;
                }
            }
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
     * @param string|int $orderId
     * @return object
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