<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\DTO;

class NPWarehousesCollectionDTO
{
    /**
     * @var NPWarehouseDTO[]
     */
    private array $warehouses = [];
    private int $totalCount = 0;


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
     * @return array<string, NPWarehouseDTO>
     */
    public function getWarehouses(): array
    {
        return $this->warehouses;
    }

    /**
     * @param NPWarehouseDTO $warehouseDTO
     */
    public function setWarehouse(NPWarehouseDTO $warehouseDTO): void
    {
        $this->warehouses[$warehouseDTO->getRef()] = $warehouseDTO;
    }

    /**
     * @return list<string>
     */
    public function getWarehousesRefs(): array
    {
        return array_keys($this->warehouses);
    }
}
