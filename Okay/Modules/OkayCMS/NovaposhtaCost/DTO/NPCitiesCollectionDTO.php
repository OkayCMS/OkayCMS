<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\DTO;

class NPCitiesCollectionDTO
{
    /**
     * @var NPCityDTO[]
     */
    private array $cities = [];
    private int $totalCount = 0;

    /**
     * @return NPCityDTO[]
     */
    public function getCities(): array
    {
        return $this->cities;
    }

    /**
     * @param NPCityDTO $city
     */
    public function setCity(NPCityDTO $city): void
    {
        $this->cities[$city->getRef()] = $city;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    /**
     * @return array
     */
    public function getCitiesRefs(): array
    {
        return array_keys($this->cities);
    }
}