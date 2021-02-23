<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Notify;

class BackendNotifyHelper
{
    /**
     * @var Notify
     */
    private $notify;

    public function __construct(Notify $notify)
    {
        $this->notify = $notify;
    }

    public function feedbackAnswerNotify($answerFeedback)
    {
        $this->notify->emailFeedbackAnswerFoUser($answerFeedback->parent_id, $answerFeedback->message);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * Метод предназначен для дебага шаблонов писем из модулей.
     * Для дебага нужно набрать урл http://domain/backend/index.php?controller=EmailTemplatesAdmin&debug=<debugType>&foo=bar
     * Этот метод нужно расширить ChainExtender-ом и сделать проверку если $debugEmail == <debugType> то вернуть
     * html вашего шаблона
     * 
     * @param string $debugEmail строка, переданная в параметре debug
     * @return false|mixed|void|null
     */
    public function debugTemplate($debugEmail)
    {
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}