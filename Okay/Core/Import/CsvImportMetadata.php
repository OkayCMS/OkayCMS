<?php

declare(strict_types=1);

namespace Okay\Core\Import;

final class CsvImportMetadata
{
    /**
     * @param list<string> $warnings
     */
    public function __construct(
        private readonly string $encoding,
        private readonly string $sourceDelimiter,
        private readonly string $targetDelimiter,
        private readonly int $rowsWritten,
        private readonly array $warnings = []
    ) {
    }

    public function encoding(): string
    {
        return $this->encoding;
    }

    public function sourceDelimiter(): string
    {
        return $this->sourceDelimiter;
    }

    public function targetDelimiter(): string
    {
        return $this->targetDelimiter;
    }

    public function rowsWritten(): int
    {
        return $this->rowsWritten;
    }

    /**
     * @return list<string>
     */
    public function warnings(): array
    {
        return $this->warnings;
    }
}
