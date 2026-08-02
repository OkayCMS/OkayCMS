<?php

declare(strict_types=1);

namespace Core\Classes;

use Okay\Core\Classes\Discount;
use PHPUnit\Framework\TestCase;

final class DiscountTest extends TestCase
{
    public function testCalculateAcceptsNumericStringDiscountValues(): void
    {
        $discount = new Discount();
        $discount->type = 'percent';
        $discount->value = '12.5';
        $discount->fromLastDiscount = true;
        $discount->priceBeforeDiscount = '80';

        $discount->calculate('100');

        self::assertSame(10.0, $discount->absoluteDiscount);
        self::assertSame(10.0, $discount->percentDiscount);
        self::assertSame(70.0, $discount->priceAfterDiscount);
    }

    public function testCalculateFloorsNegativeDiscountedPriceAtZero(): void
    {
        $discount = new Discount();
        $discount->type = 'absolute';
        $discount->value = '120';
        $discount->fromLastDiscount = false;
        $discount->priceBeforeDiscount = '80';

        $discount->calculate('100');

        self::assertSame('120', $discount->absoluteDiscount);
        self::assertSame(0, $discount->priceAfterDiscount);
    }
}
