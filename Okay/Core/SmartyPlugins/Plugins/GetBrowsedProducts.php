<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\BrowsedProducts;
use Okay\Core\SmartyPlugins\Func;

class GetBrowsedProducts extends Func
{

    protected $tag = 'get_browsed_products';
    
    /**
     * @var BrowsedProducts
     */
    private $browsedProducts;

    
    public function __construct(BrowsedProducts $browsedProducts)
    {
        $this->browsedProducts = $browsedProducts;
    }

    public function run($params, \Smarty_Internal_Template $smarty)
    {
        $smarty->assign($params['var'], $this->browsedProducts->get($params['limit']));
    }
}