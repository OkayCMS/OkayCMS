<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost;


use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Money;
use Okay\Core\Settings;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\LanguagesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;

class NovaposhtaCost
{
    
    private $npApiKey;
    
    /** @var Settings */
    private $settings;
    
    /** @var EntityFactory */
    private $entityFactory;
    
    /** @var Money */
    private $money;
    
    /** @var Languages */
    private $languages;
    
    private $cacheLifetime;
    
    public function __construct(Settings $settings, EntityFactory $entityFactory, Money $money, Languages $languages)
    {
        $this->entityFactory = $entityFactory;
        $this->npApiKey = $settings->get('newpost_key');
        $this->settings = $settings;
        $this->money = $money;
        $this->languages = $languages;
        
        $cacheLifetime = $settings->get('np_cache_lifetime');
        $this->cacheLifetime = !empty($cacheLifetime) ? $cacheLifetime : 86400;
    }

    /**
     * Выборка отделений Новой Почты
     * @param string $cityRef id города Новой Почты
     * @param string $warehouseRef id отделения Новой Почты
     * @return bool|mixed
     */
    public function getWarehouses($cityRef, $warehouseRef = '')
    {
        if (empty($cityRef)) {
            return false;
        }

        // Если включено автообновление пунктов выдачи и уже пора их обновить, тогда обновляем
        if ($this->settings->get('np_auto_update_data') && (int)$this->settings->get('np_last_update_warehouses_date') + $this->cacheLifetime < time()) {
            $this->parseWarehousesToCache();
        }

        /** @var NPWarehousesEntity $warehousesEntity */
        $warehousesEntity = $this->entityFactory->get(NPWarehousesEntity::class);
        
        $filter = ['city_ref' => $cityRef];
        
        // Если таблица пунктов пуста, спарсим все пункты
        if (!$warehousesEntity->count()) {
            $this->parseWarehousesToCache();
        }
        
        $warehouses = $warehousesEntity->find($filter);

        $result['success'] = true;
        $result['warehouses'] = '<option'.(empty($warehouseRef)? ' selected' : '').' disabled value="">Выберите отделение доставки</option>';
        foreach ($warehouses as $warehouse) {
            $result['warehouses'] .= '<option value="'.$warehouse->name.'" data-warehouse_ref="'.$warehouse->ref.'"'.(!empty($warehouseRef) && $warehouseRef == $warehouse->ref ? 'selected' : '').'>'.$warehouse->name.'</option>';
        }
        return $result;
        
    }
    
    /**
     * Выборка городов Новой Почты
     * @param string $selectedCity id города Новой Почты
     * @return bool|mixed
     * @throws \Exception
     */
    public function getCities($selectedCity = '')
    {

        /** @var NPCitiesEntity $citiesEntity */
        $citiesEntity = $this->entityFactory->get(NPCitiesEntity::class);

        if (!$cities = $citiesEntity->find()) {
            $cities = $this->parseCitiesToCache();
        }

        // Если включено автообновление городов и уже пора их обновить, тогда обновляем
        if ($this->settings->get('np_auto_update_data') && (int)$this->settings->get('np_last_update_cities_date') + $this->cacheLifetime < time()) {
            $cities = $this->parseCitiesToCache();
        }

        $result['success'] = true;
        $result['cities'] = '<option value=""></option>';
        foreach ($cities as $city) {
            $result['cities'] .= '<option value="'.$city->name.'" data-city_ref="'.$city->ref.'" '.(!empty($selectedCity) && $selectedCity == $city->ref ? 'selected' : '').'>'.$city->name.'</option>';
        }
        return $result;
    }

    /**
     * Метод сохраняет города в базу данных (локальный кеш)
     * 
     * @return array|false
     * @throws \Exception
     */
    public function parseCitiesToCache()
    {
        $request = [
            "apiKey" => $this->npApiKey,
            "modelName" => "Address",
            "calledMethod" => "getCities",
            "methodProperties" => [
                "Page" => 1,
            ],
        ];
        
        $currentLangId = $this->languages->getLangId();
        
        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $this->entityFactory->get(LanguagesEntity::class);

        $ruLanguage = $languagesEntity->findOne(['label' => 'ru']);
        $languages = $languagesEntity->find();
        
        $response = $this->npRequest(json_encode($request));
        if ($response->success) {

            /** @var NPCitiesEntity $citiesEntity */
            $citiesEntity = $this->entityFactory->get(NPCitiesEntity::class);
            $cities = $citiesEntity->mappedBy('ref')->noLimit()->find();
            $currentCitiesIds = [];
            foreach ($cities as $c) {
                $currentCitiesIds[$c->ref] = $c->id;
            }
            foreach ($response->data as $cityData) {
                unset($currentCitiesIds[$cityData->Ref]);
                if (!isset($cities[$cityData->Ref])) {
                    $city = (object)[
                        'name' => htmlspecialchars($cityData->Description),
                        'ref' => $cityData->Ref,
                    ];
                    $city->id = $citiesEntity->add($city);
                    $cities[$city->ref] = $city;
                    
                    if (!empty($ruLanguage)) {
                        $this->languages->setLangId($ruLanguage->id);
                        $city->name = htmlspecialchars($cityData->DescriptionRu);
                        if (empty($city->name)) {
                            $city->name = htmlspecialchars($cityData->Description);
                        }
                        $citiesEntity->update($city->id, $city);
                    }
                } else {

                    foreach ($languages as $l) {
                        $this->languages->setLangId($l->id);
                        $city = $cities[$cityData->Ref];

                        if ($l->label == 'ru') {
                            $city->name = htmlspecialchars($cityData->DescriptionRu);
                            if (empty($city->name)) {
                                $city->name = htmlspecialchars($cityData->Description);
                            }
                        } else {
                            $city->name = htmlspecialchars($cityData->Description);
                        }
                        $citiesEntity->update($city->id, $city);
                    }
                }
            }

            // Удаляет города которые не пришли
            if (!empty($currentCitiesIds)) {
                $citiesEntity->delete($currentCitiesIds);
            }
            
            // Возвращаем язык
            $this->languages->setLangId($currentLangId);

            $this->settings->set('np_last_update_cities_date', time());
            
            return $cities;
        } else {
            return false;
        }
    }

    public function parseWarehousesToCache()
    {
        $request = array(
            "apiKey" => $this->settings->get('newpost_key'),
            "modelName" => "Address",
            "calledMethod" => "getWarehouses",
            "methodProperties" => array(
                "Page" => 1
            )
        );

        $currentLangId = $this->languages->getLangId();

        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $this->entityFactory->get(LanguagesEntity::class);

        $ruLanguage = $languagesEntity->findOne(['label' => 'ru']);
        $languages = $languagesEntity->find();

        $response = $this->npRequest(json_encode($request));
        if ($response->success) {

            /** @var NPWarehousesEntity $warehousesEntity */
            $warehousesEntity = $this->entityFactory->get(NPWarehousesEntity::class);
            $warehouses = $warehousesEntity->mappedBy('ref')->noLimit()->find();
            $currentWarehousesIds = [];
            foreach ($warehouses as $c) {
                $currentWarehousesIds[$c->ref] = $c->id;
            }
            foreach ($response->data as $warehouseData) {
                unset($currentWarehousesIds[$warehouseData->Ref]);
                if (!isset($warehouses[$warehouseData->Ref])) {
                    $warehouse = (object)[
                        'name' => htmlspecialchars($warehouseData->Description),
                        'ref' => $warehouseData->Ref,
                        'city_ref' => $warehouseData->CityRef,
                    ];
                    $warehouse->id = $warehousesEntity->add($warehouse);
                    $warehouses[$warehouse->ref] = $warehouse;

                    if (!empty($ruLanguage)) {
                        $this->languages->setLangId($ruLanguage->id);
                        $warehouse->name = htmlspecialchars($warehouseData->DescriptionRu);
                        if (empty($warehouse->name)) {
                            $warehouse->name = htmlspecialchars($warehouseData->Description);
                        }
                        $warehousesEntity->update($warehouse->id, $warehouse);
                    }
                } else {
                    
                    foreach ($languages as $l) {
                        $this->languages->setLangId($l->id);
                        $warehouse = $warehouses[$warehouseData->Ref];
                        
                        if ($l->label == 'ru') {
                            $warehouse->name = htmlspecialchars($warehouseData->DescriptionRu);
                            if (empty($warehouse->name)) {
                                $warehouse->name = htmlspecialchars($warehouseData->Description);
                            }
                        } else {
                            $warehouse->name = htmlspecialchars($warehouseData->Description);
                        }
                        $warehousesEntity->update($warehouse->id, $warehouse);
                    }
                }
            }

            // Удаляет пункты которые не пришли
            if (!empty($currentWarehousesIds)) {
                $warehousesEntity->delete($currentWarehousesIds);
            }

            // Возвращаем язык
            $this->languages->setLangId($currentLangId);

            $this->settings->set('np_last_update_warehouses_date', time());

            return $warehouses;
        } else {
            return false;
        }
    }
    
    /**
     * Калькулятор стоимости доставки Новой Почты
     * @param string $cityRef id города Новой Почты
     * @param bool $redelivery наложенный платеж
     * @param object $data - данные о заказе
     * @param string $serviceType - тип доставки (до двери, до склада...)
     * @return bool|mixed
     * @throws \Exception
     */
    public function calcPrice($cityRef, $redelivery, $data, $serviceType)
    {
        if (empty($cityRef) && empty($data)) {
            return false;
        }

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        if (!$npCurrency = $currenciesEntity->findOne(['code' => 'UAH'])) {
            $npCurrency = $currenciesEntity->getMainCurrency();
        }

        $totalWeight = 0;
        $totalVolume = 0;
        foreach ($data->purchases as $purchase) {
            $totalWeight += (!empty($purchase->variant->weight) && $purchase->variant->weight>0 ? $purchase->variant->weight : $this->settings->get('newpost_weight'))*$purchase->amount;

            if ($this->settings->get('newpost_use_volume')){
                $totalVolume += (!empty($purchase->variant->volume) && $purchase->variant->volume>0 ? $purchase->variant->volume : $this->settings->get('newpost_volume'))*$purchase->amount;
            }
        }

        $methodProperties = [
            "CitySender" => $this->settings->get('newpost_city'),
            "CityRecipient" => $cityRef,
            "Weight" => $totalWeight,
            "ServiceType" => $serviceType,
        ];

        if ($this->settings->get('newpost_use_volume')){
            $methodProperties = array_merge($methodProperties, array("VolumeGeneral" => $totalVolume));
        }

        /* Если в настройках выбрано "оценочная стоимость" */
        if ($this->settings->get('newpost_use_assessed_value')){

            $cost = $this->money->convert($data->total_price, $npCurrency->id, false);
            $methodProperties = array_merge($methodProperties, array("Cost" => max(1, round($cost))));
        }

        /* Если выбрали наложенный платеж */
        if ($redelivery){
            $redeliveryAmount = $this->money->convert($data->total_price, $npCurrency->id, false);

            $methodProperties = array_merge($methodProperties, array("RedeliveryCalculate" => array(
                'CargoType'=>'Money',
                'Amount'=>round($redeliveryAmount),
            )));
        }

        $request = array(
            "apiKey" => $this->settings->get('newpost_key'),
            "modelName" => "InternetDocument",
            "calledMethod" => "getDocumentPrice",
            "methodProperties" => $methodProperties
        );
        
        return $this->npRequest(json_encode($request));
    }

    /**
     * Калькулятор срока доставки
     * @param string $cityRef id города Новой Почты
     * @param string $serviceType - тип доставки (до двери, до склада...)
     * @return bool|mixed
     */
    public function calcTerm($cityRef, $serviceType)
    {

        if (empty($cityRef)) {
            return false;
        }

        $request = array(
            "apiKey" => $this->settings->get('newpost_key'),
            "modelName" => "InternetDocument",
            "calledMethod" => "getDocumentDeliveryDate",
            "methodProperties" => array(
                "CitySender" => $this->settings->get('newpost_city'),
                "CityRecipient" => $cityRef,
                "ServiceType" => $serviceType,
            )
        );

        return $this->npRequest(json_encode($request));

    }

    /**
     * @param string $request json параметры запроса
     * @return bool|mixed
     */
    public function npRequest($request)
    {
        if (empty($request)) {
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: text/xml"]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
    
}