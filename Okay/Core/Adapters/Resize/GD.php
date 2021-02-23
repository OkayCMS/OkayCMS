<?php


namespace Okay\Core\Adapters\Resize;


class GD extends AbstractResize
{

    public function resize($srcFile, $dstFile, $maxW, $maxH, $setWatermark = false, $crop_params = [])
    {
        // Параметры исходного изображения
        @list($srcW, $srcH, $srcType) = array_values(getimagesize($srcFile));
        $srcType = image_type_to_mime_type($srcType);

        if(empty($srcW) || empty($srcH) || empty($srcType)) {
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
        @list($dstW, $dstH) = $this->calcContainSize($srcW, $srcH, $maxW, $maxH);

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
                imagealphablending($srcImg, true);
                break;
            default:
                return false;
        }

        if(empty($srcImg)) {
            return false;
        }

        $srcColors = imagecolorstotal($srcImg);

        // create destination image (indexed, if possible)
        if ($srcColors > 0 && $srcColors <= 256) {
            $dstImg = imagecreate($dstW, $dstH);
        } else {
            $dstImg = imagecreatetruecolor($dstW, $dstH);
        }

        if (empty($dstImg)) {
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
        }
        // or preserve alpha transparency for png
        elseif ($srcType === 'image/png') {
            if (!imagealphablending($dstImg, false)) {
                return false;
            }
            $transparency = imagecolorallocatealpha($dstImg, 0, 0, 0, 127);
            if (false === $transparency) {
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
        if($setWatermark === true && !empty($this->watermark) && is_readable($this->watermark)) {
            $overlay = imagecreatefrompng($this->watermark);

            // Get the size of overlay
            $owidth = imagesx($overlay);
            $oheight = imagesy($overlay);

            $watermarkX = min(($dstW-$owidth)*$this->watermarkOffsetX/100, $dstW);
            $watermarkY = min(($dstH-$oheight)*$this->watermarkOffsetY/100, $dstH);

            imagecopy($dstImg, $overlay, $watermarkX, $watermarkY, 0, 0, $owidth, $oheight);
        }

        // recalculate quality value for png image
        if ('image/png' === $srcType) {
            $this->imageQuality = round(($this->imageQuality / 100) * 10);
            if ($this->imageQuality < 1) {
                $this->imageQuality = 1;
            } elseif ($this->imageQuality > 10) {
                $this->imageQuality = 10;
            }
            $this->imageQuality = 10 - $this->imageQuality;
        }

        // Сохраняем изображение
        switch ($srcType) {
            case 'image/jpeg':
                return imageJpeg($dstImg, $dstFile, $this->imageQuality);
            case 'image/gif':
                return imageGif($dstImg, $dstFile, $this->imageQuality);
            case 'image/png':
                imagesavealpha($dstImg, true);
                return imagePng($dstImg, $dstFile, $this->imageQuality);
            default:
                return false;
        }
    }
}
