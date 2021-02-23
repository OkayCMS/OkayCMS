<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Requests\BackendCategoriesRequest;
use Okay\Admin\Helpers\BackendCategoriesHelper;

class CategoriesAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendCategoriesHelper  $backendCategoriesHelper,
        BackendCategoriesRequest $categoriesRequest
    ) {
        if ($this->request->method('post')) {

            // Действия с выбранными
            $ids = $categoriesRequest->postCheckedIds();
            if (is_array($ids)) {
                switch($this->request->post('action')) {
                    case 'disable': {
                        $backendCategoriesHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendCategoriesHelper->enable($ids);
                        break;
                    }
                    case 'delete': {
                        $backendCategoriesHelper->delete($ids);
                        break;
                    }
                    case 'duplicate': {
                        $backendCategoriesHelper->duplicateCategories($ids);
                        $this->postRedirectGet->redirect();
                        break;
                    }
                }
            }
            
            // Сортировка
            $positions = $categoriesRequest->postPositions();
            list($ids, $positions) = $backendCategoriesHelper->sortPositions($positions);
            $backendCategoriesHelper->updatePositions($ids, $positions);
        }

        // Категории
        $categories      = $backendCategoriesHelper->getCategoriesTree();
        $categoriesCount = $backendCategoriesHelper->countAllCategories();

        $this->design->assign('categoriesCount', $categoriesCount);
        $this->design->assign('categories',      $categories);
        $this->response->setContent($this->design->fetch('categories.tpl'));
    }
    
    public function getSubCategories(
        BackendCategoriesHelper $categoriesHelper
    ) {

        $result = [];
        /*Выборка категории и её деток*/
        if ($this->request->get("category_id")) {
            $categoryId = $this->request->get("category_id", 'integer');
            $categories = $categoriesHelper->getCategory($categoryId);
            $this->design->assign('categories', $categories->subcategories);
            $result['success'] = true;
            $result['cats'] = $this->design->fetch("categories_ajax.tpl");
        } else {
            $result['success ']= false;
        }

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
    public function getAllCategories(
        BackendCategoriesHelper $categoriesHelper
    ) {

        $this->design->assign('categories', $categoriesHelper->getCategoriesTree());
        $this->design->assign('isAllCategories', true);
        $this->design->assign('level', 1);

        $result['success'] = true;
        $result['cats'] = $this->design->fetch("categories_ajax.tpl");
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }
    
}
