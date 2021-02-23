<?php 


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Modifier;
use Okay\Core\Config;

class Token extends Modifier
{
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function run($text) 
    {
        return $this->config->token($text);
    }
}