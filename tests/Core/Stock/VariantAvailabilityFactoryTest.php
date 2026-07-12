<?php

declare(strict_types=1);

namespace Core\Stock;

use Okay\Core\Stock\VariantAvailability;
use Okay\Core\Stock\VariantAvailabilityFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VariantAvailabilityFactoryTest extends TestCase
{
    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('stockProvider')]
    public function testCreatesAvailabilityContract(
        ?int $rawStock,
        bool $allowMissingProductsOrder,
        bool $useBackorderStatus,
        array $expected
    ): void {
        $availability = (new VariantAvailabilityFactory())->fromRawStock(
            $rawStock,
            $allowMissingProductsOrder,
            10,
            $useBackorderStatus
        );

        self::assertSame($rawStock, $availability->rawStock());
        self::assertSame($expected['status'], $availability->status());
        self::assertSame($expected['effective_status'], $availability->effectiveStatus());
        self::assertSame($expected['tracked'], $availability->isTracked());
        self::assertSame($expected['orderable'], $availability->isOrderable());
        self::assertSame($expected['order_limit'], $availability->orderLimit());
        self::assertSame($expected['schema_availability'], $availability->schemaAvailabilityUrl());
    }

    /**
     * @return array<string, array{0: ?int, 1: bool, 2: bool, 3: array<string, mixed>}>
     */
    public static function stockProvider(): array
    {
        return [
            'positive tracked stock' => [
                5,
                false,
                true,
                [
                    'status' => VariantAvailability::STATUS_IN_STOCK,
                    'effective_status' => VariantAvailability::STATUS_IN_STOCK,
                    'tracked' => true,
                    'orderable' => true,
                    'order_limit' => 5,
                    'schema_availability' => VariantAvailability::SCHEMA_IN_STOCK,
                ],
            ],
            'positive tracked stock is limited by raw stock' => [
                15,
                false,
                true,
                [
                    'status' => VariantAvailability::STATUS_IN_STOCK,
                    'effective_status' => VariantAvailability::STATUS_IN_STOCK,
                    'tracked' => true,
                    'orderable' => true,
                    'order_limit' => 15,
                    'schema_availability' => VariantAvailability::SCHEMA_IN_STOCK,
                ],
            ],
            'zero stock without override' => [
                0,
                false,
                true,
                [
                    'status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'effective_status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'tracked' => true,
                    'orderable' => false,
                    'order_limit' => 0,
                    'schema_availability' => VariantAvailability::SCHEMA_OUT_OF_STOCK,
                ],
            ],
            'negative stock without override' => [
                -2,
                false,
                true,
                [
                    'status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'effective_status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'tracked' => true,
                    'orderable' => false,
                    'order_limit' => 0,
                    'schema_availability' => VariantAvailability::SCHEMA_OUT_OF_STOCK,
                ],
            ],
            'null stock is backorder without override' => [
                null,
                false,
                true,
                [
                    'status' => VariantAvailability::STATUS_BACKORDER,
                    'effective_status' => VariantAvailability::STATUS_BACKORDER,
                    'tracked' => false,
                    'orderable' => true,
                    'order_limit' => null,
                    'schema_availability' => VariantAvailability::SCHEMA_BACKORDER,
                ],
            ],
            'global override makes zero stock effectively in stock' => [
                0,
                true,
                true,
                [
                    'status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'effective_status' => VariantAvailability::STATUS_IN_STOCK,
                    'tracked' => true,
                    'orderable' => true,
                    'order_limit' => null,
                    'schema_availability' => VariantAvailability::SCHEMA_IN_STOCK,
                ],
            ],
            'global override makes null stock effectively in stock' => [
                null,
                true,
                true,
                [
                    'status' => VariantAvailability::STATUS_IN_STOCK,
                    'effective_status' => VariantAvailability::STATUS_IN_STOCK,
                    'tracked' => false,
                    'orderable' => true,
                    'order_limit' => null,
                    'schema_availability' => VariantAvailability::SCHEMA_IN_STOCK,
                ],
            ],
        ];
    }

    public function testNullStockUsesLegacyInStockStatusWhenBackorderStatusIsDisabled(): void
    {
        $availability = (new VariantAvailabilityFactory())->fromRawStock(null, false, 10, false);

        self::assertSame(VariantAvailability::STATUS_IN_STOCK, $availability->status());
        self::assertSame(VariantAvailability::STATUS_IN_STOCK, $availability->effectiveStatus());
        self::assertTrue($availability->isOrderable());
        self::assertNull($availability->orderLimit());
        self::assertSame(VariantAvailability::SCHEMA_IN_STOCK, $availability->schemaAvailabilityUrl());
        self::assertSame('in_stock', $availability->feedAvailability('google_merchant'));
    }

    public function testFeedAvailabilityUsesFeedVocabulary(): void
    {
        $availability = (new VariantAvailabilityFactory())->fromRawStock(null, false, 10, true);

        self::assertSame('backorder', $availability->feedAvailability('google_merchant'));
        self::assertSame('in stock', $availability->feedAvailability('facebook'));
        self::assertSame(VariantAvailability::STATUS_BACKORDER, $availability->feedAvailability('custom'));
    }
}
