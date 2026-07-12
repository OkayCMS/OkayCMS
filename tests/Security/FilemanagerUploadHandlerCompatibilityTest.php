<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use UploadHandler;

final class FilemanagerUploadHandlerCompatibilityTest extends TestCase
{
    private string $uploadDir;

    protected function setUp(): void
    {
        parent::setUp();

        $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'okaycms.test';
        $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/backend/design/js/filemanager/upload.php';
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['SCRIPT_FILENAME'] ?? dirname(__DIR__, 2) . '/backend/design/js/filemanager/upload.php';
        $_SERVER['SERVER_NAME'] = $_SERVER['SERVER_NAME'] ?? 'okaycms.test';
        $_SERVER['SERVER_PORT'] = $_SERVER['SERVER_PORT'] ?? 80;

        require_once dirname(__DIR__, 2) . '/backend/design/js/filemanager/UploadHandler.php';

        $this->uploadDir = sys_get_temp_dir() . '/okay-uploadhandler-' . bin2hex(random_bytes(4)) . '/';
        mkdir($this->uploadDir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->uploadDir . '*') ?: [] as $file) {
            unlink($file);
        }

        if (is_dir($this->uploadDir)) {
            rmdir($this->uploadDir);
        }

        parent::tearDown();
    }

    public function testBasenameDefaultSuffixIsPhp85Compatible(): void
    {
        $handler = new UploadHandler(['upload_dir' => $this->uploadDir], false);
        $basename = new ReflectionMethod($handler, 'basename');

        self::assertSame('image.png', $basename->invoke($handler, '/tmp/image.png'));
    }

    public function testNonChunkedUploadUniqueFilenameDoesNotReadNullContentRange(): void
    {
        file_put_contents($this->uploadDir . 'image.png', 'existing');

        $handler = new UploadHandler(['upload_dir' => $this->uploadDir], false);
        $uniqueFilename = new ReflectionMethod($handler, 'get_unique_filename');

        self::assertSame(
            'image (1).png',
            $uniqueFilename->invoke($handler, '', 'image.png', 1, 'image/png', null, 0, null)
        );
    }
}
