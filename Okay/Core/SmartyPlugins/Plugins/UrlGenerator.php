<?php 


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\Router;
use Okay\Core\SmartyPlugins\Func;

class UrlGenerator extends Func
{
    protected $tag = 'url_generator';
    
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function run($params)
    {
        $routeName  = '';
        $isAbsolute = false;
        $langId     = null;

        if (isset($params['route'])) {
            $routeName = $params['route'];
        }
        if (isset($params['absolute'])) {
            $isAbsolute = (bool)$params['absolute'];
            unset($params['absolute']);
        }
        if (isset($params['lang_id'])) {
            $langId = (int) $params['lang_id'];
            unset($params['lang_id']);
        }
        unset($params['route']);
        
        return $this->router->generateUrl($routeName, $params, $isAbsolute, $langId);
    }
}