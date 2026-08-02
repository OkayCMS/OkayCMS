<?php

declare(strict_types=1);

namespace Okay\Core\Import;

final class CsvImportValueNormalizer
{
    private const NULL_STOCK_MARKERS = [
        '',
        'null',
        '∞',
        'infinity',
        'unlimited',
        'backorder',
    ];

    public function normalizeDecimal(string $value): string
    {
        $value = trim(str_replace(["\xc2\xa0", "\xe2\x80\xaf", ' '], '', $value));
        if ($value === '') {
            return '';
        }

        $lastComma = strrpos($value, ',');
        $lastDot = strrpos($value, '.');

        if ($lastComma !== false && $lastDot !== false) {
            if ($lastComma > $lastDot) {
                return str_replace(',', '.', str_replace('.', '', $value));
            }

            return str_replace(',', '', $value);
        }

        if ($lastComma !== false) {
            return str_replace(',', '.', $value);
        }

        return $value;
    }

    public function normalizeStock(string $value): ?int
    {
        $value = trim($value);
        if (in_array(mb_strtolower($value), self::NULL_STOCK_MARKERS, true)) {
            return null;
        }

        if (!preg_match('/^-?\d+$/', $value)) {
            throw new \InvalidArgumentException('Stock value must be an integer or a backorder marker.');
        }

        return (int) $value;
    }
}
