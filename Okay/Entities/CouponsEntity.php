<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class CouponsEntity extends Entity
{

    protected static $fields = [
        'id',
        'code',
        'value',
        'type',
        'expire',
        'min_order_price',
        'single',
        'usages',
    ];

    protected static $additionalFields = [
        '((DATE(NOW()) <= DATE(c.expire) OR c.expire IS NULL) AND (c.usages=0 OR NOT c.single)) AS valid',
    ];

    protected static $searchFields = [
        'code',
    ];

    protected static $defaultOrderFields = [
        'valid DESC',
        'id DESC'
    ];

    protected static $table = '__coupons';
    protected static $tableAlias = 'c';
    protected static $alternativeIdField = 'code';
    protected static $langTable;
    protected static $langObject;
    
    protected function filter__valid($valid)
    {
        $validFilter = '((DATE(NOW()) <= DATE(c.expire) OR c.expire IS NULL) AND (c.usages=0 OR NOT c.single))';
        if (empty($valid)) {
            $validFilter = 'NOT ' . $validFilter;
        }
        $this->select->where($validFilter);
    }
    
}
