<?php

declare(strict_types=1);

namespace Okay\Core\Packaging;

use RuntimeException;

final class ProductionPackageExclude
{
    private static ?ProdIgnore $prodIgnore = null;

    public static function initialize(string $projectRoot, ?string $codeRoot = null): void
    {
        $projectRoot = rtrim($projectRoot, '/');
        self::ensureAutoload($codeRoot ?? $projectRoot);
        self::$prodIgnore = ProdIgnore::fromProjectRoot($projectRoot);
    }

    private static function ensureAutoload(string $codeRoot): void
    {
        $codeRoot = rtrim($codeRoot, '/');
        $autoloadPath = $codeRoot . '/vendor/autoload.php';

        if (!is_file($autoloadPath)) {
            throw new RuntimeException("Composer autoload is required at {$autoloadPath}");
        }

        require_once $autoloadPath;
    }

    public static function matches(string $relativePath): bool
    {
        if (!self::$prodIgnore instanceof ProdIgnore) {
            throw new RuntimeException('Production package exclude rules are not initialized.');
        }

        return self::$prodIgnore->matches($relativePath);
    }

    public static function reset(): void
    {
        self::$prodIgnore = null;
    }
}
