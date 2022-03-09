<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Image;
use Okay\Core\SmartyPlugins\Modifier;

class Webp extends Modifier
{
    /** @var Image */
    private $image;
    
    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function run($filename)
    {
        return $this->image->convertFilenameToWebp($filename);
    }
}