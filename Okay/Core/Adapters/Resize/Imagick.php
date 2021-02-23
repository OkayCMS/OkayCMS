<?php


namespace Okay\Core\Adapters\Resize;


class Imagick extends AbstractResize // todo протестить адапрер Imagick
{

    public function resize($srcFile, $dstFile, $maxW, $maxH, $setWatermark = false, $crop_params = [])
    {
        $thumb = new \Imagick();

        $sharpen = 0.2;

        // Читаем изображение
        if(!$thumb->readImage($srcFile)) {
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
        list($dstW, $dstH) = $this->calcContainSize($srcW, $srcH, $maxW, $maxH);

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

            $watermarkX = min(($dstW-$owidth)*$this->watermarkOffsetX/100, $dstW);
            $watermarkY = min(($dstH-$oheight)*$this->watermarkOffsetY/100, $dstH);
        }

        // Анимированные gif требуют прохода по фреймам
        foreach ($thumb as $frame) {
            // Уменьшаем
            $frame->thumbnailImage($dstW, $dstH);

            /* Set the virtual canvas to correct size */
            $frame->setImagePage($dstW, $dstH, 0, 0);

            // Наводим резкость
            $thumb->adaptiveSharpenImage($sharpen, $sharpen);

            if(isset($overlay) && is_object($overlay)) {
                $frame->compositeImage($overlay, \imagick::COMPOSITE_OVER, $watermarkX, $watermarkY, \imagick::COLOR_ALPHA);
            }
        }

        // Убираем комменты и т.п. из картинки
        $thumb->stripImage();
        $thumb->setImageCompressionQuality($this->imageQuality);
        $thumb->setImageCompression($this->imageQuality);

        // Записываем картинку
        if (!$thumb->writeImages($dstFile, true)) {
            return false;
        }

        // Уборка
        $thumb->destroy();
        if (isset($overlay) && is_object($overlay)) {
            $overlay->destroy();
        }
        return true;
    }
}
