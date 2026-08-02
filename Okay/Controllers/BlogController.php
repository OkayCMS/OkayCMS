<?php

namespace Okay\Controllers;

use Okay\Core\Router;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\BlogEntity;
use Okay\Helpers\BlogHelper;
use Okay\Helpers\CommentsHelper;
use Okay\Helpers\MetadataHelpers\BlogCategoryMetadataHelper;
use Okay\Helpers\MetadataHelpers\PostMetadataHelper;
use Okay\Helpers\RelatedProductsHelper;
use Okay\Helpers\ValidateHelper;

class BlogController extends AbstractController
{
    /**
     * @param string $url
     */
    public function fetchPost(
        BlogEntity $blogEntity,
        RelatedProductsHelper $relatedProductsHelper,
        BlogCategoriesEntity $blogCategoriesEntity,
        CommentsHelper $commentsHelper,
        PostMetadataHelper $postMetadataHelper,
        BlogHelper $blogHelper,
        $url
    ) {
        $post = $blogEntity->findOne(['url' => $url]);
        /** @var (object{id: int, author_id: int|string, visible: mixed, last_modify: mixed, main_category_id?: int|string|null, show_table_content?: bool|int|string, description?: string|null, date: string, url: string}&\stdClass)|false $post */

        //метод можно расширять и отменить либо переопределить дальнейшую логику работы контроллера
        if (($setPost = $blogHelper->setPost($post)) !== null) {
            return $setPost;
        }
        /** @var object{id: int, author_id: int|string, last_modify: mixed, main_category_id?: int|string|null, show_table_content?: bool|int|string, description?: string|null, date: string, url: string}&\stdClass $post */

        $this->response->setHeaderLastModify($post->last_modify);

        // Комментарии к посту
        $commentsHelper->addCommentProcedure('post', $post->id);
        $commentsFilter = $commentsHelper->getCommentsFilter('post', $post->id);
        $commentsSort = $commentsHelper->getCurrentSort();
        $comments = $commentsHelper->getList($commentsFilter, $commentsSort);
        $comments = $commentsHelper->attachAnswers($comments);
        $this->design->assign('comments', $comments);

        // Связанные товары
        $relatedProducts = $relatedProductsHelper->getRelatedProductsList($blogEntity, ['post_id' => $post->id]);
        $this->design->assign('related_products', $relatedProducts);

        if (!empty($post->main_category_id)) {
            $category = $blogCategoriesEntity->findOne(['id' => $post->main_category_id]);
            /** @var (object{id: int|string}&\stdClass)|false $category */
            $this->design->assign('category', $category);
        }

        $post = $blogHelper->attachPostData($post);
        /** @var object{id: int, show_table_content?: bool|int|string, name: string|null, annotation: string|null, description: string|null, meta_title: string|null, meta_keywords: string|null, meta_description: string|null, date: string, url: string}&\stdClass $post */

        if ($post->show_table_content && !empty($post->description)) {
            $result = $blogHelper->getTableOfContent($post->description);
            $post->description = $result[0];

            // Выводим оглавление только если там более трех пунктов
            if (count($result[1]) > 3) {
                $this->design->assign('table_of_content', $result[1]);
            }
        }

        $this->design->assign('post', $post);

        // Соседние записи
        if (!empty($category)) {
            $neighborsProducts = $blogEntity->getNeighborsPosts($category->id, $post->date);
            $this->design->assign('next_post', $neighborsProducts['next']);
            $this->design->assign('prev_post', $neighborsProducts['prev']);
        }

        $this->design->assign('canonical', Router::generateUrl('post', ['url' => $post->url], true));

        $postMetadataHelper->setUp($post);
        $this->setMetadataHelper($postMetadataHelper);

        $this->response->setContent('post.tpl');
    }

    /**
     * @param string $url
     */
    public function fetchBlog(
        BlogEntity $blogEntity,
        BlogHelper $blogHelper,
        BlogCategoriesEntity $blogCategoriesEntity,
        BlogCategoryMetadataHelper $categoryMetadataHelper,
        $url
    ) {

        $filter = $blogHelper->getPostsFilter();

        $category = null;

        $prefixRoute = $this->settings->get('all_blog_routes_template__default');
        if (empty($prefix)) {
            $prefixRoute = 'all-posts';
        }

        if (!empty($url) && ($url != $prefixRoute)) {
            $category = $blogCategoriesEntity->findOne(['url' => $url]);
            /** @var (object{id: int|string, visible: mixed, children: mixed, last_modify: mixed, url: string, name: string|null, name_h1: string|null, annotation: string|null, description: string|null, meta_title: string|null, meta_keywords: string|null, meta_description: string|null}&\stdClass)|false $category */
            if (($setCategory = $blogHelper->setBlogCategory($category)) !== null) {
                return $setCategory;
            }
            /** @var object{id: int|string, children: mixed, last_modify: mixed, url: string, name: string|null, name_h1: string|null, annotation: string|null, description: string|null, meta_title: string|null, meta_keywords: string|null, meta_description: string|null}&\stdClass $category */
        }

        if (!empty($category)) {
            $filter['category_id'] = $category->children;
            $this->design->assign('category', $category);
        }

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

        if (!empty($category)) {
            $canonical = Router::generateUrl('blog_category', ['url' => $category->url], true);
        } else {
            $canonical = Router::generateUrl('blog', [], true);
        }

        if (!empty($currentSort)) {
            $this->design->assign('noindex_follow', true);
        }

        $this->design->assign('canonical', $canonical);

        if (!empty($category)) {
            $categoryMetadataHelper->setUp($category, $this->design->getVar('is_all_pages'), $this->design->getVar('current_page_num'));
            $this->setMetadataHelper($categoryMetadataHelper);
        }

        $this->response->setContent('blog.tpl');
    }

    public function rating(BlogEntity $blogEntity, ValidateHelper $validateHelper)
    {
        if (!$this->request->isPost()) {
            $this->response->setStatusCode(405);
            $this->response->setContent(json_encode(-1), RESPONSE_JSON);
            return;
        }

        if ($validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token')) !== null) {
            $this->response->setStatusCode(403);
            $this->response->setContent(json_encode(-1), RESPONSE_JSON);
            return;
        }

        $ratingValue = $this->request->post('rating');
        if (($postInputId = $this->request->post('id')) && is_numeric($ratingValue)) {
            $postId = intval(str_replace('post_', '', (string)$postInputId));
            $rating = floatval($ratingValue);

            if (!isset($_SESSION['post_rating_ids'])) {
                $_SESSION['post_rating_ids'] = [];
            }
            if (!in_array($postId, $_SESSION['post_rating_ids'])) {
                $post = $blogEntity->cols([
                    'rating',
                    'votes',
                ])->get($postId);
                if (!empty($post)) {
                    $rate = ($post->rating * $post->votes + $rating) / ($post->votes + 1);

                    $blogEntity->update($postId, ['rating' => $rate, 'votes' => ($post->votes + 1)]);

                    $_SESSION['post_rating_ids'][] = $postId;
                    $this->response->setContent(json_encode($rate), RESPONSE_JSON);
                } else {
                    $this->response->setContent(json_encode(-1), RESPONSE_JSON);
                }
            } else {
                $this->response->setContent(json_encode(0), RESPONSE_JSON);
            }
        } else {
            $this->response->setContent(json_encode(-1), RESPONSE_JSON);
        }
    }
}
