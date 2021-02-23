<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class OrderHistoryEntity extends Entity
{
    protected static $fields = [
        'id',
        'order_id',
        'manager_id',
        'new_status_id',
        'date',
        'text',
    ];
    
    protected static $defaultOrderFields = [
        'id ASC',
    ];

    protected static $table = 'order_history';
    protected static $tableAlias = 'oh';
    protected static $langTable;
    protected static $langObject;
    
    public function getOrdersLastChanges($ordersIds)
    {
        $this->setUp();
        $this->select->distinct(true);
        $this->select->cols($this->getAllFields());
        
        $this->select->where('order_id in (:order_id)')
            ->bindValues([
                'order_id' => $ordersIds,
            ]);
        
        $this->select->where('id = (SELECT MAX(id) FROM '.self::getTable().' t WHERE t.order_id in (:sub_order_id) AND oh.order_id=t.order_id LIMIT 1)')
            ->bindValues([
                'sub_order_id' => $ordersIds,
            ]);

        $this->db->query($this->select);

        $results = $this->getResults(null, 'order_id');
        return ExtenderFacade::execute([static::class, __FUNCTION__], $results, func_get_args());
    }
    
}
