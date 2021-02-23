<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendOrderHistoryHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Requests\BackendOrdersRequest;
use Okay\Core\Image;
use Okay\Core\Notify;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\OrderLabelsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\PurchasesEntity;

class OrderAdmin extends IndexAdmin
{
    
    public function fetch(
        OrdersEntity              $ordersEntity,
        PurchasesEntity           $purchasesEntity,
        OrderLabelsEntity         $orderLabelsEntity,
        OrderStatusEntity         $orderStatusEntity,
        DeliveriesEntity          $deliveriesEntity,
        PaymentsEntity            $paymentsEntity,
        CurrenciesEntity          $currenciesEntity,
        Notify                    $notify,
        BackendOrdersRequest      $ordersRequest,
        BackendOrdersHelper       $backendOrdersHelper,
        BackendOrderHistoryHelper $backendOrderHistoryHelper,
        DiscountsEntity           $discountsEntity
    ) {
        
        /*Прием информации о заказе*/
        if ($this->request->method('post')) {
            
            $order = $ordersRequest->postOrder();
            $purchases = $ordersRequest->postPurchases();

            $orderBeforeUpdate = [];
            $purchasesBeforeUpdate = [];
            $discountsBeforeUpdate = [];
            if (!empty($order->id)) {
                $orderBeforeUpdate = $ordersEntity->get((int)$order->id);
                $purchasesBeforeUpdate = $purchasesEntity->find(['order_id' => $order->id]);
                $discountsBeforeUpdate = $backendOrdersHelper->getDiscountsBeforeUpdate($order->id);
            }
            
            if (!$orderLabels = $this->request->post('order_labels')) {
                $orderLabels = [];
            }

            // Установим отметку "доставка оплачивается отдельно"
            if ($order->delivery_id) {
                $deliverySeparatePayment = (array)$deliveriesEntity->cols(['separate_payment'])->get((int)$order->delivery_id);
                $order->separate_delivery = $deliverySeparatePayment['separate_payment'];
            }
            
            if (empty($purchases)) {
                $this->design->assign('message_error', 'empty_purchase');
            } else {
                /*Добавление/Обновление заказа*/
                if (empty($order->id)) {
                    $preparedOrder = $backendOrdersHelper->prepareAdd($order);
                    $order->id  = $backendOrdersHelper->add($preparedOrder);
                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($order->id);
                } else {
                    $preparedOrder = $backendOrdersHelper->prepareUpdate($order);
                    $backendOrdersHelper->update($preparedOrder);
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }
                
                $orderLabelsEntity->updateOrderLabels($order->id, $orderLabels);

                if ($order->id) {
                    /*Работа с покупками заказа*/
                    $postedPurchasesIds = [];
                    foreach ($purchases as $purchase) {
                        if (!empty($purchase->id)) {
                            $preparedPurchase = $backendOrdersHelper->prepareUpdatePurchase($order, $purchase);
                            $backendOrdersHelper->updatePurchase($preparedPurchase);
                        } else {
                            $preparedPurchase = $backendOrdersHelper->prepareAddPurchase($order, $purchase);
                            if (!$purchase->id = $backendOrdersHelper->addPurchase($preparedPurchase)) {
                                $this->design->assign('message_error', 'error_closing');
                            }
                        }
                        $postedPurchasesIds[] = $purchase->id;
                    }

                    $postedDiscountIds = [];
                    // Обновляем скидки заказа
                    $discounts = $ordersRequest->postOrderDiscounts();
                    foreach ($discounts as $discount) {
                        if (!empty($discount->id)) {
                            $preparedDiscount = $backendOrdersHelper->prepareUpdateOrderDiscount($discount, $order);
                            $backendOrdersHelper->updateDiscount($preparedDiscount);
                        } else {
                            $preparedDiscount = $backendOrdersHelper->prepareAddOrderDiscount($discount, $order);
                            $discount->id = $backendOrdersHelper->addDiscount($preparedDiscount);
                        }
                        $postedDiscountIds[] = $discount->id;
                    }

                    // Обновляем скидки товаров
                    $discounts = $ordersRequest->postPurchasesDiscounts();
                    $i = 0;
                    foreach ($purchases as $purchase) {
                        $purchaseDiscounts = $discounts[$i++];
                        if ($purchase->id && !empty($purchaseDiscounts)) {
                            foreach ($purchaseDiscounts as $discount) {
                                if (!empty($discount->id)) {
                                    $preparedDiscount = $backendOrdersHelper->prepareUpdatePurchaseDiscount($discount, $purchase);
                                    $backendOrdersHelper->updateDiscount($preparedDiscount);
                                } else {
                                    $preparedDiscount = $backendOrdersHelper->prepareAddPurchaseDiscount($discount, $purchase);
                                    $discount->id = $backendOrdersHelper->addDiscount($preparedDiscount);
                                }
                                $postedDiscountIds[] = $discount->id;
                            }
                        }
                    }

                    // Обновим позиции скидок
                    $positions = $ordersRequest->postDiscountPositions();
                    list($ids, $positions) = $backendOrdersHelper->sortDiscountPositions($positions);
                    $backendOrdersHelper->updateDiscountPositions($ids, $positions);

                    // Удаляем скидки
                    $backendOrdersHelper->deleteDiscounts($postedDiscountIds, $order->id);

                    // Удалить непереданные товары
                    $backendOrdersHelper->deletePurchases($order, $postedPurchasesIds);

                    // Обновим статус заказа
                    $newStatusId = $this->request->post('status_id', 'integer');
                    if (!$backendOrdersHelper->updateOrderStatus($order, $newStatusId)) {
                        $this->design->assign('message_error', 'error_closing');
                    }

                    // Обновим итоговую стоимость заказа
                    $ordersEntity->updateTotalPrice($order->id);
                    $order = $backendOrdersHelper->findOrder($order->id);

                    // Отправляем письмо пользователю
                    if ($this->request->post('notify_user')) {
                        $notify->emailOrderUser($order->id);
                    }
                }

                $backendOrderHistoryHelper->updateHistory($orderBeforeUpdate, $order, $purchasesBeforeUpdate, $discountsBeforeUpdate);

                // По умолчанию метод ничего не делает, но через него можно зацепиться модулем
                $backendOrdersHelper->executeCustomPost($order);
            }

            if (! $this->design->getVar('message_error')) {
                $this->postRedirectGet->redirect();
            }
        }
            
        $order = $backendOrdersHelper->findOrder($this->request->get('id', 'integer'));

        // Метки заказа
        $orderLabels = [];
        if (isset($order->id)) {
            $orderLabels = $orderLabelsEntity->mappedBy('id')->find(['order_id' => $order->id]);

            $purchases = $backendOrdersHelper->findOrderPurchases($order);

            $subtotal = 0;
            $hasVariantNotInStock = false;
            foreach ($purchases as $purchase) {
                if ((empty($purchase->variant) || $purchase->amount > $purchase->variant->stock || !$purchase->variant->stock) && !$hasVariantNotInStock) {
                    $hasVariantNotInStock = true;
                }
                $subtotal += $purchase->price * $purchase->amount;
            }
            // Способ доставки
            $delivery = $backendOrdersHelper->findOrderDelivery($order);
            $this->design->assign('delivery', $delivery);

            // Способ оплаты
            $paymentMethod = $backendOrdersHelper->findOrderPayment($order);
            if (!empty($paymentMethod)) {
                // Валюта оплаты
                $paymentCurrency = $currenciesEntity->get(intval($paymentMethod->currency_id));
                $this->design->assign('payment_currency', $paymentCurrency);
            }

            $discounts = $backendOrdersHelper->getOrderDiscounts($order->id);

            $user = $backendOrdersHelper->findOrderUser($order);
            $neighborsOrders = $backendOrdersHelper->findNeighborsOrders(
                $order,
                $this->request->get('label', 'integer'),
                $this->request->get('status', 'integer')
            );

            $this->design->assign('delivery', $delivery);
            $this->design->assign('payment_method', $paymentMethod);
            $this->design->assign('user', $user);
            $this->design->assign('purchases', $purchases);
            $this->design->assign('subtotal', $subtotal);
            $this->design->assign('order', $order);
            $this->design->assign('hasVariantNotInStock', $hasVariantNotInStock);
            $this->design->assign('neighbors_orders', $neighborsOrders);
            $this->design->assign('discounts', $discounts);
        }

        // все статусы
        $allStatuses = $orderStatusEntity->mappedBy('id')->find();
        $this->design->assign('all_status', $allStatuses);

        // Все способы доставки
        $deliveries = $deliveriesEntity->find();
        $this->design->assign('deliveries', $deliveries);
        
        // Все способы оплаты
        $paymentMethods = $paymentsEntity->find();
        $this->design->assign('payment_methods', $paymentMethods);
        
        // Метки заказов
        $labels = $orderLabelsEntity->find();
        $this->design->assign('labels', $labels);
        
        $this->design->assign('order_labels', $orderLabels);

        if (!empty($order->id)) {
            $orderHistory = $backendOrderHistoryHelper->getHistory($order->id);
            $this->design->assign('order_history', $orderHistory);
            
            $page             = $ordersRequest->getPage();
            $currentPage      = $backendOrdersHelper->determineCurrentPage($page);
            $perPage          = $backendOrdersHelper->getPaginationPerPage();
            $otherOrders      = $backendOrdersHelper->findOtherOrdersOfClient($order, $currentPage, $perPage);
            $otherOrdersCount = $backendOrdersHelper->countOtherOrdersOfClient($order);
            $this->design->assign('match_orders', $otherOrders);
            $this->design->assign('current_page', $currentPage);
            $this->design->assign('pages_count',  ceil($otherOrdersCount / $perPage));
        }

        if ($this->request->get('match_orders_tab_active')) {
            $this->design->assign('match_orders_tab_active', true);
        }

        if ($this->request->get('view') == 'print') {
            $this->response->setContent($this->design->fetch('order_print.tpl'));
        } else {
            $this->response->setContent($this->design->fetch('order.tpl'));
        }
    }
    
    public function addOrderProduct(BackendOrdersHelper  $backendOrdersHelper, Image $imagesCore)
    {
        $keyword = $this->request->get('query', 'string');

        $products = $backendOrdersHelper->findOrderProducts($keyword);

        $suggestions = [];
        foreach($products as $product) {
            if(!empty($product->variants)) {
                $suggestion = new \stdClass;
                if(!empty($product->image)) {
                    $product->image = $imagesCore->getResizeModifier($product->image, 35, 35);
                }
                $suggestion->value = $product->name;
                $suggestion->data = $product;
                $suggestions[] = $suggestion;
            }
        }

        $result = new \stdClass;
        $result->query = $keyword;
        $result->suggestions = $suggestions;
        $this->response->setContent(json_encode($result), RESPONSE_JSON);

    }
    
}
