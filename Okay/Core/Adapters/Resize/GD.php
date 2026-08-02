<?php

namespace Okay\Core\Adapters\Resize;

class GD extends AbstractResize
{
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
        // Параметры исходного изображения
        $size = getimagesize($srcFile);
        if ($size === false) {
            return false;
        }
        $srcW = $size[0];
        $srcH = $size[1];
        $srcType = image_type_to_mime_type($size[2]);

        if ($srcW === 0 || $srcH === 0) {
            return false;
        }

        // Нужно ли обрезать?
        if ($setWatermark === false && ($srcW <= $maxW) && ($srcH <= $maxH)) {
            // Нет - просто скопируем файл
            if (!copy($srcFile, $dstFile)) {
                return false;
            }
            return true;
        }

        // Размеры превью при пропорциональном уменьшении
        $contain = $this->calcContainSize($srcW, $srcH, $maxW, $maxH);
        if ($contain === false) {
            return false;
        }
        [$dstW, $dstH] = $contain;
        $dstW = (int) $dstW;
        $dstH = (int) $dstH;
        if ($dstW < 1 || $dstH < 1) {
            return false;
        }

        // Читаем изображение
        switch ($srcType) {
            case 'image/jpeg':
                $srcImg = imageCreateFromJpeg($srcFile);
                break;
            case 'image/gif':
                $srcImg = imageCreateFromGif($srcFile);
                break;
            case 'image/png':
                $srcImg = imageCreateFromPng($srcFile);
                if ($srcImg !== false) {
                    imagealphablending($srcImg, true);
                }
                break;
            default:
                return false;
        }

        if ($srcImg === false) {
            return false;
        }

        $srcColors = imagecolorstotal($srcImg);

        // create destination image (indexed, if possible)
        if ($srcColors > 0) {
            $dstImg = imagecreate($dstW, $dstH);
        } else {
            $dstImg = imagecreatetruecolor($dstW, $dstH);
        }

        if ($dstImg === false) {
            return false;
        }

        $transparentIndex = imagecolortransparent($srcImg);
        if ($transparentIndex >= 0 && $transparentIndex <= 128) {
            $t_c = imagecolorsforindex($srcImg, $transparentIndex);
            $transparentIndex = imagecolorallocate($dstImg, $t_c['red'], $t_c['green'], $t_c['blue']);
            if ($transparentIndex === false) {
                return false;
            }
            if (!imagefill($dstImg, 0, 0, $transparentIndex)) {
                return false;
            }
            imagecolortransparent($dstImg, $transparentIndex);
        } elseif ($srcType === 'image/png') {
            // or preserve alpha transparency for png
            if (!imagealphablending($dstImg, false)) {
                return false;
            }
            $transparency = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
            if ($transparency === false) {
                return false;
            }
            if (!imagefill($dstImg, 0, 0, $transparency)) {
                return false;
            }
            if (!imagesavealpha($dstImg, true)) {
                return false;
            }
        }

        // resample the image with new sizes
        if (!imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $dstW, $dstH, $srcW, $srcH)) {
            return false;
        }

        // Watermark
        if ($setWatermark === true && !empty($this->watermark) && is_readable($this->watermark)) {
            $overlay = imagecreatefrompng($this->watermark);
            if ($overlay === false) {
                return false;
            }

            // Get the size of overlay
            $owidth = imagesx($overlay);
            $oheight = imagesy($overlay);

            $watermarkX = min(($dstW - $owidth) * $this->watermarkOffsetX / 100, $dstW);
            $watermarkY = min(($dstH - $oheight) * $this->watermarkOffsetY / 100, $dstH);

            imagecopy($dstImg, $overlay, $watermarkX, $watermarkY, 0, 0, $owidth, $oheight);
        }

        // recalculate quality value for png image
        if ('image/png' === $srcType) {
            $q = (int) round(($this->imageQuality / 100) * 10);
            if ($q < 1) {
                $q = 1;
            } elseif ($q > 10) {
                $q = 10;
            }
            $this->imageQuality = 10 - $q;
        }

        // Сохраняем изображение
        switch ($srcType) {
            case 'image/jpeg':
                return imageJpeg($dstImg, $dstFile, $this->imageQuality);
            case 'image/gif':
                return imagegif($dstImg, $dstFile);
            case 'image/png':
                imagesavealpha($dstImg, true);
                return imagePng($dstImg, $dstFile, $this->imageQuality);
            default:
                return false;
        }
    }
}
