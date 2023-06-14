<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Entities;


use Okay\Core\Entity\Entity;

class NPDeliveryTypesEntity extends Entity
{
    protected static $fields = [
        'id',
        'warehouses_type_refs',
        'position',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $table = 'okaycms__np_delivery_types';
    protected static $langTable = 'okaycms__np_delivery_types';
    protected static $langObject = 'delivery_type';
    protected static $tableAlias = 'npdt';
    
    protected static $defaultOrderFields = [
        'position'
    ];

    public function add($object)
    {
        $object = (object)$object;
        if (property_exists($object, 'warehouses_type_refs') && is_array($object->warehouses_type_refs)) {
            $object->warehouses_type_refs = implode(',', $object->warehouses_type_refs);
        }

        return parent::add($object);
    }
    public function update($ids, $object)
    {
        $object = (object)$object;
        if (property_exists($object, 'warehouses_type_refs') && is_array($object->warehouses_type_refs)) {
            $object->warehouses_type_refs = implode(',', $object->warehouses_type_refs);
        }

        return parent::update($ids, $object);
    }

    public function get($id)
    {
        $deliveryType = parent::get($id);
        if (!empty($deliveryType)) {
            $deliveryType->warehouses_type_refs = explode(',', $deliveryType->warehouses_type_refs);
        }
        return $deliveryType;
    }

    public function find(array $filter = [])
    {
        $result = parent::find($filter);
        foreach ($result as $deliveryType) {
            $deliveryType->warehouses_type_refs = explode(',', $deliveryType->warehouses_type_refs);
        }

        return $result;
    }
}