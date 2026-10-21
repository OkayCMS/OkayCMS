<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Config;
use Okay\Core\Design;
use Okay\Core\Settings;
use Okay\Core\SmartyPlugins\Func;

class BackendCompactProductList extends Func
{

    protected $tag = 'backend_compact_product_list';
    
    private $design;
    private $config;
    private $settings;
    
    public function __construct(Design $design, Config $config, Settings $settings)
    {
        $this->design = $design;
        $this->config = $config;
        $this->settings = $settings;
    }

    public function run($params)
    {
        $isUseModuleDir = $this->design->isUseModuleDir();
        
        $this->design->useDefaultDir();
        $this->design->assign('config',      $this->config);
        $this->design->assign('settings',    $this->settings);
        $this->design->assign('title',       $params['title'] ?? null);
        $this->design->assign('label',       $params['label'] ?? null);
        $this->design->assign('placeholder', $params['placeholder'] ?? null);
        $this->design->assign('name',        $params['name'] ?? null);
        $this->design->assign('products',    $params['products'] ?? null);
        $this->design->assign('filter',      $params['filter'] ?? null);
        $html = $this->design->fetch('components/compact_product_list.tpl');
        
        if ($isUseModuleDir === true) {
            $this->design->useModuleDir();
        }
        return $html;
    }
}