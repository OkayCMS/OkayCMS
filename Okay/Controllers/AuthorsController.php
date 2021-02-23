<?php


namespace Okay\Controllers;


use Okay\Core\Router;
use Okay\Entities\AuthorsEntity;
use Okay\Entities\BlogEntity;
use Okay\Helpers\AuthorsHelper;
use Okay\Helpers\BlogHelper;
use Okay\Helpers\MetadataHelpers\AuthorMetadataHelper;

class AuthorsController extends AbstractController
{

    public function render(
        AuthorsEntity $authorsEntity,
        AuthorsHelper $authorsHelper,
        BlogEntity $blogEntity,
        BlogHelper $blogHelper,
        AuthorMetadataHelper $authorMetadataHelper,
        $url = ''
    ) {

        $filter = $blogHelper->getPostsFilter();

        $author = $authorsEntity->findOne(['url' => $url]);
        $author->socials = $authorsHelper->getSocials($author);
        if (empty($author) || (!$author->visible && empty($_SESSION['admin']))) {
            return false;
        }

        $this->setMetadataHelper($authorMetadataHelper);
        
        $filter['author_id'] = $author->id;

        //lastModify
        $lastModify[] = $blogEntity->cols(['last_modify'])->order('last_modify_desc')->findOne($filter);
        if (!empty($category)) {
            $lastModify[] = $category->last_modify;
        }
        if ($this->page) {
            $lastModify[] = $this->page->last_modify;
        }
        $this->response->setHeaderLastModify(max($lastModify));

        $paginate = $blogHelper->paginate(
            $this->settings->get('posts_num'),
            $this->request->get('page'),
            $filter,
            $this->design
        );

        if (!$paginate) {
            return false;
        }

        // Посты
        $currentSort = $blogHelper->getCurrentSort();
        $posts = $blogHelper->getList($filter, $currentSort);

        // Передаем в шаблон
        $this->design->assign('posts', $posts);
        $this->design->assign('author', $author);

        $this->design->assign('canonical', Router::generateUrl('author', ['url' => $author->url], true));

        $this->response->setContent('author.tpl');
    }
    
    public function authorsList(
        AuthorsEntity $authorsEntity,
        BlogEntity $blogEntity,
        AuthorsHelper $authorsHelper,
        AuthorMetadataHelper $authorMetadataHelper
    ) {

        $filter = $authorsHelper->getAuthorsFilter();
        
        $paginate = $authorsHelper->paginate(
            $this->settings->get('posts_num'),
            $this->request->get('page'),
            $filter,
            $this->design
        );

        if (!$paginate) {
            return false;
        }

        $currentSort = $authorsHelper->getCurrentSort();
        
        // Авторы
        $authors = $authorsHelper->getList($filter, $currentSort);

        // Передаем в шаблон
        $this->design->assign('authors', $authors);

        $this->design->assign('canonical', Router::generateUrl('authors', [], true));

        $this->response->setContent('authors.tpl');
    }

}
