<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendBlogCategoriesHelper;
use Okay\Admin\Helpers\BackendBlogHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendBlogRequest;
use Okay\Entities\AuthorsEntity;
use Okay\Entities\BlogEntity;
use Okay\Entities\RouterCacheEntity;

class PostAdmin extends IndexAdmin
{
    
    public function fetch(
        BlogEntity            $blogEntity,
        BackendBlogRequest    $backendBlogRequest,
        BackendBlogHelper     $backendBlogHelper,
        BackendValidateHelper $backendValidateHelper,
        BackendBlogCategoriesHelper $blogCategoriesHelper,
        RouterCacheEntity $routerCacheEntity,
        AuthorsEntity $authorsEntity
    ) {

        /*Прием информации о записи*/
        if ($this->request->method('post')) {
            $post = $backendBlogRequest->postArticle();
            $postCategories = $backendBlogRequest->postCategories();

            $relatedProducts = $backendBlogRequest->postRelatedProducts();

            if ($error = $backendValidateHelper->getBlogValidateError($post)) {
                $this->design->assign('message_error', $error);
            } else {
                /*Добавление/Обновление записи*/
                if (empty($post->id)) {
                    $preparedPost = $backendBlogHelper->prepareAdd($post);
                    $post->id     = $backendBlogHelper->add($preparedPost);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($post->id);
                } else {
                    $preparedPost = $backendBlogHelper->prepareUpdate($post);
                    $backendBlogHelper->update($preparedPost->id, $post);

                    $routerCacheEntity->deleteByUrl(RouterCacheEntity::TYPE_POST, $post->url);
                    
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                $postCategories = $backendBlogHelper->prepareUpdatePostCategories($post, $postCategories);
                $backendBlogHelper->updatePostCategories($post, $postCategories);
                
                // Картинка
                if ($backendBlogRequest->postDeleteImage()) {
                    $backendBlogHelper->deleteImage($post);
                }

                if ($image = $backendBlogRequest->fileImage()) {
                    $backendBlogHelper->uploadImage($image, $post);
                }

                // Связанные товары
                $relatedProducts = $backendBlogHelper->prepareUpdateRelatedProducts($post, $relatedProducts);
                $backendBlogHelper->updateRelatedProducts($post, $relatedProducts);
                
                $this->postRedirectGet->redirect();
            }
        } else {
            $postId = $this->request->get('id', 'integer');
            $post   = $backendBlogHelper->getPost($postId);
        }

        $postCategories = $backendBlogHelper->findPostCategories($post);
        
        $relatedProducts = [];
        if (!empty($post->id)) {
            $relatedProducts = $backendBlogHelper->getRelatedProductsList(['post_id' => $post->id]);
        }

        $categoriesTree = $blogCategoriesHelper->getCategoriesTree();

        $authorsCount = $authorsEntity->count();
        $authors = $authorsEntity->find(['limit' => $authorsCount]);

        $this->design->assign('authors',    $authors);
        $this->design->assign('categories', $categoriesTree);
        $this->design->assign('post_categories',  $postCategories);
        $this->design->assign('related_products', $relatedProducts);
        $this->design->assign('post', $post);
        $this->response->setContent($this->design->fetch('post.tpl'));
    }
    
}
