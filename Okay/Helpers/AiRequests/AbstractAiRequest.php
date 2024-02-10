<?php

namespace Okay\Helpers\AiRequests;

abstract class AbstractAiRequest
{
    protected ?int $entityId;
    protected ?string $name;

    public function __construct(?int $entityId, ?string $name)
    {
        $this->entityId = $entityId;
        $this->name = $name;
    }

    abstract public function getRequestText(string $field): string;

    public function getAdditionalInfo(): string
    {
        return '';
    }
}