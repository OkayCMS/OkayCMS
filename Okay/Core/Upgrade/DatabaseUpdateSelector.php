<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

use InvalidArgumentException;
use RuntimeException;

final class DatabaseUpdateSelector
{
    /**
     * @return list<DatabaseUpdateFile>
     */
    public function select(string $updatesDir, string $fromVersion, string $toVersion): array
    {
        $fromVersion = $this->normalizeVersion($fromVersion);
        $toVersion = $this->normalizeVersion($toVersion);

        if (version_compare($fromVersion, $toVersion, '>=')) {
            throw new InvalidArgumentException('Source version must be lower than target version.');
        }

        if (!is_dir($updatesDir)) {
            throw new RuntimeException("Database updates directory not found: {$updatesDir}");
        }

        $files = glob(rtrim($updatesDir, '/') . '/update_*.sql') ?: [];
        $updates = [];

        foreach ($files as $file) {
            $version = $this->extractVersion($file);
            if (version_compare($version, $fromVersion, '<=') || version_compare($version, $toVersion, '>')) {
                continue;
            }

            $updates[] = new DatabaseUpdateFile($version, $file);
        }

        usort(
            $updates,
            static fn (DatabaseUpdateFile $left, DatabaseUpdateFile $right): int => version_compare(
                $left->getVersion(),
                $right->getVersion()
            )
        );

        return $updates;
    }

    private function extractVersion(string $file): string
    {
        $basename = basename($file);
        if (!preg_match('/^update_(.+)\.sql$/', $basename, $matches)) {
            throw new InvalidArgumentException("Invalid database update filename: {$basename}");
        }

        return $this->normalizeVersion($matches[1]);
    }

    private function normalizeVersion(string $version): string
    {
        $version = preg_replace('/^v(?=\d)/', '', trim($version)) ?? $version;
        $version = str_replace('_', '-', $version);
        $version = strtolower($version);

        if (!preg_match('/^\d+\.\d+\.\d+(?:-[a-z0-9]+)?$/', $version)) {
            throw new InvalidArgumentException("Invalid version label: {$version}");
        }

        return $version;
    }
}
