<?php


namespace Okay\Core;


use Okay\Core\TemplateConfig\FrontTemplateConfig;

class TemplateConfig
{
    
    private $frontTemplateConfig;
    
    public function __construct(FrontTemplateConfig $frontTemplateConfig)
    {
        $this->frontTemplateConfig = $frontTemplateConfig;
    }

    // Метод помечен как deprecated, здесь будет для обратной совместимости.
    // Использовать стоит Okay\Core\TemplateConfig\FrontTemplateConfig::getTheme()
    public function getTheme()
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use Okay\Core\TemplateConfig\FrontTemplateConfig::getTheme()', E_USER_DEPRECATED);
        return $this->frontTemplateConfig->getTheme();
    }
    
}
