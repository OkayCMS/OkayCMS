<?php

namespace Okay\Core\Adapters\Resize;

class Imagick extends AbstractResize
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
        $thumb = new \Imagick();

        $sharpen = 0.2;

        // Читаем изображение
        if (!$thumb->readImage($srcFile)) {
            return false;
        }

        // Размеры исходного изображения
        $srcW = $thumb->getImageWidth();
        $srcH = $thumb->getImageHeight();

        // Нужно ли обрезать?
        if ($setWatermark === false && ($srcH <= $maxH)) {
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

        // Уменьшаем
        $thumb->thumbnailImage($dstW, $dstH);

        $watermarkX = 0;
        $watermarkY = 0;

        // Устанавливаем водяной знак
        if ($setWatermark === true && !empty($this->watermark) && is_readable($this->watermark)) {
            $overlay = new \Imagick($this->watermark);

            // Get the size of overlay
            $owidth = $overlay->getImageWidth();
            $oheight = $overlay->getImageHeight();

            $watermarkX = min(($dstW - $owidth) * $this->watermarkOffsetX / 100, $dstW);
            $watermarkY = min(($dstH - $oheight) * $this->watermarkOffsetY / 100, $dstH);
        }

        // Анимированные gif требуют прохода по фреймам
        foreach ($thumb as $frame) {
            // Уменьшаем
            $frame->thumbnailImage($dstW, $dstH);

            /* Set the virtual canvas to correct size */
            $frame->setImagePage($dstW, $dstH, 0, 0);

            // Наводим резкость
            $thumb->adaptiveSharpenImage($sharpen, $sharpen);

            if (isset($overlay)) {
                $frame->compositeImage($overlay, \Imagick::COMPOSITE_OVER, $watermarkX, $watermarkY, \Imagick::CHANNEL_ALPHA);
            }
        }

        // Убираем комменты и т.п. из картинки
        $thumb->stripImage();
        $thumb->setImageCompressionQuality($this->imageQuality);
        $format = strtolower((string) $thumb->getImageFormat());
        if (in_array($format, ['jpeg', 'jpg'], true)) {
            $thumb->setImageCompression(\Imagick::COMPRESSION_JPEG);
        } elseif ($format === 'png') {
            $thumb->setImageCompression(\Imagick::COMPRESSION_ZIP);
        } else {
            $thumb->setImageCompression(\Imagick::COMPRESSION_UNDEFINED);
        }

        // Записываем картинку
        if (!$thumb->writeImages($dstFile, true)) {
            return false;
        }

        // Уборка
        $thumb->clear();
        if (isset($overlay)) {
            $overlay->clear();
        }
        return true;
    }
}
