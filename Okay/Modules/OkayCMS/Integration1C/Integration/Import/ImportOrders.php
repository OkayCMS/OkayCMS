<?php

namespace Okay\Modules\OkayCMS\Integration1C\Integration\Import;


use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\PurchasesEntity;

class ImportOrders extends AbstractImport
{

    /**
     * @param string $xmlFile Full path to xml file
     * @return string
     */
    public function import($xmlFile)
    {

        /** @var OrderStatusEntity $ordersStatusesEntity */
        $ordersStatusesEntity = $this->integration1C->entityFactory->get(OrderStatusEntity::class);
        
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->integration1C->entityFactory->get(OrdersEntity::class);
        
        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->integration1C->entityFactory->get(PurchasesEntity::class);
        
        $xml = \simplexml_load_file($xmlFile);
        
        $ordersStatuses = [];
        foreach ($ordersStatusesEntity->find() as $s) {
            $ordersStatuses[$s->status_1c] = $s;
        }
        
        // Если никакой статус не отметили для новых, возьмем первый в списке
        if (!isset($ordersStatuses['new'])) {
            $ordersStatuses['new'] = reset($ordersStatuses);
        }
        
        foreach ($xml->Документ as $xmlOrder) {
            $order = new \stdClass();
            $order->status_id = 0;
            
            $order->id    = (int)$xmlOrder->Номер;
            $existedOrder = $ordersEntity->get($order->id);

            $order->date = (string)$xmlOrder->Дата.' '.$xmlOrder->Время;
            $order->name = (string)$xmlOrder->Контрагенты->Контрагент->Наименование;

            $accepted = false;
            $toDelete = false;
            if (isset($xmlOrder->ЗначенияРеквизитов->ЗначениеРеквизита)) {
                foreach ($xmlOrder->ЗначенияРеквизитов->ЗначениеРеквизита as $r) {
                    switch ($r->Наименование) {
                        case 'Проведен':
                            $accepted = ($r->Значение == 'true');
                            break;
                        case 'ПометкаУдаления':
                            $toDelete = ($r->Значение == 'true');
                            break;
                    }
                }
            }

            // Выставляем id нужного статуса
            if ($toDelete === true) {
                $order->status_id = $ordersStatuses['to_delete']->id;
            } elseif ($accepted === true) {
                $order->status_id = $ordersStatuses['accepted']->id;
            } elseif ($accepted === false) {
                $order->status_id = $ordersStatuses['new']->id;
            }

            if ($existedOrder) {
                $ordersEntity->update($order->id, $order);
            } else {
                $order->id = $ordersEntity->add($order);
            }

            $purchases_ids = [];
            // Товары
            foreach ($xmlOrder->Товары->Товар as $xmlProduct) {
                $purchase = null;
                //  Id товара и варианта (если есть) по 1С
                $product1cId = $variant1cId = '';
                @list($product1cId, $variant1cId) = explode('#', (string)$xmlProduct->Ид);
                if (empty($product1cId)) {
                    $product1cId = '';
                }
                if (empty($variant1cId)) {
                    $variant1cId = '';
                }

                // Ищем товар
                $select = $this->integration1C->queryFactory->newSelect();
                $select->cols(['id'])
                    ->from('__products')
                    ->where('external_id=:external_id')
                    ->bindValue('external_id', $product1cId);
                $this->integration1C->db->query($select);
                $productId = $this->integration1C->db->result('id');

                $variantId = null;
                // Если прилетел ID варианта из 1С, ищем вариант по нему
                if ($variant1cId) {
                    $select = $this->integration1C->queryFactory->newSelect();
                    $select->cols(['id'])
                        ->from('__variants')
                        ->where('external_id=:external_id')
                        ->where('product_id=:product_id')
                        ->bindValue('external_id', $variant1cId)
                        ->bindValue('product_id', $productId);
                    $this->integration1C->db->query($select);
                    $variantId = $this->integration1C->db->result('id');
                // или попробуем поискать по артикулу
                } elseif ($sku = (string)$xmlProduct->Артикул) {
                    $select = $this->integration1C->queryFactory->newSelect();
                    $select->cols(['id'])
                        ->from('__variants')
                        ->where('sku=:sku')
                        ->bindValue('sku', $sku);
                    
                    if (!empty($productId)) {
                        $select->where('product_id=:product_id')
                            ->bindValue('product_id', $productId);
                    }
                    
                    $this->integration1C->db->query($select);
                    $variantId = $this->integration1C->db->result('id');
                // последняя попытка, это если у товара всего один вариант, вероятнее всего он нужен
                } else {
                    $select = $this->integration1C->queryFactory->newSelect();
                    $select->cols(['id'])
                        ->from('__variants')
                        ->where('product_id=:product_id')
                        ->bindValue('product_id', $productId);
                    $this->integration1C->db->query($select);
                    $variantIds = $this->integration1C->db->results('id');
                    if (count($variantIds) == 1) {
                        $variantId = reset($variantIds);
                    }
                }
                
                $purchase = new \stdClass;
                $purchase->order_id     = $order->id;
                $purchase->product_id   = $productId;
                $purchase->variant_id   = $variantId;
                $purchase->sku          = (string)$xmlProduct->Артикул;
                $purchase->product_name = (string)$xmlProduct->Наименование;
                $purchase->amount       = (int)$xmlProduct->Количество;
                $purchase->price        = (float)$xmlProduct->ЦенаЗаЕдиницу;

                if (isset($xmlProduct->Скидки->Скидка)) {
                    $discount = $xmlProduct->Скидки->Скидка->Процент;
                    $purchase->price = $purchase->price*(100-$discount)/100;
                }
                
                if (!empty($variantId)) {
                    $select = $this->integration1C->queryFactory->newSelect();
                    $select->cols(['id'])
                        ->from('__purchases')
                        ->where('order_id=:order_id')
                        ->where('product_id=:product_id')
                        ->where('variant_id=:variant_id')
                        ->bindValue('order_id', $order->id)
                        ->bindValue('product_id', $productId)
                        ->bindValue('variant_id', $variantId);
                    $this->integration1C->db->query($select);
                    $purchaseId = $this->integration1C->db->result('id');
                    if (!empty($purchaseId)) {
                        $purchaseId = $purchasesEntity->update($purchaseId, $purchase);
                    } else {
                        $purchaseId = $purchasesEntity->add($purchase);
                    }
                    $purchases_ids[] = $purchaseId;
                }
            }
            
            // Удалим покупки, которых нет в файле
            foreach ($purchasesEntity->find(['order_id'=>intval($order->id)]) as $purchase) {
                if (!in_array($purchase->id, $purchases_ids)) {
                    $purchasesEntity->delete($purchase->id);
                }
            }

            $ordersEntity->update($order->id, [
                'discount' => 0,
                'total_price' => (float)$xmlOrder->Сумма,
            ]);
        }
        
        return "success\n";
    }
}
