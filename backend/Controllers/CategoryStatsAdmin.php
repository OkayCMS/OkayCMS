<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendBrandsHelper;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Helpers\BackendCategoryStatsHelper;
use Okay\Entities\CategoriesEntity;

class CategoryStatsAdmin extends IndexAdmin
{
    
    public $total_price;
    public $total_amount;
    
    public function fetch(
        CategoriesEntity           $categoriesEntity,
        BackendCategoryStatsHelper $backendCategoryStatsHelper,
        BackendBrandsHelper        $backendBrandsHelper,
        BackendCategoriesHelper    $backendCategoriesHelper
    ) {
        if ($brandId = $this->request->get('brand','integer')) {
            $brand   = $backendBrandsHelper->getBrand((int) $brandId);
            $this->design->assign('brand', $brand);
        }

        if ($categoryId = $this->request->get('category','integer')) {
            $category   = $backendCategoriesHelper->getCategory((int) $categoryId);
            $brands     = $backendBrandsHelper->findBrandsByCategory($category);
            $this->design->assign('category', $category);
            $this->design->assign('brands',   $brands);
        }

        $categories = $categoriesEntity->getCategoriesTree();

        $filter = $backendCategoryStatsHelper->buildFilter();

        if (isset($filter['date_from'])) {
            $this->design->assign('date_from', $filter['date_from']);
        }

        if (isset($filter['date_to'])) {
            $this->design->assign('date_to', $filter['date_to']);
        }

        $categories_list = $backendCategoryStatsHelper->getStatistic($filter);
        $this->design->assign('categories_list', $categories_list);
        $this->design->assign('categories',      $categories);
        $this->design->assign('total_price',     $this->total_price);
        $this->design->assign('total_amount',    $this->total_amount);

        $this->response->setContent($this->design->fetch('category_stats.tpl'));
    }

}
