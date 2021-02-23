<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Config;
use Okay\Core\SmartyPlugins\Modifier;

class ReadSvg extends Modifier
{

    protected $tag = 'read_svg';
    
    /**
     * @var Config
     */
    private $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function run($filename, $resizedDir = null)
    {
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) != 'svg') {
            return '';
        }
        
        if (empty($resizedDir)) {
            $resizedDir = $this->config->get('resized_images_dir');
        }
        
        $file = $this->config->get('root_dir') . $resizedDir . $filename;
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        
        return '';
    }
}