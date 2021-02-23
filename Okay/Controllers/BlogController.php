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

class BlogController extends AbstractController
{
    
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
        
        // Если не найден - ошибка
        if (empty($post) || (!$post->visible && empty($_SESSION['admin']))) {
            return false;
        }

        $this->setMetadataHelper($postMetadataHelper);
        
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
            $this->design->assign('category', $category);
        }

        $post = $blogHelper->attachPostData($post);

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
        
        $this->response->setContent('post.tpl');
    }
    
    public function fetchBlog(
        BlogEntity $blogEntity,
        BlogHelper $blogHelper,
        BlogCategoriesEntity $blogCategoriesEntity,
        BlogCategoryMetadataHelper $categoryMetadataHelper,
        $url = ''
    ) {

        $filter = $blogHelper->getPostsFilter();

        $category = null;
        if (!empty($url)) {
            if (!($category = $blogCategoriesEntity->findOne(['url' => $url])) || (!$category->visible && empty($_SESSION['admin']))) {
                return false;
            }
        }
        if (!empty($category)) {
            $filter['category_id'] = $category->children;
            $this->setMetadataHelper($categoryMetadataHelper);
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
        
        $this->response->setContent('blog.tpl');
    }

    public function rating(BlogEntity $blogEntity)
    {
        if (isset($_POST['id']) && is_numeric($_POST['rating'])) {
            $postId = intval(str_replace('post_', '', $_POST['id']));
            $rating = floatval($_POST['rating']);

            if (!isset($_SESSION['post_rating_ids'])) {
                $_SESSION['post_rating_ids'] = [];
            }
            if (!in_array($postId, $_SESSION['post_rating_ids'])) {
                $post = $blogEntity->cols([
                    'rating',
                    'votes',
                ])->get($postId);
                if(!empty($post)) {
                    $rate = ($post->rating * $post->votes + $rating) / ($post->votes + 1);

                    $blogEntity->update($postId, ['rating'=>$rate, 'votes' => ($post->votes + 1)]);

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
