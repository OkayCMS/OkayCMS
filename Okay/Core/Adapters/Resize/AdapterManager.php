<?php


namespace Okay\Core\Adapters\Resize;

use Okay\Core\Adapters\AbstractAdapterManager;

class AdapterManager extends AbstractAdapterManager
{
    private $imageQuality;
    private $watermark;
    private $watermarkOffsetX;
    private $watermarkOffsetY;
    
    public function configure($watermark, $watermarkOffsetX, $watermarkOffsetY, $imageQuality = 80)
    {
        $this->imageQuality = $imageQuality;
        $this->watermark    = $watermark;
        $this->watermarkOffsetX = $watermarkOffsetX;
        $this->watermarkOffsetY = $watermarkOffsetY;
    }
    
    protected function createAdapter($adapterName)
    {
        $adapterClass = __NAMESPACE__.'\\'.$adapterName;
        $this->adapter = new $adapterClass($this->imageQuality, $this->watermark, $this->watermarkOffsetX, $this->watermarkOffsetY);
    }

}
