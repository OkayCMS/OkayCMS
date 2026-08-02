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
        string $url = ''
    ): false|null {

        $filter = $blogHelper->getPostsFilter();

        $author = $authorsEntity->findOne(['url' => $url]);
        /** @var (object{id: int|string, visible: mixed, socials: mixed, last_modify: mixed, url: string, name: string|null, description: string|null, meta_title: string|null, meta_keywords: string|null, meta_description: string|null}&\stdClass)|false $author */
        if ($author === false || (!$author->visible && empty($_SESSION['admin']))) {
            return false;
        }
        $author->socials = $authorsHelper->getSocials($author);

        $filter['author_id'] = $author->id;

        //lastModify
        $lastModify[] = $blogEntity->cols(['last_modify'])->order('last_modify_desc')->findOne($filter);
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

        $authorMetadataHelper->setUp($author, $this->design->getVar('is_all_pages'), $this->design->getVar('current_page_num'));
        $this->setMetadataHelper($authorMetadataHelper);

        $this->response->setContent('author.tpl');

        return null;
    }

    public function authorsList(
        AuthorsEntity $authorsEntity,
        BlogEntity $blogEntity,
        AuthorsHelper $authorsHelper,
        AuthorMetadataHelper $authorMetadataHelper
    ): false|null {

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

        return null;
    }
}
