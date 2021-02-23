<?php


namespace Okay\Controllers;


use Okay\Core\Router;

class MainController extends AbstractController
{

    /*Отображение контента главной страницы*/
    public function render()
    {
        $this->design->assign('canonical', Router::generateUrl('main', [], true));
        $this->response->setContent('main.tpl');
    }
    
}
