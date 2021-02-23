<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Requests\BackendBlogCategoriesRequest;
use Okay\Admin\Helpers\BackendBlogCategoriesHelper;

class BlogCategoriesAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendBlogCategoriesHelper  $backendBlogCategoriesHelper,
        BackendBlogCategoriesRequest $categoriesRequest
    ) {
        if ($this->request->method('post')) {

            // Действия с выбранными
            $ids = $categoriesRequest->postCheckedIds();
            if (is_array($ids)) {
                switch($this->request->post('action')) {
                    case 'disable': {
                        $backendBlogCategoriesHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendBlogCategoriesHelper->enable($ids);
                        break;
                    }
                    case 'delete': {
                        $backendBlogCategoriesHelper->delete($ids);
                        break;
                    }
                    case 'duplicate': {
                        $backendBlogCategoriesHelper->duplicateCategories($ids);
                        $this->postRedirectGet->redirect();
                        break;
                    }
                }
            }
            
            // Сортировка
            $positions = $categoriesRequest->postPositions();
            list($ids, $positions) = $backendBlogCategoriesHelper->sortPositions($positions);
            $backendBlogCategoriesHelper->updatePositions($ids, $positions);
        }

        // Категории
        $categories      = $backendBlogCategoriesHelper->getCategoriesTree();
        $categoriesCount = $backendBlogCategoriesHelper->countAllCategories();

        $this->design->assign('categoriesCount', $categoriesCount);
        $this->design->assign('categories',      $categories);
        
        $this->response->setContent($this->design->fetch('blog_categories.tpl'));
    }
    
    public function getSubCategories(
        BackendBlogCategoriesHelper $categoriesHelper
    ) {

        $result = [];
        /*Выборка категории и её деток*/
        if ($this->request->get("category_id")) {
            $categoryId = $this->request->get("category_id", 'integer');
            $categories = $categoriesHelper->getCategory($categoryId);
            $this->design->assign('categories', $categories->subcategories);
            $result['success'] = true;
            $result['cats'] = $this->design->fetch("blog_categories_ajax.tpl");
        } else {
            $result['success ']= false;
        }

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
    public function getAllCategories(
        BackendBlogCategoriesHelper $categoriesHelper
    ) {

        $this->design->assign('categories', $categoriesHelper->getCategoriesTree());
        $this->design->assign('isAllCategories', true);
        $this->design->assign('level', 1);

        $result['success'] = true;
        $result['cats'] = $this->design->fetch("blog_categories_ajax.tpl");
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
}
