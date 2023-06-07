<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\Response;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCacheHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\NovaposhtaCost;

class NovaposhtaCostAdmin extends IndexAdmin
{
    public function fetch(
        PaymentsEntity $paymentsEntity,
        NovaposhtaCost $novaposhtaCost,
        NPApiHelper $apiHelper
    ) {

        if ($this->request->method('POST')) {
            $this->settings->set('newpost_key', $this->request->post('newpost_key'));
            $this->settings->set('newpost_city', $this->request->post('newpost_city'));
            $this->settings->set('newpost_weight', str_replace(',', '.', $this->request->post('newpost_weight')));
            $this->settings->set('newpost_volume', str_replace(',', '.', $this->request->post('newpost_volume')));
            $this->settings->set('newpost_use_volume', $this->request->post('newpost_use_volume'));
            $this->settings->set('newpost_use_assessed_value', $this->request->post('newpost_use_assessed_value'));
            $this->settings->set('np_auto_update_data', $this->request->post('np_auto_update_data'));
            $this->settings->set('np_cache_lifetime', $this->request->post('np_cache_lifetime'));
            $this->settings->set('np_warehouses_types', $this->request->post('np_warehouses_types'));
            $this->design->assign('message_success', 'saved');
            
            // Обновляем кеш в мануальном режиме
            if ($this->request->post('update_cache')) {
                $typeRef = [];
                if($this->request->post('warehouse_update_type')){
                    $typeRef[] = $this->request->post('warehouse_update_type');
                }
                $novaposhtaCost->parseCitiesToCache();
                $novaposhtaCost->parseWarehousesToCache($typeRef);
            }
        }

        $paymentMethods = $paymentsEntity->find();
        $this->design->assign('payment_methods', $paymentMethods);

        $this->design->assign('warehouses_types_data', $apiHelper->getWarehouseTypes());

        $this->response->setContent($this->design->fetch('novaposhta_cost.tpl'));
    }

    /**
     * @param NPCacheHelper $cacheHelper
     * @param NPApiHelper $apiHelper
     * @return Response
     *
     * Порційне оновлення списка міст.
     */
    public function updateCities(
        NPCacheHelper $cacheHelper,
        NPApiHelper $apiHelper
    ): Response
    {
        $page = $this->request->get('page', 'int', 1);
        $pagesNum = $cacheHelper->updateCitiesCache($page, 100);
        if ($pagesNum === null) {
            return $this->response->setContent(json_encode([
                'error' => $apiHelper->getLastCallError(),
            ]), RESPONSE_JSON);
        }
        if ($page > $pagesNum) {
            return $this->response->setContent(json_encode([
                'error' => sprintf('Page %d is incorrect, max %d', $page, $pagesNum),
            ]), RESPONSE_JSON);
        }
        return $this->response->setContent(json_encode([
            'pagesNum' => $pagesNum,
        ]), RESPONSE_JSON);
    }

    /**
     * @param NPCacheHelper $cacheHelper
     * @param NPApiHelper $apiHelper
     * @return Response
     *
     * Порційне оновлення списка відділень.
     */
    public function updateWarehouses(
        NPCacheHelper $cacheHelper,
        NPApiHelper $apiHelper
    ): Response
    {
        $page = $this->request->get('page', 'int', 1);
        $warehousesType = $this->request->get('warehousesType', 'string');
        if (empty($warehousesType)) {
            return $this->response->setContent(json_encode([
                'error' => 'empty warehousesType',
            ]), RESPONSE_JSON);
        }
        $pagesNum = $cacheHelper->updateWarehousesCache($warehousesType, $page, 100);
        if ($pagesNum === null) {
            return $this->response->setContent(json_encode([
                'error' => $apiHelper->getLastCallError(),
            ]), RESPONSE_JSON);
        }
        if ($page > $pagesNum) {
            return $this->response->setContent(json_encode([
                'error' => sprintf('Page %d is incorrect, max %d', $page, $pagesNum),
            ]), RESPONSE_JSON);
        }
        return $this->response->setContent(json_encode([
            'pagesNum' => $pagesNum,
        ]), RESPONSE_JSON);
    }
    
}