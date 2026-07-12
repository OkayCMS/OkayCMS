<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\Helpers;

use Okay\Core\Settings;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPCitiesCollectionDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPCityDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehouseDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehousesCollectionDTO;
use Okay\Modules\OkayCMS\NovaposhtaCost\DTO\NPWarehouseTypeDTO;
use Psr\Log\LoggerInterface;

/**
 * @phpstan-type WarehouseTypeApiRow object{Description: string, DescriptionRu?: string|null, Ref: string}&\stdClass
 * @phpstan-type WarehouseApiRow object{Description: string, DescriptionRu?: string|null, Ref: string, CityRef: string, TypeOfWarehouse: string, Number: int|string}&\stdClass
 * @phpstan-type CityApiRow object{Description: string, DescriptionRu?: string|null, Ref: string}&\stdClass
 * @phpstan-type ApiResponse object{success?: bool, data?: list<object>, info?: object{totalCount?: int|string}, errors?: mixed}&\stdClass
 */
class NPApiHelper
{
    private const THROTTLE_DELAY_MICROSECONDS = 500000;

    private string $lastCallError = '';
    private Settings $settings;
    private LoggerInterface $logger;

    public function __construct(
        Settings $settings,
        LoggerInterface $logger
    ) {
        $this->settings = $settings;
        $this->logger = $logger;
    }

    /**
     * Метод достает типы отделений из API Новой Почты
     * @return NPWarehouseTypeDTO[]
     */
    public function getWarehouseTypes(bool $throttleRequests = false): array
    {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getWarehouseTypes",
        ];

        $response = $this->request($request, false, $throttleRequests);
        /** @var ApiResponse|false $response */
        if (!empty($response->success)) {
            $result = [];
            foreach ($response->data as $warehouseTypeData) {
                /** @var WarehouseTypeApiRow $warehouseTypeData */
                $name = $nameRu = htmlspecialchars($warehouseTypeData->Description);
                if (!empty($warehouseTypeData->DescriptionRu)) {
                    $nameRu = htmlspecialchars($warehouseTypeData->DescriptionRu);
                }
                $result[] = new NPWarehouseTypeDTO(
                    $name,
                    $nameRu,
                    $warehouseTypeData->Ref
                );
            }
            return $result;
        }
        return [];
    }

    public function checkApiKey(): string
    {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getWarehouseTypes",
        ];

        $this->request($request);
        return $this->getLastCallError();
    }

    public function getWarehouses(
        string $warehouseType,
        int $page,
        int $limit,
        bool $throttleRequests = false
    ): ?NPWarehousesCollectionDTO {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getWarehouses",
            "methodProperties" => [
                "TypeOfWarehouseRef" => $warehouseType,
                "Page" => (string) $page,
                "Limit" => (string) $limit,
            ]
        ];

        $response = $this->request($request, true, $throttleRequests);
        /** @var ApiResponse|false $response */
        if (!empty($response->success)) {
            $warehousesDTO = new NPWarehousesCollectionDTO();
            foreach ($response->data as $warehouseData) {
                /** @var WarehouseApiRow $warehouseData */
                // Перевіряємо тип, оскільки НП може повернути відділення не того типу і вони задублюються на сайті
                if ($warehouseData->TypeOfWarehouse != $warehouseType) {
                    continue;
                }
                $name = htmlspecialchars($warehouseData->Description);
                $name = preg_replace('~(?:(№\d+)\S*)~', '$1', $name);
                $name = is_string($name) ? $name : '';
                $warehouseDTO = new NPWarehouseDTO(
                    $name,
                    $warehouseData->Ref,
                    $warehouseData->CityRef,
                    $warehouseData->TypeOfWarehouse,
                    (int)$warehouseData->Number
                );
                if (!empty($warehouseData->DescriptionRu)) {
                    $nameRu = htmlspecialchars($warehouseData->DescriptionRu);
                    $nameRu = preg_replace('~(?:(№\d+)\S*)~', '$1', $nameRu);
                    $nameRu = is_string($nameRu) ? $nameRu : '';
                    $warehouseDTO->setNameRu($nameRu);
                }
                $warehousesDTO->setWarehouse($warehouseDTO);
            }
            if (!empty($response->info->totalCount)) {
                $warehousesDTO->setTotalCount((int)$response->info->totalCount);
            }
            return $warehousesDTO;
        } else {
            return null;
        }
    }

    public function getCities(int $page, int $limit, bool $throttleRequests = false): ?NPCitiesCollectionDTO
    {
        $request = [
            "modelName" => "Address",
            "calledMethod" => "getCities",
            "methodProperties" => [
                "Page" => (string) $page,
                "Limit" => (string) $limit,
            ],
        ];

        $response = $this->request($request, true, $throttleRequests);
        /** @var ApiResponse|false $response */
        if (!empty($response->success)) {
            $citiesDTO = new NPCitiesCollectionDTO();
            foreach ($response->data as $cityData) {
                /** @var CityApiRow $cityData */
                $cityDTO = new NPCityDTO(
                    htmlspecialchars($cityData->Description),
                    $cityData->Ref
                );
                if (!empty($cityData->DescriptionRu)) {
                    $cityDTO->setNameRu(htmlspecialchars($cityData->DescriptionRu));
                }
                $citiesDTO->setCity($cityDTO);
            }
            if (!empty($response->info->totalCount)) {
                $citiesDTO->setTotalCount((int)$response->info->totalCount);
            }
            return $citiesDTO;
        } else {
            return null;
        }
    }

    public function getLastCallError(): string
    {
        return $this->lastCallError;
    }

    /**
     * @param array<string, mixed> $requestParams
     */
    public function request(array $requestParams, bool $isUseApiKey = true, bool $throttleRequests = false)
    {
        if (empty($requestParams)) {
            return false;
        }
        if ($isUseApiKey) {
            $requestParams["apiKey"] = $this->settings->get('newpost_key');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.novaposhta.ua/v2.0/json/');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $encodedRequest = json_encode($requestParams);
        if ($encodedRequest === false) {
            $encodedRequest = '';
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedRequest);
        curl_setopt($ch, CURLOPT_POST, 1);
        $response = curl_exec($ch);
        if ($throttleRequests) {
            usleep(self::THROTTLE_DELAY_MICROSECONDS);
        }

        if ($response === false) {
            $this->lastCallError = 'Error in API call';
            $this->logger->warning('Novaposhta cost error: "' . $this->lastCallError . '"');
            return false;
        }
        if (!is_string($response)) {
            $this->lastCallError = 'Invalid API response';
            $this->logger->warning('Novaposhta cost error: "' . $this->lastCallError . '"');
            return false;
        }

        $response = json_decode($response);
        /** @var ApiResponse|null $response */

        if (!empty($response->errors)) {
            $this->lastCallError = implode('<br>', (array)$response->errors);
            // Запам'ятовуємо помилку по API key
            if (strpos($this->lastCallError, 'API key') !== false) {
                $this->settings->set('np_api_key_error', $this->lastCallError);
            }
            $this->logger->warning('Novaposhta cost error: "' . $this->lastCallError . '"');
            return false;
        }
        if (!empty($response->success)) {
            if (empty($response->data)) {
                $this->lastCallError = 'Response data is empty';
                $this->logger->warning('Novaposhta cost error: "' . $this->lastCallError . '"');
                return false;
            }
            return $response;
        }

        return false;
    }
}
