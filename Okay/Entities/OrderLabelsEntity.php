<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class OrderLabelsEntity extends Entity
{

    protected static $fields = [
        'id',
        'color',
        'position',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $defaultOrderFields = [
        'position',
    ];

    protected static $table = '__labels';
    protected static $langObject = 'order_labels';
    protected static $langTable = 'orders_labels';
    protected static $tableAlias = 'lb';
    protected static $additionalFields = [
        'MAX(ol.order_id) as order_id',
    ];
    
    protected function filter__order_id($orderIds)
    {
        $this->select->where('ol.order_id IN (:order_ids)')
            ->bindValue('order_ids', (array)$orderIds);
    }
    
    public function get($id)
    {
        if (empty($id)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
        }
        
        $this->select->join('LEFT', '__orders_labels AS ol', 'ol.label_id = lb.id');
        return parent::get($id);
    }
    
    public function count(array $filter = [])
    {
        $this->select->join('LEFT', '__orders_labels AS ol', 'ol.label_id = lb.id');
        return parent::count($filter);
    }
    
    public function find(array $filter = [])
    {
        $this->select->join('LEFT', '__orders_labels AS ol', 'ol.label_id = lb.id');
        $this->select->groupBy(['lb.id']);
        return parent::find($filter);
    }

    public function delete($ids)
    {
        $ids = (array)$ids;
        if (!empty($ids)) {
            $delete = $this->queryFactory->newDelete();
            $delete->from('__orders_labels')
                ->where('label_id IN (:label_ids)')
                ->bindValue('label_ids', $ids);
            $this->db->query($delete);
        }
        return parent::delete($ids);
    }

    public function getOrdersLabels($ordersIds = [])
    {
        if (empty($ordersIds)) {
            return ExtenderFacade::execute([static::class, __FUNCTION__], [], func_get_args());
        }

        $select = $this->queryFactory->newSelect();
        $select->from('__orders_labels')
            ->cols([
                'order_id',
                'label_id',
            ])
            ->where('order_id IN (:orders_ids)')
            ->bindValue('orders_ids', (array)$ordersIds);
        
        $this->db->query($select);
        $labelsIds = [];
        $ordersLabels = [];
        foreach ($this->db->results() as $result) {
            $ordersLabels[$result->order_id][] = $result->label_id;
            $labelsIds[] = $result->label_id;
        }
        
        if (!empty($labelsIds)) {
            $labels = [];
            foreach ($this->find(['id'=>$labelsIds]) as $label) {
                $labels[$label->id] = $label;
            }
        }

        $result = [];
        if (!empty($labels) && !empty($ordersLabels)) {
            foreach ($ordersLabels as $orderId=>$labelsIds) {
                foreach ($labelsIds as $labelId) {
                    if (isset($labels[$labelId])) {
                        $res = clone $labels[$labelId];
                        $res->order_id = $orderId;
                        $result[] = $res;
                    }
                }
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }
    
    /*Обновление меток заказа*/
    public function updateOrderLabels($orderId, array $labelsIds)
    {
        if (!empty($labelsIds)) {
            $labelsIds = array_unique($labelsIds);
            $delete = $this->queryFactory->newDelete();
            $delete->from('__orders_labels')
                ->where('order_id=' . intval($orderId));
            $this->db->query($delete);
            foreach ($labelsIds as $lId) {
                $insert = $this->queryFactory->newInsert();
                $insert->into('__orders_labels')
                    ->cols([
                        'order_id' => $orderId,
                        'label_id' => $lId,
                    ]);
                $this->db->query($insert);
            }
        }

        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /*Добавление меток к заказу*/
    public function addOrderLabels($orderId, array $labelsIds)
    {
        if (!empty($labelsIds)) {
            $labelsIds = array_unique($labelsIds);
            foreach ($labelsIds as $lId) {
                $insert = $this->queryFactory->newInsert();
                $insert->into('__orders_labels')
                    ->cols([
                        'order_id' => $orderId,
                        'label_id' => $lId,
                    ])
                ->ignore();
                $this->db->query($insert);
            }
        }

        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    /*Удаление меток с заказа*/
    public function deleteOrderLabels($orderId, array $labelsIds)
    {
        if (!empty($labelsIds)) {
            $labelsIds = array_unique($labelsIds);
            $delete = $this->queryFactory->newDelete();
            $delete->from('__orders_labels')
                ->where('order_id=' . intval($orderId))
                ->where('label_id IN (:label_ids)')
                ->bindValue('label_ids', $labelsIds);
            $this->db->query($delete);
        }

        ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

}
