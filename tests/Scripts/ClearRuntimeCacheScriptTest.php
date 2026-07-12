<?php

declare(strict_types=1);

namespace Scripts;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ClearRuntimeCacheScriptTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-clear-runtime-cache-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testClearsGeneratedRuntimeArtifactsAndPreservesSentinels(): void
    {
        $this->writeFile('compiled/okay_shop/template.php');
        $this->writeFile('compiled/.keep_folder');
        $this->writeFile('backend/design/compiled/admin.php');
        $this->writeFile('backend/design/compiled/.htaccess');
        $this->writeFile('Okay/xml/compiled/feed.php');
        $this->writeFile('cache/css/okay_shop.head.hash.css');
        $this->writeFile('cache/js/okay_shop.footer.hash.js');
        $this->writeFile('cache/codes/example.license');
        $this->writeFile('cache/codes/.keep_folder');
        $this->writeFile('files/originals/product.jpg');

        $process = $this->runClearRuntimeCache();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertFileDoesNotExist($this->workspace . '/compiled/okay_shop/template.php');
        self::assertFileExists($this->workspace . '/compiled/.keep_folder');
        self::assertFileDoesNotExist($this->workspace . '/backend/design/compiled/admin.php');
        self::assertFileExists($this->workspace . '/backend/design/compiled/.htaccess');
        self::assertFileDoesNotExist($this->workspace . '/Okay/xml/compiled/feed.php');
        self::assertFileDoesNotExist($this->workspace . '/cache/css/okay_shop.head.hash.css');
        self::assertFileDoesNotExist($this->workspace . '/cache/js/okay_shop.footer.hash.js');
        self::assertFileExists($this->workspace . '/cache/codes/example.license');
        self::assertFileExists($this->workspace . '/cache/codes/.keep_folder');
        self::assertFileExists($this->workspace . '/files/originals/product.jpg');
    }

    public function testPreservesDirectoryWithKeepFolderSentinel(): void
    {
        $this->writeFile('cache/css/nested/stale.css');
        $this->writeFile('cache/css/.keep_folder');

        $process = $this->runClearRuntimeCache();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertFileDoesNotExist($this->workspace . '/cache/css/nested/stale.css');
        self::assertDirectoryDoesNotExist($this->workspace . '/cache/css/nested');
        self::assertDirectoryExists($this->workspace . '/cache/css');
        self::assertFileExists($this->workspace . '/cache/css/.keep_folder');
    }

    public function testMissingRuntimeDirectoriesAreNoOps(): void
    {
        $process = $this->runClearRuntimeCache();

        self::assertSame(0, $process->getExitCode(), $process->getErrorOutput());
        self::assertStringContainsString('Skipped missing compiled', $process->getOutput());
    }

    public function testReportsUnclearableGeneratedPath(): void
    {
        $this->writeFile('cache/css/stale.css');
        chmod($this->workspace . '/cache/css', 0555);

        try {
            $process = $this->runClearRuntimeCache();
        } finally {
            chmod($this->workspace . '/cache/css', 0777);
        }

        if ($process->getExitCode() === 0) {
            self::markTestSkipped('Filesystem permissions allow deletion from non-writable fixture directory.');
        }

        self::assertNotSame(0, $process->getExitCode());
        self::assertStringContainsString('cache/css/stale.css', $process->getErrorOutput());
    }

    private function runClearRuntimeCache(): Process
    {
        $process = new Process([
            PHP_BINARY,
            dirname(__DIR__, 2) . '/scripts/clear-runtime-cache.php',
            '--root=' . $this->workspace,
        ]);
        $process->run();

        return $process;
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
