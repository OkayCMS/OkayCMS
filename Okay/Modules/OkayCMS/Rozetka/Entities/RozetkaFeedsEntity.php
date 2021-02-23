<?php


namespace Okay\Modules\OkayCMS\Rozetka\Entities;


use Okay\Core\Entity\Entity;

class RozetkaFeedsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url',
        'enabled'
    ];

    protected static $table = 'okaycms__rozetka__feeds';

    protected static $tableAlias = 'rxf';

    public function delete($ids)
    {
        $ids = (array)$ids;

        $delete = $this->queryFactory->newDelete();
        $delete ->from(RozetkaRelationsEntity::getTable())
                ->where('feed_id IN (:ids)')
                ->bindValue('ids', $ids)
                ->execute();

        parent::delete($ids);
    }
}