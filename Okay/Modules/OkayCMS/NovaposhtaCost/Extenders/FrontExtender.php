<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Extenders;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Core\Modules\Module;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCostDeliveryDataEntity;

class FrontExtender implements ExtensionInterface
{
    
    private $request;
    private $entityFactory;
    
    public function __construct(Request $request, EntityFactory $entityFactory)
    {
        $this->request = $request;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @param $deliveries
     * @param $cart
     * @return array
     * @throws \Exception
     * 
     * Метод проходится по способам доставки, и подменяет текст стоимости доставки.
     * 
     */
    public function getCartDeliveriesList($deliveries, $cart)
    {
        $SL = ServiceLocator::getInstance();

        /** @var FrontTranslations $frontTranslations */
        $frontTranslations = $SL->getService(FrontTranslations::class);
        
        /** @var Module $module */
        $module = $SL->getService(Module::class);
        
        /** @var Design $design */
        $design = $SL->getService(Design::class);
        
        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);
        
        $redeliveryPaymentsIds = $paymentsEntity->cols(['id'])->find(['novaposhta_cost__cash_on_delivery' => 1]);
        foreach ($redeliveryPaymentsIds as $k=>$id) {
            $redeliveryPaymentsIds[$k] = (int)$id;
        }
        $design->assignJsVar('np_redelivery_payments_ids', $redeliveryPaymentsIds);
        $design->assign('np_redelivery_payments_ids', $redeliveryPaymentsIds);
        
        $npModuleId = $module->getModuleIdByNamespace(__CLASS__);
        $design->assignJsVar('np_delivery_module_id', $npModuleId);
        $design->assign('np_delivery_module_id', $npModuleId);
        
        foreach ($deliveries as $delivery) {
            if ($delivery->module_id == $npModuleId) {
                $delivery->delivery_price_text = $frontTranslations->getTranslation('np_need_select_city');
            }
        }
        return $deliveries;
    }

    /**
     * @param $defaultData
     * @param $user
     * @return array
     * @throws \Exception
     * 
     * Если у пользователя был ранее заказ, и он был на Новую почту, заполним данными
     */
    public function getDefaultCartData($defaultData, $user)
    {
        
        if (!empty($user->id)) {
            /** @var OrdersEntity $ordersEntity */
            $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

            /** @var NPCostDeliveryDataEntity $npDeliveryDataEntity */
            $npDeliveryDataEntity = $this->entityFactory->get(NPCostDeliveryDataEntity::class);

            /** @var NPCitiesEntity $npCitiesEntity */
            $npCitiesEntity = $this->entityFactory->get(NPCitiesEntity::class);

            if (($lastOrder = $ordersEntity->findOne(['user_id'=>$user->id])) && ($npDeliveryData = $npDeliveryDataEntity->getByOrderId($lastOrder->id))) {
                $defaultData['novaposhta_delivery_city_id'] = $npDeliveryData->city_id;
                $defaultData['novaposhta_delivery_warehouse_id'] = $npDeliveryData->warehouse_id;
                
                if (!empty($npDeliveryData->city_id) && empty($npDeliveryData->city_name)) {
                    $npDeliveryData->city_name = $npCitiesEntity->col('name')->findOne(['ref' => $npDeliveryData->city_id]);
                }
                
                $defaultData['novaposhta_city'] = $defaultData['novaposhta_city_name'] = $npDeliveryData->city_name;
                $defaultData['novaposhta_area_name'] = $npDeliveryData->area_name;
                $defaultData['novaposhta_region_name'] = $npDeliveryData->region_name;
                $defaultData['novaposhta_street'] = $defaultData['novaposhta_street_name'] = $npDeliveryData->street;
                $defaultData['novaposhta_house'] = $npDeliveryData->house;
                $defaultData['novaposhta_apartment'] = $npDeliveryData->apartment;
            }
        }
        
        return $defaultData;
    }
    
    /**
     * @param $result
     * @param $delivery
     * @param $order
     * @return mixed
     * 
     * Обновляем стоимость доставки, которая пришла из API.
     * Важно не забывать что экстендеры работают всегда, и важно проверить что выбран именно наш способ доставки
     */
    public function setCartDeliveryPrice($result, $delivery, $order)
    {
        if ($this->request->post('is_novaposhta_delivery', 'boolean')) {
            $result['delivery_price'] = $this->request->post('novaposhta_delivery_price');
        }
        return $result;
    }
    
    /**
     * @param $in
     * @param $order
     * @throws \Exception
     * Добавляем данные по доставке, для этого заказа
     */
    public function setCartDeliveryDataProcedure($in, $order)
    {
        if ($this->request->post('is_novaposhta_delivery', 'boolean')) {
            /** @var NPCostDeliveryDataEntity $npDeliveryDataEntity */
            $npDeliveryDataEntity = $this->entityFactory->get(NPCostDeliveryDataEntity::class);
            $deliveryData = new \stdClass();
            $deliveryData->city_id = $this->request->post('novaposhta_delivery_city_id');
            $deliveryData->delivery_term = $this->request->post('novaposhta_delivery_term');
            $deliveryData->redelivery = $this->request->post('novaposhta_redelivery');
            $deliveryData->order_id = $order->id;

            if ($this->request->post('novaposhta_door_delivery')) {
                if (!$deliveryData->city_name = $this->request->post('novaposhta_city_name')) {
                    // Если API заглючило, запомнить хоть что пользователь писал
                    $deliveryData->city_name = $this->request->post('novaposhta_city');
                }
                $deliveryData->area_name = $this->request->post('novaposhta_area_name');
                $deliveryData->region_name = $this->request->post('novaposhta_region_name');
                if (!$deliveryData->street = $this->request->post('novaposhta_street_name')) {
                    $deliveryData->street = $this->request->post('novaposhta_street');
                }
                $deliveryData->house = $this->request->post('novaposhta_house');
                $deliveryData->apartment = $this->request->post('novaposhta_apartment');
            } else {
                $deliveryData->warehouse_id = $this->request->post('novaposhta_delivery_warehouse_id');
            }
            
            $npDeliveryDataEntity->add($deliveryData);
        }
    }
}