<?php


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Design;
use Okay\Core\SmartyPlugins\Func;

class GetCaptcha extends Func
{

    protected $tag = 'get_captcha';
    
    private $design;
    
    /*public function __construct(Design $design)
    {
        $this->design = $design;
    }*/

    public function run($params, \Smarty_Internal_Template $smarty)
    {
        if(isset($params['var'])) {
            $number = 0;
            unset($_SESSION[$params['var']]);
            $total = rand(10,50);
            $secret = rand(1,10);
            $result[] = $total - $secret;
            $result[] = $total;
            $_SESSION[$params['var']] = $secret;
            $smarty->assign($params['var'], $result);
        } else {
            return false;
        }
    }
}