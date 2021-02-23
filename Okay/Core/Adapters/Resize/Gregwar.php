<?php


namespace Okay\Core\Adapters\Resize;


use \Gregwar\Image\Image as GregwarImage;

class Gregwar extends AbstractResize
{

    /**
     * @var GregwarImage 
     */
    private $gregwar;
    
    public function __construct($imageQuality, $watermark, $watermarkOffsetX, $watermarkOffsetY)
    {
        parent::__construct($imageQuality, $watermark, $watermarkOffsetX, $watermarkOffsetY);
        $this->gregwar = new GregwarImage();
    }

    public function resize($srcFile, $dstFile, $maxW, $maxH, $setWatermark = false, $crop_params = [])
    {
        $image = $this->gregwar->open($srcFile);

        // размеры исходного изображения
        $srcW = $image->width();
        $srcH = $image->height();

        list($dstW, $dstH) = $this->calcContainSize($srcW, $srcH, $maxW, $maxH);
        if (!empty($crop_params)) {
            $xPos = $crop_params['x_pos'];
            $yPos = $crop_params['y_pos'];

            $dstW = min($srcW, $maxW);
            $dstH = min($srcH, $maxH);

            $image->zoomCrop($dstW, $dstH, 'transparent', $xPos, $yPos);
        } else {
            $image->cropResize($dstW, $dstH);
        }

        if ($setWatermark === true && $this->watermark && is_readable($this->watermark)) {
            $watermarkImage = $this->gregwar->open($this->watermark);

            // размеры водяного знака
            $watermarkWidth  = $watermarkImage->width();
            $watermarkHeight = $watermarkImage->height();

            $watermarkX = min(($dstW-$watermarkWidth)*$this->watermarkOffsetX/100, $dstW);
            $watermarkY = min(($dstH-$watermarkHeight)*$this->watermarkOffsetY/100, $dstH);

            $image->merge($watermarkImage, $watermarkX, $watermarkY, $watermarkWidth, $watermarkHeight);
        }

        $srcExt = $image->guessType();
        if ($srcExt == 'gif') {
            $srcExt = 'png';
        }
        $image->save($dstFile, $srcExt, $this->imageQuality);
    }
    
}
