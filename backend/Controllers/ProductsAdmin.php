<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\ProductsEntity;
use Okay\Admin\Requests\BackendCategoriesRequest;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Admin\Helpers\BackendBrandsHelper;
use Okay\Admin\Helpers\BackendProductsHelper;
use Okay\Admin\Helpers\BackendVariantsHelper;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Helpers\BackendCurrenciesHelper;

class ProductsAdmin extends IndexAdmin
{
    
    public function fetch(
        ProductsEntity           $productsEntity,
        BackendCategoriesHelper  $backendCategoriesHelper,
        BackendBrandsHelper      $backendBrandsHelper,
        BackendProductsHelper    $backendProductsHelper,
        BackendVariantsHelper    $backendVariantsHelper,
        BackendCurrenciesHelper  $backendCurrenciesHelper,
        BackendCategoriesRequest $categoriesRequest,
        BackendProductsRequest   $productRequest
    ) {
        // Категории
        $categories = $backendCategoriesHelper->getCategoriesTree();
        $categoryId = $categoriesRequest->getCategoryId();

        // Бренды
        $brandsFilter = $backendBrandsHelper->prepareFilterForProductsAdmin($categoryId);
        $brands       = $backendBrandsHelper->findBrands($brandsFilter);
        $allBrands    = $backendBrandsHelper->findAllBrands();
        $brandId      = $this->request->get('brand_id', 'integer');

        // Фильтр
        $filter = $backendProductsHelper->buildFilter();

        // Обработка действий
        if ($this->request->method('post')) {

            // Сохранение цен и наличия
            $prices = $productRequest->postPrices();
            $stocks = $productRequest->postStocks();
            $backendVariantsHelper->updateStocksAndPrices($stocks, $prices);

            // Сортировка
            if (empty($backendProductsHelper->getProductsSortName())) {
                $positions = $productRequest->postPositions();
                list($ids, $positions) = $backendProductsHelper->sortPositions($positions);
                $backendProductsHelper->updatePositions($ids, $positions);
            }

            // Действия с выбранными
            $ids = $productRequest->postCheckedIds();
            if(!empty($ids)) {
                switch($this->request->post('action')) {
                    case 'disable': {
                        $backendProductsHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendProductsHelper->enable($ids);
                        break;
                    }
                    case 'set_featured': {
                        $backendProductsHelper->setFeatured($ids);
                        break;
                    }
                    case 'unset_featured': {
                        $backendProductsHelper->unsetFeatured($ids);
                        break;
                    }
                    case 'delete': {
                        $backendProductsHelper->delete($ids);
                        break;
                    }
                    case 'duplicate': {
                        $backendProductsHelper->duplicateProducts($ids);
                        break;
                    }
                    case 'move_to_page': {
                        $backendProductsHelper->moveToPage($ids, $filter);
                        break;
                    }
                    case 'add_second_category': {
                        $backendProductsHelper->actionAddSecondCategories($ids);
                        break;
                    }
                    case 'move_to_category': {
                        $backendProductsHelper->actionMoveToCategory($ids);
                        break;
                    }
                    case 'move_to_brand': {
                        $backendProductsHelper->actionMoveToBrand($ids);
                        $brandsFilter = $backendBrandsHelper->prepareFilterForProductsAdmin($categoryId);
                        $brands       = $backendBrandsHelper->findBrands($brandsFilter);
                        break;
                    }
                }
            }
        }

        $productsCount = $productsEntity->count($filter);
        if ($filter['limit'] > 0) {
            $pagesCount = ceil($productsCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }

        $products   = $backendProductsHelper->findProductsForProductsAdmin($filter, $backendProductsHelper->getProductsSortName());
        $currencies = $backendCurrenciesHelper->findAllCurrencies();

        $this->design->assign('category_id',    $categoryId);
        $this->design->assign('categories',     $categories);

        $this->design->assign('all_brands',     $allBrands);
        $this->design->assign('brand_id',       $brandId);
        $this->design->assign('brands',         $brands);

        $filter['page'] = min($filter['page'],       $pagesCount);
        $this->design->assign('products_count', $productsCount);
        $this->design->assign('pages_count',    $pagesCount);
        $this->design->assign('current_page',   $filter['page']);

        $this->design->assign('currencies',     $currencies);
        $this->design->assign('products',       $products);

        $this->design->assign('current_limit',  $filter['limit']);
        $this->design->assign('filter',         $filter['filter']);

        if (isset($filter['keyword'])) {
            $this->design->assign('keyword',    $filter['keyword']);
        }

        $this->response->setContent($this->design->fetch('products.tpl'));
    }

}
