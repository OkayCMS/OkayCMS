<?php

declare(strict_types=1);

namespace Okay\Core\Upgrade;

final class UpgradePreflight
{
    /**
     * @param array{
     *     dryRun?: bool,
     *     fromVersion?: string,
     *     toVersion?: string,
     *     backupAcknowledged?: bool,
     *     maintenanceAcknowledged?: bool,
     *     schedulerPausedAcknowledged?: bool,
     *     runtimeParityAcknowledged?: bool,
     *     manualReviewApproved?: bool,
     *     rollbackBundleVerified?: bool,
     *     interruptedReportAcknowledged?: bool,
     *     projectRoot?: string
     * } $context
     */
    public function evaluate(UpgradePlan $plan, array $context = []): UpgradePreflightResult
    {
        $dryRun = $context['dryRun'] ?? false;
        $blockers = [];
        $warnings = [];

        $this->checkVersionMatch($plan, $context, $blockers);
        $this->checkPathSafety($plan, $blockers);
        $this->checkManualReview($plan, $context, $dryRun, $blockers, $warnings);
        $this->checkProductionAcknowledgements($context, $dryRun, $blockers, $warnings);
        $this->checkRollbackBundle($plan, $context, $dryRun, $blockers, $warnings);
        $this->checkInterruptedReport($context, $dryRun, $blockers, $warnings);
        $this->checkWritableTargets($plan, $context, $dryRun, $blockers, $warnings);

        return new UpgradePreflightResult($this->sortedUnique($blockers), $this->sortedUnique($warnings));
    }

    /**
     * @param array<string, mixed> $context
     * @param list<string> $blockers
     */
    private function checkVersionMatch(UpgradePlan $plan, array $context, array &$blockers): void
    {
        if (isset($context['fromVersion']) && $context['fromVersion'] !== $plan->getFromVersion()) {
            $blockers[] = "Package source version {$plan->getFromVersion()} does not match requested "
                . "source version {$context['fromVersion']}.";
        }

        if (isset($context['toVersion']) && $context['toVersion'] !== $plan->getToVersion()) {
            $blockers[] = "Package target version {$plan->getToVersion()} does not match requested "
                . "target version {$context['toVersion']}.";
        }
    }

    /**
     * @param list<string> $blockers
     */
    private function checkPathSafety(UpgradePlan $plan, array &$blockers): void
    {
        foreach (array_merge($plan->getCopyPaths(), $plan->getOverwritePaths(), $plan->getDeletePaths()) as $path) {
            if ($path === '' || str_starts_with($path, '/') || str_contains($path, '../') || $path === '..') {
                $blockers[] = "Unsafe package path: {$path}";
            }

            if ($this->isNonProductionPath($path)) {
                $blockers[] = "Non-production path is not allowed for automatic mutation: {$path}";
            }
        }
    }

    /**
     * @param array<string, mixed> $context
     * @param list<string> $blockers
     * @param list<string> $warnings
     */
    private function checkManualReview(
        UpgradePlan $plan,
        array $context,
        bool $dryRun,
        array &$blockers,
        array &$warnings
    ): void {
        if ($plan->getManualReviewPaths() === []) {
            return;
        }

        $message = 'Manual review paths must be resolved before automatic apply.';
        if ($dryRun) {
            $warnings[] = $message;
            return;
        }

        if (!($context['manualReviewApproved'] ?? false)) {
            $blockers[] = $message;
        }
    }

    /**
     * @param array<string, mixed> $context
     * @param list<string> $blockers
     * @param list<string> $warnings
     */
    private function checkProductionAcknowledgements(
        array $context,
        bool $dryRun,
        array &$blockers,
        array &$warnings
    ): void {
        $required = [
            'backupAcknowledged' => 'Full database and filesystem backups must be confirmed.',
            'maintenanceAcknowledged' => 'Maintenance mode or traffic drain must be confirmed.',
            'schedulerPausedAcknowledged' => 'Scheduler/cron pause must be confirmed.',
            'runtimeParityAcknowledged' => 'CLI, web/FPM, and cron PHP runtime parity must be confirmed.',
        ];

        foreach ($required as $key => $message) {
            if ($context[$key] ?? false) {
                continue;
            }

            if ($dryRun) {
                $warnings[] = $message;
            } else {
                $blockers[] = $message;
            }
        }
    }

    /**
     * @param array<string, mixed> $context
     * @param list<string> $blockers
     * @param list<string> $warnings
     */
    private function checkRollbackBundle(
        UpgradePlan $plan,
        array $context,
        bool $dryRun,
        array &$blockers,
        array &$warnings
    ): void {
        if ($plan->getTouchedFileBackupPaths() === []) {
            return;
        }

        $message = 'Touched-file rollback bundle must be created and verified before file mutation.';
        if ($context['rollbackBundleVerified'] ?? false) {
            return;
        }

        if ($dryRun) {
            $warnings[] = $message;
        } else {
            $blockers[] = $message;
        }
    }

    /**
     * @param array<string, mixed> $context
     * @param list<string> $blockers
     * @param list<string> $warnings
     */
    private function checkInterruptedReport(
        array $context,
        bool $dryRun,
        array &$blockers,
        array &$warnings
    ): void {
        $projectRoot = $context['projectRoot'] ?? null;
        if (!is_string($projectRoot)) {
            return;
        }

        $activeReport = rtrim($projectRoot, '/') . '/var/upgrade-reports/upgrade-active.json';
        if (!is_file($activeReport) || ($context['interruptedReportAcknowledged'] ?? false)) {
            return;
        }

        $message = 'Interrupted previous upgrade report must be resolved or acknowledged.';
        if ($dryRun) {
            $warnings[] = $message;
        } else {
            $blockers[] = $message;
        }
    }

    /**
     * @param array<string, mixed> $context
     * @param list<string> $blockers
     * @param list<string> $warnings
     */
    private function checkWritableTargets(
        UpgradePlan $plan,
        array $context,
        bool $dryRun,
        array &$blockers,
        array &$warnings
    ): void {
        $projectRoot = $context['projectRoot'] ?? null;
        if (!is_string($projectRoot)) {
            return;
        }

        foreach ($plan->getTouchedFileBackupPaths() as $path) {
            $target = rtrim($projectRoot, '/') . '/' . $path;
            $parent = is_dir($target) ? $target : dirname($target);
            if (!is_dir($parent) || is_writable($parent)) {
                continue;
            }

            $message = "Target path is not writable: {$path}";
            if ($dryRun) {
                $warnings[] = $message;
            } else {
                $blockers[] = $message;
            }
        }
    }

    private function isNonProductionPath(string $path): bool
    {
        foreach (['dev/', 'docs/', 'tests/', '.git/', '.github/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param list<string> $messages
     * @return list<string>
     */
    private function sortedUnique(array $messages): array
    {
        $messages = array_values(array_unique($messages));
        sort($messages);

        return $messages;
    }
}
