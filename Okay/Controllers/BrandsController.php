<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Helpers\BrandsHelper;

class BrandsController extends AbstractController
{

    /*Отображение страницы всех брендов*/
    public function render(BrandsHelper $brandsHelper)
    {
        
        $filter = ['visible'=>1];
        $filter = $brandsHelper->getBrandsFilter($filter);

        $currentSort = $brandsHelper->getCurrentSort();
        
        /*Выбираем все бренды*/
        $brands = $brandsHelper->getList($filter, $currentSort);
        $this->design->assign('brands', $brands);

        $this->design->assign('canonical', Router::generateUrl('brands', [], true));

        $this->response->setContent('brands.tpl');
    }
    
}
