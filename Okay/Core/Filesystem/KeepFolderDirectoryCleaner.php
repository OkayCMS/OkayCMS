<?php

declare(strict_types=1);

namespace Okay\Core\Filesystem;

final class KeepFolderDirectoryCleaner
{
    public const KEEP_FOLDER_SENTINEL = '.keep_folder';

    public function clearDirectory(string $directory): DirectoryClearResult
    {
        $result = new DirectoryClearResult();
        $directory = rtrim(str_replace('\\', '/', $directory), '/');

        if (!is_dir($directory)) {
            $result->markMissingDirectory($directory);

            return $result;
        }

        $this->clearDirectoryContents($directory, $result);

        return $result;
    }

    private function clearDirectoryContents(string $directory, DirectoryClearResult $result): void
    {
        $items = scandir($directory);
        if ($items === false) {
            $result->markUnreadableDirectory($directory);

            return;
        }

        foreach ($items as $item) {
            if ($this->shouldPreserveEntry($item)) {
                continue;
            }

            $path = $directory . '/' . $item;
            if (is_dir($path) && !is_link($path)) {
                $this->clearDirectoryContents($path, $result);
                if ($this->canRemoveEmptyDirectory($path)) {
                    $this->removeEmptyDirectory($path, $result);
                }

                continue;
            }

            $this->removeFile($path, $result);
        }
    }

    private function shouldPreserveEntry(string $basename): bool
    {
        return $basename === '.' || $basename === '..' || str_starts_with($basename, '.');
    }

    private function canRemoveEmptyDirectory(string $directory): bool
    {
        return !$this->directoryHasKeepFolder($directory) && $this->containsOnlyPreservedEntries($directory);
    }

    private function directoryHasKeepFolder(string $directory): bool
    {
        return is_file($directory . '/' . self::KEEP_FOLDER_SENTINEL);
    }

    private function containsOnlyPreservedEntries(string $directory): bool
    {
        $items = scandir($directory);
        if ($items === false) {
            return false;
        }

        foreach ($items as $item) {
            if (!$this->shouldPreserveEntry($item)) {
                return false;
            }
        }

        return true;
    }

    private function removeEmptyDirectory(string $path, DirectoryClearResult $result): void
    {
        if (@rmdir($path)) {
            $result->markRemovedDirectory();

            return;
        }

        $result->markUnableToRemoveDirectory($path);
    }

    private function removeFile(string $path, DirectoryClearResult $result): void
    {
        if (@unlink($path)) {
            $result->markRemovedFile();

            return;
        }

        $result->markUnableToRemoveFile($path);
    }
}
