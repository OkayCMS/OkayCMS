<?php

declare(strict_types=1);

namespace Okay\Core\Stock;

final readonly class VariantAvailability
{
    public const STATUS_IN_STOCK = 'in_stock';
    public const STATUS_BACKORDER = 'backorder';
    public const STATUS_OUT_OF_STOCK = 'out_of_stock';

    public const SCHEMA_IN_STOCK = 'https://schema.org/InStock';
    public const SCHEMA_BACKORDER = 'https://schema.org/BackOrder';
    public const SCHEMA_OUT_OF_STOCK = 'https://schema.org/OutOfStock';

    public function __construct(
        private ?int $rawStock,
        private string $status,
        private string $effectiveStatus,
        private bool $tracked,
        private bool $orderable,
        private ?int $orderLimit,
        private string $schemaAvailabilityUrl
    ) {
    }

    public function rawStock(): ?int
    {
        return $this->rawStock;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function effectiveStatus(): string
    {
        return $this->effectiveStatus;
    }

    public function isTracked(): bool
    {
        return $this->tracked;
    }

    public function isOrderable(): bool
    {
        return $this->orderable;
    }

    public function orderLimit(): ?int
    {
        return $this->orderLimit;
    }

    public function schemaAvailabilityUrl(): string
    {
        return $this->schemaAvailabilityUrl;
    }

    public function feedAvailability(string $feedType): string
    {
        return match ($feedType) {
            'google_merchant' => $this->googleMerchantAvailability(),
            'facebook' => $this->effectiveStatus === self::STATUS_OUT_OF_STOCK ? 'out of stock' : 'in stock',
            default => $this->effectiveStatus,
        };
    }

    private function googleMerchantAvailability(): string
    {
        return match ($this->effectiveStatus) {
            self::STATUS_BACKORDER => 'backorder',
            self::STATUS_OUT_OF_STOCK => 'out_of_stock',
            default => 'in_stock',
        };
    }
}
