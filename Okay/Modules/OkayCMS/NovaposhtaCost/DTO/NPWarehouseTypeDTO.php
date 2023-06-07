<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\DTO;

class NPWarehouseTypeDTO
{
    private string $name;
    private string $nameRu;
    private string $ref;

    public function __construct(string $name, string $nameRu, string $ref)
    {
        $this->name = $name;
        $this->nameRu = $nameRu;
        $this->ref = $ref;
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
}