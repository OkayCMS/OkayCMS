<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

final class UpgradePreflightResult
{
    /**
     * @param list<string> $blockers
     * @param list<string> $warnings
     */
    public function __construct(
        private readonly array $blockers,
        private readonly array $warnings
    ) {
    }

    public function isAllowed(): bool
    {
        return $this->blockers === [];
    }

    /**
     * @return list<string>
     */
    public function getBlockers(): array
    {
        return $this->blockers;
    }

    /**
     * @return list<string>
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return list<string>
     */
    public function toConsoleLines(): array
    {
        $lines = ['Preflight result: ' . ($this->isAllowed() ? 'allowed' : 'blocked')];
        $lines[] = 'Blockers: ' . count($this->blockers);
        foreach ($this->blockers as $blocker) {
            $lines[] = "  ! {$blocker}";
        }

        $lines[] = 'Warnings: ' . count($this->warnings);
        foreach ($this->warnings as $warning) {
            $lines[] = "  - {$warning}";
        }

        return $lines;
    }
}
