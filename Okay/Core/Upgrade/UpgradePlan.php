<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

final class UpgradePlan
{
    /**
     * @param list<string> $copyPaths
     * @param list<string> $overwritePaths
     * @param list<string> $deletePaths
     * @param list<string> $manualReviewPaths
     * @param list<string> $skipPaths
     */
    public function __construct(
        private readonly string $packageRoot,
        private readonly string $fromVersion,
        private readonly string $toVersion,
        private readonly array $copyPaths,
        private readonly array $overwritePaths,
        private readonly array $deletePaths,
        private readonly array $manualReviewPaths,
        private readonly array $skipPaths
    ) {
    }

    public function getPackageRoot(): string
    {
        return $this->packageRoot;
    }

    public function getFromVersion(): string
    {
        return $this->fromVersion;
    }

    public function getToVersion(): string
    {
        return $this->toVersion;
    }

    /**
     * @return list<string>
     */
    public function getCopyPaths(): array
    {
        return $this->copyPaths;
    }

    /**
     * @return list<string>
     */
    public function getOverwritePaths(): array
    {
        return $this->overwritePaths;
    }

    /**
     * @return list<string>
     */
    public function getDeletePaths(): array
    {
        return $this->deletePaths;
    }

    /**
     * @return list<string>
     */
    public function getManualReviewPaths(): array
    {
        return $this->manualReviewPaths;
    }

    /**
     * @return list<string>
     */
    public function getSkipPaths(): array
    {
        return $this->skipPaths;
    }

    /**
     * @return list<string>
     */
    public function getTouchedFileBackupPaths(): array
    {
        $paths = array_values(array_unique(array_merge($this->overwritePaths, $this->deletePaths)));
        sort($paths);

        return $paths;
    }

    public function getRollbackBundlePath(): string
    {
        return "var/upgrade-reports/touched-files-{$this->fromVersion}-{$this->toVersion}.zip";
    }

    /**
     * @return list<string>
     */
    public function toConsoleLines(): array
    {
        return [
            "Package root: {$this->packageRoot}",
            "Upgrade range: {$this->fromVersion} -> {$this->toVersion}",
            $this->formatSection('Copy files', $this->copyPaths, '+'),
            $this->formatSection('Overwrite files', $this->overwritePaths, '~'),
            $this->formatSection('Delete paths', $this->deletePaths, '-'),
            $this->formatSection('Manual review paths', $this->manualReviewPaths, '!'),
            $this->formatSection('Skipped package entries', $this->skipPaths, '='),
            'Touched-file rollback bundle: ' . $this->getRollbackBundlePath(),
            $this->formatSection('Touched-file backup plan', $this->getTouchedFileBackupPaths(), '*'),
            'Post-apply action: run composer cache:clear after file/database changes.',
        ];
    }

    /**
     * @param list<string> $paths
     */
    private function formatSection(string $title, array $paths, string $prefix): string
    {
        $lines = ["{$title}: " . count($paths)];
        foreach ($paths as $path) {
            $lines[] = "  {$prefix} {$path}";
        }

        return implode(PHP_EOL, $lines);
    }
}
