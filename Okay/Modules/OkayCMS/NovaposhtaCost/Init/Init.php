<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Init;


use Okay\Admin\Helpers\BackendExportHelper;
use Okay\Admin\Helpers\BackendImportHelper;
use Okay\Admin\Helpers\BackendMainHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractInit;
use Okay\Core\Modules\EntityField;
use Okay\Core\Scheduler\Schedule;
use Okay\Core\ServiceLocator;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\VariantsEntity;
use Okay\Helpers\CartHelper;
use Okay\Helpers\DeliveriesHelper;
use Okay\Helpers\NotifyHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Helpers\ValidateHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPDeliveryTypesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\FrontExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCacheHelper;

class Init extends AbstractInit
{
    
    const VOLUME_FIELD = 'volume';
    const CASH_ON_DELIVERY = 'novaposhta_cost__cash_on_delivery';
    const UPDATE_TYPE_CITIES = 'cities';
    const UPDATE_TYPE_WAREHOUSES = 'warehouses';

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
        $this->registerEntityField(NPWarehousesEntity::class, 'type');
        
        $this->addPermission('okaycms__novaposhta_cost');

        $this->addBackendBlock('product_variant', 'product_variant_block.tpl');
        $this->addBackendBlock('order_contact', 'order_contact_block.tpl');
        $this->addFrontBlock('front_cart_delivery', 'front_cart_delivery_block.tpl');
        $this->addFrontBlock('front_scripts_after_validate', 'validation.js');
        
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
            [BackendOrdersHelper::class, 'findOrder'],
            [BackendExtender::class, 'getDeliveryDataProcedure']
        );
        $this->registerQueueExtension(
            [NotifyHelper::class, 'finalEmailOrderAdmin'],
            [FrontExtender::class, 'getDeliveryDataProcedure']
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

        $this->registerChainExtension(
            [ValidateHelper::class, 'getCartValidateError'],
            [FrontExtender::class, 'getCartValidateError']
        );

        $this->registerQueueExtension(
            [BackendMainHelper::class, 'evensCounters'],
            [BackendExtender::class, 'updateEventCounters']
        );

        $this->registerQueueExtension(
            [OrdersHelper::class, 'getOrderPaymentMethodsList'],
            [FrontExtender::class, 'getDeliveryDataProcedure']
        );

        $this->registerQueueExtension(
            [NotifyHelper::class, 'finalEmailOrderUser'],
            [FrontExtender::class, 'getDeliveryDataProcedure']
        );
        
        $this->registerBackendController('NovaposhtaCostAdmin');
        $this->addBackendControllerPermission('NovaposhtaCostAdmin', 'okaycms__novaposhta_cost');

        $this->addBackendBlock(
            'notification_counters',
            'counter_block.tpl',
            function (Design $design, CurrenciesEntity $currenciesEntity) {
                if (!$currenciesEntity->findOne(['code' => 'UAH'])) {
                    $design->assign('uahCurrencyError', true);
                }
            }
        );

        $this->addFrontBlock(
            'front_email_order_user_contact_info',
            'order_email_delivery_info.tpl',
            function (Design $design) {
                if ($delivery = $design->getVar('delivery')) {
                    if (!is_array($delivery->settings)) {
                        $delivery->settings = unserialize($delivery->settings);
                        $design->assign('delivery', $delivery);
                    }
                }
            }
        );

        $this->addBackendBlock(
            'email_order_admin_contact_info',
            'order_email_delivery_info.tpl',
            function (Design $design) {
                if ($delivery = $design->getVar('delivery')) {
                    if (!is_array($delivery->settings)) {
                        $delivery->settings = unserialize($delivery->settings);
                        $design->assign('delivery', $delivery);
                    }
                }
            }
        );

        $this->registerSchedule(
            (new Schedule([NPCacheHelper::class, 'cronUpdateCitiesCache']))
                ->name('Parses NP cities to the db cache')
                ->time('0 0 * * *')
                ->overlap(false)
                ->timeout(3600)
        );

        $this->registerSchedule(
            (new Schedule([NPCacheHelper::class, 'cronUpdateWarehousesCache']))
                ->name('Parses NP warehouses to the db cache')
                ->time('10 0 * * *')
                ->overlap(false)
                ->timeout(3600)
        );
    }

    public function update_1_1_0()
    {
        $this->migrateEntityField(NPWarehousesEntity::class, (new EntityField('type'))->setTypeVarchar(100, true)->setDefault(''));

        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);

        $warehousesTypesData = (array)json_decode(file_get_contents(dirname(__FILE__,2).'/tempData/typeData.json'));
        
        /** @var NPWarehousesEntity $warehousesEntity */
        $warehousesEntity = $entityFactory->get(NPWarehousesEntity::class);

        $warehouses = $warehousesEntity->mappedBy('ref')->noLimit()->find();
        foreach ($warehouses as $ref => $warehouse) {
            if (isset($warehousesTypesData[$ref])){
                $warehousesEntity->update((int)$warehouse->id,['type' => $warehousesTypesData[$ref]]); 
            } 
        }
    }

    public function update_1_2_0()
    {
        $this->migrateEntityField(
            NPWarehousesEntity::class,
            (new EntityField('updated_at'))->setTypeTimestamp()
        );

        $this->migrateEntityField(
            NPWarehousesEntity::class,
            (new EntityField('number'))->setTypeInt(11)
        );
        $this->migrateEntityField(
            NPCitiesEntity::class,
            (new EntityField('updated_at'))->setTypeTimestamp()
        );

        $this->migrateEntityTable(NPDeliveryTypesEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('name'))->setTypeVarchar(255)->setIsLang(),
            (new EntityField('warehouses_type_refs'))->setTypeVarchar(255),
            (new EntityField('position'))->setTypeInt(11, false),
        ]);

        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);

        /** @var NPDeliveryTypesEntity $deliveryTypesEntity */
        $deliveryTypesEntity = $entityFactory->get(NPDeliveryTypesEntity::class);

        $deliveryTypesEntity->add([
            'name' => 'Відділення',
            'warehouses_type_refs' => '841339c7-591a-42e2-8233-7a0a00f0ed6f,9a68df70-0267-42a8-bb5c-37f427e36ee4'
        ]);
    }
}
