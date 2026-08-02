<?php

declare(strict_types=1);

namespace Seo;

use PHPUnit\Framework\TestCase;

final class ServicePagesIndexingPolicyTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    public function testCartWishlistAndComparisonPagesUseNoindexFollow(): void
    {
        foreach (
            [
                'Okay/Controllers/CartController.php',
                'Okay/Controllers/WishListController.php',
                'Okay/Controllers/ComparisonController.php',
            ] as $controller
        ) {
            self::assertStringContainsString(
                "assign('noindex_follow', true)",
                $this->read($controller),
                $controller
            );
        }
    }

    public function testRobotsTxtDoesNotBlockServicePagesWithMetaNoindex(): void
    {
        $robots = $this->read('robots.txt');

        foreach (['/cart', '/wishlist', '/comparison'] as $path) {
            self::assertStringNotContainsString('Disallow: ' . $path, $robots, $path);
        }
    }

    private function read(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);

        self::assertIsString($source);
        return $source;
    }
}
