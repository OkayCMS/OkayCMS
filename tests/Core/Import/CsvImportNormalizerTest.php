<?php

declare(strict_types=1);

namespace Core\Import;

use Okay\Core\Import\CsvImportNormalizer;
use PHPUnit\Framework\TestCase;

final class CsvImportNormalizerTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-csv-import-normalizer-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->workspace . '/*') ?: [] as $file) {
            unlink($file);
        }

        rmdir($this->workspace);
    }

    public function testStripsUtf8BomAndWritesInternalCsvWithoutBom(): void
    {
        $source = $this->writeSource("\xEF\xBB\xBFProduct;Price\nТелефон;10,50\n");
        $destination = $this->workspace . '/import.csv';

        $metadata = (new CsvImportNormalizer())->normalize($source, $destination);

        self::assertSame('UTF-8', $metadata->encoding());
        self::assertSame(';', $metadata->sourceDelimiter());
        self::assertStringStartsNotWith("\xEF\xBB\xBF", (string) file_get_contents($destination));
        self::assertSame([
            ['Product', 'Price'],
            ['Телефон', '10,50'],
        ], $this->readNormalized($destination));
    }

    public function testConvertsWindows1251AndNormalizesCommaDelimiter(): void
    {
        $source = $this->writeSource(iconv(
            'UTF-8',
            'Windows-1251',
            "Product,Price\nКолонка,11.20\n"
        ));
        $destination = $this->workspace . '/import.csv';

        $metadata = (new CsvImportNormalizer())->normalize($source, $destination);

        self::assertSame('Windows-1251', $metadata->encoding());
        self::assertSame(',', $metadata->sourceDelimiter());
        self::assertSame([
            ['Product', 'Price'],
            ['Колонка', '11.20'],
        ], $this->readNormalized($destination));
    }

    public function testConvertsMacCyrillicAndSkipsExcelSeparatorHint(): void
    {
        $source = $this->writeSource(iconv(
            'UTF-8',
            'MacCyrillic',
            "sep=|\nProduct|Price\nКрісло|15,00\n"
        ));
        $destination = $this->workspace . '/import.csv';

        $metadata = (new CsvImportNormalizer())->normalize($source, $destination);

        self::assertSame('MacCyrillic', $metadata->encoding());
        self::assertSame('|', $metadata->sourceDelimiter());
        self::assertSame([
            ['Product', 'Price'],
            ['Крісло', '15,00'],
        ], $this->readNormalized($destination));
    }

    public function testConvertsUtf16LeAndNormalizesTabDelimiter(): void
    {
        $source = $this->writeSource("\xFF\xFE" . iconv(
            'UTF-8',
            'UTF-16LE',
            "Product\tPrice\nСтіл\t20.30\n"
        ));
        $destination = $this->workspace . '/import.csv';

        $metadata = (new CsvImportNormalizer())->normalize($source, $destination);

        self::assertSame('UTF-16LE', $metadata->encoding());
        self::assertSame("\t", $metadata->sourceDelimiter());
        self::assertSame([
            ['Product', 'Price'],
            ['Стіл', '20.30'],
        ], $this->readNormalized($destination));
    }

    public function testTrimsTextValuesWithoutCollapsingInternalSpaces(): void
    {
        $source = $this->writeSource("Product;Annotation\n  Назва  ;\"   дані з  файла  імпорту \"\n");
        $destination = $this->workspace . '/import.csv';

        (new CsvImportNormalizer())->normalize($source, $destination);

        self::assertSame([
            ['Product', 'Annotation'],
            ['Назва', 'дані з  файла  імпорту'],
        ], $this->readNormalized($destination));
    }

    private function writeSource(string $content): string
    {
        $path = $this->workspace . '/source.csv';
        file_put_contents($path, $content);

        return $path;
    }

    /**
     * @return list<list<string>>
     */
    private function readNormalized(string $path): array
    {
        $rows = [];
        $stream = fopen($path, 'rb');
        self::assertIsResource($stream);

        while (($row = fgetcsv($stream, 0, ';', '"', '\\')) !== false) {
            $rows[] = array_map('strval', $row);
        }

        fclose($stream);

        return $rows;
    }
}
