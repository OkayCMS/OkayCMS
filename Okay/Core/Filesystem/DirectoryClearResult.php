<?php

declare(strict_types=1);

namespace Okay\Core\Filesystem;

final class DirectoryClearResult
{
    private int $removedFiles = 0;

    private int $removedDirs = 0;

    /** @var list<string> */
    private array $errors = [];

    /** @var list<string> */
    private array $skippedMissing = [];

    public function merge(self $other): void
    {
        $this->removedFiles += $other->removedFiles;
        $this->removedDirs += $other->removedDirs;
        $this->errors = array_merge($this->errors, $other->errors);
        $this->skippedMissing = array_merge($this->skippedMissing, $other->skippedMissing);
    }

    public function hasErrors(): bool
    {
        return $this->errors !== [];
    }

    public function removedFiles(): int
    {
        return $this->removedFiles;
    }

    public function removedDirs(): int
    {
        return $this->removedDirs;
    }

    /**
     * @return list<string>
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @return list<string>
     */
    public function skippedMissing(): array
    {
        return $this->skippedMissing;
    }

    public function markMissingDirectory(string $directory): void
    {
        $this->skippedMissing[] = $directory;
    }

    public function markUnreadableDirectory(string $directory): void
    {
        $this->errors[] = "Unable to read directory: {$directory}";
    }

    public function markRemovedFile(): void
    {
        $this->removedFiles++;
    }

    public function markRemovedDirectory(): void
    {
        $this->removedDirs++;
    }

    public function markUnableToRemoveFile(string $path): void
    {
        $this->errors[] = "Unable to remove file: {$path}";
    }

    public function markUnableToRemoveDirectory(string $path): void
    {
        $this->errors[] = "Unable to remove directory: {$path}";
    }
}
