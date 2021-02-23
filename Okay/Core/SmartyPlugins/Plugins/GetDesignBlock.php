<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\DesignBlocks;
use Okay\Core\Config;
use Okay\Core\Design;
use Okay\Core\SmartyPlugins\Func;

class GetDesignBlock extends Func
{

    protected $tag = 'get_design_block';
    
    private $designBlocks;
    private $design;
    private $config;
    
    public function __construct(DesignBlocks $designBlocks, Design $design, Config $config)
    {
        $this->designBlocks = $designBlocks;
        $this->design = $design;
        $this->config = $config;
    }

    public function run($params)
    {
        if (isset($params['vars'])) {
            foreach ($params['vars'] as $var => $value) {
                $this->design->assign($var, $value);
            }
        }

        $html = '';
        if ($this->config->get('dev_mode') == true) {
            $html .= '<div class="fn_design_block_name">' . $params['block'] . '</div>';
        }
        $html .= $this->designBlocks->getBlockHtml($params['block']);
        
        // Очистим все переменные, которые установили выше
        if (isset($params['vars'])) {
            foreach ($params['vars'] as $var => $value) {
                $this->design->assign($var, null);
            }
        }
        
        return $html;
    }
}