<?php

namespace Okay\Core\Modules\DTO;

class ModificationDTO
{
    private string $file;

    /** @var TplChangeDTO[]  */
    private array $changes;

    public function __construct(string $file, array $changes)
    {
        $this->file = $file;
        $this->changes = $changes;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @return TplChangeDTO[]
     */
    public function getChanges(): array
    {
        return $this->changes;
    }
}