<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost\Controllers;


use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Settings;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\NovaposhtaCost;

class NovaposhtaCostSearchController
{
    
    public function findStreet(Request $request, Response $response, Settings $settings, NovaposhtaCost $novaposhtaCost)
    {
        $query = $request->get('query');
        $ref = $request->get('city_ref');
        $request = [
            "apiKey" => $settings->get('newpost_key'),
            "modelName" => "Address",
            "calledMethod" => "searchSettlementStreets",
            "methodProperties" => [
                "StreetName"=> $query,
                "SettlementRef" => $ref,
                "Limit"=> 10,
            ],
        ];
        
        $responseFromApi = $novaposhtaCost->npRequest(json_encode($request));
        if($responseFromApi->success && $responseFromApi->data[0]){
            $result = new \stdClass;
            $suggestions = array();
            foreach($responseFromApi->data[0]->Addresses as $r) {
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
    public function findCityForDoor(Request $request, Response $response, Settings $settings, NovaposhtaCost $novaposhtaCost)
    {
        $query = $request->get('query');
        $request = [
            "apiKey" => $settings->get('newpost_key'),
            "modelName" => "Address",
            "calledMethod" => "searchSettlements",
            "methodProperties" => [
                "CityName"=> $query,
                "Limit"=> 10,
            ],
        ];

        $responseFromApi = $novaposhtaCost->npRequest(json_encode($request));

        if ($responseFromApi->success && $responseFromApi->data[0]){
            $result = new \stdClass;
            $suggestions = [];
            foreach($responseFromApi->data[0]->Addresses as $r) {
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
    
    public function findCity(Request $request, Response $response, NPCitiesEntity $citiesEntity)
    {
        
        $filter['keyword'] = $request->get('query', 'string');
        $filter['limit'] = 10;
        
        $cities = $citiesEntity->find($filter);

        $suggestions = [];
        if (!empty($cities)) {
            foreach ($cities as $city) {
                $suggestion = new \stdClass();

                $suggestion->value = $city->name;
                $suggestion->data = $city;
                $suggestions[] = $suggestion;
            }
        }

        $res = new \stdClass;
        $res->query = $filter['keyword'];
        $res->suggestions = $suggestions;

        $response->setContent(json_encode($res), RESPONSE_JSON);
    }
}
