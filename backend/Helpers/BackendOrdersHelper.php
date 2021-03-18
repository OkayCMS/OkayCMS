<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\ImagesEntity;
use Okay\Entities\OrderHistoryEntity;
use Okay\Entities\OrderLabelsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\UserGroupsEntity;
use Okay\Entities\UsersEntity;
use Okay\Entities\VariantsEntity;

class BackendOrdersHelper
{
    /** @var OrdersEntity */
    private $ordersEntity;
    
    /** @var VariantsEntity */
    private $variantsEntity;
    
    /** @var PurchasesEntity */
    private $purchasesEntity;
    
    /** @var OrderStatusEntity */
    private $orderStatusEntity;

    /** @var OrderLabelsEntity */
    private $orderLabelsEntity;
    
    /** @var ProductsEntity */
    private $productsEntity;
    
    /** @var ImagesEntity */
    private $imagesEntity;
    
    /** @var DeliveriesEntity */
    private $deliveriesEntity;
    
    /** @var PaymentsEntity */
    private $paymentsEntity;
    
    /** @var OrderHistoryEntity */
    private $orderHistoryEntity;
    
    /** @var UsersEntity */
    private $usersEntity;
    
    /** @var UserGroupsEntity */
    private $userGroupsEntity;

    /** @var Request */
    private $request;

    /** @var Settings */
    private $settings;

    /** @var EntityFactory */
    private $entityFactory;

    /** @var BackendPurchasesHelper */
    private $backendPurchasesHelper;

    /** @var BackendDiscountsHelper */
    private $backendDiscountsHelper;
    
    public function __construct(
        EntityFactory   $entityFactory,
        Request         $request,
        Settings        $settings,
        BackendPurchasesHelper $backendPurchasesHelper,
        BackendDiscountsHelper $backendDiscountsHelper
    ) {
        $this->ordersEntity       = $entityFactory->get(OrdersEntity::class);
        $this->variantsEntity     = $entityFactory->get(VariantsEntity::class);
        $this->purchasesEntity    = $entityFactory->get(PurchasesEntity::class);
        $this->orderStatusEntity  = $entityFactory->get(OrderStatusEntity::class);
        $this->orderLabelsEntity  = $entityFactory->get(OrderLabelsEntity::class);
        $this->orderHistoryEntity = $entityFactory->get(OrderHistoryEntity::class);
        $this->productsEntity     = $entityFactory->get(ProductsEntity::class);
        $this->imagesEntity       = $entityFactory->get(ImagesEntity::class);
        $this->deliveriesEntity   = $entityFactory->get(DeliveriesEntity::class);
        $this->paymentsEntity     = $entityFactory->get(PaymentsEntity::class);
        $this->usersEntity        = $entityFactory->get(UsersEntity::class);
        $this->userGroupsEntity   = $entityFactory->get(UserGroupsEntity::class);

        $this->entityFactory          = $entityFactory;
        $this->request                = $request;
        $this->settings               = $settings;
        $this->backendPurchasesHelper = $backendPurchasesHelper;
        $this->backendDiscountsHelper = $backendDiscountsHelper;
    }

    /**
     * Метод используется для поиска нового товара в заказ
     * 
     * @param $keyword
     * @return mixed|void|null
     * @throws \Exception
     */
    public function findOrderProducts($keyword)
    {
        
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        $productsFilter = [
            'keyword' => $keyword,
            'limit' => 10,
            'in_stock' => !$this->settings->get('is_preorder'),
        ];

        $imagesIds = [];
        $products = [];
        foreach ($this->productsEntity->find($productsFilter) as $product) {
            $products[$product->id] = $product;
            $imagesIds[] = $product->main_image_id;
        }

        if (!empty($products)) {
            foreach ($this->imagesEntity->find(['id' => $imagesIds]) as $image) {
                if (isset($products[$image->product_id])) {
                    $products[$image->product_id]->image = $image->filename;
                }
            }

            $variants = $this->variantsEntity->find([
                'product_id' => array_keys($products),
                'in_stock' => !$this->settings->get('is_preorder'),
                'has_price' => true,
            ]);

            foreach ($variants as $variant) {
                if (isset($products[$variant->product_id])) {
                    $variant->units = $variant->units ? $variant->units : $this->settings->get('units');
                    $products[$variant->product_id]->variants[] = $variant;
                    if ($variant->currency_id && ($currency = $currenciesEntity->findOne(['id' => $variant->currency_id]))) {
                        if ($currency->rate_from != $currency->rate_to) {
                            $variant->price = round($variant->price*$currency->rate_to/$currency->rate_from,2);
                            $variant->compare_price = round($variant->compare_price*$currency->rate_to/$currency->rate_from,2);
                        }
                    }
                }
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());
    }
    
    /**
     * @var $order
     * Метод заглушка, чтобы модули могли зацепиться
     */
    public function executeCustomPost($order)
    {
        ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    public function getBeforeUpdate($orderId)
    {
        $order = $this->ordersEntity->findOne(['id' => $orderId]);
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }
    
    public function prepareAdd($order)
    {
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    public function add($order)
    {
        $insertId = $this->ordersEntity->add($order);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($order)
    {
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    public function update($order)
    {
        $this->ordersEntity->update($order->id, $order);
        ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }
    
    public function updateOrderStatus($order, $newStatusId)
    {
        $newStatusInfo = $this->orderStatusEntity->get((int)$newStatusId);

        $result = true;
        if ($newStatusInfo->is_close == 1) {
            if (!$this->ordersEntity->close(intval($order->id))) {
                $result = false;
            } else {
                $this->ordersEntity->update($order->id, ['status_id' => $newStatusId]);
            }
        } else {
            if ($this->ordersEntity->open(intval($order->id))) {
                $this->ordersEntity->update($order->id, ['status_id' => $newStatusId]);
            }
        }
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }
    
    public function findOrder($orderId)
    {
        $order = $this->ordersEntity->get((int)$orderId);
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }
    
    public function findOrderDelivery($order)
    {
        $delivery = null;
        if (!empty($order->delivery_id)) {
            $delivery = $this->deliveriesEntity->get($order->delivery_id);
            if (is_string($delivery->settings)) {
                $delivery->settings = unserialize($delivery->settings);
            }
        }
        return ExtenderFacade::execute(__METHOD__, $delivery, func_get_args());
    }
    
    public function findOrderPayment($order)
    {
        $payment = null;
        if (!empty($order->payment_method_id)) {
            $payment = $this->paymentsEntity->get($order->payment_method_id);
        }
        return ExtenderFacade::execute(__METHOD__, $payment, func_get_args());
    }
    
    public function findOrderUser($order)
    {
        $user = null;
        if (!empty($order->user_id)) {
            $user = $this->usersEntity->get((int)$order->user_id);
            $user->group = $this->userGroupsEntity->get((int)$user->group_id);
        }
        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }
    
    public function findNeighborsOrders($order, $labelId = null, $statusId = null)
    {
        $neighborsOrders = null;
        if (!empty($order->id)) {
            $neighborsFilter['id'] = $order->id;
            if ($statusId !== null) {
                $neighborsFilter['status_id'] = $statusId;
            }
            if ($labelId !== null) {
                $neighborsFilter['label_id'] = $labelId;
            }
            $neighborsOrders = $this->ordersEntity->getNeighborsOrders($neighborsFilter);
        }
        
        return ExtenderFacade::execute(__METHOD__, $neighborsOrders, func_get_args());
    }

    public function buildCountStatusesFilter($filter)
    {
        $countStatusesFilter = [];
        
        if (isset($filter['label'])) {
            $countStatusesFilter['label'] = $filter['label'];
        }

        if (isset($filter['keyword'])) {
            $countStatusesFilter['keyword'] = $filter['keyword'];
        }
        
        if (isset($filter['from_date'])) {
            $countStatusesFilter['from_date'] = $filter['from_date'];
        }
        
        if (isset($filter['to_date'])) {
            $countStatusesFilter['to_date'] = $filter['to_date'];
        }
        
        return ExtenderFacade::execute(__METHOD__, $countStatusesFilter, func_get_args());
    }
    
    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 40;

        // Поиск
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        // Фильтр по метке
        $label = $this->orderLabelsEntity->get($this->request->get('label', 'int'));
        
        if (!empty($label)) {
            $filter['label'] = $label->id;
        }

        if ($this->request->get('status')) {
            $filter['status_id'] = $statusId = $this->request->get('status', 'integer');
        }

        if ($this->request->get('user_id')) {
            $filter['user_id'] = $this->request->get('user_id', 'integer');
        }

        //Поиск до дате заказа
        $fromDate = $this->request->get('from_date');
        $toDate = $this->request->get('to_date');
        if (!empty($fromDate) || !empty($toDate)){
            $filter['from_date'] = $fromDate;
            $filter['to_date'] = $toDate;
        }

        $ordersCount = $this->ordersEntity->count($filter);
        // Показать все страницы сразу
        if($this->request->get('page') == 'all') {
            $filter['limit'] = $ordersCount;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function delete($ids)
    {
        $this->ordersEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function changeStatus($ids)
    {
        if($this->request->post("change_status_id")) {
            $newStatus = $this->orderStatusEntity->find(["status"=>$this->request->post("change_status_id","integer")]);
            $errorOrders = [];
            foreach($ids as $id) {
                if($newStatus[0]->is_close == 1){
                    if (!$this->ordersEntity->close(intval($id))) {
                        $errorOrders[] = $id;
                        //$this->design->assign('error_orders', $errorOrders);
                        //$this->design->assign('message_error', 'error_closing');
                    } else {
                        $this->ordersEntity->update($id, ['status_id'=>$this->request->post("change_status_id","integer")]);
                    }
                } else {
                    if ($this->ordersEntity->open(intval($id))) {
                        $this->ordersEntity->update($id, ['status_id'=>$this->request->post("change_status_id","integer")]);
                    }
                }

            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function setLabel($ids)
    {
        if($this->request->post("change_label_id")) {
            foreach($ids as $id) {
                $this->orderLabelsEntity->addOrderLabels($id, [$this->request->post("change_label_id","integer")]);
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function unsetLabel($ids)
    {
        if($this->request->post("change_label_id")) {
            foreach($ids as $id) {
                $this->orderLabelsEntity->deleteOrderLabels($id, [$this->request->post("change_label_id","integer")]);
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function findStatuses()
    {
        $statuses = $this->orderStatusEntity->mappedBy('id')->find();
        return ExtenderFacade::execute(__METHOD__, $statuses, func_get_args());
    }

    public function attachLastUpdate($orders)
    {
        // Метки заказов
        if (!empty($orders)) {
            $ordersHistory = $this->orderHistoryEntity->getOrdersLastChanges(array_keys($orders));
            if ($ordersHistory) {
                foreach ($ordersHistory as $item) {
                    $orders[$item->order_id]->last_update = $item;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $orders, func_get_args());
    }

    public function attachLabels($orders)
    {
        // Метки заказов
        if (!empty($orders)) {
            $ordersLabels = $this->orderLabelsEntity->getOrdersLabels(array_keys($orders));
            if ($ordersLabels) {
                foreach ($ordersLabels as $ordersLabel) {
                    $orders[$ordersLabel->order_id]->labels[] = $ordersLabel;
                    $orders[$ordersLabel->order_id]->labels_ids[] = $ordersLabel->id;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $orders, func_get_args());
    }

    public function findOrders($filter = [])
    {
        $orders = $this->ordersEntity->mappedBy('id')->find($filter);
        foreach($orders as $o) {
            $o->purchases = $this->purchasesEntity->find(['order_id'=>$o->id]);
        }

        return ExtenderFacade::execute(__METHOD__, $orders, func_get_args());
    }

    public function count($filter)
    {
        $obj = new \ArrayObject($filter);
        $copyFilter = $obj->getArrayCopy();

        if (isset($copyFilter['limit'])) {
            unset($copyFilter['limit']);
        }

        if (isset($copyFilter['page'])) {
            unset($copyFilter['page']);
        }

        $count = $this->ordersEntity->count($copyFilter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function findLabels($filter = [])
    {
        $labels = $this->orderLabelsEntity->find($filter = []);
        return ExtenderFacade::execute(__METHOD__, $labels, func_get_args());
    }

    public function findOtherOrdersOfClient($order, $page = 1, $perPage = 10)
    {
        $orders = $this->ordersEntity->findOtherOfClient($order, $page, $perPage);
        return ExtenderFacade::execute(__METHOD__, $orders, func_get_args());
    }

    public function countOtherOrdersOfClient($order)
    {
        $count = $this->ordersEntity->countOtherOfClient($order);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function getPaginationPerPage()
    {
        return ExtenderFacade::execute(__METHOD__, 10, func_get_args());
    }

    public function determineCurrentPage($page)
    {
        if (empty($page)) {
            $page = 1;
        }
        return ExtenderFacade::execute(__METHOD__, $page, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function prepareAddPurchase($order, $purchase)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendPurchasesHelper::class, E_USER_DEPRECATED);

        $purchase = $this->backendPurchasesHelper->prepareAdd($order, $purchase);
        return ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function addPurchase($purchase)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendPurchasesHelper::class, E_USER_DEPRECATED);

        $purchaseId = $this->backendPurchasesHelper->add($purchase);
        return ExtenderFacade::execute(__METHOD__, $purchaseId, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function prepareUpdatePurchase($order, $purchase)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendPurchasesHelper::class, E_USER_DEPRECATED);

        $purchase = $this->backendPurchasesHelper->prepareUpdate($order, $purchase);
        return ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function updatePurchase($purchase)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendPurchasesHelper::class, E_USER_DEPRECATED);

        $this->backendPurchasesHelper->update($purchase);
        ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function deletePurchases($order, array $postedPurchasesIds)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendPurchasesHelper::class, E_USER_DEPRECATED);

        $this->backendPurchasesHelper->delete($order, $postedPurchasesIds);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function findOrderPurchases($order)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendPurchasesHelper::class, E_USER_DEPRECATED);

        $purchases = $this->backendPurchasesHelper->findOrderPurchases($order);
        return ExtenderFacade::execute(__METHOD__, $purchases, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function getDiscountsBeforeUpdate($orderId)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discounts = $this->backendDiscountsHelper->getBeforeUpdate($orderId);
        return $discounts;
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function getOrderDiscounts($orderId)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discounts = $this->backendDiscountsHelper->getOrderDiscounts($orderId);
        return ExtenderFacade::execute(__METHOD__, $discounts, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function prepareAddOrderDiscount($discount, $order)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discount = $this->backendDiscountsHelper->prepareAddOrderDiscount($discount, $order);
        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function prepareUpdateOrderDiscount($discount, $order)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discount = $this->backendDiscountsHelper->prepareUpdateOrderDiscount($discount, $order);
        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function prepareAddPurchaseDiscount($discount, $purchase)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discount = $this->backendDiscountsHelper->prepareAddPurchaseDiscount($discount, $purchase);
        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function prepareUpdatePurchaseDiscount($discount, $purchase)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discount = $this->backendDiscountsHelper->prepareUpdatePurchaseDiscount($discount, $purchase);
        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function updateDiscount($discount)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $this->backendDiscountsHelper->update($discount);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function addDiscount($discount)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $discountId = $this->backendDiscountsHelper->add($discount);
        return ExtenderFacade::execute(__METHOD__, $discountId, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function deleteDiscounts($discountIds, $orderId)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $this->backendDiscountsHelper->delete($discountIds, $orderId);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function sortDiscountPositions($positions)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        list ($ids, $positions) = $this->backendDiscountsHelper->sortPositions($positions);
        return ExtenderFacade::execute(__METHOD__, [$ids, $positions], func_get_args());
    }

    /** TODO Remove deprecated method. Deprecated: 4.1.0)*/
    public function updateDiscountPositions($ids, $positions)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use ' . BackendDiscountsHelper::class, E_USER_DEPRECATED);

        $this->backendDiscountsHelper->updatePositions($ids, $positions);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}