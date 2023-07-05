<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\DTO;

class NPWarehouseDTO
{
    private string $name;
    private string $nameRu = '';
    private string $ref;
    private string $cityRef;
    private string $typeOfWarehouse;
    private int $number;

    public function __construct(
        string $name,
        string $ref,
        string $cityRef,
        string $typeOfWarehouse,
        int $number
    ) {
        $this->name = $name;
        $this->ref = $ref;
        $this->cityRef = $cityRef;
        $this->typeOfWarehouse = $typeOfWarehouse;
        $this->number = $number;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRef(): string
    {
        return $this->ref;
    }

    /**
     * @return string
     */
    public function getNameRu(): string
    {
        return $this->nameRu;
    }

    /**
     * @param string $nameRu
     */
    public function setNameRu(string $nameRu): void
    {
        $this->nameRu = $nameRu;
    }

    /**
     * @return string
     */
    public function getCityRef(): string
    {
        return $this->cityRef;
    }

    /**
     * @return string
     */
    public function getTypeOfWarehouse(): string
    {
        return $this->typeOfWarehouse;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}