<?php

declare(strict_types=1);

namespace Core\Adapters\Resize;

use GdImage;
use Okay\Core\Adapters\Resize\WebpConverter;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class WebpConverterTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $workspace = sys_get_temp_dir() . '/okay-webp-converter-' . bin2hex(random_bytes(6));
        if (!mkdir($workspace) && !is_dir($workspace)) {
            throw new RuntimeException('Unable to create test workspace');
        }

        $this->workspace = $workspace;
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->workspace);

        parent::tearDown();
    }

    public function testJpegDerivativeConvertsToWebp(): void
    {
        $source = $this->path('resized.jpg');
        $destination = $source . '.webp';
        $this->saveJpeg($this->createImage(40, 20, [20, 80, 160]), $source);

        $converter = new WebpConverter(80);

        self::assertTrue($converter->convert($source, $destination));
        self::assertSame('image/webp', $this->imageMime($destination));
    }

    public function testPngWithAlphaConvertsToWebp(): void
    {
        $source = $this->path('alpha.png');
        $destination = $source . '.webp';
        $this->savePng($this->createTransparentImage(30, 30), $source);

        $converter = new WebpConverter(80);

        self::assertTrue($converter->convert($source, $destination));
        self::assertSame('image/webp', $this->imageMime($destination));
    }

    public function testUnreadableSourceReturnsFalseWithoutPartialTarget(): void
    {
        $destination = $this->path('broken.jpg.webp');
        $converter = new WebpConverter(80);

        self::assertFalse($converter->convert($this->path('missing.jpg'), $destination));
        self::assertFileDoesNotExist($destination);
        self::assertFileDoesNotExist($destination . '.tmp');
    }

    private function path(string $filename): string
    {
        return $this->workspace . '/' . $filename;
    }

    /**
     * @param array{0: int, 1: int, 2: int} $rgb
     */
    private function createImage(int $width, int $height, array $rgb): GdImage
    {
        $image = imagecreatetruecolor($width, $height);
        if (!$image instanceof GdImage) {
            throw new RuntimeException('Unable to create GD image');
        }

        $color = imagecolorallocate($image, $rgb[0], $rgb[1], $rgb[2]);
        if ($color === false) {
            throw new RuntimeException('Unable to allocate color');
        }

        imagefill($image, 0, 0, $color);

        return $image;
    }

    private function createTransparentImage(int $width, int $height): GdImage
    {
        $image = imagecreatetruecolor($width, $height);
        if (!$image instanceof GdImage) {
            throw new RuntimeException('Unable to create transparent GD image');
        }

        imagealphablending($image, false);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        if ($transparent === false) {
            throw new RuntimeException('Unable to allocate transparent color');
        }
        imagefill($image, 0, 0, $transparent);

        return $image;
    }

    private function saveJpeg(GdImage $image, string $path): void
    {
        if (!imagejpeg($image, $path, 90)) {
            throw new RuntimeException('Unable to save JPEG image');
        }
    }

    private function savePng(GdImage $image, string $path): void
    {
        if (!imagepng($image, $path)) {
            throw new RuntimeException('Unable to save PNG image');
        }
    }

    private function imageMime(string $path): string
    {
        $size = getimagesize($path);
        if ($size === false) {
            throw new RuntimeException('Unable to read image mime type');
        }

        return $size['mime'];
    }

    private function removeDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        foreach (glob($path . '/*') ?: [] as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        rmdir($path);
    }
}
