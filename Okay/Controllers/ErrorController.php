<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Entities\PagesEntity;

class ErrorController extends AbstractController
{
    
    public function pageNotFound(PagesEntity $pagesEntity)
    {
        $this->response->setStatusCode(404);
        
        $page = $pagesEntity->get('404');
        $this->design->assign('page', $page);
        $this->design->assign('noindex_nofollow', true);

        $this->design->assign('canonical', Router::generateUrl('page', ['url' => $page->url], true));
        $this->response->setContent('page.tpl');
    }
    
    public function siteDisabled()
    {
        $this->response->setStatusCode(503);
        $this->response->setContent('tech.tpl');
    }
    
}
