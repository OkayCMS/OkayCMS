<?php

namespace Okay\Core\Adapters\Resize;

use Intervention\Image\Alignment;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class Intervention extends AbstractResize
{
    private ImageManager $manager;

    public function __construct(int $imageQuality, ?string $watermark, int $watermarkOffsetX, int $watermarkOffsetY)
    {
        parent::__construct($imageQuality, $watermark, $watermarkOffsetX, $watermarkOffsetY);
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * @param array<string, mixed> $crop_params
     */
    public function resize(
        string $srcFile,
        string $dstFile,
        int $maxW,
        int $maxH,
        bool $setWatermark = false,
        array $crop_params = []
    ): bool {
        try {
            $image = $this->manager->decodePath($srcFile);

            $srcW = $image->width();
            $srcH = $image->height();

            $contain = $this->calcContainSize($srcW, $srcH, $maxW, $maxH);
            if ($contain === false) {
                return false;
            }
            [$dstW, $dstH] = $contain;
            $dstW = (int) $dstW;
            $dstH = (int) $dstH;

            if (!empty($crop_params)) {
                $dstW = min($srcW, $maxW);
                $dstH = min($srcH, $maxH);

                $image->cover($dstW, $dstH, $this->cropAlignment($crop_params));
            } else {
                $image->scale($dstW, $dstH);
            }

            if ($setWatermark === true && $this->watermark && is_readable($this->watermark)) {
                $watermarkImage = $this->manager->decodePath($this->watermark);
                $watermarkWidth = $watermarkImage->width();
                $watermarkHeight = $watermarkImage->height();

                $watermarkX = min(($dstW - $watermarkWidth) * $this->watermarkOffsetX / 100, $dstW);
                $watermarkY = min(($dstH - $watermarkHeight) * $this->watermarkOffsetY / 100, $dstH);

                $image->insert($watermarkImage, (int) $watermarkX, (int) $watermarkY);
            }

            $outputExtension = $this->outputExtension($srcFile);
            if ($outputExtension === null) {
                return false;
            }

            if (in_array($outputExtension, ['jpg', 'jpeg', 'webp'], true)) {
                $image->encodeUsingFileExtension($outputExtension, quality: $this->imageQuality)->save($dstFile);
            } else {
                $image->encodeUsingFileExtension($outputExtension)->save($dstFile);
            }

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @param array<string, mixed> $cropParams
     */
    private function cropAlignment(array $cropParams): Alignment
    {
        $xPos = $this->positionValue($cropParams['x_pos'] ?? null);
        $yPos = $this->positionValue($cropParams['y_pos'] ?? null);

        return match ([$xPos, $yPos]) {
            ['left', 'top'] => Alignment::TOP_LEFT,
            ['center', 'top'] => Alignment::TOP,
            ['right', 'top'] => Alignment::TOP_RIGHT,
            ['left', 'center'] => Alignment::LEFT,
            ['right', 'center'] => Alignment::RIGHT,
            ['left', 'bottom'] => Alignment::BOTTOM_LEFT,
            ['center', 'bottom'] => Alignment::BOTTOM,
            ['right', 'bottom'] => Alignment::BOTTOM_RIGHT,
            default => Alignment::CENTER,
        };
    }

    private function positionValue(mixed $value): string
    {
        if (!is_string($value)) {
            return 'center';
        }

        return match (strtolower($value)) {
            'left', 'right', 'top', 'bottom' => strtolower($value),
            default => 'center',
        };
    }

    private function outputExtension(string $srcFile): ?string
    {
        $size = getimagesize($srcFile);
        if ($size === false) {
            return null;
        }

        return match ($size['mime']) {
            'image/jpeg' => 'jpg',
            'image/png', 'image/gif' => 'png',
            'image/webp' => 'webp',
            default => null,
        };
    }
}
