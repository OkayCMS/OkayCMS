<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class UserBrowsedProductsEntity extends Entity
{
    protected static $fields = [
        'id',
        'user_id',
        'product_id',
    ];

    protected static $defaultOrderFields = [
        'id',
    ];

    protected static $table = 'user_browsed_products';
    protected static $tableAlias = 'ub';
    protected static $langTable;
    protected static $langObject;

    public function sliceToLimit($userId, $limit = 100)
    {
        if ($this->count(['user_id' => $userId]) > 0) {
            
            $query = $this->queryFactory->newSqlQuery();
            $query->setStatement('SET @i=0;')->execute();
            
            $select = $this->queryFactory->newSelect();
            $select->from(self::getTable())
                ->cols(['product_id'])
                ->where('user_id=:user_id')
                ->having('(@i:=@i+1) > :limit')
                ->bindValue('user_id', $userId)
                ->bindValue('limit', $limit)
                ->orderBy(['id DESC']);
            
            if ($productsToDelete = $select->results('product_id')) {
                $this->deleteByProductId($userId, $productsToDelete);
            }
        }
    }
    
    private function deleteByProductId($userId, $productsIds)
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
