<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\BackendTranslations;
use Okay\Core\Response;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Helpers\NPBackendHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Requests\NPBackendRequest;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPDeliveryTypesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCacheHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Init\Init;

class NovaposhtaCostAdmin extends IndexAdmin
{
    public function fetch(
        PaymentsEntity $paymentsEntity,
        CurrenciesEntity $currenciesEntity,
        NPWarehousesEntity $warehousesEntity,
        NPCacheHelper $cacheHelper,
        NPBackendRequest $backendRequest,
        NPDeliveryTypesEntity $deliveryTypesEntity,
        NPBackendHelper $backendHelper,
        NPApiHelper $apiHelper
    ) {
        if ($this->request->method('POST')) {
            $this->settings->set('newpost_key', $this->request->post('newpost_key'));
            $this->settings->set('np_api_key_error', false);
            $this->settings->set('newpost_city', $this->request->post('newpost_city'));
            $this->settings->set('newpost_weight', str_replace(',', '.', $this->request->post('newpost_weight')));
            $this->settings->set('newpost_volume', str_replace(',', '.', $this->request->post('newpost_volume')));
            $this->settings->set('newpost_use_volume', $this->request->post('newpost_use_volume'));
            $this->settings->set('newpost_use_assessed_value', $this->request->post('newpost_use_assessed_value'));

            $deliveryTypes = $backendRequest->postDeliveryTypes();
            $backendHelper->updateDeliveryTypes($deliveryTypes);

            $apiHelper->checkApiKey();

            $this->design->assign('message_success', 'saved');
        }

        $paymentMethods = $paymentsEntity->find();
        $this->design->assign('payment_methods', $paymentMethods);

        // Валюта, необхідна для роботи модуля
        $this->design->assign('uah_currency', $currenciesEntity->findOne(['code' => 'UAH']));

        $lastUpdateDate = $warehousesEntity->order('updated_at_ASC')->cols(['updated_at'])->findOne();
        $this->design->assign('last_update_date', $lastUpdateDate);

        $this->design->assign('warehousesTypesDTO', $cacheHelper->getUpdatedWarehousesTypes());
        $this->design->assign('deliveryTypes', $deliveryTypesEntity->find());
        $this->design->assign('countWarehousesByTypes', $warehousesEntity->countByTypes());

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
        $page = $this->request->get('updatePage', 'int', 1);
        $perPage = 500;
        $updateType = $this->request->get('updateType', 'string');
        if (!$updateType || !in_array($updateType, [Init::UPDATE_TYPE_CITIES, Init::UPDATE_TYPE_WAREHOUSES])) {
            return $this->response->setContent(json_encode([
                'error' => 'Empty or wrong updateType',
            ]), RESPONSE_JSON);
        }
        $pagesNum = null;
        if ($updateType === Init::UPDATE_TYPE_WAREHOUSES) {
            $warehousesType = $this->request->get('warehousesType', 'string');
            if (empty($warehousesType)) {
                return $this->response->setContent(json_encode([
                    'error' => 'Empty warehousesType',
                ]), RESPONSE_JSON);
            }
            $pagesNum = $cacheHelper->updateWarehousesCache($warehousesType, $page, $perPage);
        } elseif ($updateType === Init::UPDATE_TYPE_CITIES) {
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

    public function finalImport(NPCacheHelper $cacheHelper): Response
    {
        $removeType = $this->request->get('removeType', 'string');
        $typeRef = '';
        if ($removeType == Init::UPDATE_TYPE_WAREHOUSES) {
            $typeRef = $this->request->get('warehousesType', 'string');
            if (empty($typeRef)) {
                return $this->response->setContent(json_encode([
                    'error' => 'Empty warehousesType',
                ]), RESPONSE_JSON);
            }
        }

        $cacheHelper->removeRedundant($removeType, $typeRef);
        return $this->response->setContent(json_encode([
            'ok' => true,
        ]), RESPONSE_JSON);
    }

    public function getUpdateTypes(NPCacheHelper $cacheHelper, BackendTranslations $backendTranslations)
    {
        $updateTypes[] = [
            'updateType' => Init::UPDATE_TYPE_CITIES,
            'updateName' => $backendTranslations->getTranslation('np_update_type_cities'),
            'updateParams' => [],
        ];

        foreach ($cacheHelper->getUpdatedWarehousesTypes() as $warehouseTypeDTO) {
            $warehouseTypeName = $warehouseTypeDTO->getName();

            if ($this->manager->lang == 'ru' && !empty($warehouseTypeDTO->getNameRu())) {
                $warehouseTypeName = $warehouseTypeDTO->getNameRu();
            }

            $updateTypes[] = [
                'updateType' => Init::UPDATE_TYPE_WAREHOUSES,
                'updateName' => $warehouseTypeName,
                'updateParams' => [
                    'warehousesType' => $warehouseTypeDTO->getTypeRef()
                ],
            ];
        }

        $cacheHelper->rememberStartUpdateTime();

        $this->response->setContent(json_encode([
            'updateTypes' => $updateTypes,
        ]), RESPONSE_JSON);
    }
}