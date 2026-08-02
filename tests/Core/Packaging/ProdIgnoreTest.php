<?php

declare(strict_types=1);

namespace Core\Packaging;

use Okay\Core\Packaging\ProdIgnore;
use PHPUnit\Framework\TestCase;

final class ProdIgnoreTest extends TestCase
{
    public function testMatchesDirectoryPrefixesAndExactPaths(): void
    {
        $prodIgnore = ProdIgnore::fromFile(dirname(__DIR__, 3) . '/.prodignore');

        self::assertTrue($prodIgnore->matches('cache/css/stale.css'));
        self::assertTrue($prodIgnore->matches('compiled/okay_shop/template.php'));
        self::assertTrue($prodIgnore->matches('dev/scripts/build-install-package.php'));
        self::assertFalse($prodIgnore->matches('Okay/Core/Foo.php'));
        self::assertFalse($prodIgnore->matches('scripts/clear-runtime-cache.php'));
    }
}
