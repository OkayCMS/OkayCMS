<?php

namespace Migration;

use Okay\Core\Console\Application;
use Okay\Core\ServiceLocator;
use PHPUnit\Framework\TestCase;

/**
 * Minimal migration safety net: bootstrap paths that historically break on PHP upgrades.
 * Complements unit tests; does not replace HTTP/admin/import/mail manual validation.
 */
final class MigrationChannelSmokeTest extends TestCase
{
    public function testPhpVersionIsAtLeast85(): void
    {
        self::assertGreaterThanOrEqual(
            80500,
            PHP_VERSION_ID,
            'Migration baseline targets PHP 8.5+ per composer.json'
        );
    }

    public function testRequiredExtensionsAreLoaded(): void
    {
        // Keep in sync with `.github/workflows/php-migration.yml` migration-gates extensions.
        $required = ['pdo', 'json', 'mbstring', 'curl', 'zip', 'xml', 'simplexml', 'gd', 'intl'];
        foreach ($required as $ext) {
            self::assertTrue(
                extension_loaded($ext),
                sprintf('Extension %s must be loaded (parity with CI and application-stack docs)', $ext)
            );
        }
    }

    public function testConsoleApplicationBootstrapsFromServiceLocator(): void
    {
        $locator = ServiceLocator::getInstance();
        $app = $locator->getService(Application::class);
        self::assertInstanceOf(Application::class, $app);
        self::assertSame('scheduler:list', $app->find('scheduler:list')->getName());
        self::assertSame('scheduler:run', $app->find('scheduler:run')->getName());
        self::assertSame('database:deploy', $app->find('database:deploy')->getName());
    }

    public function testImageServiceBootstrapsFromServiceLocator(): void
    {
        $_SERVER['HTTP_HOST'] ??= 'localhost';

        $locator = ServiceLocator::getInstance();
        $image = $locator->getService(\Okay\Core\Image::class);

        self::assertInstanceOf(\Okay\Core\Image::class, $image);
    }

    public function testDesignUsesMobileDetect4FromServiceLocator(): void
    {
        $_SERVER['HTTP_HOST'] ??= 'localhost';

        $locator = ServiceLocator::getInstance();
        $design = $locator->getService(\Okay\Core\Design::class);

        self::assertInstanceOf(\Okay\Core\Design::class, $design);
        self::assertInstanceOf(\Detection\MobileDetect::class, $design->detect);
        self::assertFalse($design->isMobile());
        self::assertFalse($design->isTablet());
    }

    public function testPatchedRuntimeLibrariesAreAutoloadable(): void
    {
        self::assertTrue(class_exists(\Sabberworm\CSS\Parser::class));
        self::assertTrue(class_exists(\Wikimedia\Minify\JavaScriptMinifier::class));
        self::assertTrue(class_exists(\Intervention\Image\ImageManager::class));
        self::assertTrue(class_exists(\OpenAI\Client::class));
        self::assertTrue(class_exists(\GuzzleHttp\Client::class));
        self::assertTrue(class_exists(\Detection\MobileDetect::class));
        self::assertTrue(
            class_exists(\Snowplow\RefererParser\Parser::class),
            'Referer parser vendored under Okay/Core/UserReferer/Snowplow (M7-08)'
        );
    }

    public function testMobileDetectIsMajorVersion4(): void
    {
        $version = \Composer\InstalledVersions::getVersion('mobiledetect/mobiledetectlib');

        self::assertNotNull($version);
        self::assertStringStartsWith('4.', $version, 'M7-06 requires Mobile Detect 4.x');
        self::assertFalse(class_exists(\Mobile_Detect::class), 'Mobile Detect 4 no longer exposes legacy Mobile_Detect');
    }

    public function testDefaultResizeAdapterUsesIntervention(): void
    {
        $config = parse_ini_file(__DIR__ . '/../../config/config.php');

        self::assertIsArray($config);
        self::assertSame('Intervention', $config['resize_adapter']);
        self::assertTrue(class_exists(\Okay\Core\Adapters\Resize\Intervention::class));
    }

    public function testRemovedPatchLibrariesAreNotInstalled(): void
    {
        self::assertFalse(\Composer\InstalledVersions::isInstalled('orhanerday/open-ai'));
        self::assertFalse(\Composer\InstalledVersions::isInstalled('rosell-dk/webp-convert'));
    }

    /**
     * PHP 8.5 migration: v8.x did not declare PHP 8.5; v9+ is required before raising platform.
     */
    public function testSabberwormCssParserIsMajorVersion9(): void
    {
        $version = \Composer\InstalledVersions::getVersion('sabberworm/php-css-parser');
        self::assertNotNull($version);
        self::assertStringStartsWith('9.', $version, 'Lock must stay on sabberworm/php-css-parser v9.x for PHP 8.5 path');
    }
}
