<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Notify;
use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Settings;
use Okay\Entities\BlogEntity;
use Okay\Entities\CommentsEntity;
use Okay\Entities\ProductsEntity;

class BackendCommentsHelper
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var CommentsEntity
     */
    private $commentsEntity;

    /**
     * @var ProductsEntity
     */
    private $productsEntity;

    /**
     * @var BlogEntity
     */
    private $blogEntity;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var Notify
     */
    private $notify;

    public function __construct(
        Request       $request,
        EntityFactory $entityFactory,
        Settings      $settings,
        Notify        $notify
    ){
        $this->request        = $request;
        $this->commentsEntity = $entityFactory->get(CommentsEntity::class);
        $this->productsEntity = $entityFactory->get(ProductsEntity::class);
        $this->blogEntity     = $entityFactory->get(BlogEntity::class);
        $this->settings       = $settings;
        $this->notify         = $notify;
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['comments_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['comments_num_admin'])) {
            $filter['limit'] = $_SESSION['comments_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        // Выбираем главные сообщения
        $filter['has_parent'] = false;

        // Тип
        $type = $this->request->get('type', 'string');
        if ($type) {
            $filter['type'] = $type;
        }

        // Сортировка по статусу
        $status = $this->request->get('status', 'string');
        if ($status == 'approved') {
            $filter['approved'] = 1;
        } elseif ($status == 'unapproved') {
            $filter['approved'] = 0;
        }

        // Поиск
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        // Отображение
        $commentsCount = $this->commentsEntity->count($filter);
        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $commentsCount;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function prepareCommentAnswer()
    {
        $parentId      = $this->request->post('parent_id', 'integer');
        $parentComment = $this->commentsEntity->get($parentId);

        $comment = new \stdClass();
        $comment->parent_id = $parentComment->id;
        $comment->type      = $parentComment->type;
        $comment->object_id = $parentComment->object_id;
        $comment->text      = $this->request->post('text');
        $comment->name      = ($this->settings->get('notify_from_name') ? $this->settings->get('notify_from_name') : 'Administrator');
        $comment->approved  = 1;

        return ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }

    public function addCommentAnswer($comment)
    {
        $insertId = $this->commentsEntity->add($comment);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function notifyCommentAnswerToUser($comment)
    {
        $parentComment = $this->commentsEntity->get((int) $comment->parent_id);
        if (!empty($parentComment->email) && $comment->id) {
            $this->notify->emailCommentAnswerToUser($comment->id);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete($ids)
    {
        $this->commentsEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function approve($ids)
    {
        $this->commentsEntity->update($ids, array('approved'=>1));
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function attachTargetEntitiesToComments($comments)
    {
        $productsIds = [];
        $postsIds    = [];
        foreach ($comments as $comment) {
            if ($comment->type == 'product') {
                $productsIds[] = $comment->object_id;
            }
            if ($comment->type == 'post') {
                $postsIds[] = $comment->object_id;
            }
        }

        $products = [];
        if (!empty($productsIds)) {
            foreach ($this->productsEntity->find(['id' => $productsIds, 'limit' => count($productsIds)]) as $p) {
                $products[$p->id] = $p;
            }
        }

        $posts = [];
        if (!empty($postsIds)) {
            foreach ($this->blogEntity->find(['id' => $postsIds]) as $p) {
                $posts[$p->id] = $p;
            }
        }

        foreach ($comments as $comment) {
            if ($comment->type == 'product' && isset($products[$comment->object_id])) {
                $comment->product = $products[$comment->object_id];
            }
            if ($comment->type == 'post' && isset($posts[$comment->object_id])) {
                $comment->post = $posts[$comment->object_id];
            }
        }

        return ExtenderFacade::execute(__METHOD__, $comments, func_get_args());
    }

    public function findComments($filter)
    {
        $comments = $this->commentsEntity->mappedBy('id')->find($filter);
        return ExtenderFacade::execute(__METHOD__, $comments, func_get_args());
    }

    public function findAnswers($comments)
    {
        $commentsIds = [];
        foreach ($comments as $comment) {
            $commentsIds[] = $comment->id;
        }

        $answers = [];
        if (!empty($commentsIds)) {
            foreach ($this->commentsEntity->find(['parent_id' => $commentsIds]) as $c) {
                $answers[$c->parent_id][] = $c;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $answers, func_get_args());
    }

    public function count($filter)
    {
        $obj = new \ArrayObject($filter);
        $filter = $obj->getArrayCopy();

        $countFilter = $filter;
        unset($countFilter['limit']);
        $count = $this->commentsEntity->count($countFilter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function matchStatus($filter)
    {
        $status = '';
        if (isset($filter['approved'])) {
            $status = 'approved';
        } elseif (isset($filter['unapproved'])) {
            $status = 'unapproved';
        }

        return ExtenderFacade::execute(__METHOD__, $status, func_get_args());
    }
}