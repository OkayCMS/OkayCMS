<?php

namespace Core\Adapters\Resize;

use GdImage;
use Okay\Core\Adapters\Resize\Intervention;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class InterventionResizeAdapterTest extends TestCase
{
    private string $workspace;

    protected function setUp(): void
    {
        parent::setUp();

        $workspace = sys_get_temp_dir() . '/okay-intervention-resize-' . bin2hex(random_bytes(6));
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

    public function testContainResizePreservesAspectRatioForJpeg(): void
    {
        $source = $this->path('source.jpg');
        $target = $this->path('resized.jpg');

        $image = $this->createImage(120, 60, [220, 30, 30]);
        $this->saveJpeg($image, $source);

        $adapter = new Intervention(90, null, 0, 0);

        self::assertTrue($adapter->resize($source, $target, 60, 60));
        self::assertSame([60, 30], $this->imageDimensions($target));
        self::assertSame('image/jpeg', $this->imageMime($target));
    }

    public function testPngResizePreservesTransparentPixels(): void
    {
        $source = $this->path('alpha.png');
        $target = $this->path('alpha-resized.png');

        $image = $this->createTransparentImage(60, 60);
        $red = $this->allocateColor($image, 255, 0, 0);
        imagefilledrectangle($image, 15, 15, 44, 44, $red);
        $this->savePng($image, $source);

        $adapter = new Intervention(90, null, 0, 0);

        self::assertTrue($adapter->resize($source, $target, 30, 30));
        self::assertSame([30, 30], $this->imageDimensions($target));
        self::assertGreaterThan(120, $this->pngAlphaAt($target, 0, 0));
        self::assertSame([255, 0, 0], $this->rgbAt($target, 15, 15));
    }

    /**
     * @param array{0: int, 1: int, 2: int} $expectedRgb
     */
    #[DataProvider('cropPositionProvider')]
    public function testCropPositionSelectsExpectedHorizontalSourceArea(string $xPosition, array $expectedRgb): void
    {
        $source = $this->path('wide.png');
        $target = $this->path('wide-' . $xPosition . '.png');

        $image = $this->createImage(100, 50, [255, 0, 0]);
        $blue = $this->allocateColor($image, 0, 0, 255);
        imagefilledrectangle($image, 50, 0, 99, 49, $blue);
        $this->savePng($image, $source);

        $adapter = new Intervention(90, null, 0, 0);

        self::assertTrue($adapter->resize($source, $target, 50, 50, false, [
            'x_pos' => $xPosition,
            'y_pos' => 'center',
        ]));
        self::assertSame([50, 50], $this->imageDimensions($target));
        self::assertSame($expectedRgb, $this->rgbAt($target, 25, 25));
    }

    /**
     * @return array<string, array{0: string, 1: array{0: int, 1: int, 2: int}}>
     */
    public static function cropPositionProvider(): array
    {
        return [
            'left crop keeps left side' => ['left', [255, 0, 0]],
            'right crop keeps right side' => ['right', [0, 0, 255]],
        ];
    }

    public function testWatermarkUsesPercentageOffsetsFromBottomRightAvailableSpace(): void
    {
        $source = $this->path('watermark-source.png');
        $watermark = $this->path('watermark.png');
        $target = $this->path('watermarked.png');

        $base = $this->createImage(80, 80, [255, 255, 255]);
        $this->savePng($base, $source);

        $mark = $this->createImage(10, 10, [255, 0, 0]);
        $this->savePng($mark, $watermark);

        $adapter = new Intervention(90, $watermark, 100, 100);

        self::assertTrue($adapter->resize($source, $target, 40, 40, true));
        self::assertSame([40, 40], $this->imageDimensions($target));
        self::assertSame([255, 255, 255], $this->rgbAt($target, 5, 5));
        self::assertSame([255, 0, 0], $this->rgbAt($target, 35, 35));
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

        $color = $this->allocateColor($image, $rgb[0], $rgb[1], $rgb[2]);
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

    private function allocateColor(GdImage $image, int $red, int $green, int $blue): int
    {
        $color = imagecolorallocate($image, $red, $green, $blue);
        if ($color === false) {
            throw new RuntimeException('Unable to allocate color');
        }

        return $color;
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

    /**
     * @return array{0: int, 1: int}
     */
    private function imageDimensions(string $path): array
    {
        $size = getimagesize($path);
        if ($size === false) {
            throw new RuntimeException('Unable to read image dimensions');
        }

        return [$size[0], $size[1]];
    }

    private function imageMime(string $path): string
    {
        $size = getimagesize($path);
        if ($size === false) {
            throw new RuntimeException('Unable to read image mime type');
        }

        return $size['mime'];
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function rgbAt(string $path, int $x, int $y): array
    {
        $image = imagecreatefromstring((string) file_get_contents($path));
        if (!$image instanceof GdImage) {
            throw new RuntimeException('Unable to load image');
        }

        $index = imagecolorat($image, $x, $y);

        return [
            ($index >> 16) & 0xFF,
            ($index >> 8) & 0xFF,
            $index & 0xFF,
        ];
    }

    private function pngAlphaAt(string $path, int $x, int $y): int
    {
        $image = imagecreatefrompng($path);
        if (!$image instanceof GdImage) {
            throw new RuntimeException('Unable to load PNG image');
        }

        $colors = imagesx($image) > $x && imagesy($image) > $y
            ? imagecolorsforindex($image, imagecolorat($image, $x, $y))
            : ['alpha' => 0];

        return $colors['alpha'];
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
