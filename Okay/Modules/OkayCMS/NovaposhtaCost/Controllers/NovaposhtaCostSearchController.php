<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Controllers;


use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPDeliveryTypesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;

class NovaposhtaCostSearchController
{
    public function findStreet(
        Request $request,
        Response $response,
        NPApiHelper $apiHelper
    ) {
        $query = $request->get('query');
        $ref = $request->get('city_ref');
        $request = [
            'modelName' => 'Address',
            'calledMethod' => 'searchSettlementStreets',
            'methodProperties' => [
                'StreetName'=> $query,
                'SettlementRef' => $ref,
                'Limit'=> 10,
            ],
        ];
        
        $responseFromApi = $apiHelper->request($request);
        if (!empty($responseFromApi->success) && $responseFromApi->data[0]){
            $result = new \stdClass;
            $suggestions = [];
            foreach ($responseFromApi->data[0]->Addresses as $r) {
                $suggestion = new \stdClass;
                unset($r->postCodeArray);
                $suggestion->value = $r->Present;
                $suggestion->street = $r->SettlementStreetDescription;
                $suggestion->ref = $r->SettlementStreetRef;
                $suggestions[] = $suggestion;
            }

            $result->query = $query;
            $result->suggestions = $suggestions;

            $response->setContent(json_encode($result), RESPONSE_JSON);
        } else {
            $response->setContent(json_encode(['error' => $responseFromApi]), RESPONSE_JSON);
        }
    }
    
    // Метод ищет города, куда может быть осущствлена доставка курьером
    public function findCityForDoor(
        Request $request,
        Response $response,
        NPApiHelper $apiHelper
    ) {
        $query = $request->get('query');
        $request = [
            'modelName' => 'Address',
            'calledMethod' => 'searchSettlements',
            'methodProperties' => [
                'CityName'=> $query,
                'Limit'=> 25,
            ],
        ];

        $responseFromApi = $apiHelper->request($request);

        if (!empty($responseFromApi->success) && $responseFromApi->data[0]) {
            $result = new \stdClass;
            $suggestions = [];
            foreach ($responseFromApi->data[0]->Addresses as $r) {
                $suggestion = new \stdClass;
                unset($r->postCodeArray);
                $suggestion->value = $r->Present;
                $suggestion->ref = $r->Ref;
                $suggestion->city = $r->MainDescription;
                $suggestion->area = $r->Area;
                $suggestion->region = $r->Region;
                $suggestion->streets_availability = $r->StreetsAvailability;
                $suggestions[] = $suggestion;
            }

            $result->query = $query;
            $result->suggestions = $suggestions;

            $response->setContent(json_encode($result), RESPONSE_JSON);
        } else {
            $response->setContent(json_encode(['error' => $responseFromApi]), RESPONSE_JSON);
        }
    }
    
    public function findCity(
        Request $request,
        Response $response,
        NPCitiesEntity $citiesEntity
    ) {

        $filter['keyword'] = $request->get('query');
        $filter['limit'] = 25;
        
        $cities = $citiesEntity->find($filter);

        $suggestions = [];
        if (!empty($cities)) {
            foreach ($cities as $city) {
                $suggestion = new \stdClass();

                $suggestion->value = $city->name;
                $suggestion->data = (object)[
                    'id' => $city->id,
                    'ref' => $city->ref,
                    'name' => $city->name,
                ];
                $suggestions[] = $suggestion;
            }
        }

        $res = new \stdClass;
        $res->query = $filter['keyword'];
        $res->suggestions = $suggestions;

        $response->setContent(json_encode($res), RESPONSE_JSON);
    }

    public function getWarehouses(
        Request $request,
        Response $response,
        NPWarehousesEntity $warehousesEntity,
        NPDeliveryTypesEntity $deliveryTypesEntity
    ): Response
    {
        $cityRef = $request->get('city');

        $deliveryTypes = $deliveryTypesEntity->find();
        if (empty($cityRef) || empty($deliveryTypes)) {
            return $response->setContent(json_encode([
                'success' => false,
            ]), RESPONSE_JSON);
        }

        $result['success'] = true;
        $deliveryTypesRefs = [];
        $deliveryTypesResponse = [];
        foreach ($deliveryTypes as $deliveryType) {
            foreach ($deliveryType->warehouses_type_refs as $typeRef) {
                $deliveryTypesRefs[$typeRef] = $typeRef;
            }

            $deliveryTypesResponse[] = (object)[
                'name' => $deliveryType->name,
                'typeRefs' => $deliveryType->warehouses_type_refs,
            ];
        }

        $filter = [
            'city_ref' => $cityRef,
            'type' => $deliveryTypesRefs,
        ];
        $warehouses = $warehousesEntity->find($filter);

        $warehousesResponse = [];
        $currentWarehousesTypes = [];
        foreach ($warehouses as $warehouse) {
            $currentWarehousesTypes[$warehouse->type] = $warehouse->type;
            $warehousesResponse[] = (object)[
                'name' => $warehouse->name,
                'ref' => $warehouse->ref,
                'typeRef' => $warehouse->type,
            ];
        }

        // Прибираємо типи доставки, для яких немає пунктів видачі у вибраному місті
        foreach ($deliveryTypesResponse as $key => $deliveryTypeResponse) {
            if (!array_intersect($currentWarehousesTypes, $deliveryTypeResponse->typeRefs)) {
                unset($deliveryTypesResponse[$key]);
            }
        }

        $result['delivery_types'] = $deliveryTypesResponse;
        $result['warehouses'] = $warehousesResponse;

        return $response->setContent(json_encode($result), RESPONSE_JSON);
    }
}
