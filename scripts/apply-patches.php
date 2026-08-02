<?php

declare(strict_types=1);

/**
 * Apply vendor patches after composer install/update.
 *
 * If a patch reports "Hunk FAILED" after local experiments, reset the package and re-run:
 *   composer reinstall <vendor/package>
 */

$patches = [
    'axy/sourcemap' => [__DIR__ . '/../patches/axy-sourcemap-php85-null-array-offset.patch'],
];

$vendorDir = __DIR__ . '/../vendor';
$errors = [];

foreach ($patches as $package => $patchFiles) {
    $packageDir = "{$vendorDir}/{$package}";
    if (!is_dir($packageDir)) {
        continue;
    }

    foreach ($patchFiles as $patchFile) {
        if (!file_exists($patchFile)) {
            echo "Warning: Patch file not found: {$patchFile}\n";
            continue;
        }

        $command = "cd " . escapeshellarg($packageDir) . " && patch -p1 < " . escapeshellarg($patchFile) . " 2>&1";
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $outputStr = implode(' ', $output);
        if ($returnCode !== 0 && !preg_match('/already applied|succeeded|ignored|reversed|previously applied/i', $outputStr)) {
            $errors[] = "Failed to apply patch {$patchFile} for {$package}: " . implode("\n", $output);
            break;
        }

        echo "Applied patch for {$package}: " . basename($patchFile) . "\n";
    }
}

if ($errors !== []) {
    echo "\nErrors:\n" . implode("\n\n", $errors) . "\n";
    exit(1);
}

echo "\nAll patches applied successfully.\n";
