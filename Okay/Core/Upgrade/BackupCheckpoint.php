<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

use RuntimeException;
use ZipArchive;

final class BackupCheckpoint
{
    public function createTouchedFileBundle(UpgradePlan $plan, string $projectRoot, ?string $bundlePath = null): string
    {
        $projectRoot = $this->normalizeProjectRoot($projectRoot);
        $bundlePath ??= $projectRoot . '/' . $plan->getRollbackBundlePath();
        $bundleDirectory = dirname($bundlePath);

        if (!is_dir($bundleDirectory) && !mkdir($bundleDirectory, 0775, true) && !is_dir($bundleDirectory)) {
            throw new RuntimeException("Unable to create rollback bundle directory: {$bundleDirectory}");
        }

        $zip = new ZipArchive();
        if ($zip->open($bundlePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("Unable to create rollback bundle: {$bundlePath}");
        }

        $metadata = [
            'package_root' => $plan->getPackageRoot(),
            'from_version' => $plan->getFromVersion(),
            'to_version' => $plan->getToVersion(),
            'created_at' => gmdate('c'),
            'paths' => $plan->getTouchedFileBackupPaths(),
        ];
        $encodedMetadata = json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encodedMetadata === false) {
            throw new RuntimeException('Unable to encode rollback bundle metadata.');
        }

        $zip->addFromString('metadata.json', $encodedMetadata . "\n");

        foreach ($plan->getTouchedFileBackupPaths() as $path) {
            $absolutePath = $projectRoot . '/' . $path;
            if (is_file($absolutePath)) {
                $zip->addFile($absolutePath, 'files/' . $path);
            }
        }

        $zip->close();

        if (!$this->verifyBundle($bundlePath)) {
            throw new RuntimeException("Rollback bundle verification failed: {$bundlePath}");
        }

        return $bundlePath;
    }

    public function verifyBundle(string $bundlePath): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($bundlePath) !== true) {
            return false;
        }

        $hasMetadata = $zip->locateName('metadata.json') !== false;
        $zip->close();

        return $hasMetadata;
    }

    private function normalizeProjectRoot(string $projectRoot): string
    {
        $realRoot = realpath($projectRoot);
        if ($realRoot === false || !is_dir($realRoot)) {
            throw new RuntimeException("Project root not found: {$projectRoot}");
        }

        return rtrim($realRoot, '/');
    }
}
