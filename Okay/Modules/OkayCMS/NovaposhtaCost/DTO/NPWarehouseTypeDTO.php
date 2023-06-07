<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\DTO;

class NPWarehouseTypeDTO
{
    private string $name;
    private string $nameRu;
    private string $typeRef;

    public function __construct(string $name, string $nameRu, string $typeRef)
    {
        $this->name = $name;
        $this->nameRu = $nameRu;
        $this->typeRef = $typeRef;
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
    public function getTypeRef(): string
    {
        return $this->typeRef;
    }

    /**
     * @return string
     */
    public function getNameRu(): string
    {
        return $this->nameRu;
    }
}