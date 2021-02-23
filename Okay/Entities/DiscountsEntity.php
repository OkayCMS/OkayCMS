<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class DiscountsEntity extends Entity
{
    protected static $fields = [
        'id',
        'entity',
        'entity_id',
        'type',
        'value',
        'from_last_discount',
        'position'
    ];

    protected static $langFields = [
        'name',
        'description'
    ];

    protected static $defaultOrderFields = [
        'dis.position ASC'
    ];

    protected static $table = 'discounts';
    protected static $tableAlias = 'dis';
    protected static $langObject = 'discount';
    protected static $langTable = 'discounts';
}