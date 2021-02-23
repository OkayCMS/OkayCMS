<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendBlogCategoriesRequest;
use Okay\Admin\Helpers\BackendBlogCategoriesHelper;
use Okay\Core\Routes\BlogCategoryRoute;
use Okay\Core\Routes\PostRoute;
use Okay\Entities\RouterCacheEntity;

class BlogCategoryAdmin extends IndexAdmin
{

    public function fetch(
        BackendBlogCategoriesRequest $blogCategoriesRequest,
        BackendBlogCategoriesHelper  $backendBlogCategoriesHelper,
        BackendValidateHelper    $backendValidateHelper,
        RouterCacheEntity        $routerCacheEntity
    ) {
        if ($this->request->method('post')) {
            $category = $blogCategoriesRequest->postCategory();

            if ($error = $backendValidateHelper->getBlogCategoryValidateError($category)) {
                $this->design->assign('message_error', $error);
            } else {
                if (empty($category->id)) {
                    // Добавление категории
                    $category     = $backendBlogCategoriesHelper->prepareAdd($category);
                    $category->id = $backendBlogCategoriesHelper->add($category);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($category->id);
                } else {

                    $categoryBeforeUpdate   = $backendBlogCategoriesHelper->getCategory($category->id);
                    
                    // Обновление категории
                    $category     = $backendBlogCategoriesHelper->prepareUpdate($category->id, $category);
                    $backendBlogCategoriesHelper->update($category->id, $category);

                    if ($categoryBeforeUpdate->url != $category->url || $categoryBeforeUpdate->parent_id != $category->parent_id) {
                        if (in_array($this->settings->get('blog_category_routes_template'), [BlogCategoryRoute::TYPE_NO_PREFIX_AND_PATH, BlogCategoryRoute::TYPE_PREFIX_AND_PATH])) {
                            $routerCacheEntity->deleteBlogCategoriesCache();
                        }
                        if (in_array($this->settings->get('post_routes_template'), [PostRoute::TYPE_NO_PREFIX_AND_PATH, PostRoute::TYPE_PREFIX_AND_PATH, PostRoute::TYPE_NO_PREFIX_AND_CATEGORY])) {
                            $routerCacheEntity->deleteBlogCache();
                        }
                    }
                    
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                // Удаление изображения
                $deleteImage = $blogCategoriesRequest->postDeleteImage();
                if (!empty($deleteImage)) {
                    $backendBlogCategoriesHelper->deleteCategoryImage($category);
                }

                // Загрузка изображения
                $image = $blogCategoriesRequest->fileImage();
                $image = $backendBlogCategoriesHelper->prepareUploadCategoryImage($category, $image);
                $backendBlogCategoriesHelper->uploadCategoryImage($category, $image);

                $this->postRedirectGet->redirect();
            }
        } else {
            $categoryId = $this->request->get('id', 'integer');
            $category   = $backendBlogCategoriesHelper->getCategory($categoryId);
        }

        $categories = $backendBlogCategoriesHelper->getCategoriesTree();

        $this->design->assign('category',   $category);
        $this->design->assign('categories', $categories);
        $this->response->setContent($this->design->fetch('blog_category.tpl'));
    }
}
