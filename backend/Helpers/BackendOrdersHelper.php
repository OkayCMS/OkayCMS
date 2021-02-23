<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Classes\Discount;
use Okay\Core\EntityFactory;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\DiscountsEntity;
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
use Okay\Helpers\DiscountsHelper;
use Okay\Helpers\MoneyHelper;

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

    /** @var DiscountsEntity */
    private $discountsEntity;
    
    /** @var MoneyHelper */
    private $moneyHelper;

    /** @var Request */
    private $request;

    /** @var Settings */
    private $settings;

    /** @var QueryFactory */
    private $queryFactory;

    /** @var EntityFactory */
    private $entityFactory;

    /** @var DiscountsHelper */
    private $discountsHelper;
    
    public function __construct(
        EntityFactory   $entityFactory,
        MoneyHelper     $moneyHelper,
        Request         $request,
        Settings        $settings,
        QueryFactory    $queryFactory,
        DiscountsHelper $discountsHelper
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
        $this->discountsEntity    = $entityFactory->get(DiscountsEntity::class);

        $this->entityFactory   = $entityFactory;
        $this->moneyHelper     = $moneyHelper;
        $this->request         = $request;
        $this->settings        = $settings;
        $this->queryFactory    = $queryFactory;
        $this->discountsHelper = $discountsHelper;
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
        
        return ExtenderFacade::execute(__METHOD__, $products, func_get_args());;
    }
    
    /**
     * @var $order
     * Метод заглушка, чтобы модули могли зацепиться
     */
    public function executeCustomPost($order)
    {
        ExtenderFacade::execute(__METHOD__, $order, func_get_args());
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

    public function updatePurchase($purchase)
    {
        $this->purchasesEntity->update($purchase->id, $purchase);
        ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }

    public function prepareUpdatePurchase($order, $purchase)
    {
        $variant = $this->variantsEntity->get($purchase->variant_id);
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

    public function addPurchase($purchase)
    {
        $purchaseId = $this->purchasesEntity->add($purchase);
        return ExtenderFacade::execute(__METHOD__, $purchaseId, func_get_args());
    }

    public function prepareAddPurchase($order, $purchase)
    {
        $purchase->id = null;
        $purchase->order_id = $order->id;
        return ExtenderFacade::execute(__METHOD__, $purchase, func_get_args());
    }
    
    public function deletePurchases($order, array $postedPurchasesIds)
    {
        foreach ($this->purchasesEntity->find(['order_id' => $order->id]) as $p) {
            if (!in_array($p->id, $postedPurchasesIds)) {
                $this->purchasesEntity->delete($p->id);
            }
        }
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
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

    public function getDiscountsBeforeUpdate($orderId)
    {
        $select = $this->queryFactory->newSelect();
        $select ->from(DiscountsEntity::getTable())
                ->cols(['*'])
                ->where("((`entity` = 'order' AND `entity_id` = :order_id) OR
                                (`entity` = 'purchase' AND `entity_id` IN (SELECT `id` FROM `ok_purchases` WHERE `order_id` = :order_id)))")
                ->bindValue('order_id', $orderId);
        $discountIds = $select->results('id');
        if (!empty($discountIds)) {
            $discounts = $this->discountsEntity->mappedBy('id')->find(['id' => $discountIds]);
        } else {
            $discounts = [];
        }

        return $discounts;
    }

    public function getOrderDiscounts($orderId)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        $discountsDB = $this->discountsEntity->find([
            'entity' => 'order',
            'entity_id' => $orderId
        ]);
        $order = $ordersEntity->findOne(['id' => $orderId]);
        list($discounts) = $this->discountsHelper->calculateDiscounts($this->discountsHelper->buildFromDB($discountsDB), $order->undiscounted_total_price);

        return ExtenderFacade::execute(__METHOD__, $discounts, func_get_args());
    }

    public function prepareUpdateOrderDiscount($discount, $order)
    {
        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    public function prepareAddOrderDiscount($discount, $order)
    {
        $discount->entity = 'order';
        $discount->entity_id = $order->id;

        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    public function prepareUpdatePurchaseDiscount($discount, $purchase)
    {
        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    public function prepareAddPurchaseDiscount($discount, $purchase)
    {
        $discount->entity = 'purchase';
        $discount->entity_id = $purchase->id;

        return ExtenderFacade::execute(__METHOD__, $discount, func_get_args());
    }

    public function updateDiscount($discount)
    {
        $this->discountsEntity->update($discount->id, $discount);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function addDiscount($discount)
    {
        $discountId = $this->discountsEntity->add($discount);

        return ExtenderFacade::execute(__METHOD__, $discountId, func_get_args());
    }

    public function deleteDiscounts($discountIds, $orderId)
    {
        $select = $this->queryFactory->newSelect();
        $select ->from(DiscountsEntity::getTable())
            ->cols(['id'])
            ->where("((`entity` = 'order' AND `entity_id` = :order_id) OR
                            (`entity` = 'purchase' AND `entity_id` IN (SELECT `id` FROM `ok_purchases` WHERE `order_id` = :order_id)))")
            ->bindValues(['order_id' => $orderId]);
        if (!empty($discountIds)) {
            $select->where('`id` NOT IN (:discount_ids)')
                ->bindValues([ 'discount_ids' => $discountIds]);
        }
        $idsForDelete = $select->results('id');
        $this->discountsEntity->delete($idsForDelete);
    }

    public function sortDiscountPositions($positions)
    {
        $ids = array_keys($positions);
        sort($positions);

        return ExtenderFacade::execute(__METHOD__, [$ids, $positions], func_get_args());
    }

    public function updateDiscountPositions($ids, $positions)
    {
        foreach($positions as $i=>$position) {
            $this->discountsEntity->update($ids[$i], array('position' => (int) $position));
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}