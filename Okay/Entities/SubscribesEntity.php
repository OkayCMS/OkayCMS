<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class SubscribesEntity extends Entity
{

    protected static $fields = [
        'id',
        'email',
    ];
    
    protected static $searchFields = [
        'email',
    ];

    protected static $table = '__subscribe_mailing';
    protected static $tableAlias = 's';
    protected static $alternativeIdField = 'email';
    protected static $langTable;
    protected static $langObject;
    
}
