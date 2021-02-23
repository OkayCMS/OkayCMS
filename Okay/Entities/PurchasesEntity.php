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
        $oldPurchase = $this->get($id);
        if (!$oldPurchase) {
            return false;
        }
        
        $order = $this->getOrder((int)$oldPurchase->order_id);
        if (empty($order->id)) {
            return false;
        }
        
        // Не допустить нехватки на складе
        if (!empty($purchase->variant_id)) {
            $variant = $variantsEntity->get($purchase->variant_id);
        }
        if ($order->closed && !empty($purchase->amount) && !empty($variant) && !$variant->infinity && $variant->stock<($purchase->amount-$oldPurchase->amount)) {
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
        if (!empty($purchase->variant_id)) {
            $variant = $variantsEntity->get($purchase->variant_id);
            if (empty($variant)) {
                return false;
            }
            
            $variant = $moneyHelper->convertVariantPriceToMainCurrency($variant);
            
            $product = $productsEntity->get(intval($variant->product_id));
            if (empty($product)) {
                ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
            }
        }
        
        $order = $this->getOrder(intval($purchase->order_id));
        if (empty($order->id)) {
            ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }
        
        // Не допустить нехватки на складе
        if ($order->closed && !empty($purchase->amount) && !$variant->infinity && $variant->stock<$purchase->amount) {
            ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }
        
        if (!isset($purchase->product_id) && isset($variant)) {
            $purchase->product_id = $variant->product_id;
        }
        
        if (!isset($purchase->product_name)  && !empty($product)) {
            $purchase->product_name = $product->name;
        }
        
        if (!isset($purchase->sku) && !empty($variant)) {
            $purchase->sku = $variant->sku;
        }
        
        if (!isset($purchase->variant_name) && !empty($variant)) {
            $purchase->variant_name = $variant->name;
        }

        if (!isset($purchase->undiscounted_price) && !empty($variant)) {
            $purchase->undiscounted_price = $variant->price;
        }
        
        if (!isset($purchase->price) && !empty($variant)) {
            $purchase->price = $variant->price;
        }
        
        if (!isset($purchase->amount)) {
            $purchase->amount = 1;
        }

        if (!isset($purchase->units) && !empty($variant)) {
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
    
                $order = $this->getOrder(intval($purchase->order_id));
                if (empty($order->id)) {
                    return false;
                }
    
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
