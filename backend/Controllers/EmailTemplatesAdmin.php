<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendNotifyHelper;
use Okay\Core\Notify;
use Okay\Core\QueryFactory;
use Okay\Entities\CommentsEntity;
use Okay\Entities\FeedbacksEntity;
use Okay\Entities\ManagersEntity;

class EmailTemplatesAdmin extends IndexAdmin
{

    /*Чтение файлов шаблона*/
    public function fetch(
        Notify               $notify,
        BackendNotifyHelper  $notifyHelper,
        ManagersEntity       $managersEntity,
        QueryFactory         $queryFactory
    ) {
        if ($debugEmail = $this->request->get('debug')) {
            switch ($debugEmail) {
                case 'emailOrderAdmin':
                    $orderId = $this->request->get('order_id', 'integer', 1);
                    $this->response->setContent($notify->emailOrderAdmin($orderId, true));
                    break;
                case 'emailCommentAdmin':
                    $commentId = $this->request->get('comment_id', 'integer', 1);
                    $this->response->setContent($notify->emailCommentAdmin($commentId, true));
                    break;
                case 'emailCallbackAdmin':
                    $callbackId = $this->request->get('callback_id', 'integer', 1);
                    $this->response->setContent($notify->emailCallbackAdmin($callbackId, true));
                    break;
                case 'emailFeedbackAdmin':
                    $feedbackId = $this->request->get('feedback_id', 'integer', 1);
                    $this->response->setContent($notify->emailFeedbackAdmin($feedbackId, true));
                    break;
                case 'emailPasswordRecoveryAdmin':
                    $this->response->setContent($notify->emailPasswordRecoveryAdmin($managersEntity->get($_SESSION['admin']), 'test', true));
                    break;
                    
                case 'emailOrderUser':
                    $orderId = $this->request->get('order_id', 'integer', 1);
                    $this->response->setContent($notify->emailOrderUser($orderId, true));
                    break;
                case 'emailCommentAnswerToUser':
                    if (empty($commentAnswerId = $this->request->get('comment_id', 'integer'))) {
                        $commentAnswerId = $queryFactory->newSelect()
                            ->from(CommentsEntity::getTable().' AS c1')
                            ->cols(['c1.*'])
                            ->join('left', CommentsEntity::getTable().' AS c2', 'c1.parent_id = c2.id')
                            ->where('c2.id IS NOT NULL')
                            ->where("c2.email != ''")
                            ->result('id');
                    }

                    $this->response->setContent($notify->emailCommentAnswerToUser($commentAnswerId, true));
                    break;
                case 'emailFeedbackAnswerFoUser':
                    if (empty($feedbackAnswerId = $this->request->get('feedback_id', 'integer'))) {
                        $feedbackAnswerId = $queryFactory->newSelect()
                            ->from(FeedbacksEntity::getTable().' AS f1')
                            ->cols(['f1.*'])
                            ->join('left', FeedbacksEntity::getTable().' AS f2', 'f1.parent_id = f2.id')
                            ->where('f2.id IS NOT NULL')
                            ->where("f2.email != ''")
                            ->result('id');
                    }

                    $this->response->setContent($notify->emailFeedbackAnswerFoUser($feedbackAnswerId, true));
                    break;
                case 'emailPasswordRemind':
                    $userId = $this->request->get('user_id', 'integer', 1);
                    $this->response->setContent($notify->emailPasswordRemind($userId, 'test', true));
                    break;

                default:
                    if ($response = $notifyHelper->debugTemplate($debugEmail)) {
                        $this->response->setContent($response);
                    }
            }
        } else {
            $this->response->setContent($this->design->fetch('email_templates_global.tpl'));
        }
    }
    
}
