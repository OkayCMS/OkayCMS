<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class OrderStatusEntity extends Entity
{

    protected static $fields = [
        'id',
        'is_close',
        'color',
        'position',
        'status_1c',
    ];

    protected static $langFields = [
        'name',
    ];

    protected static $defaultOrderFields = [
        'position ASC',
    ];

    protected static $table = '__orders_status';
    protected static $langObject = 'order_status';
    protected static $langTable = 'orders_status';
    protected static $tableAlias = 'os';
    
}
