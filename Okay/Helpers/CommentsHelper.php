<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Notify;
use Okay\Core\Response;
use Okay\Entities\BlogEntity;
use Okay\Entities\CommentsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Requests\CommonRequest;

class CommentsHelper implements GetListInterface
{

    private $entityFactory;
    private $commentsRequest;
    private $validateHelper;
    private $design;
    private $notify;
    private $languages;
    private $user;

    public function __construct(
        EntityFactory  $entityFactory,
        CommonRequest  $commentsRequest,
        ValidateHelper $validateHelper,
        Design         $design,
        Notify         $notify,
        MainHelper     $mainHelper,
        Languages      $languages
    ) {
        $this->entityFactory = $entityFactory;
        $this->commentsRequest = $commentsRequest;
        $this->validateHelper = $validateHelper;
        $this->design = $design;
        $this->notify = $notify;
        $this->languages = $languages;
        $this->user = $mainHelper->getCurrentUser();
    }

    /**
     * Метод возвращает комментарии для товаров или записей блога
     *
     * Данный метод остаётся для обратной совместимости, но объявлен как deprecated, и будет удалён в будущих версиях
     * 
     * @param string $objectType
     * @param int $objectId
     * @return array
     * @throws \Exception
     */
    public function getCommentsList($objectType, $objectId)
    {
        trigger_error('Method ' . __METHOD__ . ' is deprecated. Please use getList', E_USER_DEPRECATED);
        $filter = $this->getCommentsFilter($objectType, $objectId);
        $sortName = $this->getCurrentSort();
        $comments = $this->getList($filter, $sortName);
        $comments = $this->attachAnswers($comments);

        return ExtenderFacade::execute(__METHOD__, $comments, func_get_args());
    }

    /**
     * @param string $objectType
     * @param int $objectId
     * @return array
     */
    public function getCommentsFilter($objectType, $objectId)
    {
        $filter = [
            'has_parent' => false,
            'type' => $objectType,
            'object_id' => $objectId,
            'approved' => 1,
            'ip' => $_SERVER['REMOTE_ADDR']
        ];

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    /**
     * @return mixed
     */
    public function getCurrentSort()
    {
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param array $filter
     * @param string $sortName
     * @param array $excludedFields
     * @return array
     * @throws \Exception
     */
    public function getList($filter = [], $sortName = null, $excludedFields = null)
    {
        if ($excludedFields === null) {
            $excludedFields = $this->getExcludeFields();
        }

        /** @var CommentsEntity $commentsEntity */
        $commentsEntity = $this->entityFactory->get(CommentsEntity::class);

        // Исключаем колонки, которые нам не нужны
        if (is_array($excludedFields) && !empty($excludedFields)) {
            $commentsEntity->cols(CommentsEntity::getDifferentFields($excludedFields));
        }

        $commentsEntity->order($sortName, $this->getOrderCommentsAdditionalData());
        $comments = $commentsEntity->mappedBy('id')->find($filter);

        return ExtenderFacade::execute(__METHOD__, $comments, func_get_args());
    }

    /**
     * @return array
     */
    public function getExcludeFields()
    {
        $excludedFields = [];
        return ExtenderFacade::execute(__METHOD__, $excludedFields, func_get_args());
    }

    /**
     * @return array
     */
    private function getOrderCommentsAdditionalData()
    {
        $orderAdditionalData = [];
        return ExtenderFacade::execute(__METHOD__, $orderAdditionalData, func_get_args());
    }

    /**
     * @param array $comments
     * @return array mixed
     */
    public function attachAnswers($comments)
    {
        if (!empty($comments)) {
            /** @var CommentsEntity $commentsEntity */
            $commentsEntity = $this->entityFactory->get(CommentsEntity::class);

            $filter = [
                'has_parent' => true,
                'approved' => 1,
                'ip' => $_SERVER['REMOTE_ADDR'],
            ];
            foreach ($comments as $comment) {
                $filter['composite_object_type_id'][$comment->type][] = $comment->object_id;
            }
            $answers = $commentsEntity->mappedBy('id')->order('id DESC')->find($filter);
            foreach ($answers as $answer) {
                if (isset($answers[$answer->parent_id])) {
                    $answers[$answer->parent_id]->children[$answer->id] = $answer;
                } else if (isset($comments[$answer->parent_id])) {
                    $comments[$answer->parent_id]->children[$answer->id] = $answer;
                }
            }
        }

        return ExtenderFacade::execute(__METHOD__, $comments, func_get_args());
    }

    /**
     * @param string $objectType
     * @param int $objectId
     * @throws \Exception
     */
    public function addCommentProcedure($objectType, $objectId)
    {
        if (($comment = $this->commentsRequest->postComment()) !== null) {
            if ($error = $this->validateHelper->getCommentValidateError($comment)) {
                $this->design->assign('error', $error);
            } else {

                /** @var CommentsEntity $commentsEntity */
                $commentsEntity = $this->entityFactory->get(CommentsEntity::class);
                
                // Создаем комментарий
                $comment->object_id = $objectId;
                $comment->type      = $objectType;
                $comment->ip        = $_SERVER['REMOTE_ADDR'];
                $comment->lang_id   = $this->languages->getLangId();
                
                if (!empty($this->user->id)) {
                    $comment->user_id = $this->user->id;
                }
                
                // Добавляем комментарий в базу
                $commentId = $commentsEntity->add($comment);
                // Отправляем email
                $this->notify->emailCommentAdmin($commentId);
                
                ExtenderFacade::execute(__METHOD__, $commentId, func_get_args());
                
                Response::redirectTo($_SERVER['REQUEST_URI'].'#comment_'.$commentId);
            }
        }
    }

    public function attachTargetEntitiesToComments($comments)
    {

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);
        
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
            foreach ($productsEntity->find(['id' => $productsIds, 'limit' => count($productsIds)]) as $p) {
                $products[$p->id] = $p;
            }
        }

        $posts = [];
        if (!empty($postsIds)) {
            foreach ($blogEntity->find(['id' => $postsIds]) as $p) {
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
    
}