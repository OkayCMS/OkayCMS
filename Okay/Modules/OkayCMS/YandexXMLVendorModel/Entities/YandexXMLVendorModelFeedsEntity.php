<?php


namespace Okay\Modules\OkayCMS\YandexXMLVendorModel\Entities;


use Okay\Core\Entity\Entity;

class YandexXMLVendorModelFeedsEntity extends Entity
{
    protected static $fields = [
        'id',
        'name',
        'url',
        'enabled'
    ];

    protected static $table = 'okaycms__yandex_xml_vendor_model__feeds';

    protected static $tableAlias = 'yxvmf';

    public function delete($ids)
    {
        $ids = (array)$ids;

        $delete = $this->queryFactory->newDelete();
        $delete ->from(YandexXMLVendorModelRelationsEntity::getTable())
                ->where('feed_id IN (:ids)')
                ->bindValue('ids', $ids)
                ->execute();

        parent::delete($ids);
    }
}