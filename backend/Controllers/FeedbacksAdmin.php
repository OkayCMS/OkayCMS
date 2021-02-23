<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendFeedbacksHelper;

use Okay\Admin\Helpers\BackendNotifyHelper;
use Okay\Admin\Requests\BackendFeedbacksRequest;

class FeedbacksAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendFeedbacksRequest $feedbacksRequest,
        BackendNotifyHelper     $backendNotifyHelper,
        BackendFeedbacksHelper  $backendFeedbacksHelper
    ){
        // Обработка действий
        $ids    = $feedbacksRequest->postCheck();
        $action = $feedbacksRequest->postAction();
        if (!empty($ids)) {
            switch ($action) {
                case 'delete': {
                    $backendFeedbacksHelper->delete($ids);
                    break;
                }
            }
        }

        /*Ответ админисратора на заявку с формы обратной связи*/
        if ($feedbacksRequest->postFeedbackAnswer()) {
            $answerFeedback = $feedbacksRequest->postFeedback();
            $answerFeedback = $backendFeedbacksHelper->prepareAddAnswer($answerFeedback);
            $success        = $backendFeedbacksHelper->addAnswer($answerFeedback);

            if ($success) {
                $backendNotifyHelper->feedbackAnswerNotify($answerFeedback);
            }
        }
        
        // Отображение
        $filter = $backendFeedbacksHelper->buildFilter();

        if (isset($filter['current_limit'])) {
            $this->design->assign('current_limit', $filter['current_limit']);
        }

        if (isset($filter['keyword'])) {
            $this->design->assign('keyword', $filter['keyword']);
        }

        if ($status = $backendFeedbacksHelper->getFilterStatus($filter)) {
            $this->design->assign('status', $status);
        }

        $feedbacks      = $backendFeedbacksHelper->findFeedbacks($filter);
        $adminAnswers   = $backendFeedbacksHelper->selectAnswers($feedbacks);
        $feedbacksCount = $backendFeedbacksHelper->count($filter);

        $this->design->assign('admin_answer',    $adminAnswers);
        $this->design->assign('pages_count',     ceil($feedbacksCount/$filter['limit']));
        $this->design->assign('current_page',    $filter['page']);
        $this->design->assign('feedbacks',       $feedbacks);
        $this->design->assign('feedbacks_count', $feedbacksCount);

        $this->response->setContent($this->design->fetch('feedbacks.tpl'));
    }
    
}
