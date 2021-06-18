<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Entities\PagesEntity;
use Okay\Helpers\PagesHelper;

class PageController extends AbstractController
{

    /*Отображение страниц сайта*/
    public function render(
        PagesEntity $pagesEntity,
        PagesHelper $pagesHelper,
        $url
    ) {
        $page = $pagesEntity->get($url);

        //метод можно расширять и отменить либо переопределить дальнейшую логику работы контроллера
        if (($setPage = $pagesHelper->setPage($page, $url)) !== null) {
            return $setPage;
        }
        
        //lastModify
        $this->response->setHeaderLastModify($page->last_modify);
        
        $this->design->assign('page', $page);
        $this->design->assign('canonical', Router::generateUrl('page', ['url' => $page->url], true));
        
        $this->response->setContent('page.tpl');
    }
}
