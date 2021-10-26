<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Init;


use Okay\Admin\Helpers\BackendExportHelper;
use Okay\Admin\Helpers\BackendImportHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Helpers\CartHelper;
use Okay\Helpers\DeliveriesHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\FrontExtender;

class Init extends AbstractInit
{
    
    const VOLUME_FIELD = 'volume';
    const CASH_ON_DELIVERY = 'novaposhta_cost__cash_on_delivery';

    public function install()
    {
        $this->setModuleType(MODULE_TYPE_DELIVERY);
        $this->setBackendMainController('NovaposhtaCostAdmin');
        $this->migrateEntityTable(NPCostDeliveryDataEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('order_id'))->setTypeInt(11)->setIndex(),
            (new EntityField('city_id'))->setTypeVarchar(255, true),
            (new EntityField('warehouse_id'))->setTypeVarchar(255, true),
            (new EntityField('delivery_term'))->setTypeVarchar(8, true),
            (new EntityField('redelivery'))->setTypeTinyInt(1, true),
            (new EntityField('city_name'))->setTypeVarchar(255, true),
            (new EntityField('area_name'))->setTypeVarchar(255, true),
            (new EntityField('region_name'))->setTypeVarchar(255, true),
            (new EntityField('street'))->setTypeVarchar(255, true),
            (new EntityField('house'))->setTypeVarchar(255, true),
            (new EntityField('apartment'))->setTypeVarchar(255, true),
        ]);
        
        $this->migrateEntityTable(NPCitiesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('ref'))->setTypeVarchar(255)->setIndex(),
            (new EntityField('name'))->setTypeVarchar(255, true)->setIsLang()->setIndex(100),
        ]);
        
        $this->migrateEntityTable(NPWarehousesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('ref'))->setTypeVarchar(255),
            (new EntityField('city_ref'))->setTypeVarchar(255)->setIndex(),
            (new EntityField('name'))->setTypeVarchar(255, true)->setIsLang()->setIndex(100),
        ]);

        $this->migrateEntityField(VariantsEntity::class, (new EntityField(self::VOLUME_FIELD))->setTypeDecimal('10,5'));
        $this->migrateEntityField(PaymentsEntity::class, (new EntityField(self::CASH_ON_DELIVERY))->setTypeTinyInt(1));
    }

    public function init()
    {

        $this->registerEntityField(VariantsEntity::class, self::VOLUME_FIELD);
        $this->registerEntityField(PaymentsEntity::class, self::CASH_ON_DELIVERY);
        
        $this->addPermission('okaycms__novaposhta_cost');

        $this->addBackendBlock('product_variant', 'product_variant_block.tpl');
        $this->addBackendBlock('order_contact', 'order_contact_block.tpl');
        $this->addFrontBlock('front_cart_delivery', 'front_cart_delivery_block.tpl');
        
        $this->registerChainExtension(
            [DeliveriesHelper::class, 'prepareDeliveryPriceInfo'],
            [FrontExtender::class, 'setCartDeliveryPrice']
        );
        
        $this->registerChainExtension(
            [CartHelper::class, 'getDefaultCartData'],
            [FrontExtender::class, 'getDefaultCartData']
        );
        
        $this->registerChainExtension(
            [DeliveriesHelper::class, 'getCartDeliveriesList'],
            [FrontExtender::class, 'getCartDeliveriesList']
        );
        
        $this->registerQueueExtension(
            [OrdersHelper::class, 'finalCreateOrderProcedure'],
            [FrontExtender::class, 'setCartDeliveryDataProcedure']
        );

        $this->registerChainExtension(
            [BackendProductsRequest::class, 'postVariants'],
            [BackendExtender::class, 'correctVariantsVolume']
        );
        
        // В админке в заказе достаём данные по доставке
        $this->registerQueueExtension(
            [BackendOrdersHelper::class, 'findOrderDelivery'],
            [BackendExtender::class, 'getDeliveryDataProcedure']
        );

        // В админке в заказе обновляем данные по доставке
        $this->registerQueueExtension(
            [BackendOrdersHelper::class, 'executeCustomPost'],
            [BackendExtender::class, 'updateDeliveryDataProcedure']
        );

        // Добавляемся в импорт
        $this->addBackendBlock('import_fields_association', 'import_fields_association.tpl');

        $this->registerChainExtension(
            [BackendImportHelper::class, 'parseVariantData'],
            [BackendExtender::class, 'parseVariantData']
        );

        $this->registerChainExtension(
            [BackendExportHelper::class, 'getColumnsNames'],
            [BackendExtender::class, 'extendExportColumnsNames']
        );

        $this->registerChainExtension(
            [BackendExportHelper::class, 'prepareVariantsData'],
            [BackendExtender::class, 'extendExportPrepareVariantData']
        );
        
        $this->registerBackendController('NovaposhtaCostAdmin');
        $this->addBackendControllerPermission('NovaposhtaCostAdmin', 'okaycms__novaposhta_cost');
    }
}
