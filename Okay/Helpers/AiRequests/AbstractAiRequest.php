<?php

namespace Okay\Helpers\AiRequests;

use Okay\Core\ServiceLocator;
use Okay\Core\Settings;

abstract class AbstractAiRequest
{
    protected ?int $entityId;
    protected ?string $name;
    protected Settings $settings;

    public function __construct(?int $entityId, ?string $name)
    {
        $this->entityId = $entityId;
        $this->name = $name;
        $SL = ServiceLocator::getInstance();
        $this->settings = $SL->getService(Settings::class);
    }

    abstract public function getRequestText(string $field): string;

    public function getAdditionalInfo(): string
    {
        return '';
    }
}