<?php

namespace Okay\Modules\OkayCMS\DeliveryFields\Entities;

use Okay\Core\Entity\Entity;
use Okay\Modules\OkayCMS\DeliveryFields\Init\Init;

class DeliveryFieldsEntity extends Entity
{
    protected static $fields = [
        'id',
        'position',
        'required',
        'visible',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $table = 'okaycms__delivery_fields';
    protected static $langTable = 'okaycms__delivery_fields';
    protected static $langObject = 'delivery_field';
    protected static $tableAlias = 'df';
    protected static $defaultOrderFields = [
        'position',
    ];

    public function getFieldsDeliveries(array $fieldId): array
    {
        $select = $this->queryFactory->newSelect();
        $select->from(Init::FIELD_DELIVERY_RELATION_TABLE)
            ->cols([
                'field_id',
                'delivery_id',
            ])->where('field_id IN (:field_id)')
            ->bindValue('field_id', $fieldId);

        $this->db->query($select);
        return $this->db->results();
    }

    public function addFieldDelivery(int $fieldId, int $deliveryId): bool
    {
        $insert = $this->queryFactory->newInsert();
        $insert->into(Init::FIELD_DELIVERY_RELATION_TABLE)
            ->cols([
                'field_id',
                'delivery_id',
            ])
            ->bindValues([
                'field_id' => $fieldId,
                'delivery_id' => $deliveryId,
            ])
            ->ignore();

        return $this->db->query($insert);
    }

    public function deleteFieldDeliveries(int $fieldId): bool
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from(Init::FIELD_DELIVERY_RELATION_TABLE)
            ->where('field_id = :field_id')
            ->bindValues([
                'field_id' => $fieldId,
            ]);

        return $this->db->query($delete);
    }

    public function deleteFieldDelivery(int $deliveryId): bool
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from(Init::FIELD_DELIVERY_RELATION_TABLE)
            ->where('delivery_id = :delivery_id')
            ->bindValues([
                'delivery_id' => $deliveryId,
            ]);

        return $this->db->query($delete);
    }

    public function filter__delivery_id($deliveriesIds)
    {
        $this->select->innerJoin(
            Init::FIELD_DELIVERY_RELATION_TABLE . ' dfr',
            'df.id = dfr.field_id AND dfr.delivery_id IN (?)',
            [
                (array)$deliveriesIds,
            ]
        );
    }
}