<?php

declare(strict_types=1);

use Okay\Core\Upgrade\UpgradeApplier;
use Okay\Core\Upgrade\UpgradePackageInspector;
use Okay\Core\Upgrade\UpgradePreflight;

$root = dirname(__DIR__);
$packagePath = null;
$context = [];
$dryRun = false;
$usage = static function (): string {
    return "Usage: php scripts/upgrade-apply.php <upgrade.zip|extracted-package-dir> --from=<version> --to=<version> "
        . "[--root=/path/to/okaycms] [--dry-run] [--backup-ok] [--maintenance-ok] [--scheduler-paused] "
        . "[--runtime-parity-ok] [--manual-review-approved] [--acknowledge-interrupted-report]\n";
};

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--help' || $arg === '-h') {
        echo $usage();
        exit(0);
    }

    if (str_starts_with($arg, '--root=')) {
        $root = substr($arg, strlen('--root='));
        continue;
    }

    if (str_starts_with($arg, '--from=')) {
        $context['fromVersion'] = substr($arg, strlen('--from='));
        continue;
    }

    if (str_starts_with($arg, '--to=')) {
        $context['toVersion'] = substr($arg, strlen('--to='));
        continue;
    }

    if ($arg === '--dry-run') {
        $dryRun = true;
        continue;
    }

    if ($arg === '--backup-ok') {
        $context['backupAcknowledged'] = true;
        continue;
    }

    if ($arg === '--maintenance-ok') {
        $context['maintenanceAcknowledged'] = true;
        continue;
    }

    if ($arg === '--scheduler-paused') {
        $context['schedulerPausedAcknowledged'] = true;
        continue;
    }

    if ($arg === '--runtime-parity-ok') {
        $context['runtimeParityAcknowledged'] = true;
        continue;
    }

    if ($arg === '--manual-review-approved') {
        $context['manualReviewApproved'] = true;
        continue;
    }

    if ($arg === '--acknowledge-interrupted-report') {
        $context['interruptedReportAcknowledged'] = true;
        continue;
    }

    if ($packagePath === null) {
        $packagePath = $arg;
        continue;
    }

    fwrite(STDERR, "Unknown argument: {$arg}\n");
    exit(1);
}

if ($packagePath === null) {
    fwrite(STDERR, $usage());
    exit(1);
}

$autoload = $root . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Composer autoload file not found: {$autoload}\n");
    exit(1);
}

require $autoload;

try {
    if ($dryRun) {
        $plan = (new UpgradePackageInspector())->inspect($packagePath, $root);
        $preflight = (new UpgradePreflight())->evaluate($plan, [
            'dryRun' => true,
            'projectRoot' => $root,
        ] + $context);
        echo implode(PHP_EOL, $plan->toConsoleLines()) . PHP_EOL;
        echo implode(PHP_EOL, $preflight->toConsoleLines()) . PHP_EOL;
        exit(0);
    }

    $result = (new UpgradeApplier())->apply($packagePath, $root, $context);
    echo "Upgrade apply status: {$result['status']}\n";
    echo "Upgrade report: {$result['report']}\n";
    if ($result['rollback_bundle'] !== null) {
        echo "Touched-file rollback bundle: {$result['rollback_bundle']}\n";
    }

    exit($result['status'] === 'complete' ? 0 : 1);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
