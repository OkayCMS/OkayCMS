<?php

declare(strict_types=1);

namespace Core\Upgrade;

use Okay\Core\Upgrade\BackupCheckpoint;
use Okay\Core\Upgrade\UpgradePlan;
use Okay\Core\Upgrade\UpgradePreflight;
use PHPUnit\Framework\TestCase;
use ZipArchive;

final class UpgradePreflightTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        $this->workspace = sys_get_temp_dir() . '/okay-upgrade-preflight-' . bin2hex(random_bytes(8));
        mkdir($this->workspace, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);
    }

    public function testDryRunReportsMissingAcknowledgementsAsWarnings(): void
    {
        $result = (new UpgradePreflight())->evaluate($this->plan(overwritePaths: ['index.php']), [
            'dryRun' => true,
            'projectRoot' => $this->workspace,
        ]);

        self::assertTrue($result->isAllowed());
        self::assertSame([], $result->getBlockers());
        self::assertContains('Full database and filesystem backups must be confirmed.', $result->getWarnings());
        self::assertContains(
            'Touched-file rollback bundle must be created and verified before file mutation.',
            $result->getWarnings()
        );
    }

    public function testMutatingPreflightAllowsAcknowledgedMatchingPackage(): void
    {
        $result = (new UpgradePreflight())->evaluate($this->plan(), [
            'fromVersion' => '4.5.2',
            'toVersion' => '4.6.0',
            'backupAcknowledged' => true,
            'maintenanceAcknowledged' => true,
            'schedulerPausedAcknowledged' => true,
            'runtimeParityAcknowledged' => true,
            'rollbackBundleVerified' => true,
            'projectRoot' => $this->workspace,
        ]);

        self::assertTrue($result->isAllowed());
        self::assertSame([], $result->getBlockers());
    }

    public function testMismatchedVersionsBlockApply(): void
    {
        $result = (new UpgradePreflight())->evaluate($this->plan(), [
            'fromVersion' => '4.6.0',
            'toVersion' => '4.6.1',
            'backupAcknowledged' => true,
            'maintenanceAcknowledged' => true,
            'schedulerPausedAcknowledged' => true,
            'runtimeParityAcknowledged' => true,
            'rollbackBundleVerified' => true,
        ]);

        self::assertFalse($result->isAllowed());
        self::assertStringContainsString('Package source version 4.5.2', implode("\n", $result->getBlockers()));
        self::assertStringContainsString('Package target version 4.6.0', implode("\n", $result->getBlockers()));
    }

    public function testManualReviewPathsBlockApplyUntilApproved(): void
    {
        $result = (new UpgradePreflight())->evaluate($this->plan(manualReviewPaths: ['config/config.local.php']), [
            'backupAcknowledged' => true,
            'maintenanceAcknowledged' => true,
            'schedulerPausedAcknowledged' => true,
            'runtimeParityAcknowledged' => true,
            'rollbackBundleVerified' => true,
        ]);

        self::assertFalse($result->isAllowed());
        self::assertContains('Manual review paths must be resolved before automatic apply.', $result->getBlockers());
    }

    public function testInterruptedReportBlocksApplyUntilAcknowledged(): void
    {
        mkdir($this->workspace . '/var/upgrade-reports', 0777, true);
        file_put_contents($this->workspace . '/var/upgrade-reports/upgrade-active.json', '{}');

        $result = (new UpgradePreflight())->evaluate($this->plan(), [
            'backupAcknowledged' => true,
            'maintenanceAcknowledged' => true,
            'schedulerPausedAcknowledged' => true,
            'runtimeParityAcknowledged' => true,
            'rollbackBundleVerified' => true,
            'projectRoot' => $this->workspace,
        ]);

        self::assertFalse($result->isAllowed());
        self::assertContains(
            'Interrupted previous upgrade report must be resolved or acknowledged.',
            $result->getBlockers()
        );
    }

    public function testCreatesAndVerifiesTouchedFileRollbackBundle(): void
    {
        file_put_contents($this->workspace . '/index.php', 'current');
        $bundlePath = (new BackupCheckpoint())->createTouchedFileBundle(
            $this->plan(overwritePaths: ['index.php'], deletePaths: ['missing.php']),
            $this->workspace
        );

        self::assertFileExists($bundlePath);
        self::assertTrue((new BackupCheckpoint())->verifyBundle($bundlePath));

        $zip = new ZipArchive();
        self::assertTrue($zip->open($bundlePath));
        self::assertNotFalse($zip->locateName('metadata.json'));
        self::assertNotFalse($zip->locateName('files/index.php'));
        self::assertFalse($zip->locateName('files/missing.php'));
        $zip->close();
    }

    /**
     * @param list<string> $overwritePaths
     * @param list<string> $deletePaths
     * @param list<string> $manualReviewPaths
     */
    private function plan(
        array $overwritePaths = [],
        array $deletePaths = [],
        array $manualReviewPaths = []
    ): UpgradePlan {
        return new UpgradePlan(
            'diff_4.5.2_4.6.0',
            '4.5.2',
            '4.6.0',
            [],
            $overwritePaths,
            $deletePaths,
            $manualReviewPaths,
            []
        );
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
