<?php

namespace Okay\Helpers\AiRequests;

abstract class AbstractAiRequest
{
    protected ?int    $entityId;
    protected ?array  $parts;
    protected ?string $name;
    protected ?array  $additionalInfoData;

    public function __construct(int $entityId, ?array $parts, ?string $name, ?array $additionalInfoData)
    {
        $this->entityId       = $entityId;
        $this->parts          = $parts;
        $this->name           = $name;
        $this->additionalInfoData = $additionalInfoData;
    }

    abstract public function getRequestText(string $field): string;

    public function getAdditionalInfo(): string
    {
        return '';
    }
}