<?php


namespace Okay\Modules\OkayCMS\GoogleMerchant\Entities;


use Okay\Core\Entity\Entity;

class GoogleMerchantFeedsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url',
        'enabled'
    ];

    protected static $table = 'okaycms__google_merchant__feeds';

    protected static $tableAlias = 'gmf';

    public function delete($ids)
    {
        $ids = (array)$ids;

        $delete = $this->queryFactory->newDelete();
        $delete ->from(GoogleMerchantRelationsEntity::getTable())
                ->where('feed_id IN (:ids)')
                ->bindValue('ids', $ids)
                ->execute();

        parent::delete($ids);
    }
}