<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Entities;


use Okay\Core\Entity\Entity;

class NPCostDeliveryDataEntity extends Entity
{
    protected static $fields = [
        'id',
        'city_id',
        'warehouse_id',
        'order_id',
        'delivery_term',
        'redelivery',
        'city_name',
        'area_name',
        'region_name',
        'street',
        'house',
        'apartment',
    ];

    protected static $table = '__okaycms__np_cost_delivery_data';
    protected static $tableAlias = 'npdd';
    
    public function getByOrderId($orderId)
    {
        if (empty($orderId)) {
            return null;
        }

        $this->setUp();

        $filter['order_id'] = (int)$orderId;

        $this->buildFilter($filter);
        $this->select->cols($this->getAllFields());

        $this->db->query($this->select);
        return $this->getResult();
    }
    
}