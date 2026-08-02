<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

use RuntimeException;

final class UpgradeReport
{
    /** @var list<array<string, mixed>> */
    private array $phases = [];

    /** @var list<string> */
    private array $pending = [];

    private string $activePath;

    public function __construct(
        private readonly string $projectRoot,
        private readonly UpgradePlan $plan
    ) {
        $this->activePath = rtrim($projectRoot, '/') . '/var/upgrade-reports/upgrade-active.json';
    }

    public function start(): void
    {
        $directory = dirname($this->activePath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException("Unable to create upgrade report directory: {$directory}");
        }

        $this->write('active');
    }

    /**
     * @param array<string, mixed> $details
     */
    public function addPhase(string $name, string $status, array $details = []): void
    {
        $this->phases[] = [
            'name' => $name,
            'status' => $status,
            'details' => $details,
            'recorded_at' => gmdate('c'),
        ];

        $this->write('active');
    }

    public function addPending(string $message): void
    {
        $this->pending[] = $message;
        $this->write('active');
    }

    public function finalize(string $status): string
    {
        $finalPath = rtrim($this->projectRoot, '/') . '/var/upgrade-reports/upgrade-'
            . $this->plan->getFromVersion() . '-' . $this->plan->getToVersion() . '-' . gmdate('YmdHis') . '.json';
        $this->write($status, $finalPath);

        if (is_file($this->activePath)) {
            unlink($this->activePath);
        }

        return $finalPath;
    }

    private function write(string $status, ?string $path = null): void
    {
        $payload = [
            'status' => $status,
            'package_root' => $this->plan->getPackageRoot(),
            'from_version' => $this->plan->getFromVersion(),
            'to_version' => $this->plan->getToVersion(),
            'updated_at' => gmdate('c'),
            'phases' => $this->phases,
            'pending' => $this->pending,
        ];

        $encoded = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if ($encoded === false || file_put_contents($path ?? $this->activePath, $encoded . "\n") === false) {
            throw new RuntimeException('Unable to write upgrade report.');
        }
    }
}
