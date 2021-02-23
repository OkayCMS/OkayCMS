<?php


namespace Okay\Controllers;


use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\TemplateConfig\JsConfig;

class DynamicJsController extends AbstractController
{
    
    public function getJs(
        FrontTemplateConfig $frontTemplateConfig,
        $fileId
    ) {
        $dynamicJsFile = "design/" . $frontTemplateConfig->getTheme() . "/html/scripts.tpl";

        $dynamicJs = '';
        
        if (is_file($dynamicJsFile)) {
            if (!empty($_SESSION['dynamic_js']['controller'])) {
                $this->design->assign('controller', $_SESSION['dynamic_js']['controller']);
            }
    
            if (isset($_SESSION['dynamic_js']['vars'])) {
                // Передаем глобальные переменные в шаблон
                foreach ($_SESSION['dynamic_js']['vars'] as $var => $value) {
                    $this->design->assign($var, $value);
                }
            }
    
            $dynamicJs = $this->design->fetch('scripts.tpl');
            $dynamicJs = preg_replace('~<script(.*?)>(.*?)</script>~is', '$2', $dynamicJs);

            if ($this->config->get('dev_mode') == true) {
                $dynamicJs .= '$(function() {
                    $(".fn_design_block_name").parent().addClass("design_block_parent_element");
                    $(".fn_design_block_name").on("mouseover", function () {
                        $(this).parent().addClass("focus");
                    });
                    $(".fn_design_block_name").on("mouseout", function () {
                        $(this).parent().removeClass("focus");
                    });
                    });';
            }
            
            $dynamicJs = JsConfig::minifyJs($dynamicJs);
        }
        
        $this->response->setContent($dynamicJs, RESPONSE_JAVASCRIPT);
    }
    
    public function getCommonJs(
        FrontTemplateConfig $frontTemplateConfig,
        $fileId
    ) {
        $commonJsFile = "design/" . $frontTemplateConfig->getTheme() . "/html/common_js.tpl";

        $commonJs = '';
        if (is_file($commonJsFile)) {
            if (!empty($_SESSION['common_js']['controller'])) {
                $this->design->assign('controller', $_SESSION['common_js']['controller']);
            }

            $this->design->assign('front_routes', $this->router->getFrontRoutes());
            
            if (isset($_SESSION['common_js']['vars'])) {
                // Передаем глобальные переменные в шаблон
                $jsVars = $_SESSION['common_js']['vars'];
                
                foreach ($jsVars as $var=>$value) {
                    $jsVars[$var] = json_encode($value);
                }
                
                $this->design->assign('common_js_vars', $jsVars);
                unset($_SESSION['common_js']);
            }

            $commonJs = $this->design->fetch('common_js.tpl');
            $commonJs = preg_replace('~<script(.*?)>(.*?)</script>~is', '$2', $commonJs);

            $commonJs = JsConfig::minifyJs($commonJs);
        }
        
        $this->response->setContent($commonJs, RESPONSE_JAVASCRIPT);
    }
}
