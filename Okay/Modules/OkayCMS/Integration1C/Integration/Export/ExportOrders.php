<?php

namespace Okay\Modules\OkayCMS\Integration1C\Integration\Export;


use Okay\Entities\DeliveriesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;

class ExportOrders extends AbstractExport
{
    
    public function export()
    {
        
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->integration1C->entityFactory->get(OrdersEntity::class);
        
        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->integration1C->entityFactory->get(PurchasesEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->integration1C->entityFactory->get(PaymentsEntity::class);

        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $this->integration1C->entityFactory->get(DeliveriesEntity::class);

        /** @var OrderStatusEntity $orderStatusesEntity */
        $orderStatusesEntity = $this->integration1C->entityFactory->get(OrderStatusEntity::class);
        $allStatuses = $orderStatusesEntity->mappedBy('id')->find();
        
        $no_spaces = '<?xml version="1.0" encoding="utf-8"?>
        <КоммерческаяИнформация ВерсияСхемы="2.04" ДатаФормирования="' . date('Y-m-d') . '"></КоммерческаяИнформация>';
        $xml = new \SimpleXMLElement($no_spaces);

        $orders = $ordersEntity->mappedBy('id')->find(['modified_since'=>$this->integration1C->settings->last_1c_orders_export_date]);
        
        $purchases = [];
        if (!empty($orders)) {
            foreach ($purchasesEntity->find(['order_id' => array_keys($orders)]) as $purchase) {
                $purchases[$purchase->order_id][] = $purchase;
            }
        }
        
        foreach ($orders as $order) {
            $date = new \DateTime($order->date);

            $doc = $xml->addChild ("Документ");
            $doc->addChild ( "Ид", $order->id);
            $doc->addChild ( "Номер", $order->id);
            $doc->addChild ( "Дата", $date->format('Y-m-d'));
            $doc->addChild ( "ХозОперация", "Заказ товара" );
            $doc->addChild ( "Роль", "Продавец" );
            $doc->addChild ( "Валюта", "грн" );//Вводится в зависимости от валюты в 1С // todo валюта сайта и 1С
            $doc->addChild ( "Курс", "1" );
            $doc->addChild ( "Сумма", $order->total_price);
            $doc->addChild ( "Время",  $date->format('H:i:s'));
            $doc->addChild ( "Комментарий", $order->comment. 'Адрес доставки: '.$order->address);

            if ($order->total_price != $order->undiscounted_total_price) {
                $t1_2 = $doc->addChild("Скидки");
                $t1_3 = $t1_2->addChild("Скидка");
                $t1_4 = $t1_3->addChild("Сумма", ($order->undiscounted_total_price - $order->total_price));
                $t1_4 = $t1_3->addChild("УчтеноВСумме", "true");
            }

            // Контрагенты
            $k1 = $doc->addChild ( 'Контрагенты' );
            $k1_1 = $k1->addChild ( 'Контрагент' );
            $k1_2 = $k1_1->addChild ( "Ид", $order->name);
            $k1_2 = $k1_1->addChild ( "Наименование", $order->name);
            $k1_2 = $k1_1->addChild ( "Роль", "Покупатель" );
            $k1_2 = $k1_1->addChild ( "ПолноеНаименование", $order->name );

            //Представители
            $p1_1 = $k1_1->addChild ( 'Представители' );
            $p1_2 = $p1_1->addChild ( 'Представитель' );
            $p1_3 = $p1_2->addChild ( 'Контрагент' );
            $p1_4 = $p1_3->addChild ( "Отношение", "Контактное лицо" );
            $p1_4 = $p1_3->addChild ( "Ид", $order->name );
            $p1_4 = $p1_3->addChild ( "Наименование", $order->name);

            // Доп параметры
            $addr = $k1_1->addChild ('АдресРегистрации');
            $addr->addChild ( 'Представление', $order->address );
            $addrField = $addr->addChild ( 'АдресноеПоле' );
            $addrField->addChild ( 'Тип', 'Страна' );
            $addrField->addChild ( 'Значение', 'УКРАИНА' );// Для России значение РОССИЯ
            $addrField = $addr->addChild ( 'АдресноеПоле' );
            $addrField->addChild ( 'Тип', 'Регион' );
            $addrField->addChild ( 'Значение', $order->address );

            $contacts = $k1_1->addChild ( 'Контакты' );
            $cont = $contacts->addChild ( 'Контакт' );
            $cont->addChild ( 'Тип', 'ТелефонРабочий' );
            $cont->addChild ( 'Значение', $order->phone );
            $cont = $contacts->addChild ( 'Контакт' );
            $cont->addChild ( 'Тип', 'Почта' );
            $cont->addChild ( 'Значение', $order->email );

            $t1 = $doc->addChild ( 'Товары' );
            if (isset($purchases[$order->id])) {
                foreach ($purchases[$order->id] as $purchase) {
                    if (!empty($purchase->product_id) && !empty($purchase->variant_id)) {
                        $select = $this->integration1C->queryFactory->newSelect();
                        $select->cols(['external_id'])->from(ProductsEntity::getTable())->where('id=:id')->bindValue('id', $purchase->product_id);

                        $this->integration1C->db->query($select);
                        $productId = $this->integration1C->db->result('external_id');
                        
                        $select = $this->integration1C->queryFactory->newSelect();
                        $select->cols(['external_id'])->from(VariantsEntity::getTable())->where('id=:id')->bindValue('id', $purchase->variant_id);
                        
                        $this->integration1C->db->query($select);
                        $variantId = $this->integration1C->db->result('external_id');

                        // Если нет внешнего ключа товара - указываем наш id
                        if (!empty($productId)) {
                            $id = $productId;
                        } else {
                            $update = $this->integration1C->queryFactory->newUpdate();
                            $update->set('external_id', 'id')->table(ProductsEntity::getTable())->where('id=:id')->bindValue('id', $purchase->product_id);
                            $this->integration1C->db->query($update);
                            $id = $purchase->product_id;
                        }

                        // Если нет внешнего ключа варианта - указываем наш id
                        if (!empty($variantId)) {
                            $id = $id . '#' . $variantId;
                        } else {
                            $update = $this->integration1C->queryFactory->newUpdate();
                            $update->set('external_id', 'id')->table(VariantsEntity::getTable())->where('id=:id')->bindValue('id', $purchase->product_id);
                            $this->integration1C->db->query($update);
                            $id = $id . '#' . $purchase->variant_id;
                        }

                        $t1_1 = $t1->addChild('Товар');

                        if ($id) {
                            $t1_2 = $t1_1->addChild("Ид", $id);
                        }

                        $t1_2 = $t1_1->addChild("Артикул", $purchase->sku);

                        $name = $purchase->product_name;
                        if ($purchase->variant_name) {
                            $name .= " $purchase->variant_name $id";
                        }
                        $t1_2 = $t1_1->addChild("Наименование", $name);
                        $t1_2 = $t1_1->addChild("ЦенаЗаЕдиницу", $purchase->price);
                        $t1_2 = $t1_1->addChild("Количество", $purchase->amount);
                        $t1_2 = $t1_1->addChild("Сумма", ($purchase->price * $purchase->amount));


                        if ($purchase->price != $purchase->undiscounted_price) {
                            $t1_2 = $t1_1->addChild("Скидки");
                            $t1_3 = $t1_2->addChild("Скидка");
                            $t1_4 = $t1_3->addChild("Сумма", (($purchase->undiscounted_price - $purchase->price) * $purchase->amount));
                            $t1_4 = $t1_3->addChild("УчтеноВСумме", "true");
                        }


                        $t1_2 = $t1_1->addChild("ЗначенияРеквизитов");
                        $t1_3 = $t1_2->addChild("ЗначениеРеквизита");
                        $t1_4 = $t1_3->addChild("Наименование", "ВидНоменклатуры");
                        $t1_4 = $t1_3->addChild("Значение", "Товар");

                        //$t1_2 = $t1_1->addChild ( "ЗначенияРеквизитов" );
                        $t1_3 = $t1_2->addChild("ЗначениеРеквизита");
                        $t1_4 = $t1_3->addChild("Наименование", "ТипНоменклатуры");
                        $t1_4 = $t1_3->addChild("Значение", "Товар");
                    }
                }
            }

            // Доставка
            if ($order->delivery_price>0 && !$order->separate_delivery) {
                $t1 = $t1->addChild ( 'Товар' );
                $t1->addChild ( "Ид", 'ORDER_DELIVERY');
                $t1->addChild ( "Наименование", 'Доставка');
                $t1->addChild ( "ЦенаЗаЕдиницу", $order->delivery_price);
                $t1->addChild ( "Количество", 1 );
                $t1->addChild ( "Сумма", $order->delivery_price);
                $t1_2 = $t1->addChild ( "ЗначенияРеквизитов" );
                $t1_3 = $t1_2->addChild ( "ЗначениеРеквизита" );
                $t1_4 = $t1_3->addChild ( "Наименование", "ВидНоменклатуры" );
                $t1_4 = $t1_3->addChild ( "Значение", "Услуга" );

                //$t1_2 = $t1->addChild ( "ЗначенияРеквизитов" );
                $t1_3 = $t1_2->addChild ( "ЗначениеРеквизита" );
                $t1_4 = $t1_3->addChild ( "Наименование", "ТипНоменклатуры" );
                $t1_4 = $t1_3->addChild ( "Значение", "Услуга" );
            }

            // Способ оплаты и доставки
            $s1_2 = $doc->addChild ( "ЗначенияРеквизитов");
            
            $paymentMethod = $paymentsEntity->get((int)$order->payment_method_id);
            $delivery = $deliveriesEntity->get((int)$order->delivery_id);

            if($paymentMethod) {
                $s1_3 = $s1_2->addChild ( "ЗначениеРеквизита");
                $s1_3->addChild ( "Наименование", "Метод оплаты" );
                $s1_3->addChild ( "Значение", $paymentMethod->name );
            }
            if($delivery) {
                $s1_3 = $s1_2->addChild ( "ЗначениеРеквизита");
                $s1_3->addChild ( "Наименование", "Способ доставки" );
                $s1_3->addChild ( "Значение", $delivery->name);
            }
            $s1_3 = $s1_2->addChild ( "ЗначениеРеквизита");
            $s1_3->addChild ( "Наименование", "Заказ оплачен" );
            $s1_3->addChild ( "Значение", $order->paid?'true':'false' );
            
            if (!empty($order->payment_date)) {
                $date = new \DateTime($order->payment_date);
                $s1_3 = $s1_2->addChild("ЗначениеРеквизита");
                $s1_3->addChild("Наименование", "Дата оплаты");
                $s1_3->addChild("Значение", $date->format('Y-m-d'));
            }
            
            // Статус
            if (isset($allStatuses[$order->status_id])) {
                $status = $allStatuses[$order->status_id];
                $s1_3 = $s1_2->addChild ( "ЗначениеРеквизита" );
                $s1_3->addChild ( "Наименование", "Статус заказа" );
                $s1_3->addChild ( "Значение", $status->name );
            }
        }
        
        return $xml->asXML();
    }
}