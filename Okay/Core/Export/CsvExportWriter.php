<?php

declare(strict_types=1);

namespace Okay\Core\Export;

final class CsvExportWriter
{
    public const FORMAT_UTF8 = 'csv_utf8';
    public const FORMAT_UTF8_BOM = 'csv_utf8_bom';

    /**
     * @return resource
     */
    public function openAppendStream(string $directory, string $filename, ?string $format = self::FORMAT_UTF8)
    {
        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0775, true) && !is_dir($directory)) {
                throw new \RuntimeException(sprintf('Export directory missing and could not be created: %s', $directory));
            }
        }

        $path = rtrim($directory, '/') . '/' . $filename;
        $shouldWriteBom = $this->isUtf8BomFormat($format) && (!is_file($path) || filesize($path) === 0);
        $stream = @fopen($path, 'ab');
        if ($stream === false) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot write export file %s. Ensure the web server user can read, write, and execute (traverse) %s '
                    . '(e.g. chmod 775 on the directory and chown to the PHP-FPM user; remove root-owned stale CSV if present).',
                    $path,
                    $directory
                )
            );
        }

        if ($shouldWriteBom) {
            fwrite($stream, "\xEF\xBB\xBF");
        }

        return $stream;
    }

    public function normalizeFormat(?string $format): string
    {
        return $this->isUtf8BomFormat($format) ? self::FORMAT_UTF8_BOM : self::FORMAT_UTF8;
    }

    /**
     * @param resource $stream
     * @param array<int|string, mixed> $row
     */
    public function writeRow($stream, array $row, string $delimiter, ?string $format = self::FORMAT_UTF8): void
    {
        fputcsv($stream, $this->prepareRow($row, $format), $delimiter, '"', '\\');
    }

    public function isUtf8BomFormat(?string $format): bool
    {
        return $format === self::FORMAT_UTF8_BOM;
    }

    /**
     * @param array<int|string, mixed> $row
     * @return array<int|string, mixed>
     */
    private function prepareRow(array $row, ?string $format): array
    {
        if (!$this->isUtf8BomFormat($format)) {
            return $row;
        }

        foreach ($row as $key => $value) {
            if (!is_string($value) || $value === '') {
                continue;
            }

            if (preg_match('/^[=+\-@\t\r]/', $value) === 1) {
                $row[$key] = "'" . $value;
            }
        }

        return $row;
    }
}
