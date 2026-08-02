<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class FilemanagerPathResolverTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'okaycms.test';
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/backend/design/js/filemanager/dialog.php';
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] ?? dirname(__DIR__, 2) . '/backend/design/js/filemanager/dialog.php';
        $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'okaycms.test';
        $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;
        $previousDirectory = getcwd();
        self::assertIsString($previousDirectory);

        chdir(dirname(__DIR__, 2) . '/backend/design/js/filemanager');
        $config = include 'config/config.php';
        $GLOBALS['config'] = $config;
        $_SESSION['RF'] = [
            'verify' => 'RESPONSIVEfilemanager',
            'language' => 'en_EN',
        ];
        require_once 'include/utils.php';
        chdir($previousDirectory);
    }

    public function testRelativePathNormalizationRejectsTraversalAndAbsolutePaths(): void
    {
        foreach (
            [
                '../config.php',
                '..%2Fconfig.php',
                '/absolute/path',
                'C:/windows/system.ini',
                'php://filter/resource=index.php',
                'https://example.com/file.jpg',
                "folder/\0file.jpg",
            ] as $path
        ) {
            self::assertNull(\normalizeFilemanagerRelativePath($path), $path);
            self::assertFalse(\checkRelativePath($path), $path);
        }
    }

    public function testRelativePathNormalizationPreservesSafeFilemanagerPaths(): void
    {
        self::assertSame('', \normalizeFilemanagerRelativePath(''));
        self::assertSame('products/', \normalizeFilemanagerRelativePath('products/'));
        self::assertSame('products/image.jpg', \normalizeFilemanagerRelativePath('products/image.jpg'));
        self::assertTrue(\checkRelativePath('products/image.jpg'));
    }

    public function testUploadFolderNormalizationAllowsRootFolder(): void
    {
        self::assertSame('', \normalizeFilemanagerUploadFolder(''));
        self::assertSame('', \normalizeFilemanagerUploadFolder('/'));
        self::assertSame('products/', \normalizeFilemanagerUploadFolder('products'));
        self::assertSame('products/', \normalizeFilemanagerUploadFolder('/products/'));
        self::assertNull(\normalizeFilemanagerUploadFolder('../products'));
    }

    public function testResolverKeepsResolvedPathsInsideUploadRoot(): void
    {
        $uploadRoot = dirname(__DIR__, 2) . '/files/uploads';
        $rootRealPath = realpath($uploadRoot);
        self::assertIsString($rootRealPath);

        self::assertSame($rootRealPath, \resolveFilemanagerPath($uploadRoot, ''));
        self::assertSame(
            $rootRealPath . DIRECTORY_SEPARATOR . 'new-image.jpg',
            \resolveFilemanagerPath($uploadRoot, 'new-image.jpg')
        );
        self::assertNull(\resolveFilemanagerPath($uploadRoot, '../config/config.php'));
    }
}
