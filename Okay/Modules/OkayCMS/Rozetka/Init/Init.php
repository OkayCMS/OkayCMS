<?php


namespace Okay\Modules\OkayCMS\Rozetka\Init;


use Okay\Admin\Helpers\BackendExportHelper;
use Okay\Admin\Helpers\BackendImportHelper;
use Okay\Core\Design;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\BrandsEntity;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\ProductsEntity;
use Okay\Modules\OkayCMS\Rozetka\Entities\RozetkaFeedsEntity;
use Okay\Modules\OkayCMS\Rozetka\Entities\RozetkaRelationsEntity;
use Okay\Modules\OkayCMS\Rozetka\Extenders\BackendExtender;

class Init extends AbstractInit
{
    const TO_FEED_FIELD = 'to__okaycms__rozetka';
    const FILTER_FEEDS  = 'okaycms__rozetka__feeds';
    const PERMISSION    = 'okaycms__rozetka';

    public function install()
    {
        $this->setModuleType(MODULE_TYPE_XML);
        $this->setBackendMainController('RozetkaXmlAdmin');

        $this->migrateEntityTable(RozetkaFeedsEntity::class, [
            (new EntityField('id'))->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(100, false),
            (new EntityField('url'))->setTypeVarchar(100, false)->setIndexUnique(),
            (new EntityField('enabled'))->setTypeTinyInt(1, false)->setDefault(0),
        ]);

        $entityTypeField = (new EntityField('entity_type'))->setTypeEnum(['product', 'category', 'brand'], false);
        $includeField = (new EntityField('include'))->setTypeTinyInt(1, false);
        $entityIdField = (new EntityField('entity_id'))->setTypeInt(11, false);
        $this->migrateEntityTable(RozetkaRelationsEntity::class, [
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
        $this->registerBackendController('RozetkaXmlAdmin');
        $this->addBackendControllerPermission('RozetkaXmlAdmin', self::PERMISSION);

        $this->addBackendBlock('import_fields_association',
            'import_fields_association.tpl',
            function(
                RozetkaFeedsEntity $feedsEntity,
                Design             $design
            ) {
                $design->assign('rozetkaFeeds', $feedsEntity->find());
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
            \Okay\Modules\OkayCMS\Rozetka\ExtendsEntities\ProductsEntity::class,
            self::FILTER_FEEDS
        );
    }
}