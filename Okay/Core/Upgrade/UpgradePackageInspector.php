<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

use RuntimeException;
use ZipArchive;

final class UpgradePackageInspector
{
    public const DELETED_PATHS_FILE = 'deleted_paths.txt';

    public function inspect(string $packagePath, string $projectRoot): UpgradePlan
    {
        $projectRoot = $this->normalizeExistingDirectory($projectRoot);
        if (is_dir($packagePath)) {
            $packageDirectory = $this->normalizeExistingDirectory($packagePath);
            return $this->inspectEntries($this->collectDirectoryEntries($packageDirectory), $projectRoot);
        }

        if (is_file($packagePath)) {
            return $this->inspectEntries($this->collectZipEntries($packagePath), $projectRoot);
        }

        throw new RuntimeException("Upgrade package not found: {$packagePath}");
    }

    /**
     * @param array<string, string|null> $entries relative package path => content or null for directories
     */
    private function inspectEntries(array $entries, string $projectRoot): UpgradePlan
    {
        [$packageRoot, $relativeEntries] = $this->extractPackageRoot($entries);
        [$fromVersion, $toVersion] = $this->parsePackageRoot($packageRoot);

        $copyPaths = [];
        $overwritePaths = [];
        $deletePaths = [];
        $manualReviewPaths = [];
        $skipPaths = [];
        $deletedManifest = [];

        foreach ($relativeEntries as $path => $content) {
            if ($content === null) {
                continue;
            }

            $normalizedPath = $this->normalizePackagePath($path);
            if ($normalizedPath === self::DELETED_PATHS_FILE) {
                $skipPaths[] = $normalizedPath;
                $deletedManifest = $this->parseDeletedPaths((string) $content);
                continue;
            }

            if ($this->isProtectedPath($normalizedPath)) {
                $manualReviewPaths[] = $normalizedPath;
                continue;
            }

            if (is_file($projectRoot . '/' . $normalizedPath)) {
                $overwritePaths[] = $normalizedPath;
                continue;
            }

            $copyPaths[] = $normalizedPath;
        }

        foreach ($deletedManifest as $path) {
            if ($this->isProtectedPath($path)) {
                $manualReviewPaths[] = $path;
                continue;
            }

            $deletePaths[] = $path;
        }

        return new UpgradePlan(
            $packageRoot,
            $fromVersion,
            $toVersion,
            $this->sortedUnique($copyPaths),
            $this->sortedUnique($overwritePaths),
            $this->sortedUnique($deletePaths),
            $this->sortedUnique($manualReviewPaths),
            $this->sortedUnique($skipPaths)
        );
    }

    private function normalizeExistingDirectory(string $path): string
    {
        $realPath = realpath($path);
        if ($realPath === false || !is_dir($realPath)) {
            throw new RuntimeException("Directory not found: {$path}");
        }

        return rtrim($realPath, '/');
    }

    /**
     * @return array<string, string|null>
     */
    private function collectDirectoryEntries(string $packagePath): array
    {
        $entries = [];
        $rootPrefix = $this->looksLikePackageRoot(basename($packagePath)) ? basename($packagePath) . '/' : '';
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($packagePath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $fileInfo) {
            $relativePath = $rootPrefix . substr($fileInfo->getPathname(), strlen($packagePath) + 1);
            $entries[$relativePath] = $fileInfo->isDir() ? null : (string) file_get_contents($fileInfo->getPathname());
        }

        return $entries;
    }

    /**
     * @return array<string, string|null>
     */
    private function collectZipEntries(string $packagePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($packagePath) !== true) {
            throw new RuntimeException("Unable to open upgrade archive: {$packagePath}");
        }

        $entries = [];
        for ($index = 0; $index < $zip->numFiles; $index++) {
            $name = $zip->getNameIndex($index);
            if ($name === false) {
                continue;
            }

            $isDirectory = str_ends_with($name, '/');
            $entries[rtrim($name, '/')] = $isDirectory ? null : (string) $zip->getFromIndex($index);
        }

        $zip->close();

        return $entries;
    }

    /**
     * @param array<string, string|null> $entries
     * @return array{0: string, 1: array<string, string|null>}
     */
    private function extractPackageRoot(array $entries): array
    {
        $roots = [];
        $relativeEntries = [];

        foreach ($entries as $path => $content) {
            $parts = explode('/', $this->normalizePackagePath($path), 2);
            $root = $parts[0];
            if ($root === '') {
                continue;
            }

            $roots[$root] = true;
            if (isset($parts[1]) && $parts[1] !== '') {
                $relativeEntries[$parts[1]] = $content;
            }
        }

        $rootNames = array_keys($roots);
        sort($rootNames);

        if (count($rootNames) !== 1) {
            throw new RuntimeException('Upgrade package must contain exactly one diff_<from>_<to> root.');
        }

        return [$rootNames[0], $relativeEntries];
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function parsePackageRoot(string $packageRoot): array
    {
        if (!preg_match('/^diff_([A-Za-z0-9._-]+)_([A-Za-z0-9._-]+)$/', $packageRoot, $matches)) {
            throw new RuntimeException('Upgrade package root must be named diff_<from>_<to>.');
        }

        return [$matches[1], $matches[2]];
    }

    private function looksLikePackageRoot(string $directoryName): bool
    {
        return preg_match('/^diff_[A-Za-z0-9._-]+_[A-Za-z0-9._-]+$/', $directoryName) === 1;
    }

    private function normalizePackagePath(string $path): string
    {
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');
        $parts = [];

        foreach (explode('/', $path) as $part) {
            if ($part === '' || $part === '.') {
                continue;
            }

            if ($part === '..') {
                throw new RuntimeException("Package path traversal is not allowed: {$path}");
            }

            $parts[] = $part;
        }

        return implode('/', $parts);
    }

    /**
     * @return list<string>
     */
    private function parseDeletedPaths(string $content): array
    {
        $paths = [];
        foreach (preg_split('/\R/', $content) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $paths[] = $this->normalizePackagePath($line);
        }

        return $this->sortedUnique($paths);
    }

    private function isProtectedPath(string $path): bool
    {
        if (in_array($path, ['config/config.php', 'config/config.local.php'], true)) {
            return true;
        }

        foreach (['files/', 'cache/', 'compiled/', 'var/', 'vendor/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $paths
     * @return list<string>
     */
    private function sortedUnique(array $paths): array
    {
        $paths = array_values(array_unique($paths));
        sort($paths);

        return $paths;
    }
}
