<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Func;
use Okay\Core\TemplateConfig\BackendTemplateConfig;
use Okay\Core\TemplateConfig\FrontTemplateConfig;

class JsFile extends Func
{
    protected $tag = 'js';

    private $frontTemplateConfig;
    private $backendTemplateConfig;
    
    public function __construct(FrontTemplateConfig $frontTemplateConfig, BackendTemplateConfig $backendTemplateConfig)
    {
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->backendTemplateConfig = $backendTemplateConfig;
    }

    public function run($params)
    {
        $filename = '';
        $dir = null;
        $defer = false;
        
        if (!empty($params['filename'])) {
            $filename = $params['filename'];
        } elseif (!empty($params['file'])) {
            $filename = $params['file'];
        }

        if (!empty($params['dir'])) {
            $dir = $params['dir'];
        }

        if (!empty($params['defer'])) {
            $defer = $params['defer'];
        }
        
        if (!empty($params['backend']) || !empty($params['admin'])) {
            return $this->backendTemplateConfig->compileIndividualJs($filename, $dir, $defer);
        }
        
        return $this->frontTemplateConfig->compileIndividualJs($filename, $dir, $defer);
    }
}