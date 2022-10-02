<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\QueryFactory;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Helpers\DiscountsHelper;

class BackendDiscountsHelper
{
    /** @var QueryFactory */
    private $queryFactory;

    /** @var DiscountsHelper */
    private $discountsHelper;


    /** @var OrdersEntity */
    private $ordersEntity;

    /** @var DiscountsEntity */
    private $discountsEntity;

    public function __construct(
        EntityFactory   $entityFactory,
        QueryFactory    $queryFactory,
        DiscountsHelper $discountsHelper
    ) {
        $this->queryFactory    = $queryFactory;
        $this->discountsHelper = $discountsHelper;

        $this->ordersEntity    = $entityFactory->get(OrdersEntity::class);
        $this->discountsEntity = $entityFactory->get(DiscountsEntity::class);
    }

    public function getBeforeUpdate($orderId)
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

        return ExtenderFacade::execute(__METHOD__, $discounts, func_get_args());
    }

    public function getOrderDiscounts($orderId)
    {
        $discountsDB = $this->discountsEntity->find([
            'entity' => 'order',
            'entity_id' => $orderId
        ]);
        $order = $this->ordersEntity->findOne(['id' => $orderId]);
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

    public function update($discount)
    {
        $this->discountsEntity->update($discount->id, $discount);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function add($discount)
    {
        $discountId = $this->discountsEntity->add($discount);

        return ExtenderFacade::execute(__METHOD__, $discountId, func_get_args());
    }

    public function delete($postedDiscountsIds, $orderId)
    {
        if (empty($orderId)) {
            return false;
        }
        $select = $this->queryFactory->newSelect();
        $select ->from(DiscountsEntity::getTable())
            ->cols(['id'])
            ->where("((`entity` = 'order' AND `entity_id` = :order_id) OR
                            (`entity` = 'purchase' AND `entity_id` IN (SELECT `id` FROM `ok_purchases` WHERE `order_id` = :order_id)))")
            ->bindValues(['order_id' => $orderId]);
        if (!empty($postedDiscountsIds)) {
            $select->where('`id` NOT IN (:discount_ids)')
                ->bindValues([ 'discount_ids' => $postedDiscountsIds]);
        }
        $idsForDelete = $select->results('id');
        $this->discountsEntity->delete($idsForDelete);
    }

    public function sortPositions($positions)
    {
        $ids = array_keys($positions);
        sort($positions);

        return ExtenderFacade::execute(__METHOD__, [$ids, $positions], func_get_args());
    }

    public function updatePositions($ids, $positions)
    {
        foreach($positions as $i=>$position) {
            $this->discountsEntity->update($ids[$i], array('position' => (int) $position));
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}