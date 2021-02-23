<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendCommentsHelper;
use Okay\Admin\Requests\BackendCommentsRequest;

class CommentsAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendCommentsHelper  $backendCommentsHelper,
        BackendCommentsRequest $commentsRequest
    ){
        if ($commentsRequest->postCommentAnswer()) {
            $comment     = $backendCommentsHelper->prepareCommentAnswer();
            $comment->id = $backendCommentsHelper->addCommentAnswer($comment);
            $backendCommentsHelper->notifyCommentAnswerToUser($comment);
        }

        if ($this->request->method('post')) {
            $ids = $commentsRequest->postCheck();
            switch($this->request->post('action')) {
                case 'approve': {
                    $backendCommentsHelper->approve($ids);
                    break;
                }
                case 'delete': {
                    $backendCommentsHelper->delete($ids);
                    break;
                }
            }
        }

        $filter        = $backendCommentsHelper->buildFilter();
        $comments      = $backendCommentsHelper->findComments($filter);
        $comments      = $backendCommentsHelper->attachTargetEntitiesToComments($comments);
        $children      = $backendCommentsHelper->findAnswers($comments);
        $commentsCount = $backendCommentsHelper->count($filter);

        if (isset($filter['type'])) {
            $this->design->assign('type', $filter['type']);
        }

        if ($status = $backendCommentsHelper->matchStatus($filter)) {
            $this->design->assign('type', $status);
        }

        if (isset($filter['type'])) {
            $this->design->assign('type', $filter['type']);
        }

        if (isset($filter['keyword'])) {
            $this->design->assign('type', $filter['keyword']);
        }

        $this->design->assign('pages_count',    ceil($commentsCount/$filter['limit']));
        $this->design->assign('current_page',   $filter['page']);
        $this->design->assign('comments',       $comments);
        $this->design->assign('children',       $children);
        $this->design->assign('comments_count', $commentsCount);
        $this->response->setContent($this->design->fetch('comments.tpl'));
    }
}
