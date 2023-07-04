<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Extenders;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Modules\Module;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPDeliveryDataHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Init\Init;

class BackendExtender implements ExtensionInterface
{
    
    private Request $request;
    private EntityFactory $entityFactory;
    private Design $design;
    private Module $module;
    private Settings $settings;
    private NPDeliveryDataHelper $deliveryDataHelper;

    public function __construct(
        Request $request,
        EntityFactory $entityFactory,
        Design $design,
        Module $module,
        Settings $settings,
        NPDeliveryDataHelper $deliveryDataHelper
    ) {
        $this->request = $request;
        $this->entityFactory = $entityFactory;
        $this->design = $design;
        $this->module = $module;
        $this->settings = $settings;
        $this->deliveryDataHelper = $deliveryDataHelper;
    }

    public function parseVariantData($variant, $itemFromCsv)
    {
        if (isset($itemFromCsv[Init::VOLUME_FIELD])) {
            $variant[Init::VOLUME_FIELD] = trim($itemFromCsv[Init::VOLUME_FIELD]);
        }

        return ExtenderFacade::execute(__METHOD__, $variant, func_get_args());
    }
    
    /**
     * @param $variants
     * @return mixed
     * Метод корректирует данные для поля volume, т.к. оно decimal, туда нельзя строку писать
     */
    public function correctVariantsVolume(array $variants)
    {
        foreach ($variants as $variant) {
            if (empty($variant->volume)) {
                $variant->volume = 0;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $variants, func_get_args());
    }
    
    public function getDeliveryDataProcedure($order)
    {
        $moduleId = $this->module->getModuleIdByNamespace(__NAMESPACE__);
        $this->design->assign('novaposhta_module_id', $moduleId);

        if (!empty($order->id)) {
            $this->design->assign(
                'novaposhta_delivery_data',
                $this->deliveryDataHelper->getFullDeliveryData((int)$order->id)
            );

            // Визначаємо які варіанти доставки ведуть до двері або до складу
            $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
            $doorsDeliveries = [];
            $warehousesDeliveries = [];
            foreach ($deliveriesEntity->find() as $delivery) {
                if ($delivery->module_id == $moduleId) {
                    $deliverySettings = unserialize($delivery->settings);
                    if ($deliverySettings['service_type'] == 'DoorsDoors'
                        || $deliverySettings['service_type'] == 'WarehouseDoors') {
                        $doorsDeliveries[] = $delivery->id;
                    } else {
                        $warehousesDeliveries[] = $delivery->id;
                    }
                }
            }

            $this->design->assign('doorsDeliveries', $doorsDeliveries);
            $this->design->assign('warehousesDeliveries', $warehousesDeliveries);
        }
    }
    
    public function updateDeliveryDataProcedure($order)
    {
        if (!empty($order->id)) {
            
            $moduleId = $this->module->getModuleIdByNamespace(__NAMESPACE__);
            
            /** @var NPCostDeliveryDataEntity $npDdEntity */
            $npDdEntity = $this->entityFactory->get(NPCostDeliveryDataEntity::class);
            if (!$npDeliveryData = $npDdEntity->getByOrderId($order->id)) {
                $npDeliveryData = new \stdClass();
            }
            
            if (!empty($order->delivery_id)) {
                /** @var DeliveriesEntity $deliveryEntity */
                $deliveryEntity = $this->entityFactory->get(DeliveriesEntity::class);
                $delivery = $deliveryEntity->get($order->delivery_id);
                
                if ($delivery->module_id == $moduleId) {
                    $npDeliveryData->city_id = $this->request->post('novaposhta_city_id');
                    $npDeliveryData->warehouse_id = $this->request->post('novaposhta_warehouse_id');
                    $npDeliveryData->delivery_term = $this->request->post('novaposhta_delivery_term');
                    $npDeliveryData->redelivery = $this->request->post('novaposhta_redelivery');
                    
                    if ($this->request->post('novaposhta_door_delivery')) {
                        $npDeliveryData->warehouse_id = '';
                        if (!$npDeliveryData->city_name = $this->request->post('novaposhta_city_name')) {
                            // Если API заглючило, запомнить хоть что пользователь писал
                            $npDeliveryData->city_name = $this->request->post('novaposhta_city');
                        }
                        $npDeliveryData->area_name = $this->request->post('novaposhta_area_name');
                        $npDeliveryData->region_name = $this->request->post('novaposhta_region_name');
                        if (!$npDeliveryData->street = $this->request->post('novaposhta_street_name')) {
                            $npDeliveryData->street = $this->request->post('novaposhta_street');
                        }
                        $npDeliveryData->house = $this->request->post('novaposhta_house');
                        $npDeliveryData->apartment = $this->request->post('novaposhta_apartment');
                    } else {
                        $npDeliveryData->city_name = '';
                        $npDeliveryData->area_name = '';
                        $npDeliveryData->region_name = '';
                        $npDeliveryData->street = '';
                        $npDeliveryData->house = '';
                        $npDeliveryData->apartment = '';
                        $npDeliveryData->warehouse_id = $this->request->post('novaposhta_warehouse_id');
                    }
                    
                    if (!empty($npDeliveryData->id)) {
                        $npDdEntity->update($npDeliveryData->id, $npDeliveryData);
                    } else {
                        $npDeliveryData->order_id = $order->id;
                        $npDdEntity->add($npDeliveryData);
                    }
                } elseif (!empty($npDeliveryData->id)) {
                    $npDdEntity->delete($npDeliveryData->id);
                }
            } elseif (!empty($npDeliveryData->id)) {
                $npDdEntity->delete($npDeliveryData->id);
            }
            
            
            $this->design->assign('novaposhta_delivery_data', $npDeliveryData);
        }
    }

    public function extendExportColumnsNames($columnsNames)
    {
        $columnsNames[Init::VOLUME_FIELD] = Init::VOLUME_FIELD;

        return ExtenderFacade::execute(__METHOD__, $columnsNames, func_get_args());
    }

    public function extendExportPrepareVariantData($preparedVariantData, $variant)
    {
        $preparedVariantData[Init::VOLUME_FIELD] = $variant->{Init::VOLUME_FIELD};

        return ExtenderFacade::execute(__METHOD__, $preparedVariantData, func_get_args());
    }

    public function updateEventCounters()
    {
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
        if ($this->settings->get('np_api_key_error') || !$currenciesEntity->findOne(['code' => 'UAH'])) {
            $this->design->assign(
                'all_counter',
                $this->design->getVar('all_counter') + 1
            );
        }
    }
}