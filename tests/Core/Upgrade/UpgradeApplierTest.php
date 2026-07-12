<?php

declare(strict_types=1);

namespace Core\Upgrade;

use Okay\Core\Upgrade\UpgradeApplier;
use PHPUnit\Framework\TestCase;
use ZipArchive;

final class UpgradeApplierTest extends TestCase
{
    private string $workspace;
    private string $projectRoot;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-upgrade-applier-' . bin2hex(random_bytes(8));
        $this->projectRoot = $this->workspace . '/project';
        $sourceRoot = dirname(__DIR__, 3);

        mkdir($this->projectRoot . '/scripts', 0777, true);
        copy($sourceRoot . '/scripts/clear-runtime-cache.php', $this->projectRoot . '/scripts/clear-runtime-cache.php');
        $this->linkProjectPath($sourceRoot . '/vendor', $this->projectRoot . '/vendor');
        $this->copyDirectory($sourceRoot . '/Okay/Core/Adapters', $this->projectRoot . '/Okay/Core/Adapters');
        $this->copyDirectory($sourceRoot . '/Okay/Core/config', $this->projectRoot . '/Okay/Core/config');
        $this->copyDirectory($sourceRoot . '/config', $this->projectRoot . '/config');
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testAppliesFilesDeletesManifestPathsClearsCacheAndWritesReport(): void
    {
        $this->writeProjectFile('index.php', 'old');
        $this->writeProjectFile('design/old.tpl', 'old');
        $this->writeProjectFile('cache/css/stale.css', 'stale');
        $package = $this->createPackage([
            'diff_4.5.2_4.6.0/index.php' => 'new',
            'diff_4.5.2_4.6.0/Okay/NewFile.php' => 'new file',
            'diff_4.5.2_4.6.0/deleted_paths.txt' => "design/old.tpl\n",
        ]);

        $result = (new UpgradeApplier())->apply($package, $this->projectRoot, $this->acknowledgedContext());

        self::assertSame('complete', $result['status'], $this->readReport($result));
        self::assertSame('new', file_get_contents($this->projectRoot . '/index.php'));
        self::assertFileExists($this->projectRoot . '/Okay/NewFile.php');
        self::assertFileDoesNotExist($this->projectRoot . '/design/old.tpl');
        self::assertFileDoesNotExist($this->projectRoot . '/cache/css/stale.css');
        self::assertFileExists($result['report']);
        self::assertFileExists($result['rollback_bundle']);

        $report = (string) file_get_contents($result['report']);
        self::assertStringContainsString('runtime_cache_clear', $report);
        self::assertStringContainsString('admin module management', $report);
    }

    public function testManualReviewFilesAreNotOverwritten(): void
    {
        $this->writeProjectFile('config/config.local.php', 'local');
        $package = $this->createPackage([
            'diff_4.5.2_4.6.0/config/config.local.php' => 'package',
        ]);

        $result = (new UpgradeApplier())->apply($package, $this->projectRoot, $this->acknowledgedContext());

        self::assertSame('blocked', $result['status']);
        self::assertSame('local', file_get_contents($this->projectRoot . '/config/config.local.php'));
    }

    public function testAppliesExtractedDiffRootDirectory(): void
    {
        $packageRoot = $this->workspace . '/diff_4.5.2_4.6.0';
        mkdir($packageRoot, 0777, true);
        file_put_contents($packageRoot . '/index.php', 'new');

        $result = (new UpgradeApplier())->apply($packageRoot, $this->projectRoot, $this->acknowledgedContext());

        self::assertSame('complete', $result['status'], $this->readReport($result));
        self::assertSame('new', file_get_contents($this->projectRoot . '/index.php'));
    }

    public function testApplyPreservesExecutablePackageFileMode(): void
    {
        $package = $this->createPackage([
            'diff_4.5.2_4.6.0/ok' => "#!/usr/bin/env php\n<?php\n",
        ], [
            'diff_4.5.2_4.6.0/ok',
        ]);

        $result = (new UpgradeApplier())->apply($package, $this->projectRoot, $this->acknowledgedContext());

        self::assertSame('complete', $result['status'], $this->readReport($result));
        self::assertTrue(is_executable($this->projectRoot . '/ok'));
    }

    public function testComposerRefreshRequirementStopsAfterFileOperations(): void
    {
        $this->writeProjectFile('composer.json', '{"name":"old"}');
        $package = $this->createPackage([
            'diff_4.5.2_4.6.0/composer.json' => '{"name":"new"}',
        ]);

        $result = (new UpgradeApplier())->apply($package, $this->projectRoot, $this->acknowledgedContext());

        self::assertSame('composer-refresh-required', $result['status']);
        self::assertSame('{"name":"new"}', file_get_contents($this->projectRoot . '/composer.json'));
        self::assertStringContainsString('fresh process', (string) file_get_contents($result['report']));
    }

    public function testFailedPreflightDoesNotMutateFiles(): void
    {
        $this->writeProjectFile('index.php', 'old');
        $package = $this->createPackage([
            'diff_4.5.2_4.6.0/index.php' => 'new',
        ]);

        $result = (new UpgradeApplier())->apply($package, $this->projectRoot, [
            'fromVersion' => '4.5.2',
            'toVersion' => '4.6.0',
        ]);

        self::assertSame('blocked', $result['status']);
        self::assertSame('old', file_get_contents($this->projectRoot . '/index.php'));
    }

    public function testFailedApplyReturnsCreatedRollbackBundlePath(): void
    {
        mkdir($this->projectRoot . '/blocked', 0777, true);
        $package = $this->createPackage([
            'diff_4.5.2_4.6.0/blocked' => 'file',
        ]);

        $result = (new UpgradeApplier())->apply($package, $this->projectRoot, $this->acknowledgedContext());

        self::assertSame('failed', $result['status']);
        self::assertIsString($result['rollback_bundle']);
        self::assertFileExists($result['rollback_bundle']);
    }

    /**
     * @return array<string, bool|string>
     */
    private function acknowledgedContext(): array
    {
        return [
            'fromVersion' => '4.5.2',
            'toVersion' => '4.6.0',
            'backupAcknowledged' => true,
            'maintenanceAcknowledged' => true,
            'schedulerPausedAcknowledged' => true,
            'runtimeParityAcknowledged' => true,
        ];
    }

    /**
     * @param array<string, string> $files
     * @param list<string> $executablePaths
     */
    private function createPackage(array $files, array $executablePaths = []): string
    {
        $packagePath = $this->workspace . '/upgrade.zip';
        $zip = new ZipArchive();
        self::assertTrue($zip->open($packagePath, ZipArchive::CREATE | ZipArchive::OVERWRITE));

        foreach ($files as $path => $content) {
            $zip->addFromString($path, $content);
            if (in_array($path, $executablePaths, true)) {
                $zip->setExternalAttributesName($path, ZipArchive::OPSYS_UNIX, 0100755 << 16);
            }
        }

        $zip->close();

        return $packagePath;
    }

    private function writeProjectFile(string $path, string $content): void
    {
        $fullPath = $this->projectRoot . '/' . $path;
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($fullPath, $content);
    }

    /**
     * @param array{report: string} $result
     */
    private function readReport(array $result): string
    {
        if (!is_file($result['report'])) {
            return '';
        }

        return (string) file_get_contents($result['report']);
    }

    private function linkProjectPath(string $source, string $target): void
    {
        if (is_link($target) || is_dir($target)) {
            if (!unlink($target) && !rmdir($target)) {
                $this->removeDirectory($target);
            }
        }

        if (!symlink($source, $target)) {
            self::fail('Unable to link project path: ' . $target);
        }
    }

    private function copyDirectory(string $source, string $target): void
    {
        if (!is_dir($source) || (!mkdir($target, 0777, true) && !is_dir($target))) {
            self::fail('Unable to prepare test directory: ' . $target);
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileInfo) {
            $targetPath = $target . '/' . $iterator->getSubPathName();
            if ($fileInfo->isDir()) {
                if (!is_dir($targetPath) && !mkdir($targetPath, 0777, true) && !is_dir($targetPath)) {
                    self::fail('Unable to create test directory: ' . $targetPath);
                }
                continue;
            }

            copy($fileInfo->getPathname(), $targetPath);
        }
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $items = scandir($directory);
        if ($items === false) {
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;
            if (is_link($path) || is_file($path)) {
                unlink($path);
                continue;
            }

            $this->removeDirectory($path);
        }

        rmdir($directory);
    }
}
