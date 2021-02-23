<?php


namespace Okay\Modules\OkayCMS\YandexXML\Entities;


use Okay\Core\Entity\Entity;

class YandexXMLFeedsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url',
        'enabled'
    ];

    protected static $table = 'okaycms__yandex_xml__feeds';

    protected static $tableAlias = 'yxf';

    public function delete($ids)
    {
        $ids = (array)$ids;

        $delete = $this->queryFactory->newDelete();
        $delete ->from(YandexXMLRelationsEntity::getTable())
                ->where('feed_id IN (:ids)')
                ->bindValue('ids', $ids)
                ->execute();

        parent::delete($ids);
    }
}