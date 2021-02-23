<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class UserComparisonItemsEntity extends Entity
{
    protected static $fields = [
        'id',
        'user_id',
        'product_id',
    ];

    protected static $defaultOrderFields = [
        'id',
    ];

    protected static $table = 'user_comparison_items';
    protected static $tableAlias = 'uc';
    protected static $langTable;
    protected static $langObject;

    public function deleteByProductId($userId, $productsIds)
    {

        $delete = $this->queryFactory->newDelete();
        $delete->from(self::getTable())
            ->where('product_id IN (:product_id)')
            ->where('user_id = :user_id')
            ->bindValue('product_id', (array)$productsIds)
            ->bindValue('user_id', $userId);
        $this->db->query($delete);
    }
    
}
