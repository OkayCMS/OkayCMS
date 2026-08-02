<?php

declare(strict_types=1);

namespace Okay\Core\Stock;

final class VariantAvailabilityFactory
{
    public function fromRawStock(
        ?int $rawStock,
        bool $allowMissingProductsOrder,
        int $maxOrderAmount,
        bool $useBackorderStatus = false
    ): VariantAvailability {
        if ($allowMissingProductsOrder) {
            return new VariantAvailability(
                $rawStock,
                $this->rawStatus($rawStock, false),
                VariantAvailability::STATUS_IN_STOCK,
                $rawStock !== null,
                true,
                null,
                VariantAvailability::SCHEMA_IN_STOCK
            );
        }

        if ($rawStock === null) {
            if (!$useBackorderStatus) {
                return new VariantAvailability(
                    null,
                    VariantAvailability::STATUS_IN_STOCK,
                    VariantAvailability::STATUS_IN_STOCK,
                    false,
                    true,
                    null,
                    VariantAvailability::SCHEMA_IN_STOCK
                );
            }

            return new VariantAvailability(
                null,
                VariantAvailability::STATUS_BACKORDER,
                VariantAvailability::STATUS_BACKORDER,
                false,
                true,
                null,
                VariantAvailability::SCHEMA_BACKORDER
            );
        }

        if ($rawStock > 0) {
            return new VariantAvailability(
                $rawStock,
                VariantAvailability::STATUS_IN_STOCK,
                VariantAvailability::STATUS_IN_STOCK,
                true,
                true,
                $rawStock,
                VariantAvailability::SCHEMA_IN_STOCK
            );
        }

        return new VariantAvailability(
            $rawStock,
            VariantAvailability::STATUS_OUT_OF_STOCK,
            VariantAvailability::STATUS_OUT_OF_STOCK,
            true,
            false,
            0,
            VariantAvailability::SCHEMA_OUT_OF_STOCK
        );
    }

    private function rawStatus(?int $rawStock, bool $useBackorderStatus): string
    {
        if ($rawStock === null) {
            return $useBackorderStatus
                ? VariantAvailability::STATUS_BACKORDER
                : VariantAvailability::STATUS_IN_STOCK;
        }

        if ($rawStock > 0) {
            return VariantAvailability::STATUS_IN_STOCK;
        }

        return VariantAvailability::STATUS_OUT_OF_STOCK;
    }
}
