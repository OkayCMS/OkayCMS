<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;

class UserCartItemsEntity extends Entity
{
    protected static $fields = [
        'id',
        'user_id',
        'variant_id',
        'amount',
    ];

    protected static $defaultOrderFields = [
        'id',
    ];

    protected static $table = 'user_cart_items';
    protected static $tableAlias = 'uc';
    protected static $langTable;
    protected static $langObject;

    public function updateAmount($userId, $variantId, $amount) 
    {
        if ($id = $this->col('id')->findOne(['user_id' => $userId, 'variant_id' => $variantId])) {
            $this->update($id, ['amount' => $amount]);
        } else {
            $this->add([
                'user_id' => $userId,
                'variant_id' => $variantId,
                'amount' => $amount,
            ]);
        }
    }
    
    public function deleteByVariantId($userId, $variantsIds)
    {

        $delete = $this->queryFactory->newDelete();
        $delete->from(self::getTable())
            ->where('variant_id IN (:variant_id)')
            ->where('user_id = :user_id')
            ->bindValue('variant_id', (array)$variantsIds)
            ->bindValue('user_id', $userId);
        $this->db->query($delete);
    }
    
}
