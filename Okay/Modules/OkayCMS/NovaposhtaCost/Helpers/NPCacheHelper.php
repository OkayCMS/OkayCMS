<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Entities\LanguagesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPPaginationDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;

class NPCacheHelper
{
    private NPApiHelper $apiHelper;
    private EntityFactory $entityFactory;
    private Languages $languages;

    public function __construct(
        NPApiHelper   $apiHelper,
        EntityFactory $entityFactory,
        Languages     $languages
    ) {
        $this->apiHelper = $apiHelper;
        $this->entityFactory = $entityFactory;
        $this->languages = $languages;
    }

    public function updateCitiesCache(int $page, int $limit): ?int
    {
        $citiesDTO = $this->apiHelper->getCities($page, $limit);

        if ($citiesDTO === null) {
            return null;
        }

        $currentLangId = $this->languages->getLangId();

        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $this->entityFactory->get(LanguagesEntity::class);

        $ruLanguage = $languagesEntity->findOne(['label' => 'ru']);
        $languages = $languagesEntity->find();

        /** @var NPCitiesEntity $citiesEntity */
        $citiesEntity = $this->entityFactory->get(NPCitiesEntity::class);
        $currentCities = $citiesEntity->mappedBy('ref')->find([
            'ref' => $citiesDTO->getCitiesRefs(),
            'limit' => $limit,
        ]);

        foreach ($citiesDTO->getCities() as $cityDTO) {
            if (!isset($currentCities[$cityDTO->getRef()])) {
                $city = (object)[
                    'name' => $cityDTO->getName(),
                    'ref' => $cityDTO->getRef(),
                ];
                $city->id = $citiesEntity->add($city);

                if (!empty($ruLanguage) && !empty($cityDTO->getNameRu())) {
                    $this->languages->setLangId($ruLanguage->id);
                    $city->name = $cityDTO->getNameRu();
                    $citiesEntity->update($city->id, $city);
                }
            } else {
                $currentCity = $currentCities[$cityDTO->getRef()];
                foreach ($languages as $l) {
                    $this->languages->setLangId($l->id);

                    $cityName = $cityDTO->getName();
                    if ($l->label == 'ru' && !empty($cityDTO->getNameRu())) {
                        $cityName = $cityDTO->getNameRu();
                    }
                    $citiesEntity->update($currentCity->id, [
                        'name' => $cityName
                    ]);
                }
            }
        }

        // Повертаємо мову
        $this->languages->setLangId($currentLangId);

        return ceil($citiesDTO->getTotalCount() / $limit);
    }

    public function updateWarehousesCache(string $warehouseType, int $page, int $limit): ?int
    {
        $warehousesDTO = $this->apiHelper->getWarehouses($warehouseType, $page, $limit);

        if ($warehousesDTO === null) {
            return null;
        }

        $currentLangId = $this->languages->getLangId();

        /** @var LanguagesEntity $languagesEntity */
        $languagesEntity = $this->entityFactory->get(LanguagesEntity::class);

        $ruLanguage = $languagesEntity->findOne(['label' => 'ru']);
        $languages = $languagesEntity->find();

        /** @var NPWarehousesEntity $warehousesEntity */
        $warehousesEntity = $this->entityFactory->get(NPWarehousesEntity::class);
        $currentWarehouses = $warehousesEntity->mappedBy('ref')->find([
            'type' => $warehouseType,
            'ref' => $warehousesDTO->getWarehousesRefs(),
            'limit' => $limit,
        ]);

        foreach ($warehousesDTO->getWarehouses() as $warehouseDTO) {
            if (!isset($currentWarehouses[$warehouseDTO->getRef()])) {
                $warehouse = (object)[
                    'name' => $warehouseDTO->getName(),
                    'ref' => $warehouseDTO->getRef(),
                    'city_ref' => $warehouseDTO->getCityRef(),
                    'type' => $warehouseDTO->getTypeOfWarehouse(),
                ];
                $warehouse->id = $warehousesEntity->add($warehouse);

                if (!empty($ruLanguage) && !empty($warehouseDTO->getNameRu())) {
                    $this->languages->setLangId($ruLanguage->id);
                    $warehousesEntity->update($warehouse->id, [
                        'name' => $warehouseDTO->getNameRu(),
                    ]);
                }
            } else {
                $currentWarehouse = $currentWarehouses[$warehouseDTO->getRef()];
                foreach ($languages as $l) {
                    $this->languages->setLangId($l->id);

                    $cityName = $warehouseDTO->getName();
                    if ($l->label == 'ru' && !empty($warehouseDTO->getNameRu())) {
                        $cityName = $warehouseDTO->getNameRu();
                    }
                    $warehousesEntity->update($currentWarehouse->id, [
                        'name' => $cityName,
                        'type' => $warehouseDTO->getTypeOfWarehouse(),
                    ]);
                }
            }
        }

        // Повертаємо мову
        $this->languages->setLangId($currentLangId);

        return ceil($warehousesDTO->getTotalCount() / $limit);
    }
}