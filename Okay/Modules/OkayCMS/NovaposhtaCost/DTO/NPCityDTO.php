<?php

namespace Okay\Modules\OkayCMS\NovaposhtaCost\DTO;

class NPCityDTO
{
    private string $name;
    private string $nameRu = '';
    private string $ref;

    public function __construct(string $name, string $ref)
    {
        $this->name = $name;
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

    /**
     * @param string $nameRu
     */
    public function setNameRu(string $nameRu): void
    {
        $this->nameRu = $nameRu;
    }
}