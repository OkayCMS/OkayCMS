<?php

declare(strict_types=1);

namespace Core\Classes;

use Okay\Core\Classes\Purchase;
use PHPUnit\Framework\TestCase;

final class PurchaseTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $_SESSION = [];
    }

    public function testVariantAndAmountPopulatePurchaseTotals(): void
    {
        $purchase = new Purchase();

        $purchase->setProduct((object) [
            'id' => 10,
            'name' => 'Test product',
        ]);
        $purchase->setVariant((object) [
            'id' => 20,
            'name' => 'Small',
            'price' => '12.50',
            'sku' => 'SKU-20',
            'units' => 'pcs',
        ]);
        $purchase->setAmount(3);

        self::assertSame(10, $purchase->product_id);
        self::assertSame('Test product', $purchase->product_name);
        self::assertSame(20, $purchase->variant_id);
        self::assertSame('Small', $purchase->variant_name);
        self::assertSame(37.5, $purchase->meta->undiscounted_total_price);
    }

    public function testUpdateTotalsFloorsNegativeVariantTotalsAtZero(): void
    {
        $purchase = new Purchase();

        $purchase->setVariant((object) [
            'id' => 20,
            'name' => 'Small',
            'price' => -5,
            'sku' => 'SKU-20',
            'units' => 'pcs',
        ]);
        $purchase->setAmount(3);

        self::assertSame(0, $purchase->meta->undiscounted_total_price);
    }
}
