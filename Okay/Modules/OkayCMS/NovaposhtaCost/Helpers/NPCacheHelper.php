<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Settings;
use Okay\Entities\LanguagesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehouseTypeDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPCitiesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Entities\NPWarehousesEntity;
use Okay\Modules\OkayCMS\NovaposhtaCost\Init\Init;

class NPCacheHelper
{
    private NPApiHelper $apiHelper;
    private EntityFactory $entityFactory;
    private Languages $languages;
    private Settings $settings;
    private array $skippedWarehousesTypes = [
        '6f8c7162-4b72-4b0a-88e5-906948c6a92f', // Parcel Shop
        '95dc212d-479c-4ffb-a8ab-8c1b9073d0bc', // Поштомат Приват банка
    ];

    public function __construct(
        NPApiHelper   $apiHelper,
        EntityFactory $entityFactory,
        Languages     $languages,
        Settings      $settings
    ) {
        $this->apiHelper = $apiHelper;
        $this->entityFactory = $entityFactory;
        $this->languages = $languages;
        $this->settings = $settings;
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
                    'updated_at' => $this->getDate(),
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
                        'name' => $cityName,
                        'updated_at' => $this->getDate(),
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
                    'number' => $warehouseDTO->getNumber(),
                    'updated_at' => $this->getDate(),
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
                        'updated_at' => $this->getDate(),
                        'number' => $warehouseDTO->getNumber(),
                    ]);
                }
            }
        }

        // Повертаємо мову
        $this->languages->setLangId($currentLangId);

        return ceil($warehousesDTO->getTotalCount() / $limit);
    }

    /**
     * @return NPWarehouseTypeDTO[]
     *
     * Метод повертає оновлюємі типи відділень
     */
    public function getUpdatedWarehousesTypes(): array
    {
        $updatedTypes = [];
        foreach ($this->apiHelper->getWarehouseTypes() as $warehouseTypeDTO) {
            if (in_array($warehouseTypeDTO->getTypeRef(), $this->skippedWarehousesTypes)) {
                continue;
            }
            $updatedTypes[] = $warehouseTypeDTO;
        }
        return $updatedTypes;
    }

    public function cronUpdateWarehousesCache()
    {
        $this->rememberStartUpdateTime();
        foreach ($this->getUpdatedWarehousesTypes() as $warehouseTypeDTO) {
            $page = 1;
            do {
                $pagesNum = $this->updateWarehousesCache($warehouseTypeDTO->getTypeRef(), $page++, 1000);
            } while ($pagesNum !== null && $page <= $pagesNum);
        }
        $this->removeRedundant(Init::UPDATE_TYPE_WAREHOUSES);
    }

    public function cronUpdateCitiesCache()
    {
        $this->rememberStartUpdateTime();
        $page = 1;
        do {
            $pagesNum = $this->updateCitiesCache($page++, 1000);
        } while ($pagesNum !== null && $page <= $pagesNum);
        $this->removeRedundant(Init::UPDATE_TYPE_CITIES);
    }

    /**
     * @param string $removeType
     * @param string $typeRef
     * @return bool
     * @throws \Exception
     *
     * Видаляє старі дані, які після оновлення не прийшли з API НП.
     */
    public function removeRedundant(string $removeType, string $typeRef = ''): bool
    {
        /** @var NPCitiesEntity $citiesEntity */
        $citiesEntity = $this->entityFactory->get(NPCitiesEntity::class);
        /** @var NPWarehousesEntity $warehousesEntity */
        $warehousesEntity = $this->entityFactory->get(NPWarehousesEntity::class);

        if (!$startUpdateTime = $this->getStartUpdateTime()) {
            return false;
        }

        if ($removeType == Init::UPDATE_TYPE_CITIES) {
            $citiesEntity->removeRedundant($startUpdateTime);
        } elseif ($removeType == Init::UPDATE_TYPE_WAREHOUSES) {
            $updatedWarehousesTypes = [];
            foreach ($this->getUpdatedWarehousesTypes() as $warehouseTypeDTO) {
                if (empty($typeRef) || $typeRef == $warehouseTypeDTO->getTypeRef()) {
                    $updatedWarehousesTypes[] = $warehouseTypeDTO->getTypeRef();
                }
            }
            $warehousesEntity->removeRedundant(
                $startUpdateTime,
                $updatedWarehousesTypes
            );
        }

        return true;
    }

    /**
     * @return void
     *
     * Запам'ятовує час початку запуску оновлення, по якому будуть видалятися старі дані.
     */
    public function rememberStartUpdateTime(): void
    {
        $this->settings->set('np_start_update_datetime', $this->getDate());
    }

    public function getStartUpdateTime(): ?string
    {
        if (!$startTime = $this->settings->get('np_start_update_datetime')) {
            return null;
        }

        // Якщо дату запуску оновлення зберігали більше 30 хв тому, на неї не орієнтуємось
        if (date_diff(new \DateTime(), new \DateTime($startTime))->format('%i') > 30) {
            return null;
        }
        return $startTime;
    }

    private function getDate(): string
    {
        return date('Y-m-d H:i:s', time());
    }
}