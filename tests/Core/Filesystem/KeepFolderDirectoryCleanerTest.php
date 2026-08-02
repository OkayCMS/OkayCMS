<?php

declare(strict_types=1);

namespace Core\Filesystem;

use Okay\Core\Filesystem\KeepFolderDirectoryCleaner;
use PHPUnit\Framework\TestCase;

final class KeepFolderDirectoryCleanerTest extends TestCase
{
    private string $workspace;

    private KeepFolderDirectoryCleaner $cleaner;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-keep-folder-cleaner-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
        $this->cleaner = new KeepFolderDirectoryCleaner();
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testRemovesGeneratedFilesAndPreservesDotfiles(): void
    {
        $this->writeFile('root/stale.php');
        $this->writeFile('root/.keep_folder');
        $this->writeFile('root/.htaccess');

        $result = $this->cleaner->clearDirectory($this->workspace . '/root');

        self::assertFalse($result->hasErrors());
        self::assertSame(1, $result->removedFiles());
        self::assertFileDoesNotExist($this->workspace . '/root/stale.php');
        self::assertFileExists($this->workspace . '/root/.keep_folder');
        self::assertFileExists($this->workspace . '/root/.htaccess');
    }

    public function testPreservesDirectoryThatContainsKeepFolderSentinel(): void
    {
        $this->writeFile('parent/child/stale.css');
        $this->writeFile('parent/.keep_folder');

        $result = $this->cleaner->clearDirectory($this->workspace . '/parent');

        self::assertFalse($result->hasErrors());
        self::assertFileDoesNotExist($this->workspace . '/parent/child/stale.css');
        self::assertDirectoryDoesNotExist($this->workspace . '/parent/child');
        self::assertDirectoryExists($this->workspace . '/parent');
        self::assertFileExists($this->workspace . '/parent/.keep_folder');
    }

    public function testRemovesNestedDirectoryWithoutKeepFolder(): void
    {
        $this->writeFile('compiled/okay_shop/template.php');

        $result = $this->cleaner->clearDirectory($this->workspace . '/compiled');

        self::assertFalse($result->hasErrors());
        self::assertGreaterThanOrEqual(1, $result->removedDirs());
        self::assertFileDoesNotExist($this->workspace . '/compiled/okay_shop/template.php');
        self::assertDirectoryDoesNotExist($this->workspace . '/compiled/okay_shop');
    }

    public function testMissingDirectoryIsRecordedAsSkipped(): void
    {
        $result = $this->cleaner->clearDirectory($this->workspace . '/missing');

        self::assertFalse($result->hasErrors());
        self::assertSame([$this->workspace . '/missing'], $result->skippedMissing());
    }

    private function writeFile(string $path): void
    {
        $fullPath = $this->workspace . '/' . $path;
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($fullPath, 'fixture');
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                chmod($fileInfo->getPathname(), 0777);
                rmdir($fileInfo->getPathname());
                continue;
            }

            chmod($fileInfo->getPathname(), 0666);
            unlink($fileInfo->getPathname());
        }

        chmod($directory, 0777);
        rmdir($directory);
    }
}
