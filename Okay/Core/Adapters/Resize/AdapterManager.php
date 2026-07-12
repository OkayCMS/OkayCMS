<?php

namespace Okay\Core\Adapters\Resize;

use Okay\Core\Adapters\AbstractAdapterManager;

class AdapterManager extends AbstractAdapterManager
{
    private int $imageQuality = 80;

    private ?string $watermark = null;

    private int $watermarkOffsetX = 0;

    private int $watermarkOffsetY = 0;

    public function configure(
        ?string $watermark,
        int|string $watermarkOffsetX,
        int|string $watermarkOffsetY,
        int|string $imageQuality = 80
    ): void {
        $this->imageQuality = (int) $imageQuality;
        $this->watermark = $watermark;
        $this->watermarkOffsetX = (int) $watermarkOffsetX;
        $this->watermarkOffsetY = (int) $watermarkOffsetY;
    }

    /**
     * @param string $adapterName Short adapter class name (e.g. Intervention, GD, Imagick)
     */
    protected function createAdapter($adapterName): void
    {
        $adapterClass = __NAMESPACE__ . '\\' . $adapterName;
        $this->adapter = new $adapterClass($this->imageQuality, $this->watermark, $this->watermarkOffsetX, $this->watermarkOffsetY);
    }
}
