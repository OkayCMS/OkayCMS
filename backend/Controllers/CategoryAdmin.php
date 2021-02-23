<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Core\Entity\UrlUniqueValidator;
use Okay\Core\Routes\CategoryRoute;
use Okay\Core\Routes\ProductRoute;
use Okay\Entities\CategoriesEntity;
use Okay\Admin\Requests\BackendCategoriesRequest;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Entities\RouterCacheEntity;

class CategoryAdmin extends IndexAdmin
{

    public function fetch(
        BackendCategoriesRequest $categoriesRequest,
        BackendCategoriesHelper  $backendCategoriesHelper,
        BackendValidateHelper    $backendValidateHelper,
        RouterCacheEntity        $routerCacheEntity
    ) {
        if ($this->request->method('post')) {
            $category = $categoriesRequest->postCategory();

            if ($error = $backendValidateHelper->getCategoryValidateError($category)) {
                $this->design->assign('message_error', $error);
            } else {
                if (empty($category->id)) {
                    // Добавление категории
                    $category     = $backendCategoriesHelper->prepareAdd($category);
                    $category->id = $backendCategoriesHelper->add($category);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($category->id);
                } else {

                    $categoryBeforeUpdate   = $backendCategoriesHelper->getCategory($category->id);
                    
                    // Обновление категории
                    $category     = $backendCategoriesHelper->prepareUpdate($category->id, $category);
                    $backendCategoriesHelper->update($category->id, $category);

                    if ($categoryBeforeUpdate->url != $category->url || $categoryBeforeUpdate->parent_id != $category->parent_id) {
                        if (in_array($this->settings->get('category_routes_template'), [CategoryRoute::TYPE_NO_PREFIX_AND_PATH, CategoryRoute::TYPE_PREFIX_AND_PATH])) {
                            $routerCacheEntity->deleteCategoriesCache();
                        }
                        if (in_array($this->settings->get('product_routes_template'), [ProductRoute::TYPE_NO_PREFIX_AND_PATH, ProductRoute::TYPE_PREFIX_AND_PATH, ProductRoute::TYPE_NO_PREFIX_AND_CATEGORY])) {
                            $routerCacheEntity->deleteProductsCache();
                        }
                    }
                    
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                // Удаление изображения
                $deleteImage = $categoriesRequest->postDeleteImage();
                if (!empty($deleteImage)) {
                    $backendCategoriesHelper->deleteCategoryImage($category);
                }

                // Загрузка изображения
                $image = $categoriesRequest->fileImage();
                $image = $backendCategoriesHelper->prepareUploadCategoryImage($category, $image);
                $backendCategoriesHelper->uploadCategoryImage($category, $image);

                $this->postRedirectGet->redirect();
            }
        } else {
            $categoryId = $this->request->get('id', 'integer');
            $category   = $backendCategoriesHelper->getCategory($categoryId);
        }

        $categories = $backendCategoriesHelper->getCategoriesTree();

        $this->design->assign('category',   $category);
        $this->design->assign('categories', $categories);
        $this->response->setContent($this->design->fetch('category.tpl'));
    }
}
