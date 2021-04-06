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

    public function removeRedundant()
    {
        $warehousesTypesToSave = $this->settings->get('np_warehouses_types');
        $delete = $this->queryFactory->newDelete();
        $delete->from($this->getTable())->where('type NOT IN (:types)');
        $delete->bindValue('types', $warehousesTypesToSave);
        $this->db->query($delete);
    }
    
}