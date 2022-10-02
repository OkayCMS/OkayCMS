<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendDiscountsHelper;
use Okay\Admin\Helpers\BackendOrderHistoryHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Helpers\BackendPurchasesHelper;
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
        BackendPurchasesHelper    $backendPurchasesHelper,
        BackendDiscountsHelper    $backendDiscountsHelper,
        BackendOrderHistoryHelper $backendOrderHistoryHelper
    ) {
        
        /*Прием информации о заказе*/
        if ($this->request->method('post')) {
            
            $order = $ordersRequest->postOrder();
            $purchases = $ordersRequest->postPurchases();

            $orderBeforeUpdate = [];
            $purchasesBeforeUpdate = [];
            $discountsBeforeUpdate = [];
            if (!empty($order->id)) {
                $orderBeforeUpdate = $backendOrdersHelper->getBeforeUpdate($order->id);
                $purchasesBeforeUpdate = $backendPurchasesHelper->getBeforeUpdate($order->id);
                $discountsBeforeUpdate = $backendDiscountsHelper->getBeforeUpdate($order->id);
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
                    $purchasesDiscounts = $ordersRequest->postPurchasesDiscounts();
                    $postedDiscountIds = [];

                    // Обновляем скидки товаров
                    foreach ($purchases as $i => $purchase) {
                        $purchaseDiscounts = $purchasesDiscounts[$i] ?? [];

                        if (!empty($purchase->id) && !empty($purchaseDiscounts)) {
                            foreach ($purchaseDiscounts as $discount) {
                                if (!empty($discount->id)) {
                                    $preparedDiscount = $backendDiscountsHelper->prepareUpdatePurchaseDiscount($discount, $purchase);
                                    $backendDiscountsHelper->update($preparedDiscount);
                                } else {
                                    $preparedDiscount = $backendDiscountsHelper->prepareAddPurchaseDiscount($discount, $purchase);
                                    $discount->id = $backendDiscountsHelper->add($preparedDiscount);
                                }
                                $postedDiscountIds[] = $discount->id;
                            }
                        }
                    }

                    // Обновляем скидки заказа
                    $discounts = $ordersRequest->postOrderDiscounts();
                    foreach ($discounts as $discount) {
                        if (!empty($discount->id)) {
                            $preparedDiscount = $backendDiscountsHelper->prepareUpdateOrderDiscount($discount, $order);
                            $backendDiscountsHelper->update($preparedDiscount);
                        } else {
                            $preparedDiscount = $backendDiscountsHelper->prepareAddOrderDiscount($discount, $order);
                            $discount->id = $backendDiscountsHelper->add($preparedDiscount);
                        }
                        $postedDiscountIds[] = $discount->id;
                    }

                    /*Работа с покупками заказа*/
                    foreach ($purchases as $i => $purchase) {
                        if (!empty($purchase->id)) {
                            $preparedPurchase = $backendPurchasesHelper->prepareUpdate($order, $purchase);
                            $backendPurchasesHelper->update($preparedPurchase);
                        } else {
                            $preparedPurchase = $backendPurchasesHelper->prepareAdd($order, $purchase);
                            if (!$purchase->id = $backendPurchasesHelper->add($preparedPurchase)) {
                                $this->design->assign('message_error', 'error_closing');
                            }
                        }
                        $postedPurchasesIds[] = $purchase->id;
                    }

                    // Удалить непереданные товары
                    $backendPurchasesHelper->delete($order, $postedPurchasesIds ?? []);

                    // Обновим позиции скидок
                    $positions = $ordersRequest->postDiscountPositions();
                    list($ids, $positions) = $backendDiscountsHelper->sortPositions($positions);
                    $backendDiscountsHelper->updatePositions($ids, $positions);

                    // Удаляем скидки
                    $backendDiscountsHelper->delete($postedDiscountIds, $order->id);

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
                $buttonRedirectToList = $this->request->post('apply_and_quit', 'integer', 0);
                if (($buttonRedirectToList == 1) && !empty($urlRedirectToList = $this->request->getRootUrl() . '/backend/index.php?controller=OrdersAdmin')) {
                    $this->postRedirectGet->redirect($urlRedirectToList);
                }

                $this->postRedirectGet->redirect();
            }
        }
            
        $order = $backendOrdersHelper->findOrder($this->request->get('id', 'integer'));

        // Метки заказа
        $orderLabels = [];
        if (isset($order->id)) {
            $orderLabels = $orderLabelsEntity->mappedBy('id')->find(['order_id' => $order->id]);

            $purchases = $backendPurchasesHelper->findOrderPurchases($order);

            $subtotal = 0;
            $hasVariantNotInStock = false;
            foreach ($purchases as $purchase) {
                if (!$order->closed && ((empty($purchase->variant) || $purchase->amount > $purchase->variant->stock || !$purchase->variant->stock) && !$hasVariantNotInStock)) {
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
                $paymentCurrency = $currenciesEntity->findOne(['id' => $paymentMethod->currency_id]);
                $this->design->assign('payment_currency', $paymentCurrency);
            }

            $discounts = $backendDiscountsHelper->getOrderDiscounts($order->id);

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
        foreach ($products as $product) {
            if (!empty($product->variants)) {
                $suggestion = new \stdClass;
                if (!empty($product->image)) {
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
