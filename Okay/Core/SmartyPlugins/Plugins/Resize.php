<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Image;
use Okay\Core\SmartyPlugins\Modifier;

class Resize extends Modifier
{

    /**
     * @var Image
     */
    private $image;
    
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function run($filename, $width = 0, $height = 0, $setWatermark = false, $resizedDir = null, $cropPositionX = null, $cropPositionY = null)
    {
        return $this->image->getResizeModifier($filename, $width, $height, $setWatermark, $resizedDir, $cropPositionX, $cropPositionY);
    }
}