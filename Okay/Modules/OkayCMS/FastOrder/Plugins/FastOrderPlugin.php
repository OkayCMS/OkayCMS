<?php


namespace Okay\Modules\OkayCMS\FastOrder\Plugins;


use Okay\Core\Design;
use Okay\Core\SmartyPlugins\Func;

class FastOrderPlugin extends Func
{
    protected $tag = 'fast_order_btn';

    protected $design;

    public function __construct(Design $design)
    {
        $this->design = $design;
    }

    public function run($vars)
    {
        $this->design->assign('fast_order_product_name', $vars['product']->name);
        $this->design->assign('fastOrderProduct', $vars['product']);
        return $this->design->fetch('fast_order_btn.tpl');
    }
}