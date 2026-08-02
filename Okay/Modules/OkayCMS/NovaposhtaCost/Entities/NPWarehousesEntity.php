<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Entities;

use Okay\Core\Entity\Entity;

class NPWarehousesEntity extends Entity
{
    protected static $fields = [
        'id',
        'ref',
        'city_ref',
        'type',
        'updated_at',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $table = 'okaycms__np_warehouses';
    protected static $tableAlias = 'npw';
    protected static $langTable = 'okaycms__np_warehouses';
    protected static $langObject = 'warehouse';
    protected static $defaultOrderFields = [
        'number'
    ];

    public function add($object)
    {
        $object = (object)$object;
        /** @var object{updated_at: string}&\stdClass $object */
        $object->updated_at = 'NOW()';
        return parent::add($object);
    }

    public function update($ids, $object)
    {
        $object = (object)$object;
        /** @var object{updated_at: string}&\stdClass $object */
        $object->updated_at = 'NOW()';
        parent::update($ids, $object);
    }

    /**
     * @param list<string> $warehousesTypes
     */
    public function removeRedundant(string $updatedAt, array $warehousesTypes = [])
    {
        $sql = $this->queryFactory->newSqlQuery();

        $warehousesTypesWhere = '';
        if (!empty($warehousesTypes)) {
            $warehousesTypesWhere = 'AND npw.type IN (:types)';
            $sql->bindValue('types', $warehousesTypes);
        }

        $sql->setStatement(sprintf(
            '
                DELETE npw, l FROM %s npw
                INNER JOIN %s l ON l.warehouse_id = npw.id
                WHERE
                npw.updated_at < :updated_at
                %s
            ',
            self::getTable(),
            self::getLangTable(),
            $warehousesTypesWhere
        ))->bindValues([
            'updated_at' => $updatedAt,
        ]);

        $this->db->query($sql);
    }

    /**
     * @return array<string, int|string>
     */
    public function countByTypes(): array
    {
        $this->setUp();
        $this->select->cols([
            'COUNT(DISTINCT id) AS count',
            'type'
        ]);
        $this->select->groupBy([
            'type',
        ]);
        $this->db->query($this->select);

        return $this->getResults('count', 'type');
    }
}
