<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Func;
use Okay\Core\TemplateConfig\FrontTemplateConfig;

class GetTheme extends Func
{
    protected $tag = 'get_theme';

    private $frontTemplateConfig;
    
    public function __construct(FrontTemplateConfig $frontTemplateConfig)
    {
        $this->frontTemplateConfig = $frontTemplateConfig;
    }

    public function run()
    {
        return $this->frontTemplateConfig->getTheme();
    }
}