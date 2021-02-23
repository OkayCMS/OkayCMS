<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Controllers;


use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Controllers\AbstractController;
use Okay\Core\Cart;
use Okay\Core\EntityFactory;
use Okay\Core\Money;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\ProductsEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\NovaposhtaCost;
use Psr\Log\LoggerInterface;

class NovaposhtaCostController extends AbstractController
{
    
    public function getCities(NovaposhtaCost $novaposhtaCost)
    {
        $selected_city = $this->request->get('selected_city');
        $result['cities_response'] = $novaposhtaCost->getCities($selected_city);
        
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
    public function getWarehouses(NovaposhtaCost $novaposhtaCost)
    {
        $cityRef = $this->request->get('city');
        $warehouseRef = $this->request->get('warehouse');
        $result['warehouses_response'] = $novaposhtaCost->getWarehouses($cityRef, $warehouseRef);
        
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
    public function calc(
        NovaposhtaCost $novaposhtaCost,
        Cart $cart,
        Money $money,
        CurrenciesEntity $currenciesEntity,
        DeliveriesEntity $deliveriesEntity,
        OrdersEntity $ordersEntity,
        LoggerInterface $logger,
        BackendOrdersHelper $backendOrdersHelper
    ) {
        
        $this->design->assignJsVar('np_delivery_module_id', 1);
        $cityRef = $this->request->get('city');
        $deliveryId = $this->request->get('delivery_id', 'integer');
        $warehouseRef = $this->request->get('warehouse');
        $redelivery = $this->request->get('redelivery', 'boolean');
        
        $orderId = $this->request->get('order_id', 'integer');
        $currencyId = $this->request->get('currency', 'integer', $_SESSION['currency_id']);
        
        if ($orderId) {
            if (!$order = $ordersEntity->get($orderId)) {
                $this->response->setContent(json_encode(['error' => 'order not found']), RESPONSE_JSON);
                return;
            }

            $data = new \stdClass();
            $data->purchases = $backendOrdersHelper->findOrderPurchases($order);
            $data->total_price = $order->total_price;
        } else {
            $data = $cart->get();
            $data->client = 1; // На фронте обновляем novaposhta_cost_payments.tpl
        }

        if (!$delivery = $deliveriesEntity->get($deliveryId)) {
            $this->response->setContent(json_encode(['error' => 'delivery not found']), RESPONSE_JSON);
            return;
        }
        $deliverySettings = $deliveriesEntity->getSettings($deliveryId);
        $serviceType = $deliverySettings['service_type'];
        
        $response = $novaposhtaCost->calcPrice($cityRef, $redelivery, $data, $serviceType);
        if ($response->success) {
            $currency = $currenciesEntity->get($currencyId);
            
            if ($npCurrency = $currenciesEntity->findOne(['code' => 'UAH'])) {
            
                $priceResponse['success'] = $response->success;
                $priceResponse['price'] = $response->data[0]->Cost + (isset($response->data[0]->CostRedelivery) ? $response->data[0]->CostRedelivery : 0);
                // Переводим цену в валюту по умолчанию для сайта
                $priceResponse['price'] = $money->convert($priceResponse['price'], $npCurrency->id, false, true);

                // Изменим стоимость доставки, на ту, что просчитала Новая почта
                $priceResponse['cart_total_price'] = $cart->get()->total_price;
                if (!$delivery->separate_payment && $cart->get()->total_price < $delivery->free_from) {
                    $priceResponse['cart_total_price'] += $priceResponse['price'];
                }
                
                $priceResponse['price_formatted'] = $money->convert($priceResponse['price'], $currency->id) . ' ' . $currency->sign;
            } else {
                $logger->warning('Novaposhta cost need create currency with code UAH');
                $priceResponse['success'] = false;
            }

            $priceResponse['delivery_id'] = $deliveryId;

            $result['price_response'] = $priceResponse;
        } elseif(!empty($response->errors)) {
            $logger->warning('Novaposhta cost ERRORS ' . implode(', ', $response->errors));
        }

        $response = $novaposhtaCost->calcTerm($cityRef, $serviceType);
        if ($response->success){
            $term = strtotime($response->data[0]->DeliveryDate->date);
            $result['term_response']['success'] = $response->success;
            
            //От НП приходит дата доставки, рассчитываем сколько это дней от сегодня
            $result['term_response']['term'] = ceil(($term - time()) / 86400);
            
        } else {
            $logger->warning('Novaposhta term ERRORS ' . implode(', ', $response->errors));
            $result['term_response']['success'] = false;
        }
        
        $result['warehouses_response'] = $novaposhtaCost->getWarehouses($cityRef, $warehouseRef);

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
}
