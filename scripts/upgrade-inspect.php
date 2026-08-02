<?php

declare(strict_types=1);

use Okay\Core\Upgrade\UpgradePackageInspector;
use Okay\Core\Upgrade\UpgradePreflight;

$root = dirname(__DIR__);
$packagePath = null;

foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--help' || $arg === '-h') {
        echo "Usage: php scripts/upgrade-inspect.php <upgrade.zip|extracted-package-dir> [--root=/path/to/okaycms]\n";
        exit(0);
    }

    if (str_starts_with($arg, '--root=')) {
        $root = substr($arg, strlen('--root='));
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
    fwrite(STDERR, "Usage: php scripts/upgrade-inspect.php <upgrade.zip|extracted-package-dir> [--root=/path/to/okaycms]\n");
    exit(1);
}

$autoload = $root . '/vendor/autoload.php';
if (!is_file($autoload)) {
    fwrite(STDERR, "Composer autoload file not found: {$autoload}\n");
    exit(1);
}

require $autoload;

try {
    $plan = (new UpgradePackageInspector())->inspect($packagePath, $root);
    $preflight = (new UpgradePreflight())->evaluate($plan, [
        'dryRun' => true,
        'projectRoot' => $root,
    ]);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}

echo implode(PHP_EOL, $plan->toConsoleLines()) . PHP_EOL;
echo implode(PHP_EOL, $preflight->toConsoleLines()) . PHP_EOL;
