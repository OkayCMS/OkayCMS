<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

use RuntimeException;
use ZipArchive;

final class UpgradeApplier
{
    /** @var array<string, int> */
    private array $packageFileModes = [];

    public function __construct(
        private readonly UpgradePackageInspector $inspector = new UpgradePackageInspector(),
        private readonly UpgradePreflight $preflight = new UpgradePreflight(),
        private readonly BackupCheckpoint $backupCheckpoint = new BackupCheckpoint()
    ) {
    }

    /**
     * @param array{
     *     fromVersion?: string,
     *     toVersion?: string,
     *     backupAcknowledged?: bool,
     *     maintenanceAcknowledged?: bool,
     *     schedulerPausedAcknowledged?: bool,
     *     runtimeParityAcknowledged?: bool,
     *     manualReviewApproved?: bool,
     *     interruptedReportAcknowledged?: bool
     * } $context
     * @return array{status: string, report: string, rollback_bundle: string|null}
     */
    public function apply(string $packagePath, string $projectRoot, array $context): array
    {
        $projectRoot = $this->normalizeProjectRoot($projectRoot);
        $this->failIfInterruptedReportExists($projectRoot, (bool) ($context['interruptedReportAcknowledged'] ?? false));

        $plan = $this->inspector->inspect($packagePath, $projectRoot);
        $report = new UpgradeReport($projectRoot, $plan);
        $report->start();
        $rollbackBundle = null;

        try {
            $rollbackBundle = $this->backupCheckpoint->createTouchedFileBundle($plan, $projectRoot);
            $report->addPhase('rollback_bundle', 'complete', ['path' => $rollbackBundle]);

            $preflight = $this->preflight->evaluate($plan, $context + [
                'rollbackBundleVerified' => true,
                'interruptedReportAcknowledged' => true,
                'projectRoot' => $projectRoot,
            ]);
            if (!$preflight->isAllowed()) {
                $report->addPhase('preflight', 'blocked', ['blockers' => $preflight->getBlockers()]);
                return [
                    'status' => 'blocked',
                    'report' => $report->finalize('blocked'),
                    'rollback_bundle' => $rollbackBundle,
                ];
            }
            $report->addPhase('preflight', 'complete');

            $packageRootPath = $this->preparePackageRoot($packagePath, $plan);
            try {
                $this->applyFileOperations($plan, $packageRootPath, $projectRoot);
            } finally {
                $this->removeTemporaryPackageRoot($packageRootPath, $packagePath);
            }
            $report->addPhase('file_operations', 'complete', [
                'copy' => $plan->getCopyPaths(),
                'overwrite' => $plan->getOverwritePaths(),
                'delete' => $plan->getDeletePaths(),
                'manual_review' => $plan->getManualReviewPaths(),
            ]);

            if ($this->requiresComposerRefresh($plan)) {
                $report->addPending('Run composer install/dump-autoload in a fresh process, then resume database/module steps.');
                return [
                    'status' => 'composer-refresh-required',
                    'report' => $report->finalize('incomplete'),
                    'rollback_bundle' => $rollbackBundle,
                ];
            }

            $report->addPending(
                "Run php ok database:upgrade --from={$plan->getFromVersion()} --to={$plan->getToVersion()} after file apply."
            );
            $this->runCacheClear($projectRoot);
            $report->addPhase('runtime_cache_clear', 'complete');
            $report->addPending('Open admin module management and apply any visible bundled module update buttons.');
            $report->addPending('Reload OPcache/FPM according to hosting policy, then run admin/front smoke checks.');

            return [
                'status' => 'complete',
                'report' => $report->finalize('complete'),
                'rollback_bundle' => $rollbackBundle,
            ];
        } catch (\Throwable $exception) {
            $report->addPhase('apply', 'failed', ['error' => $exception->getMessage()]);

            return [
                'status' => 'failed',
                'report' => $report->finalize('failed'),
                'rollback_bundle' => $rollbackBundle,
            ];
        }
    }

    private function normalizeProjectRoot(string $projectRoot): string
    {
        $realRoot = realpath($projectRoot);
        if ($realRoot === false || !is_dir($realRoot)) {
            throw new RuntimeException("Project root not found: {$projectRoot}");
        }

        return rtrim($realRoot, '/');
    }

    private function failIfInterruptedReportExists(string $projectRoot, bool $acknowledged): void
    {
        $activeReport = $projectRoot . '/var/upgrade-reports/upgrade-active.json';
        if (!$acknowledged && is_file($activeReport)) {
            throw new RuntimeException('Interrupted previous upgrade report must be resolved or acknowledged.');
        }
    }

    private function preparePackageRoot(string $packagePath, UpgradePlan $plan): string
    {
        $this->packageFileModes = [];

        if (is_dir($packagePath)) {
            $realPackagePath = rtrim(realpath($packagePath) ?: $packagePath, '/');
            if (basename($realPackagePath) === $plan->getPackageRoot()) {
                return $realPackagePath;
            }

            return $realPackagePath . '/' . $plan->getPackageRoot();
        }

        $tempDir = sys_get_temp_dir() . '/okay-upgrade-apply-' . bin2hex(random_bytes(6));
        if (!mkdir($tempDir, 0775, true) && !is_dir($tempDir)) {
            throw new RuntimeException("Unable to create temporary package directory: {$tempDir}");
        }

        $zip = new ZipArchive();
        if ($zip->open($packagePath) !== true) {
            throw new RuntimeException("Unable to open upgrade archive: {$packagePath}");
        }

        $this->packageFileModes = $this->collectZipFileModes($zip, $plan);
        $zip->extractTo($tempDir);
        $zip->close();

        return $tempDir . '/' . $plan->getPackageRoot();
    }

    /**
     * @return array<string, int>
     */
    private function collectZipFileModes(ZipArchive $zip, UpgradePlan $plan): array
    {
        $modes = [];
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            if ($name === false || str_ends_with($name, '/')) {
                continue;
            }

            $opsys = 0;
            $attributes = 0;
            if (!$zip->getExternalAttributesIndex($index, $opsys, $attributes) || $opsys !== ZipArchive::OPSYS_UNIX) {
                continue;
            }

            $relativePath = $this->stripPackageRoot($name, $plan);
            if ($relativePath === null) {
                continue;
            }

            $mode = ($attributes >> 16) & 0777;
            if ($mode > 0) {
                $modes[$relativePath] = $mode;
            }
        }

        return $modes;
    }

    private function stripPackageRoot(string $zipPath, UpgradePlan $plan): ?string
    {
        $zipPath = ltrim(str_replace('\\', '/', $zipPath), '/');
        $prefix = $plan->getPackageRoot() . '/';
        if (!str_starts_with($zipPath, $prefix)) {
            return null;
        }

        return substr($zipPath, strlen($prefix));
    }

    private function removeTemporaryPackageRoot(string $packageRootPath, string $packagePath): void
    {
        if (is_dir($packagePath)) {
            return;
        }

        $tempDir = dirname($packageRootPath);
        if (!is_dir($tempDir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDir()) {
                rmdir($fileInfo->getPathname());
                continue;
            }

            unlink($fileInfo->getPathname());
        }

        rmdir($tempDir);
    }

    private function applyFileOperations(UpgradePlan $plan, string $packageRootPath, string $projectRoot): void
    {
        foreach (array_merge($plan->getCopyPaths(), $plan->getOverwritePaths()) as $path) {
            $source = $packageRootPath . '/' . $path;
            $target = $projectRoot . '/' . $path;
            $targetDirectory = dirname($target);

            if (!is_file($source)) {
                throw new RuntimeException("Package file missing during apply: {$path}");
            }

            if (!is_dir($targetDirectory) && !mkdir($targetDirectory, 0775, true) && !is_dir($targetDirectory)) {
                throw new RuntimeException("Unable to create target directory: {$targetDirectory}");
            }

            if (is_dir($target) && !is_link($target)) {
                throw new RuntimeException("Unable to overwrite directory with package file: {$path}");
            }

            if (!copy($source, $target)) {
                throw new RuntimeException("Unable to copy package file: {$path}");
            }

            $this->preserveFileMode($source, $target, $path);
        }

        foreach ($plan->getDeletePaths() as $path) {
            $target = $projectRoot . '/' . $path;
            if (!file_exists($target)) {
                continue;
            }

            if (is_dir($target) && !is_link($target)) {
                $this->removeDirectory($target);
                continue;
            }

            if (!unlink($target)) {
                throw new RuntimeException("Unable to delete path: {$path}");
            }
        }
    }

    private function removeDirectory(string $directory): void
    {
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

    private function preserveFileMode(string $source, string $target, string $relativePath): void
    {
        $mode = $this->packageFileModes[$relativePath] ?? null;
        if ($mode === null) {
            $sourceMode = fileperms($source);
            $mode = $sourceMode === false ? null : $sourceMode & 0777;
        }

        if ($mode !== null && !chmod($target, $mode)) {
            throw new RuntimeException("Unable to preserve package file mode: {$relativePath}");
        }
    }

    private function requiresComposerRefresh(UpgradePlan $plan): bool
    {
        return array_intersect(['composer.json', 'composer.lock'], array_merge(
            $plan->getCopyPaths(),
            $plan->getOverwritePaths()
        )) !== [];
    }

    private function runCacheClear(string $projectRoot): void
    {
        $command = [
            PHP_BINARY,
            $projectRoot . '/scripts/clear-runtime-cache.php',
            '--root=' . $projectRoot,
        ];
        $process = proc_open($command, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes, $projectRoot);

        if (!is_resource($process)) {
            throw new RuntimeException('Unable to start runtime cache clear.');
        }

        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            throw new RuntimeException('Runtime cache clear failed: ' . trim((string) $stderr));
        }
    }
}
