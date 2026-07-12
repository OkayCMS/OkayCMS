<?php

namespace Okay\Helpers;

use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Core\Translit;
use Okay\Entities\AuthorsEntity;
use Okay\Entities\BlogCategoriesEntity;
use Okay\Entities\BlogEntity;
use Okay\Entities\CommentsEntity;

/**
 * @phpstan-type BlogPostRow object{id: int|string, author_id: int|string, visible?: mixed, categories?: array<int|string, BlogCategoryRow>, author?: object, comments_count?: int|string}&\stdClass
 * @phpstan-type BlogCategoryRow object{id: int|string, visible?: mixed}&\stdClass
 * @phpstan-type BlogCategoryRelationRow object{post_id: int|string, category_id: int|string}&\stdClass
 * @phpstan-type CommentCountRow object{object_id: int|string, count: int|string}&\stdClass
 */
class BlogHelper implements GetListInterface
{
    private $entityFactory;
    private $authorsHelper;

    public function __construct(EntityFactory $entityFactory, AuthorsHelper $authorsHelper)
    {
        $this->entityFactory = $entityFactory;
        $this->authorsHelper = $authorsHelper;
    }

    public function getCurrentSort()
    {
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод генерирует оглавление
     *
     * @param $text
     * @param null $postUrl
     * @return mixed|void|null
     */
    public function getTableOfContent($text, $postUrl = null)
    {

        if ($postUrl === null) {
            $postUrl = Request::getRequestUri();
        }

        $tableOfContent = [];
        $items = [];
        preg_match_all("~<([hH]([1-6]))(.*?)>(.*?)</[hH]([1-6])>~", $text, $items);

        if (!empty($items[4])) {
            $parts = [];
            foreach ($items[4] as $key => $string) {
                $sourceHeader = $items[0][$key];

                $id = Translit::translit(strip_tags($string));
                $id = preg_replace('~^[^a-zA-Z]*(.+?)[^a-zA-Z0-9]*$~', '$1', $id);
                $anchorUrl = $postUrl . '#' . $id;

                // формируем массив где ключ оригинальный заголовок (H) значение заголовок со вставленным в него якорем
                $parts[$sourceHeader] = str_replace($string, '<a id="' . $id . '" href="' . $anchorUrl . '" class="fn_auto_navigation_anchor"></a>' . $string, $sourceHeader);

                // Если у заголовка есть свой клас, добавим наш клас к существующим
                if (preg_match("~.*?class=['\"](.*?)?['\"].*~", $items[3][$key], $headerAttr) && isset($headerAttr[1])) {
                    $headerClass = $headerAttr[1];
                    $parts[$sourceHeader] = str_replace($headerClass, "{$headerClass} fn_auto_navigation_header", $parts[$sourceHeader]);
                // Иначе добавляем только наш клас
                } else {
                    $parts[$sourceHeader] = str_replace("<{$items[1][$key]}", "<{$items[1][$key]} class=\"fn_auto_navigation_header\"", $parts[$sourceHeader]);
                }

                $tableOfContentItem['anchor_text']  = strip_tags($string);
                $tableOfContentItem['anchor_id']    = $id;
                $tableOfContentItem['url']          = $anchorUrl;
                $tableOfContentItem['header_level'] = $items[2][$key]; // Уровень заголовка, который поймали

                $tableOfContent[] = $tableOfContentItem;
            }

            $text = strtr($text, $parts);
        }

        return ExtenderFacade::execute(__METHOD__, [$text, $tableOfContent]);
    }

    /**
     * @inheritDoc
     * @param array<string, mixed> $filter
     * @param array<int, string>|false|null $excludedFields
     * @return array<int|string, BlogPostRow>
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {
        if ($excludedFields === null) {
            $excludedFields = $this->getExcludeFields();
        }

        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);

        // Исключаем колонки, которые нам не нужны
        if (is_array($excludedFields) && !empty($excludedFields)) {
            $blogEntity->cols(BlogEntity::getDifferentFields($excludedFields));
        }

        if ($sortName !== null) {
            $blogEntity->order($sortName, $this->getOrderPostsAdditionalData());
        }

        $posts = $blogEntity->mappedBy('id')->find($filter);
        /** @var array<int|string, BlogPostRow> $posts */

        $posts = $this->attachAuthor($posts);
        $posts = $this->attachCategories($posts);
        $posts = $this->attachCommentsCount($posts);

        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    /**
     * @param BlogPostRow|false $post
     * @return BlogPostRow|false
     */
    public function attachPostData($post)
    {
        if (empty($post->id)) {
            return ExtenderFacade::execute(__METHOD__, false);
        }
        $posts[$post->id] = $post;

        $posts = $this->attachAuthor($posts);
        $posts = $this->attachCategories($posts);

        return ExtenderFacade::execute(__METHOD__, reset($posts), func_get_args());
    }

    /**
     * @param array<int|string, BlogPostRow> $posts
     * @return array<int|string, BlogPostRow>
     */
    public function attachCategories(array $posts)
    {

        if (empty($posts)) {
            return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
        }

        $postsIds = array_keys($posts);
        /** @var list<int|string> $postsIds */

        /** @var BlogCategoriesEntity $blogCategoriesEntity */
        $blogCategoriesEntity = $this->entityFactory->get(BlogCategoriesEntity::class);

        $postsCategories = $blogCategoriesEntity->find(['post_id' => $postsIds]);
        /** @var array<int|string, BlogCategoryRow> $postsCategories */
        /** @var list<BlogCategoryRelationRow> $postCategoryRelations */
        $postCategoryRelations = $blogCategoriesEntity->getPostCategories($postsIds);
        foreach ($postCategoryRelations as $categoryRelation) {
            if (isset($posts[$categoryRelation->post_id]) && isset($postsCategories[$categoryRelation->category_id])) {
                $posts[$categoryRelation->post_id]->categories[$categoryRelation->category_id] = $postsCategories[$categoryRelation->category_id];
            }
        }

        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    /**
     * @param array<int|string, BlogPostRow> $posts
     * @return array<int|string, BlogPostRow>
     */
    public function attachAuthor(array $posts)
    {
        /** @var AuthorsEntity $authorsEntity */
        $authorsEntity = $this->entityFactory->get(AuthorsEntity::class);

        $authorsIds = [];
        foreach ($posts as $post) {
            $authorsIds[] = $post->author_id;
        }

        if (empty($authorsIds)) {
            return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
        }

        $authors = $authorsEntity->mappedBy('id')->find(['id' => $authorsIds]);

        foreach ($posts as $post) {
            if (isset($authors[$post->author_id])) {
                $author = $authors[$post->author_id];
                $author->socials = $this->authorsHelper->getSocials($author);

                $post->author = $author;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    /**
     * @param array<int|string, BlogPostRow> $posts
     * @return array<int|string, BlogPostRow>
     */
    public function attachCommentsCount(array $posts)
    {
        /** @var CommentsEntity $commentsEntity */
        $commentsEntity = $this->entityFactory->get(CommentsEntity::class);

        $postsIds = array_keys($posts);
        /** @var list<int|string> $postsIds */
        if (empty($postsIds)) {
            return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
        }

        // Получаем часть запроса с примененными фильтрами и немного его модифицируем
        $query = $commentsEntity->getSelect(['type' => 'post', 'object_id' => $postsIds]);
        $query->groupBy(['object_id'])->resetCols()->cols(["COUNT( DISTINCT id) as count", "object_id"]);

        /** @var list<CommentCountRow> $results */
        $results = $query->results();
        foreach ($results as $result) {
            if (isset($posts[$result->object_id])) {
                $posts[$result->object_id]->comments_count = $result->count;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    public function getExcludeFields()
    {
        $excludedFields = [
            'description',
            'meta_title',
            'meta_keywords',
            'meta_description',
        ];
        return ExtenderFacade::execute(__METHOD__, $excludedFields, func_get_args());
    }

    // Данный метод остаётся для обратной совместимости, но объявлен как deprecated, и будет удалён в будущих версиях
    /**
     * @param array<string, mixed> $filter
     * @return array<int|string, BlogPostRow>
     */
    public function getPostsList($filter = [], $sort = null)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use getList', E_USER_DEPRECATED);
        $posts = $this->getList($filter, $sort, false);
        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    public function getPostsFilter()
    {

        // Выбираем только видимые посты
        $filter['visible'] = 1;

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    /**
     * @param array<string, mixed> $filter
     */
    public function paginate($itemsPerPage, $currentPage, array &$filter, Design $design)
    {

        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);

        // Вычисляем количество страниц
        $productsCount = $blogEntity->count($filter);

        // Показать все страницы сразу
        $allPages = false;
        if ($currentPage == 'all') {
            $allPages = true;
            $itemsPerPage = $productsCount;
        }

        // Если не задана, то равна 1
        $currentPage = max(1, (int)$currentPage);
        $design->assign('current_page_num', $currentPage);
        $design->assign('is_all_pages', $allPages);

        $pagesNum = !empty($itemsPerPage) ? ceil($productsCount / $itemsPerPage) : 0;
        $design->assign('total_pages_num', $pagesNum);
        $design->assign('total_products_num', $productsCount);

        $filter['page'] = $currentPage;
        $filter['limit'] = $itemsPerPage;

        $result = true;
        if ($allPages === false && $currentPage > 1 && $currentPage > $pagesNum) {
            $result = false;
        }

        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    /**
     * @return array<string, mixed>
     */
    private function getOrderPostsAdditionalData()
    {
        $orderAdditionalData = [];
        return ExtenderFacade::execute(__METHOD__, $orderAdditionalData, func_get_args());
    }

    /**
     * Метод проверяет доступность поста для показа в контроллере
     * можно переопределить логику работы контроллера и отменить дальнейшие действия
     * для этого после реализации другой логики необходимо вернуть true из экстендера
     *
     * @param object{visible: mixed}|false $post
     * @return mixed
     */
    public function setPost($post)
    {
        if (empty($post) || (!$post->visible && empty($_SESSION['admin']))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод проверяет доступность категории блога для показа в контроллере
     * можно переопределить логику работы контроллера и отменить дальнейшие действия
     * для этого после реализации другой логики необходимо вернуть true из экстендера
     *
     * @param object{visible: mixed}|false $category
     * @return mixed
     */
    public function setBlogCategory($category)
    {
        if (empty($category) || (!$category->visible && empty($_SESSION['admin']))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}
