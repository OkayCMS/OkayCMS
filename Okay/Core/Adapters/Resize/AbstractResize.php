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

    public function __construct(int $imageQuality = 80, ?string $watermark = null, int $watermarkOffsetX = 0, int $watermarkOffsetY = 0)
    {
        $this->imageQuality = $imageQuality;
        $this->watermark    = $watermark;
        $this->watermarkOffsetX = $watermarkOffsetX;
        $this->watermarkOffsetY = $watermarkOffsetY;
    }

    /**
     * @param array<string, mixed> $crop_params Optional crop hints (e.g. x_pos, y_pos for crop-aware adapters)
     */
    abstract public function resize(
        string $srcFile,
        string $dstFile,
        int $maxW,
        int $maxH,
        bool $setWatermark = false,
        array $crop_params = []
    ): bool;

    /**
     * Вычисляет размеры изображения, до которых нужно его пропорционально уменьшить, чтобы вписать в квадрат $maxW x $maxH
     *
     * @return array<int, float>|false [width, height] or false when source size is invalid
     */
    protected function calcContainSize(float|int $srcW, float|int $srcH, int $maxW = 0, int $maxH = 0)
    {
        if ($srcW == 0 || $srcH == 0) {
            return false;
        }

        $dstW = $srcW;
        $dstH = $srcH;

        if ($srcW > $maxW && $maxW > 0) {
            $dstH = $srcH * ($maxW / $srcW);
            $dstW = $maxW;
        }
        if ($dstH > $maxH && $maxH > 0) {
            $dstW = $dstW * ($maxH / $dstH);
            $dstH = $maxH;
        }
        return [$dstW, $dstH];
    }
}
