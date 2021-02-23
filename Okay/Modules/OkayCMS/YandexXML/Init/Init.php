<?php


namespace Okay\Modules\OkayCMS\YandexXML\Init;


use Okay\Admin\Helpers\BackendExportHelper;
use Okay\Admin\Helpers\BackendImportHelper;
use Okay\Core\Design;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\ProductsEntity;
use Okay\Modules\OkayCMS\YandexXML\Entities\YandexXMLFeedsEntity;
use Okay\Modules\OkayCMS\YandexXML\Entities\YandexXMLRelationsEntity;
use Okay\Modules\OkayCMS\YandexXML\Extenders\BackendExtender;

class Init extends AbstractInit
{
    const TO_FEED_FIELD = 'to__okaycms__yandex_xml';
    const FILTER_FEEDS  = 'okaycms__yandex_xml__feeds';
    const PERMISSION    = 'okaycms__yandex_xml';

    public function install()
    {
        $this->setModuleType(MODULE_TYPE_XML);
        $this->setBackendMainController('YandexXmlAdmin');

        $this->migrateEntityTable(YandexXMLFeedsEntity::class, [
            (new EntityField('id'))->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(100, false),
            (new EntityField('url'))->setTypeVarchar(100, false)->setIndexUnique(),
            (new EntityField('enabled'))->setTypeTinyInt(1, false)->setDefault(0),
        ]);

        $entityTypeField = (new EntityField('entity_type'))->setTypeEnum(['product', 'category', 'brand'], false);
        $includeField = (new EntityField('include'))->setTypeTinyInt(1, false);
        $entityIdField = (new EntityField('entity_id'))->setTypeInt(11, false);
        $this->migrateEntityTable(YandexXMLRelationsEntity::class, [
            (new EntityField('id'))->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('feed_id'))->setTypeInt(11, false)->setIndexUnique(null, $entityTypeField, $includeField, $entityIdField),
            $entityIdField,
            $entityTypeField,
            $includeField,
        ]);
    }

    public function init()
    {
        $this->addPermission(self::PERMISSION);
        $this->registerBackendController('YandexXmlAdmin');
        $this->addBackendControllerPermission('YandexXmlAdmin', self::PERMISSION);

        $this->addBackendBlock('import_fields_association',
            'import_fields_association.tpl',
            function(
                YandexXMLFeedsEntity $feedsEntity,
                Design               $design
            ) {
                $design->assign('yandexFeeds', $feedsEntity->find());
            }
        );

        $this->registerQueueExtension(
            ['class' => BackendImportHelper::class, 'method' => 'importItem'],
            ['class' => BackendExtender::class, 'method' => 'importItem']
        );

        $this->registerQueueExtension(
            ['class' => BackendImportHelper::class, 'method' => 'parseProductData'],
            ['class' => BackendExtender::class, 'method' => 'parseProductData']
        );

        $this->registerChainExtension(
            ['class' => BackendExportHelper::class, 'method' => 'getColumnsNames'],
            ['class' => BackendExtender::class, 'method' => 'extendExportColumnsNames']
        );

        $this->registerChainExtension(
            ['class' => BackendExportHelper::class, 'method' => 'setUp'],
            ['class' => BackendExtender::class, 'method' => 'extendFilter']
        );

        $this->registerChainExtension(
            ['class' => BackendImportHelper::class, 'method' => 'getModulesColumnsNames'],
            ['class' => BackendExtender::class, 'method' => 'getModulesColumnsNames']
        );

        $this->registerEntityFilter(
            ProductsEntity::class,
            self::FILTER_FEEDS,
            \Okay\Modules\OkayCMS\YandexXML\ExtendsEntities\ProductsEntity::class,
            self::FILTER_FEEDS
        );
    }
}