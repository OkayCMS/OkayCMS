<?php

declare(strict_types=1);

namespace Okay\Core\Import;

final class CsvImportNormalizer
{
    private const SAMPLE_BYTES = 1048576;
    private const TARGET_DELIMITER = ';';

    /** @var list<string> */
    private const DELIMITERS = [";", ",", "|", "\t"];

    /** @var list<string> */
    private const ENCODING_CANDIDATES = [
        'Windows-1251',
        'MacCyrillic',
        'CP10007',
        'CP866',
        'KOI8-R',
        'KOI8-U',
        'ISO-8859-5',
    ];

    /** @var array<string, true> */
    private const KNOWN_HEADERS = [
        'category' => true,
        'категория' => true,
        'категорія' => true,
        'brand' => true,
        'бренд' => true,
        'product' => true,
        'name' => true,
        'товар' => true,
        'название' => true,
        'назва' => true,
        'наименование' => true,
        'найменування' => true,
        'variant' => true,
        'вариант' => true,
        'варіант' => true,
        'sku' => true,
        'артикул' => true,
        'price' => true,
        'цена' => true,
        'ціна' => true,
        'compare price' => true,
        'old price' => true,
        'старая цена' => true,
        'стара ціна' => true,
        'currency_id' => true,
        'currency' => true,
        'currency id' => true,
        'id валюты' => true,
        'id валюти' => true,
        'weight' => true,
        'вес варианта' => true,
        'вага варіанта' => true,
        'stock' => true,
        'склад' => true,
        'на складе' => true,
        'units' => true,
        'ед. изм.' => true,
        'visible' => true,
        'published' => true,
        'видим' => true,
        'featured' => true,
        'hit' => true,
        'хит' => true,
        'хіт' => true,
        'meta title' => true,
        'meta keywords' => true,
        'meta description' => true,
        'annotation' => true,
        'аннотация' => true,
        'анотація' => true,
        'краткое описание' => true,
        'короткий опис' => true,
        'description' => true,
        'описание' => true,
        'опис' => true,
        'images' => true,
        'изображения' => true,
        'зображення' => true,
        'url' => true,
        'адрес' => true,
        'адреса' => true,
    ];

    public function normalize(string $source, string $destination): CsvImportMetadata
    {
        $sample = file_get_contents($source, false, null, 0, self::SAMPLE_BYTES);
        if ($sample === false) {
            throw new \RuntimeException(sprintf('Cannot read import file sample: %s', $source));
        }

        $encoding = $this->detectEncoding($sample);
        $decodedSample = $this->decodeSample($sample, $encoding);
        $sourceDelimiter = $this->detectDelimiter($decodedSample);

        $sourceStream = $this->openDecodedStream($source, $encoding);
        $this->ensureDestinationDirectory($destination);

        $destinationStream = fopen($destination, 'wb');
        if ($destinationStream === false) {
            fclose($sourceStream);
            throw new \RuntimeException(sprintf('Cannot open normalized import file for writing: %s', $destination));
        }

        $rowsWritten = 0;
        $isFirstRow = true;
        while (($row = fgetcsv($sourceStream, 0, $sourceDelimiter, '"', '\\')) !== false) {
            $row = $this->normalizeRow($row);
            if ($isFirstRow) {
                $row = $this->stripFirstFieldBom($row);
                if ($this->isSeparatorHintRow($row)) {
                    $isFirstRow = false;
                    continue;
                }
            }

            fputcsv($destinationStream, $row, self::TARGET_DELIMITER, '"', '\\');
            $rowsWritten++;
            $isFirstRow = false;
        }

        fclose($sourceStream);
        fclose($destinationStream);

        return new CsvImportMetadata($encoding, $sourceDelimiter, self::TARGET_DELIMITER, $rowsWritten);
    }

    private function detectEncoding(string $sample): string
    {
        if (str_starts_with($sample, "\xEF\xBB\xBF")) {
            return 'UTF-8';
        }

        if (str_starts_with($sample, "\xFF\xFE")) {
            return 'UTF-16LE';
        }

        if (str_starts_with($sample, "\xFE\xFF")) {
            return 'UTF-16BE';
        }

        if (!str_contains($sample, "\x00") && mb_check_encoding($sample, 'UTF-8')) {
            return 'UTF-8';
        }

        $bestEncoding = null;
        $bestScore = PHP_INT_MIN;
        foreach (self::ENCODING_CANDIDATES as $encoding) {
            $decoded = $this->tryDecodeSample($sample, $encoding);
            if ($decoded === null) {
                continue;
            }

            $score = $this->scoreDecodedSample($decoded);
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestEncoding = $encoding;
            }
        }

        if ($bestEncoding === null) {
            throw new \RuntimeException('Cannot detect CSV encoding.');
        }

        return $bestEncoding === 'CP10007' ? 'MacCyrillic' : $bestEncoding;
    }

    private function decodeSample(string $sample, string $encoding): string
    {
        if ($encoding === 'UTF-8') {
            return $this->stripStringBom($sample);
        }

        $decoded = $this->tryDecodeSample($sample, $encoding);
        if ($decoded === null) {
            throw new \RuntimeException(sprintf('Cannot convert CSV sample from %s to UTF-8.', $encoding));
        }

        return $this->stripStringBom($decoded);
    }

    private function tryDecodeSample(string $sample, string $encoding): ?string
    {
        $decoded = @iconv($encoding, 'UTF-8//IGNORE', $sample);
        if ($decoded === false || !mb_check_encoding($decoded, 'UTF-8')) {
            return null;
        }

        return $decoded;
    }

    private function detectDelimiter(string $decodedSample): string
    {
        $lines = $this->sampleLines($decodedSample);
        if ($lines === []) {
            return self::TARGET_DELIMITER;
        }

        $firstLine = $this->stripStringBom(trim($lines[0]));
        if (preg_match('/^sep=(.)$/iu', $firstLine, $match) === 1 && in_array($match[1], self::DELIMITERS, true)) {
            return $match[1];
        }

        $bestDelimiter = self::TARGET_DELIMITER;
        $bestScore = PHP_INT_MIN;
        foreach (self::DELIMITERS as $delimiter) {
            $score = $this->scoreDelimiter($lines, $delimiter);
            if ($score > $bestScore) {
                $bestDelimiter = $delimiter;
                $bestScore = $score;
            }
        }

        return $bestDelimiter;
    }

    /**
     * @return list<string>
     */
    private function sampleLines(string $decodedSample): array
    {
        $rawLines = preg_split('/\R/u', $decodedSample);
        if ($rawLines === false) {
            return [];
        }

        $lines = [];
        foreach ($rawLines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $lines[] = $line;
            if (count($lines) >= 10) {
                break;
            }
        }

        return $lines;
    }

    /**
     * @param list<string> $lines
     */
    private function scoreDelimiter(array $lines, string $delimiter): int
    {
        $counts = [];
        $headerMatches = 0;
        foreach ($lines as $index => $line) {
            $row = str_getcsv($line, $delimiter, '"', '\\');
            $count = count($row);
            $counts[] = $count;

            if ($index === 0 || ($index === 1 && $this->isSeparatorHintRow($this->normalizeRow($row)))) {
                $headerMatches = max($headerMatches, $this->countKnownHeaders($row));
            }
        }

        if ($counts === []) {
            return -1000;
        }

        $maxColumns = max($counts);
        if ($maxColumns < 2) {
            return -1000;
        }

        $frequency = array_count_values($counts);
        $consistency = max($frequency);

        return ($maxColumns * 10) + ($consistency * 5) + ($headerMatches * 30);
    }

    private function scoreDecodedSample(string $decoded): int
    {
        $delimiterScore = $this->scoreDelimiter($this->sampleLines($decoded), $this->detectDelimiter($decoded));
        preg_match_all('/[А-Яа-яЁёІіЇїЄєҐґ]/u', $decoded, $cyrillicMatches);
        preg_match_all('/\x{FFFD}/u', $decoded, $replacementMatches);
        preg_match_all('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', $decoded, $controlMatches);

        return $delimiterScore
            + min(count($cyrillicMatches[0]), 200)
            - (count($replacementMatches[0]) * 20)
            - (count($controlMatches[0]) * 10);
    }

    /**
     * @param list<string|null> $headers
     */
    private function countKnownHeaders(array $headers): int
    {
        $matches = 0;
        foreach ($headers as $header) {
            if (!is_string($header)) {
                continue;
            }

            $normalized = mb_strtolower(trim($this->stripStringBom($header)));
            if (isset(self::KNOWN_HEADERS[$normalized])) {
                $matches++;
            }
        }

        return $matches;
    }

    /**
     * @return resource
     */
    private function openDecodedStream(string $source, string $encoding)
    {
        $stream = fopen($source, 'rb');
        if ($stream === false) {
            throw new \RuntimeException(sprintf('Cannot open import file: %s', $source));
        }

        if ($encoding !== 'UTF-8') {
            $filter = @stream_filter_append($stream, 'convert.iconv.' . $encoding . '/UTF-8', STREAM_FILTER_READ);
            if ($filter === false) {
                fclose($stream);
                throw new \RuntimeException(sprintf('Cannot create import conversion filter for %s.', $encoding));
            }
        }

        return $stream;
    }

    private function ensureDestinationDirectory(string $destination): void
    {
        $directory = dirname($destination);
        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Cannot create import destination directory: %s', $directory));
        }
    }

    /**
     * @param list<string|null> $row
     * @return list<string>
     */
    private function normalizeRow(array $row): array
    {
        $normalized = [];
        foreach ($row as $value) {
            $normalized[] = trim((string) $value);
        }

        return $normalized;
    }

    /**
     * @param list<string> $row
     * @return list<string>
     */
    private function stripFirstFieldBom(array $row): array
    {
        if (isset($row[0])) {
            $row[0] = $this->stripStringBom($row[0]);
        }

        return $row;
    }

    /**
     * @param list<string> $row
     */
    private function isSeparatorHintRow(array $row): bool
    {
        if (!isset($row[0]) || preg_match('/^sep=/iu', $this->stripStringBom(trim($row[0]))) !== 1) {
            return false;
        }

        return count($row) === 1 || (count($row) === 2 && $row[1] === '');
    }

    private function stripStringBom(string $value): string
    {
        if (str_starts_with($value, "\xEF\xBB\xBF")) {
            $value = substr($value, 3);
        }

        if (str_starts_with($value, "\u{FEFF}")) {
            return mb_substr($value, 1);
        }

        return $value;
    }
}
