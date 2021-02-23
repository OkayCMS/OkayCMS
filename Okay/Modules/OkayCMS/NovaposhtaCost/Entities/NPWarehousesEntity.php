<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Entities;


use Okay\Core\Entity\Entity;

class NPWarehousesEntity extends Entity
{
    protected static $fields = [
        'id',
        'ref',
        'city_ref',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $table = 'okaycms__np_warehouses';
    protected static $tableAlias = 'npw';
    protected static $langTable = 'okaycms__np_warehouses';
    protected static $langObject = 'warehouse';
}