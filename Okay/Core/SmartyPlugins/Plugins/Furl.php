<?php 


namespace Okay\Core\SmartyPlugins\Plugins;


use Okay\Core\SmartyPlugins\Func;
use Okay\Core\Router;
use Okay\Helpers\FilterHelper;

class Furl extends Func
{
    private $router;
    private $filterHelper;

    public function __construct(Router $router, FilterHelper $filterHelper)
    {
        $this->router = $router;
        $this->filterHelper = $filterHelper;
    }

    public function run($params, \Smarty_Internal_Template $smarty)
    {

        if (is_array($params) && is_array(reset($params))) {
            $params = reset($params);
        }
        
        if (!empty($params['route'])) {
            $routeName = $params['route'];
        } else {
            $routeName = $this->router->getCurrentRouteName();
        }
        
        $isAbsolute = false;
        
        if (isset($params['absolute'])) {
            $isAbsolute = (bool)$params['absolute'];
            unset($params['absolute']);
        }
        
        $routeParams = $this->router->getCurrentRouteRequiredParams();
        $baseUrl = $this->router->generateUrl($routeName, $routeParams, $isAbsolute);
        $chpuUrl = $this->filterHelper->filterChpuUrl($params, [], $smarty);

        $chpuUrl = ltrim($chpuUrl, '/');
        if (!empty($chpuUrl)) {
            $baseUrl = rtrim($baseUrl, '/');

            return trim(strip_tags(htmlspecialchars($baseUrl . '/' . $chpuUrl)));
        }

        return trim(strip_tags(htmlspecialchars($baseUrl)));
    }
}