<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendNotifyHelper;
use Okay\Core\Notify;

class EmailTemplatesAdmin extends IndexAdmin
{

    /*Чтение файлов шаблона*/
    public function fetch(Notify $notify, BackendNotifyHelper $notifyHelper)
    {
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
                    
                case 'emailOrderUser':
                    $orderId = $this->request->get('order_id', 'integer', 1);
                    $this->response->setContent($notify->emailOrderUser($orderId, true));
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
