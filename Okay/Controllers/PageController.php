<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Entities\PagesEntity;

class PageController extends AbstractController
{

    /*Отображение страниц сайта*/
    public function render(PagesEntity $pagesEntity, $url)
    {
        $page = $pagesEntity->get($url);
        
        // Отображать скрытые страницы только админу
        if (empty($page) || (!$page->visible && empty($_SESSION['admin'])) || $url == '404') {
            return false;
        }
        
        //lastModify
        $this->response->setHeaderLastModify($page->last_modify);
        
        $this->design->assign('page', $page);
        $this->design->assign('canonical', Router::generateUrl('page', ['url' => $page->url], true));
        
        $this->response->setContent('page.tpl');
    }
    
}
