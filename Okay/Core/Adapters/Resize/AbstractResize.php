<?php


namespace Okay\Core\Adapters\Resize;


abstract class AbstractResize
{

    /**
     * @var int качество изображения, берется из настроек 0-100
     */
    protected $imageQuality;

    /**
     * @var null|string путь к файлу водяного знака
     */
    protected $watermark;

    /**
     * @var int смещение водяного знака по оси X
     */
    protected $watermarkOffsetX;

    /**
     * @var int смещение водяного знака по оси Y
     */
    protected $watermarkOffsetY;
    
    public function __construct($imageQuality = 80, $watermark = null, $watermarkOffsetX = 0, $watermarkOffsetY = 0)
    {
        $this->imageQuality = $imageQuality;
        $this->watermark    = $watermark;
        $this->watermarkOffsetX = $watermarkOffsetX;
        $this->watermarkOffsetY = $watermarkOffsetY;
    }

    abstract public function resize(
        $srcFile,
        $dstFile,
        $maxW,
        $maxH,
        $setWatermark = null,
        $crop_params = []
    );

    /**
     * Вычисляет размеры изображения, до которых нужно его пропорционально уменьшить, чтобы вписать в квадрат $maxW x $maxH
     * @param $srcW - ширина исходного изображения
     * @param $srcH - высота исходного изображения
     * @param int $maxW - максимальная ширина
     * @param int $maxH - максимальная высота
     * @return array|bool
     */
    protected function calcContainSize($srcW, $srcH, $maxW = 0, $maxH = 0)
    {
        if($srcW == 0 || $srcH == 0) {
            return false;
        }

        $dstW = $srcW;
        $dstH = $srcH;

        if($srcW > $maxW && $maxW>0) {
            $dstH = $srcH * ($maxW/$srcW);
            $dstW = $maxW;
        }
        if($dstH > $maxH && $maxH>0) {
            $dstW = $dstW * ($maxH/$dstH);
            $dstH = $maxH;
        }
        return [$dstW, $dstH];
    }
    
}
