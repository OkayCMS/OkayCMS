<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendBlogCategoriesHelper;
use Okay\Admin\Helpers\BackendBlogHelper;
use Okay\Admin\Requests\BackendBlogCategoriesRequest;
use Okay\Admin\Requests\BackendBlogRequest;

class BlogAdmin extends IndexAdmin
{
    public function fetch(
        BackendBlogRequest $blogRequest,
        BackendBlogHelper  $backendBlogHelper,
        BackendBlogCategoriesHelper $blogCategoriesHelper,
        BackendBlogCategoriesRequest $blogCategoriesRequest
    ) {
        if ($this->request->method('post')) {
            $ids = $blogRequest->postCheck();
            switch ($blogRequest->postAction()) {
                case 'disable': {
                    $backendBlogHelper->disable($ids);
                    break;
                }
                case 'enable': {
                    $backendBlogHelper->enable($ids);
                    break;
                }
                case 'delete': {
                    $backendBlogHelper->delete($ids);
                    break;
                }
            }
        }

        // Категории
        $categories = $blogCategoriesHelper->getCategoriesTree();
        $categoryId = $blogCategoriesRequest->getCategoryId();
        
        $filter     = $backendBlogHelper->buildPostsFilter();
        
        $posts      = $backendBlogHelper->findPosts($filter);
        $postsCount = $backendBlogHelper->getPostsCount($filter);

        $keyword  = isset($filter['keyword'])   ? $filter['keyword']   : '';

        $this->design->assign('category_id',    $categoryId);
        $this->design->assign('categories',     $categories);
        
        $this->design->assign('keyword',      $keyword);
        $this->design->assign('posts_count',  $postsCount);
        $this->design->assign('pages_count',  ceil($postsCount/$filter['limit']));
        $this->design->assign('current_page', $filter['page']);
        $this->design->assign('posts',        $posts);

        $this->response->setContent($this->design->fetch('blog.tpl'));
    }
    
}
