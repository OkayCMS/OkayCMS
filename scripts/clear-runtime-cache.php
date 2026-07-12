<?php

declare(strict_types=1);

$parseRootPath = static function (array $argv): string {
    $root = dirname(__DIR__);

    foreach (array_slice($argv, 1) as $arg) {
        if ($arg === '--help' || $arg === '-h') {
            echo "Usage: php scripts/clear-runtime-cache.php [--root=/path/to/okaycms]\n";
            exit(0);
        }

        if (str_starts_with($arg, '--root=')) {
            $root = substr($arg, strlen('--root='));
            continue;
        }

        fwrite(STDERR, "Unknown argument: {$arg}\n");
        exit(1);
    }

    $realRoot = realpath($root);
    if ($realRoot === false || !is_dir($realRoot)) {
        fwrite(STDERR, "Invalid OkayCMS root: {$root}\n");
        exit(1);
    }

    return rtrim($realRoot, '/');
};

$installRoot = $parseRootPath($argv);
$codeRoot = dirname(__DIR__);

require $codeRoot . '/vendor/autoload.php';

try {
    $result = \Okay\Core\Runtime\RuntimeArtifactsClearerFactory::clearInstallRoot($codeRoot, $installRoot);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Runtime cache clear failed: ' . $exception->getMessage() . "\n");
    exit(1);
}

foreach ($result->skippedMissing() as $skippedPath) {
    $relativePath = str_starts_with($skippedPath, $installRoot)
        ? substr($skippedPath, strlen($installRoot) + 1)
        : $skippedPath;
    echo "Skipped missing {$relativePath}\n";
}

if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache reset requested.\n";
}

if ($result->hasErrors()) {
    fwrite(STDERR, "Runtime cache clear failed:\n" . implode("\n", $result->errors()) . "\n");
    exit(1);
}

echo "Runtime cache clear complete. Removed {$result->removedFiles()} files and {$result->removedDirs()} directories.\n";
