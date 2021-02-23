<?php


namespace Okay\Modules\OkayCMS\Hotline\Entities;


use Okay\Core\Entity\Entity;

class HotlineFeedsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url',
        'enabled'
    ];

    protected static $table = 'okaycms__hotline__feeds';

    protected static $tableAlias = 'hxf';

    public function delete($ids)
    {
        $ids = (array)$ids;

        $delete = $this->queryFactory->newDelete();
        $delete ->from(HotlineRelationsEntity::getTable())
                ->where('feed_id IN (:ids)')
                ->bindValue('ids', $ids)
                ->execute();

        parent::delete($ids);
    }
}