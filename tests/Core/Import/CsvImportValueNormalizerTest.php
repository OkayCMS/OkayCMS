<?php

declare(strict_types=1);

namespace Core\Import;

use Okay\Core\Import\CsvImportValueNormalizer;
use PHPUnit\Framework\TestCase;

final class CsvImportValueNormalizerTest extends TestCase
{
    public function testNormalizesDecimalCommaAndDecimalDot(): void
    {
        $normalizer = new CsvImportValueNormalizer();

        self::assertSame('10.50', $normalizer->normalizeDecimal('10,50'));
        self::assertSame('10.50', $normalizer->normalizeDecimal('10.50'));
    }

    public function testNormalizesThousandsSeparators(): void
    {
        $normalizer = new CsvImportValueNormalizer();

        self::assertSame('1234.50', $normalizer->normalizeDecimal('1 234,50'));
        self::assertSame('1234.50', $normalizer->normalizeDecimal("1\xc2\xa0234,50"));
        self::assertSame('1234.50', $normalizer->normalizeDecimal('1.234,50'));
        self::assertSame('1234.50', $normalizer->normalizeDecimal('1,234.50'));
    }

    public function testKeepsEmptyValuesEmpty(): void
    {
        self::assertSame('', (new CsvImportValueNormalizer())->normalizeDecimal('  '));
    }

    public function testNormalizesStockBackorderMarkersToNull(): void
    {
        $normalizer = new CsvImportValueNormalizer();

        self::assertNull($normalizer->normalizeStock(''));
        self::assertNull($normalizer->normalizeStock('NULL'));
        self::assertNull($normalizer->normalizeStock('infinity'));
        self::assertNull($normalizer->normalizeStock('unlimited'));
        self::assertNull($normalizer->normalizeStock('backorder'));
    }

    public function testNormalizesIntegerStockValues(): void
    {
        $normalizer = new CsvImportValueNormalizer();

        self::assertSame(10, $normalizer->normalizeStock('10'));
        self::assertSame(0, $normalizer->normalizeStock('0'));
        self::assertSame(-2, $normalizer->normalizeStock('-2'));
    }

    public function testRejectsFractionalStockValues(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new CsvImportValueNormalizer())->normalizeStock('1.5');
    }
}
