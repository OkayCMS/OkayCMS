<?php

declare(strict_types=1);

namespace Core\Export;

use Okay\Core\Export\CsvExportWriter;
use PHPUnit\Framework\TestCase;

final class CsvExportWriterTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-csv-export-writer-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->workspace . '/*') ?: [] as $file) {
            unlink($file);
        }

        rmdir($this->workspace);
    }

    public function testDefaultCsvIsUtf8WithoutBom(): void
    {
        $writer = new CsvExportWriter();
        $stream = $writer->openAppendStream($this->workspace, 'export.csv', CsvExportWriter::FORMAT_UTF8);

        fputcsv($stream, ['Product', 'Назва'], ';', '"', '\\');
        fclose($stream);

        $content = (string) file_get_contents($this->workspace . '/export.csv');
        self::assertStringStartsNotWith("\xEF\xBB\xBF", $content);
        self::assertTrue(mb_check_encoding($content, 'UTF-8'));
        self::assertStringContainsString('Назва', $content);
    }

    public function testExcelCsvWritesBomOnlyOnceAcrossAppends(): void
    {
        $writer = new CsvExportWriter();

        $first = $writer->openAppendStream($this->workspace, 'export.csv', CsvExportWriter::FORMAT_UTF8_BOM);
        fputcsv($first, ['Product'], ';', '"', '\\');
        fclose($first);

        $second = $writer->openAppendStream($this->workspace, 'export.csv', CsvExportWriter::FORMAT_UTF8_BOM);
        fputcsv($second, ['Назва'], ';', '"', '\\');
        fclose($second);

        $content = (string) file_get_contents($this->workspace . '/export.csv');
        self::assertStringStartsWith("\xEF\xBB\xBF", $content);
        self::assertSame(1, substr_count($content, "\xEF\xBB\xBF"));
        self::assertStringContainsString('Product', $content);
        self::assertStringContainsString('Назва', $content);
    }

    public function testUnknownFormatFallsBackToUtf8WithoutBom(): void
    {
        $writer = new CsvExportWriter();
        $stream = $writer->openAppendStream($this->workspace, 'export.csv', 'unknown');

        fputcsv($stream, ['Product'], ';', '"', '\\');
        fclose($stream);

        self::assertStringStartsNotWith("\xEF\xBB\xBF", (string) file_get_contents($this->workspace . '/export.csv'));
    }

    public function testExcelCsvEscapesFormulaLikeTextCells(): void
    {
        $writer = new CsvExportWriter();
        $stream = $writer->openAppendStream($this->workspace, 'export.csv', CsvExportWriter::FORMAT_UTF8_BOM);

        $writer->writeRow($stream, ['=SUM(A1:A2)', '+value', '-value', '@value', 'safe'], ';', CsvExportWriter::FORMAT_UTF8_BOM);
        fclose($stream);

        $content = (string) file_get_contents($this->workspace . '/export.csv');
        self::assertStringContainsString("'=SUM(A1:A2)", $content);
        self::assertStringContainsString("'+value", $content);
        self::assertStringContainsString("'-value", $content);
        self::assertStringContainsString("'@value", $content);
        self::assertStringContainsString(';safe', $content);
    }

    public function testDefaultCsvDoesNotEscapeFormulaLikeTextCells(): void
    {
        $writer = new CsvExportWriter();
        $stream = $writer->openAppendStream($this->workspace, 'export.csv', CsvExportWriter::FORMAT_UTF8);

        $writer->writeRow($stream, ['=SUM(A1:A2)', 'safe'], ';', CsvExportWriter::FORMAT_UTF8);
        fclose($stream);

        $content = (string) file_get_contents($this->workspace . '/export.csv');
        self::assertStringContainsString('=SUM(A1:A2);safe', $content);
        self::assertStringNotContainsString("'=SUM(A1:A2)", $content);
    }
}
