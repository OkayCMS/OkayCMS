<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;
use Okay\Helpers\DiscountsHelper;
use Okay\Helpers\MoneyHelper;

class BackendPurchasesHelper
{
    /** @var MoneyHelper */
    private $moneyHelper;

    /** @var DiscountsHelper */
    private $discountsHelper;


    /** @var PurchasesEntity */
    private $purchasesEntity;

    /** @var VariantsEntity */
    private $variantsEntity;

    /** @var DiscountsEntity */
    private $discountsEntity;

    /** @var ProductsEntity */
    private $productsEntity;

    /** @var ImagesEntity */
    private $imagesEntity;

    public function __construct(
        EntityFactory   $entityFactory,
        MoneyHelper     $moneyHelper,
        DiscountsHelper $discountsHelper
    ) {
        $this->moneyHelper     = $moneyHelper;
        $this->discountsHelper = $discountsHelper;

        $this->purchasesEntity = $entityFactory->get(PurchasesEntity::class);
        $this->variantsEntity  = $entityFactory->get(VariantsEntity::class);
        $this->discountsEntity = $entityFactory->get(DiscountsEntity::class);
        $this->productsEntity  = $entityFactory->get(ProductsEntity::class);
        $this->imagesEntity    = $entityFactory->get(ImagesEntity::class);
    }

    public function getBeforeUpdate($orderId)
    {
        $purchases = $this->purchasesEntity->find(['order_id' => $orderId]);
        return ExtenderFacade::execute(__METHOD__, $purchases, func_get_args());
    }

    public function update($purchase)
    {
        $this->purchasesEntity->update($purchase->id, $purchase);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function prepareUpdate($order, $purchase)
    {
        $variant = $this->variantsEntity->findOne(['id' => $purchase->variant_id]);
        $discounts = $this->discountsEntity->order('position')->find([
            'entity' => 'purchase',
            'entity_id' => $purchase->id
        ]);

        $purchase->price = $purchase->undiscounted_price;
        if (!empty($discounts)) {
            foreach ($discounts as $discount) {
                switch ($discount->type) {
                    case 'absolute':
                        $purchase->price -= $discount->value;
                        break;

                    case 'percent':
                        if ($discount->from_last_discount) {
                            $purchase->price -= $purchase->price * ($discount->value / 100);
                        } else {
                            $purchase->price -= $purchase->undiscounted_price * ($discount->value / 100);
                        }
                        break;
                }
            }
        }

        if (!empty($variant)) {
            $purchase->variant_name = $variant->name;
            $purchase->sku = $variant->sku;
        }

        return ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }

    public function prepareAdd($order, $purchase)
    {
        $purchase->id = null;
        $purchase->order_id = $order->id;
        return ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }

    public function add($purchase)
    {
        $purchaseId = $this->purchasesEntity->add($purchase);
        return ExtenderFacade::execute(__METHOD__, $purchaseId, func_get_args());
    }

    public function delete($order, array $postedPurchasesIds)
    {
        if (empty($order) || empty($order->id)) {
            return ExtenderFacade::execute(__METHOD__, null, func_get_args());
        }

        foreach ($this->purchasesEntity->find(['order_id' => $order->id]) as $p) {
            if (!in_array($p->id, $postedPurchasesIds)) {
                $this->purchasesEntity->delete($p->id);
            }
        }
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findOrderPurchases($order)
    {
        if ($purchases = $this->purchasesEntity->mappedBy('id')->find(['order_id'=>$order->id])) {
            // Покупки
            $productsIds = [];
            $variantsIds = [];
            $imagesIds = [];
            foreach ($purchases as $purchase) {
                $productsIds[] = $purchase->product_id;
                $variantsIds[] = $purchase->variant_id;
            }

            $products = [];
            foreach ($this->productsEntity->find(['id'=>$productsIds, 'limit' => count($productsIds)]) as $p) {
                $products[$p->id] = $p;
                $imagesIds[] = $p->main_image_id;
            }

            if (!empty($imagesIds)) {
                $images = $this->imagesEntity->find(['id'=>$imagesIds]);
                foreach ($images as $image) {
                    if (isset($products[$image->product_id])) {
                        $products[$image->product_id]->image = $image;
                    }
                }
            }

            $variants = $this->variantsEntity->mappedBy('id')->find(['product_id'=>$productsIds]);
            $variants = $this->moneyHelper->convertVariantsPriceToMainCurrency($variants);

            foreach ($variants as $variant) {
                if (!empty($products[$variant->product_id])) {
                    $products[$variant->product_id]->variants[] = $variant;
                }
            }

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
                if(!empty($products[$purchase->product_id])) {
                    $purchase->product = $products[$purchase->product_id];
                }
                if (!empty($variants[$purchase->variant_id])) {
                    $purchase->variant = $variants[$purchase->variant_id];
                }
                if (isset($sortedDiscounts[$purchase->id])) {
                    list($purchase->discounts) = $this->discountsHelper->calculateDiscounts($this->discountsHelper->buildFromDB($sortedDiscounts[$purchase->id]), $purchase->undiscounted_price);
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $purchases, func_get_args());
    }
}