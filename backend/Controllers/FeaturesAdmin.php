<?php


namespace Okay\Admin\Controllers;


use Aura\SqlQuery\QueryFactory;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Helpers\BackendFeaturesHelper;
use Okay\Admin\Requests\BackendFeaturesRequest;
use Okay\Entities\CategoriesEntity;
use Okay\Entities\FeaturesEntity;

class FeaturesAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendFeaturesRequest  $featuresRequest,
        CategoriesEntity        $categoriesEntity,
        BackendFeaturesHelper   $backendFeaturesHelper,
        BackendCategoriesHelper $backendCategoriesHelper
    ) {
        $filter = $backendFeaturesHelper->buildFeaturesFilter();
        $this->design->assign('current_limit', $filter['limit']);

        if ($this->request->method('post')) {
            $positions = $featuresRequest->postPositions();
            $backendFeaturesHelper->sortPositions($positions);

            $ids    = $featuresRequest->postCheck();
            $action = $featuresRequest->postAction();
            if (is_array($ids)) {
                switch($action) {
                    case 'set_in_filter': {
                        $backendFeaturesHelper->setInFilter($ids);
                        break;
                    }
                    case 'unset_in_filter': {
                        $backendFeaturesHelper->unsetInFilter($ids);
                        break;
                    }
                    case 'delete': {
                        $backendFeaturesHelper->delete($ids);
                        break;
                    }
                    case 'move_to_page': {
                        $targetPage = $featuresRequest->postTargetPage();
                        $backendFeaturesHelper->moveToPage($ids, $targetPage, $filter);
                        break;
                    }
                }
            }
        }

        $categoryId = $this->request->get('category_id', 'integer');
        $category   = $backendCategoriesHelper->getCategory((int) $categoryId);
        $categories = $backendCategoriesHelper->findCategories();

        if ($categoryId) {
            $filter['category_id'] = $category->id;
        }

        $featuresCount  = $backendFeaturesHelper->count($filter);
        $pagesCount     = $backendFeaturesHelper->countPages($filter);
        $keyword        = isset($filter['keyword']) ? $filter['keyword'] : '';

        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $featuresCount;
            $filter['page'] = $this->request->get('page');
        } else {
            $filter['page'] = min($filter['page'], $pagesCount);
        }
        
        $features       = $backendFeaturesHelper->findFeatures($filter, 'position_desc');

        $this->design->assign('features_count',  $featuresCount);
        $this->design->assign('pages_count',     $pagesCount);
        $this->design->assign('current_page',    $filter['page']);
        $this->design->assign('keyword',         $keyword);
        $this->design->assign('categories',      $categories);
        $this->design->assign('categories_tree', $categoriesEntity->getCategoriesTree());
        $this->design->assign('category',        $category);
        $this->design->assign('features',        $features);

        $this->response->setContent($this->design->fetch('features.tpl'));
    }
    
}
