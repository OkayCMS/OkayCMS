<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Phone;
use Okay\Core\Modules\Extender\ExtenderFacade;

class OrdersEntity extends Entity
{
    protected static $fields = [
        'id',
        'delivery_id',
        'delivery_price',
        'payment_method_id',
        'separate_delivery',
        'paid',
        'payment_date',
        'closed',
        'date',
        'user_id',
        'name',
        'last_name',
        'address',
        'phone',
        'email',
        'comment',
        'status_id',
        'url',
        'undiscounted_total_price',
        'total_price',
        'note',
        'ip',
        'lang_id',
        'referer_channel',
        'referer_source',
    ];

    protected static $defaultOrderFields = [
        'id DESC',
    ];

    protected static $table = '__orders';
    protected static $tableAlias = 'o';
    protected static $alternativeIdField = 'url';
    protected static $additionalFields = [
        'os.color as status_color',
    ];

    public function get($id)
    {
        if (empty($id)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }

        $this->select->join('LEFT', '__orders_status AS os', 'o.status_id=os.id');
        return parent::get($id);
    }

    public function find(array $filter = [])
    {
        $this->select->join('LEFT', '__orders_labels AS ol', 'o.id=ol.order_id');
        $this->select->join('LEFT', '__orders_status AS os', 'o.status_id=os.id');
        
        // Устанавливаем группировку по id только если эта колонка есть в запросе
        $selectFields = $this->getAllFieldsKeyLabel();
        if (in_array('id', $selectFields)) {
            $this->select->groupBy(['id']);
        }
        
        return parent::find($filter);
    }

    public function count(array $filter = [])
    {
        $this->select->join('LEFT', '__orders_labels AS ol', 'o.id=ol.order_id');
        return parent::count($filter);
    }

    public function update($ids, $order)
    {
        $ids = (array)$ids;
        
        if (is_object($order)) {
            $order = (array)$order;
        }

        if (isset($order['paid'])) {
            $currentPaid = $this->col('paid')->findOne(['id' => $ids]);
            if ($order['paid'] != $currentPaid) {
                $this->markedPaid($ids, (bool)$order['paid']);
                $order['payment_date'] = 'now()';
            }
        }
        
        parent::update($ids, $order);
        return $ids;
    }

    /**
     * Метод вызывается при отметке заказов как оплаченых.
     * 
     * @param array $ids
     * @param bool $state
     */
    private function markedPaid(array $ids, $state)
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function delete($ids)
    {
        $ids = (array)$ids;
        if (!empty($ids)) {

            // Возвращаем товары на склад
            foreach ($ids as $id) {
                $this->open($id);
            }
            
            $delete = $this->queryFactory->newDelete();
            $delete->from(PurchasesEntity::getTable())
                ->where('order_id IN (:order_ids)')
                ->bindValue('order_ids', $ids);
            $this->db->query($delete);

            $delete = $this->queryFactory->newDelete();
            $delete->from('__orders_labels')
                ->where('order_id IN (:order_ids)')
                ->bindValue('order_ids', $ids);
            $this->db->query($delete);
        }

        return parent::delete($ids);
    }

    public function findOrdersDates(array $filter = [])
    {
        $this->select->join('LEFT', '__orders_labels AS ol', 'o.id=ol.order_id');
        $this->select->join('LEFT', '__orders_status AS os', 'o.status_id=os.id');
        if ($result = parent::find($filter)) {
            $result = reset($result);
        }
        return $result;
    }
    
    public function countOrdersByStatuses(array $filter = [])
    {
        $this->select->join('LEFT', '__orders_labels AS ol', 'o.id=ol.order_id');
        $this->select->join('LEFT', '__orders_status AS os', 'o.status_id=os.id');
        $this->cols(['count( DISTINCT o.id) AS count', 'status_id']);
        $this->select->groupBy(['status_id']);
        $this->mappedBy('status_id');
        return parent::find($filter);
    }
    
    public function add($order)
    {
        /** @var OrderStatusEntity $orderStatusEntity */
        $orderStatusEntity = $this->entity->get(OrderStatusEntity::class);

        $order = (object)$order;
        $order->url = md5(uniqid($this->config->salt, true));
        if (empty($order->date)) {
            $order->date = 'now()';
        }

        $allStatuses = $orderStatusEntity->mappedBy('id')->find();
        if (empty($order->status_id)) {
            $order->status_id = reset($allStatuses)->id;
        }

        $id = parent::add($order);
        if ($allStatuses[$order->status_id]->is_close == 1) {
            $this->close(intval($id));
        } else {
            $this->open(intval($id));
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $id, func_get_args());
    }

    /*Закрытие заказа(списание количества)*/
    public function close($orderId)
    {
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entity->get(VariantsEntity::class);

        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entity->get(PurchasesEntity::class);

        $order = $this->get(intval($orderId));
        if (empty($order)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        if (!$order->closed) {
            $variantsAmounts = [];
            $purchases = $purchasesEntity->find(['order_id' => $order->id]);
            foreach ($purchases as $purchase) {
                if (isset($variantsAmounts[$purchase->variant_id])) {
                    $variantsAmounts[$purchase->variant_id] += $purchase->amount;
                } else {
                    $variantsAmounts[$purchase->variant_id] = $purchase->amount;
                }
            }

            foreach ($variantsAmounts as $id => $amount) {
                $variant = $variantsEntity->get($id);
                if (empty($variant) || ($variant->stock < $amount)) {
                    return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
                }
            }
            foreach ($purchases as $purchase) {
                $variant = $variantsEntity->get($purchase->variant_id);
                if (!$variant->infinity) {
                    $newStock = $variant->stock - $purchase->amount;
                    $variantsEntity->update($variant->id, ['stock' => $newStock]);
                }
            }
            $this->update($order->id, ['closed' => 1]);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $order->id, func_get_args());
    }

    /*Открытие заказа (возвращение количества)*/
    public function open($orderId)
    {
        /** @var VariantsEntity $variantsEntity */
        $variantsEntity = $this->entity->get(VariantsEntity::class);

        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entity->get(PurchasesEntity::class);

        $order = $this->get(intval($orderId));
        if (empty($order)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        if ($order->closed) {
            $purchases = $purchasesEntity->find(['order_id' => $order->id]);
            foreach ($purchases as $purchase) {
                $variant = $variantsEntity->get($purchase->variant_id);
                if ($variant && !$variant->infinity) {
                    $newStock = $variant->stock + $purchase->amount;
                    $variantsEntity->update($variant->id, ['stock' => $newStock]);
                }
            }
            $this->update($order->id, ['closed' => 0]);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $order->id, func_get_args());
    }

    public function getNeighborsOrders($filter)
    {
        if (empty($filter['id'])) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $prevSelect = $this->queryFactory->newSelect();
        $nextSelect = $this->queryFactory->newSelect();

        $nextSelect->from('__orders AS o')
            ->cols(['MIN(o.id) as id'])
            ->where("o.id>:id")
            ->bindValue('id', (int)$filter['id'])
            ->limit(1);

        $prevSelect->from('__orders AS o')
            ->cols(['MAX(o.id) as id'])
            ->where("o.id<:id")
            ->bindValue('id', (int)$filter['id'])
            ->limit(1);

        if (!empty($filter['status_id'])) {
            $nextSelect->where('status_id=:status_id')
                ->bindValue('status_id', (int)$filter['status_id']);

            $prevSelect->where('status_id=:status_id')
                ->bindValue('status_id', (int)$filter['status_id']);
        }

        if (!empty($filter['label'])) {
            $nextSelect->join('INNER', '__orders_labels AS ol', 'o.id=ol.order_id AND label_id=:label_id')
                ->bindValue('label_id', (int)$filter['label_id']);

            $prevSelect->join('INNER', '__orders_labels AS ol', 'o.id=ol.order_id AND label_id=:label_id')
                ->bindValue('label_id', (int)$filter['label_id']);
        }

        $ordersIds = [];
        $this->db->query($nextSelect);
        $id = $this->db->result('id');
        $ordersIds[$id] = 'next';

        $this->db->query($prevSelect);
        $id = $this->db->result('id');
        $ordersIds[$id] = 'prev';

        $result = ['next' => null, 'prev' => null];
        if (!empty($ordersIds)) {
            foreach ($this->find(['id' => array_keys($ordersIds)]) as $o) {
                $result[$ordersIds[$o->id]] = $o;
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    public function updateTotalPrice($orderId)
    {
        /** @var DiscountsEntity $discountsEntity */
        $discountsEntity = $this->entity->get(DiscountsEntity::class);

        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entity->get(PurchasesEntity::class);

        $order = $this->get(intval($orderId));
        if (empty($order)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], false, func_get_args());
        }

        $purchases = $purchasesEntity->find(['order_id' => $order->id]);
        $undiscountedTotalPrice = 0;
        $totalPrice = 0;
        if (!empty($purchases)) {
            foreach ($purchases as $purchase) {
                $undiscountedTotalPrice += $purchase->price * $purchase->amount;
            }

            $totalPrice = $undiscountedTotalPrice;
            $cartDiscounts = $discountsEntity->order('position')->find([
                'entity' => 'order',
                'entity_id' => $orderId
            ]);
            if (!empty($cartDiscounts)) {
                foreach ($cartDiscounts as $discount) {
                    switch ($discount->type) {
                        case 'absolute':
                            $totalPrice -= $discount->value;
                            break;

                        case 'percent':
                            if ($discount->from_last_discount) {
                                $totalPrice -= $totalPrice * ($discount->value / 100);
                            } else {
                                $totalPrice -= $undiscountedTotalPrice * ($discount->value / 100);
                            }
                            break;
                    }
                }
            }
        }

        if (!$order->separate_delivery) {
            $totalPrice += $order->delivery_price;
        }

        $this->update($order->id, [
            'undiscounted_total_price' => $undiscountedTotalPrice,
            'total_price' => $totalPrice
        ]);
        return ExtenderFacade::execute([static::class, __FUNCTION__], $order->id, func_get_args());
    }

    /**
     * Метод ищет другие заказы клиента сравнивая по почте или телефону
     * 
     * @param $order
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function findOtherOfClient($order, $page = 1, $perPage = 10)
    {
        $offset       = $this->calculateOffset($page, $perPage);
        if (!$orderMatches = $this->determineOrderMatchParams($order, $page, $perPage)) {
            return [];
        }

        $filter       = [];
        $filter['id'] = array_keys($orderMatches);
        if ($page !== 'all') {
            $filter['limit']  = $perPage;
            $filter['offset'] = $offset;
        }

        $orders = $this->find($filter);
        $orders = $this->attachFindBy($orders, $orderMatches);
        $orders = $this->attachStatusName($orders);
        return $orders;
    }

    private function calculateOffset($page, $perPage)
    {
        return $perPage * $page - $perPage;
    }

    private function determineOrderMatchParams($order, $page, $perPage)
    {
        if ($page === 'all') {
            $limitOffset = '';
        } else {
            $limitOffset = 'LIMIT :per_page OFFSET :offset';
        }

        $sql = $this->queryFactory->newSqlQuery();
        return $sql->setStatement("
                SELECT 
                    id, 
                    IF(o.email IS NOT NULL AND o.email != '' AND o.email = :email, 1, 0) AS `email_match`, 
                    IF(o.phone IS NOT NULL AND o.phone != '' AND o.phone = :phone, 1, 0) AS `phone_match` 
                FROM ".self::getTable()." AS o 
                WHERE id <> :order_id
                  AND ( (o.email != '' AND o.email = :email) OR (o.phone != '' AND o.phone = :phone) )
                ORDER BY id DESC
                {$limitOffset}
            ")
            ->bindValues([
                'order_id'  => $order->id,
                'email'     => $order->email,
                'phone'     => Phone::clear($order->phone),
                'per_page'  => $perPage,
                'offset'    => $this->calculateOffset($page, $perPage)
            ])
            ->results(null, 'id');
    }

    private function attachFindBy($orders, $matches)
    {
        foreach($orders as $order) {
            $order->match_by_email = $matches[$order->id]->email_match;
            $order->match_by_phone = $matches[$order->id]->phone_match;
        }
        return $orders;
    }

    private function attachStatusName($orders)
    {
        /** @var OrderStatusEntity $orderStatusEntity */
        $orderStatusEntity = $this->entity->get(OrderStatusEntity::class);
        $orderStatuses = $orderStatusEntity->mappedBy('id')->find();
        foreach($orders as $order) {
            $order->status_name = $orderStatuses[$order->status_id]->name;
        }

        return $orders;
    }

    public function countOtherOfClient($order)
    {
        $order = (object) $order;

        $sql = $this->queryFactory->newSqlQuery();
        return $sql->setStatement("
                SELECT 
                    COUNT(*) AS count  
                FROM ".self::getTable()." AS o 
                WHERE id <> :order_id
                  AND ( (o.email != '' AND o.email = :email) OR (o.phone != '' AND o.phone = :phone) )
            ")
            ->bindValues([
                'order_id'  => $order->id,
                'email' => $order->email,
                'phone' => Phone::clear($order->phone),
            ])
            ->result('count');
    }

    protected function filter__modified_since($modified)
    {
        $this->select->where('o.modified > :modified')
            ->bindValue('modified', $modified);
    }

    protected function filter__label($labelId)
    {
        $this->select->where('ol.label_id = :label_id')
            ->bindValue('label_id', $labelId);
    }

    protected function filter__from_date($fromDate)
    {
        if (!empty($fromDate)) {
            $this->select->where('o.date >= :from_date')
                ->bindValue('from_date', date('Y-m-d 00:00:00', strtotime($fromDate)));
        }
    }

    protected function filter__to_date($toDate)
    {
        if (!empty($toDate)) {
            $this->select->where('o.date < :to_date')
                ->bindValue('to_date', date('Y-m-d 23:59:59', strtotime($toDate)));
        }
    }

    protected function filter__not_id($ids)
    {
        $this->select->where('o.id NOT IN(:ids)')
            ->bindValue(':ids', (array) $ids);
    }

    protected function filter__keyword($keywords)
    {
        $keywords = explode(' ', $keywords);

        foreach ($keywords as $keyNum=>$keyword) {
            $this->select->where("(
                o.id LIKE :keyword_id_{$keyNum}
                OR o.name LIKE :keyword_name_{$keyNum}
                OR o.last_name LIKE :keyword_last_name_{$keyNum}
                OR REPLACE(o.phone, '-', '') LIKE :keyword_phone_{$keyNum}
                OR o.address LIKE :keyword_address_{$keyNum}
                OR o.email LIKE :keyword_email_{$keyNum}
                OR o.id IN (SELECT order_id FROM __purchases WHERE product_name LIKE :keyword_product_name_{$keyNum} OR variant_name LIKE :keyword_product_name_{$keyNum})
            )");

            $this->select->bindValues([
                "keyword_id_{$keyNum}"           => '%' . $keyword . '%',
                "keyword_name_{$keyNum}"         => '%' . $keyword . '%',
                "keyword_last_name_{$keyNum}"         => '%' . $keyword . '%',
                "keyword_phone_{$keyNum}"        => '%' . $keyword . '%',
                "keyword_address_{$keyNum}"      => '%' . $keyword . '%',
                "keyword_email_{$keyNum}"        => '%' . $keyword . '%',
                "keyword_product_name_{$keyNum}" => '%' . $keyword . '%',
                "keyword_product_name_{$keyNum}" => '%' . $keyword . '%',
            ]);
        }
    }
}
