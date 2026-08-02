<?php

declare(strict_types=1);

namespace Core\Upgrade;

use Okay\Core\Upgrade\UpgradePackageInspector;
use PHPUnit\Framework\TestCase;
use ZipArchive;

final class UpgradePackageInspectorTest extends TestCase
{
    private string $workspace;
    private string $projectRoot;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-upgrade-inspect-' . bin2hex(random_bytes(8));
        $this->projectRoot = $this->workspace . '/project';
        mkdir($this->projectRoot, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testInspectsChangedAndDeletedPathsInSortedOrder(): void
    {
        $this->writeProjectFile('index.php');

        $packagePath = $this->createPackage([
            'diff_4.5.2_4.6.0/index.php' => 'changed',
            'diff_4.5.2_4.6.0/Okay/NewFile.php' => 'new',
            'diff_4.5.2_4.6.0/deleted_paths.txt' => "design/old.tpl\nOkay/OldFile.php\n",
        ]);

        $plan = (new UpgradePackageInspector())->inspect($packagePath, $this->projectRoot);

        self::assertSame('diff_4.5.2_4.6.0', $plan->getPackageRoot());
        self::assertSame('4.5.2', $plan->getFromVersion());
        self::assertSame('4.6.0', $plan->getToVersion());
        self::assertSame(['Okay/NewFile.php'], $plan->getCopyPaths());
        self::assertSame(['index.php'], $plan->getOverwritePaths());
        self::assertSame(['Okay/OldFile.php', 'design/old.tpl'], $plan->getDeletePaths());
        self::assertSame(['deleted_paths.txt'], $plan->getSkipPaths());
        self::assertSame(
            ['Okay/OldFile.php', 'design/old.tpl', 'index.php'],
            $plan->getTouchedFileBackupPaths()
        );
    }

    public function testPackageWithoutDeletionManifestStillProducesValidPlan(): void
    {
        $packagePath = $this->createPackage([
            'diff_4.5.2_4.6.0/Okay/NewFile.php' => 'new',
        ]);

        $plan = (new UpgradePackageInspector())->inspect($packagePath, $this->projectRoot);

        self::assertSame(['Okay/NewFile.php'], $plan->getCopyPaths());
        self::assertSame([], $plan->getDeletePaths());
    }

    public function testInspectsExtractedDiffRootDirectory(): void
    {
        $packageRoot = $this->workspace . '/diff_4.5.2_4.6.0';
        mkdir($packageRoot . '/Okay', 0777, true);
        file_put_contents($packageRoot . '/Okay/NewFile.php', 'new');

        $plan = (new UpgradePackageInspector())->inspect($packageRoot, $this->projectRoot);

        self::assertSame('diff_4.5.2_4.6.0', $plan->getPackageRoot());
        self::assertSame(['Okay/NewFile.php'], $plan->getCopyPaths());
    }

    public function testRejectsPackageWithoutSingleExpectedDiffRoot(): void
    {
        $packagePath = $this->createPackage([
            'diff_4.5.2_4.6.0/index.php' => 'changed',
            'diff_4.6.0_4.6.1/index.php' => 'changed',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('exactly one diff_<from>_<to> root');

        (new UpgradePackageInspector())->inspect($packagePath, $this->projectRoot);
    }

    public function testRejectsPathTraversal(): void
    {
        $packagePath = $this->createPackage([
            'diff_4.5.2_4.6.0/../evil.php' => 'evil',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('path traversal');

        (new UpgradePackageInspector())->inspect($packagePath, $this->projectRoot);
    }

    public function testProtectedPathsRequireManualReview(): void
    {
        $packagePath = $this->createPackage([
            'diff_4.5.2_4.6.0/config/config.local.php' => 'local',
            'diff_4.5.2_4.6.0/files/originals/product.jpg' => 'image',
            'diff_4.5.2_4.6.0/deleted_paths.txt' => "config/config.php\nfiles/originals/old.jpg\n",
        ]);

        $plan = (new UpgradePackageInspector())->inspect($packagePath, $this->projectRoot);

        self::assertSame([], $plan->getCopyPaths());
        self::assertSame([], $plan->getDeletePaths());
        self::assertSame(
            [
                'config/config.local.php',
                'config/config.php',
                'files/originals/old.jpg',
                'files/originals/product.jpg',
            ],
            $plan->getManualReviewPaths()
        );
    }

    public function testDryRunOutputIncludesRollbackBundleAndCacheClearStep(): void
    {
        $packagePath = $this->createPackage([
            'diff_4.5.2_4.6.0/Okay/NewFile.php' => 'new',
        ]);

        $plan = (new UpgradePackageInspector())->inspect($packagePath, $this->projectRoot);
        $output = implode(PHP_EOL, $plan->toConsoleLines());

        self::assertStringContainsString('Touched-file rollback bundle:', $output);
        self::assertStringContainsString('Post-apply action: run composer cache:clear', $output);
    }

    /**
     * @param array<string, string> $files
     */
    private function createPackage(array $files): string
    {
        $packagePath = $this->workspace . '/upgrade.zip';
        $zip = new ZipArchive();
        self::assertTrue($zip->open($packagePath, ZipArchive::CREATE | ZipArchive::OVERWRITE));

        foreach ($files as $path => $content) {
            $zip->addFromString($path, $content);
        }

        $zip->close();

        return $packagePath;
    }

    private function writeProjectFile(string $path): void
    {
        $fullPath = $this->projectRoot . '/' . $path;
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($fullPath, 'local');
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
                rmdir($fileInfo->getPathname());
                continue;
            }

            unlink($fileInfo->getPathname());
        }

        rmdir($directory);
    }
}
