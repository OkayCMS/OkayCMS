<?php

namespace Okay\Entities;

use Okay\Helpers\MoneyHelper;
use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class PurchasesEntity extends Entity
{
    protected static $fields = [
        'id',
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_name',
        'undiscounted_price',
        'price',
        'amount',
        'sku',
        'units',
    ];

    protected static $defaultOrderFields = [
        'id',
    ];

    protected static $table = '__purchases';
    protected static $tableAlias = 'p';
    protected static $langTable;
    protected static $langObject;
    private static $order;
    private $useCache = false;

    public function useCache($useCache)
    {
        $this->useCache = (bool)$useCache;
        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    public function update($id, $purchase)
    {
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entity->get(VariantsEntity::class);

        $purchase = (object)$purchase;
        /** @var object{variant_id: int|string|null, amount: int|float|null}&\stdClass $purchase */
        $oldPurchase = $this->get($id);
        if (!$oldPurchase) {
            return false;
        }
        /** @var object{order_id: int|string, variant_id: int|string|null, amount: int|float} $oldPurchase */

        $order = $this->getOrder((int)$oldPurchase->order_id);
        if (empty($order->id)) {
            return false;
        }
        /** @var object{id: int|string, closed: mixed} $order */

        // Не допустить нехватки на складе
        if (!empty($purchase->variant_id)) {
            $variant = $variantsEntity->get($purchase->variant_id);
        }
        /** @var object|null $variant */
        $trackedStock = !empty($variant) ? $this->trackedStock($variant) : null;
        if (
            $order->closed
            && !$this->settings->get('is_preorder')
            && !empty($purchase->amount)
            && $trackedStock !== null
            && $this->variantStockShortageAmount($purchase, $oldPurchase) > $trackedStock
        ) {
            return false;
        }

        // Если заказ закрыт, нужно обновить склад при изменении покупки
        if ($order->closed && !empty($purchase->amount)) {
            if ($oldPurchase->variant_id != $purchase->variant_id) {
                if (!empty($oldPurchase->variant_id)) {
                    $update = $this->queryFactory->newUpdate();
                    $update->table('__variants')
                        ->set('stock', 'stock+:amount')
                        ->where('id=:id AND stock IS NOT NULL')
                        ->bindValues([
                            'amount' => $oldPurchase->amount,
                            'id' => $oldPurchase->variant_id,
                        ]);
                    $this->db->query($update);
                }
                if (!empty($purchase->variant_id)) {
                    $update = $this->queryFactory->newUpdate();
                    $update->table('__variants')
                        ->set('stock', 'stock-:amount')
                        ->where('id=:id AND stock IS NOT NULL')
                        ->bindValues([
                            'amount' => $purchase->amount,
                            'id' => $purchase->variant_id,
                        ]);
                    $this->db->query($update);
                }
            } elseif (!empty($purchase->variant_id)) {
                $update = $this->queryFactory->newUpdate();
                $update->table('__variants')
                    ->set('stock', 'stock+:amount')
                    ->where('id=:id AND stock IS NOT NULL')
                    ->bindValues([
                        'amount' => $oldPurchase->amount - $purchase->amount,
                        'id' => $purchase->variant_id,
                    ]);
                $this->db->query($update);
            }
        }

        return parent::update($id, $purchase);
    }

    public function add($purchase)
    {
        /** @var MoneyHelper $moneyHelper */
        $moneyHelper = $this->serviceLocator->getService(MoneyHelper::class);

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entity->get(ProductsEntity::class);

        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entity->get(VariantsEntity::class);

        $variant = (object)[];
        $purchase = (object)$purchase;
        /** @var object{id: int|string, product_id: int|string, infinity: mixed, stock: int|float, sku: mixed, name: mixed, price: int|float, units: mixed}&\stdClass $variant */
        /** @var object{order_id: int|string, variant_id: int|string|null, amount: int|float|null, product_id: int|string|null, product_name: mixed, sku: mixed, variant_name: mixed, undiscounted_price: mixed, price: int|float|null, units: mixed}&\stdClass $purchase */
        if (!empty($purchase->variant_id)) {
            $variant = $variantsEntity->get($purchase->variant_id);
            if (empty($variant)) {
                return false;
            }

            $variant = $moneyHelper->convertVariantPriceToMainCurrency($variant);
            /** @var object{id: int|string, product_id: int|string, infinity: mixed, stock: int|float, sku: mixed, name: mixed, price: int|float, units: mixed} $variant */

            /** @var object{product_id: int|string} $variantProduct */
            $variantProduct = $variant;
            $product = $productsEntity->get(intval($variantProduct->product_id));
            if (empty($product)) {
                ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
            }
            /** @var object{name: mixed} $product */
        }

        $order = $this->getOrder(intval($purchase->order_id));
        if (empty($order->id)) {
            ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }
        /** @var object{id: int|string, closed: mixed, total_price: int|float} $order */

        // Не допустить нехватки на складе
        /** @var object $variantStock */
        $variantStock = $variant;
        $trackedStock = $this->trackedStock($variantStock);
        if (
            $order->closed
            && !$this->settings->get('is_preorder')
            && !empty($purchase->amount)
            && $trackedStock !== null
            && $trackedStock < $purchase->amount
        ) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        if (!isset($purchase->product_id)) {
            $purchase->product_id = $variant->product_id;
        }

        if (!isset($purchase->product_name)  && !empty($product)) {
            $purchase->product_name = $product->name;
        }

        if (!isset($purchase->sku)) {
            $purchase->sku = $variant->sku;
        }

        if (!isset($purchase->variant_name)) {
            $purchase->variant_name = $variant->name;
        }

        if (!isset($purchase->undiscounted_price)) {
            $purchase->undiscounted_price = $variant->price;
        }

        if (!isset($purchase->price)) {
            $purchase->price = $variant->price;
        }

        if (!isset($purchase->amount)) {
            $purchase->amount = 1;
        }

        if (!isset($purchase->units)) {
            $purchase->units = $variant->units;
        }

        // Если заказ закрыт, нужно обновить склад при добавлении покупки
        if ($order->closed && !empty($purchase->amount) && !empty($variant->id)) {
            $update = $this->queryFactory->newUpdate();
            $update->table('__variants')
                ->set('stock', 'stock-:amount')
                ->where('id=:id AND stock IS NOT NULL')
                ->bindValues([
                    'amount' => $purchase->amount,
                    'id' => $variant->id,
                ]);
            $this->db->query($update);
        }

        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entity->get(OrdersEntity::class);
        $ordersEntity->update($order->id, [
            'total_price' => $order->total_price + $purchase->price
        ]);

        return parent::add($purchase);
    }

    private function trackedStock(object $variant): ?int
    {
        if (
            property_exists($variant, 'stock_is_tracked')
            && property_exists($variant, 'stock_raw')
            && $variant->stock_is_tracked
        ) {
            return (int) $variant->stock_raw;
        }

        if (property_exists($variant, 'infinity') && empty($variant->infinity) && property_exists($variant, 'stock')) {
            return (int) $variant->stock;
        }

        return null;
    }

    /**
     * @param object{variant_id: int|string|null, amount: int|float|null} $purchase
     * @param object{variant_id: int|string|null, amount: int|float} $oldPurchase
     */
    private function variantStockShortageAmount(object $purchase, object $oldPurchase): int
    {
        if ($purchase->variant_id != $oldPurchase->variant_id) {
            return (int) $purchase->amount;
        }

        return (int) $purchase->amount - (int) $oldPurchase->amount;
    }

    public function delete($ids)
    {
        $this->useCache(false);
        $ids = (array)$ids;
        if (!empty($ids)) {
            foreach ($ids as $id) {
                $purchase = $this->get($id);
                if (!$purchase) {
                    return false;
                }
                /** @var object{order_id: int|string, variant_id: int|string, amount: int|float} $purchase */

                $order = $this->getOrder(intval($purchase->order_id));
                if (empty($order->id)) {
                    return false;
                }
                /** @var object{id: int|string, closed: mixed} $order */

                // Если заказ закрыт, нужно обновить склад при изменении покупки
                if ($order->closed && !empty($purchase->amount)) {
                    $update = $this->queryFactory->newUpdate();
                    $update->table('__variants')
                        ->set('stock', 'stock+:amount')
                        ->where('id=:id AND stock IS NOT NULL')
                        ->bindValues([
                            'amount' => $purchase->amount,
                            'id' => $purchase->variant_id,
                        ]);

                    $this->db->query($update);
                }
            }
        }
        return parent::delete($ids);
    }

    private function getOrder($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entity->get(OrdersEntity::class);

        $order = $ordersEntity->get((int)$orderId);

        if ($this->useCache === false) {
            return $order;
        }

        if (empty(self::$order)) {
            self::$order = $order;
        }
        return self::$order;
    }
}
