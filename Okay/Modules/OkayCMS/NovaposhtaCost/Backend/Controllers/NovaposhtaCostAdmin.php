<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\BackendTranslations;
use Okay\Core\Response;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCacheHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\NovaposhtaCost;

class NovaposhtaCostAdmin extends IndexAdmin
{
    private const UPDATE_TYPE_CITIES = 'cities';
    private const UPDATE_TYPE_WAREHOUSES = 'warehouses';

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
     * Порційне оновлення даних НП.
     */
    public function updateData(
        NPCacheHelper $cacheHelper,
        NPApiHelper $apiHelper
    ): Response
    {
        $page = $this->request->get('page', 'int', 1);
        $perPage = 140;
        $updateType = $this->request->get('updateType', 'string');
        if (!$updateType || !in_array($updateType, [self::UPDATE_TYPE_CITIES, self::UPDATE_TYPE_WAREHOUSES])) {
            return $this->response->setContent(json_encode([
                'error' => 'Empty or wrong updateType',
            ]), RESPONSE_JSON);
        }
        $pagesNum = null;
        if ($updateType === self::UPDATE_TYPE_WAREHOUSES) {
            $warehousesType = $this->request->get('warehousesType', 'string');
            if (empty($warehousesType)) {
                return $this->response->setContent(json_encode([
                    'error' => 'Empty warehousesType',
                ]), RESPONSE_JSON);
            }
            $pagesNum = $cacheHelper->updateWarehousesCache($warehousesType, $page, $perPage);
        } elseif ($updateType === self::UPDATE_TYPE_CITIES) {
            $pagesNum = $cacheHelper->updateCitiesCache($page, $perPage);
        }

        if ($pagesNum === null) {
            return $this->response->setContent(json_encode([
                'error' => $apiHelper->getLastCallError(),
            ]), RESPONSE_JSON);
        }
        if ($page > $pagesNum && $pagesNum > 0) {
            return $this->response->setContent(json_encode([
                'error' => sprintf('Page %d is incorrect, max %d', $page, $pagesNum),
            ]), RESPONSE_JSON);
        }
        return $this->response->setContent(json_encode([
            'pagesNum' => $pagesNum,
        ]), RESPONSE_JSON);
    }

    public function getUpdateTypes(NPApiHelper $apiHelper, BackendTranslations $backendTranslations)
    {
        $updateTypes[] = [
            'updateType' => self::UPDATE_TYPE_CITIES,
            'updateName' => $backendTranslations->getTranslation('np_update_type_cities'),
            'updateParams' => [],
        ];

        foreach ($apiHelper->getWarehouseTypes() as $warehouseTypeDTO) {
            $warehouseTypeName = $warehouseTypeDTO->getName();

            if ($this->manager->lang == 'ru' && !empty($warehouseTypeDTO->getNameRu())) {
                $warehouseTypeName = $warehouseTypeDTO->getNameRu();
            }

            $updateTypes[] = [
                'updateType' => self::UPDATE_TYPE_WAREHOUSES,
                'updateName' => $warehouseTypeName,
                'updateParams' => [
                    'warehousesType' => $warehouseTypeDTO->getTypeRef()
                ],
            ];
        }
        $this->response->setContent(json_encode([
            'updateTypes' => $updateTypes,
        ]), RESPONSE_JSON);
    }
}