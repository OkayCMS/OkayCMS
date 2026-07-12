<?php

declare(strict_types=1);

namespace Entities;

use Okay\Core\Entity\Entity;
use Okay\Core\Stock\VariantAvailability;
use Okay\Core\Stock\VariantAvailabilityFactory;
use Okay\Entities\VariantsEntity;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VariantsEntityStockContractTest extends TestCase
{
    /**
     * @param array<string, mixed> $expected
     */
    #[DataProvider('stockProvider')]
    public function testResetInfoAddsExplicitStockContractFields(?int $rawStock, array $expected): void
    {
        $variant = (object) [
            'stock' => $rawStock,
            'infinity' => $rawStock === null ? 1 : 0,
            'compare_price' => 0,
            'units' => null,
        ];

        $variant = $this->resetVariantInfo($variant, false, true);

        self::assertSame($expected['legacy_stock'], $variant->stock);
        self::assertSame($expected['stock_raw'], $variant->stock_raw);
        self::assertSame($expected['status'], $variant->stock_status);
        self::assertSame($expected['effective_status'], $variant->stock_effective_status);
        self::assertSame($expected['tracked'], $variant->stock_is_tracked);
        self::assertSame($expected['orderable'], $variant->available_to_order);
        self::assertSame($expected['order_limit'], $variant->order_amount_limit);
        self::assertSame($expected['schema_availability'], $variant->schema_availability);
        self::assertSame($rawStock === null ? 1 : 0, $variant->infinity);
    }

    /**
     * @return array<string, array{0: ?int, 1: array<string, mixed>}>
     */
    public static function stockProvider(): array
    {
        return [
            'negative stock' => [
                -2,
                [
                    'legacy_stock' => -2,
                    'stock_raw' => -2,
                    'status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'effective_status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'tracked' => true,
                    'orderable' => false,
                    'order_limit' => 0,
                    'schema_availability' => VariantAvailability::SCHEMA_OUT_OF_STOCK,
                ],
            ],
            'zero stock' => [
                0,
                [
                    'legacy_stock' => 0,
                    'stock_raw' => 0,
                    'status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'effective_status' => VariantAvailability::STATUS_OUT_OF_STOCK,
                    'tracked' => true,
                    'orderable' => false,
                    'order_limit' => 0,
                    'schema_availability' => VariantAvailability::SCHEMA_OUT_OF_STOCK,
                ],
            ],
            'positive stock' => [
                5,
                [
                    'legacy_stock' => 5,
                    'stock_raw' => 5,
                    'status' => VariantAvailability::STATUS_IN_STOCK,
                    'effective_status' => VariantAvailability::STATUS_IN_STOCK,
                    'tracked' => true,
                    'orderable' => true,
                    'order_limit' => 5,
                    'schema_availability' => VariantAvailability::SCHEMA_IN_STOCK,
                ],
            ],
            'null stock' => [
                null,
                [
                    'legacy_stock' => 10,
                    'stock_raw' => null,
                    'status' => VariantAvailability::STATUS_BACKORDER,
                    'effective_status' => VariantAvailability::STATUS_BACKORDER,
                    'tracked' => false,
                    'orderable' => true,
                    'order_limit' => null,
                    'schema_availability' => VariantAvailability::SCHEMA_BACKORDER,
                ],
            ],
        ];
    }

    public function testGlobalOverrideKeepsRawStatusAndMakesEffectiveStatusInStock(): void
    {
        $variant = $this->resetVariantInfo((object) ['stock' => 0, 'infinity' => 0], true, true);

        self::assertSame(VariantAvailability::STATUS_OUT_OF_STOCK, $variant->stock_status);
        self::assertSame(VariantAvailability::STATUS_IN_STOCK, $variant->stock_effective_status);
        self::assertTrue($variant->available_to_order);
        self::assertNull($variant->order_amount_limit);
        self::assertSame(VariantAvailability::SCHEMA_IN_STOCK, $variant->schema_availability);
    }

    public function testNullStockKeepsLegacyInStockStatusWhenBackorderStatusIsDisabled(): void
    {
        $variant = $this->resetVariantInfo((object) ['stock' => null, 'infinity' => 1], false, false);

        self::assertSame(10, $variant->stock);
        self::assertNull($variant->stock_raw);
        self::assertSame(VariantAvailability::STATUS_IN_STOCK, $variant->stock_status);
        self::assertSame(VariantAvailability::STATUS_IN_STOCK, $variant->stock_effective_status);
        self::assertTrue($variant->available_to_order);
        self::assertSame(VariantAvailability::SCHEMA_IN_STOCK, $variant->schema_availability);
    }

    /**
     * @return object{
     *     stock: int,
     *     stock_raw: ?int,
     *     stock_status: string,
     *     stock_effective_status: string,
     *     stock_is_tracked: bool,
     *     available_to_order: bool,
     *     order_amount_limit: ?int,
     *     schema_availability: string,
     *     infinity: int
     * }
     */
    private function resetVariantInfo(object $variant, bool $allowMissingProductsOrder, bool $useBackorderStatus): object
    {
        $entity = (new \ReflectionClass(VariantsEntity::class))->newInstanceWithoutConstructor();

        $settings = new class ($allowMissingProductsOrder, $useBackorderStatus) {
            public int $max_order_amount = 10;
            public string $units = 'pcs';

            public function __construct(
                private bool $allowMissingProductsOrder,
                private bool $useBackorderStatus
            ) {
            }

            public function get(string $key): mixed
            {
                return match ($key) {
                    'is_preorder' => $this->allowMissingProductsOrder,
                    'use_backorder_status' => $this->useBackorderStatus,
                    'max_order_amount' => $this->max_order_amount,
                    default => null,
                };
            }
        };

        $serviceLocator = new class {
            public function getService(string $class): object
            {
                if ($class !== VariantAvailabilityFactory::class) {
                    throw new \LogicException('Unexpected service requested.');
                }

                return new VariantAvailabilityFactory();
            }
        };

        $entityReflection = new \ReflectionClass(Entity::class);
        $settingsProperty = $entityReflection->getProperty('settings');
        $settingsProperty->setValue($entity, $settings);
        $serviceLocatorProperty = $entityReflection->getProperty('serviceLocator');
        $serviceLocatorProperty->setValue($entity, $serviceLocator);

        $method = new \ReflectionMethod(VariantsEntity::class, 'resetInfo');

        return $method->invoke($entity, $variant);
    }
}
