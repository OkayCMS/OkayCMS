<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Func;
use Okay\Core\TemplateConfig\BackendTemplateConfig;
use Okay\Core\TemplateConfig\FrontTemplateConfig;

class CssFile extends Func
{
    protected $tag = 'css';

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
        
        if (!empty($params['filename'])) {
            $filename = $params['filename'];
        } elseif (!empty($params['file'])) {
            $filename = $params['file'];
        }

        if (!empty($params['dir'])) {
            $dir = $params['dir'];
        }

        if (!empty($params['backend']) || !empty($params['admin'])) {
            return $this->backendTemplateConfig->compileIndividualCss($filename, $dir);
        }
        
        return $this->frontTemplateConfig->compileIndividualCss($filename, $dir);
    }
}