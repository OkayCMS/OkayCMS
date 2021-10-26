<?php


namespace Okay\Helpers;


use Okay\Core\Modules\Extender\ExtenderFacade;

class NotifyHelper
{
    /**
     * Метод вызывается непосредственно перед отправкой письма о заказа админу. Письмо будет отправлено
     * 
     * @param $order
     */
    public function finalEmailOrderAdmin($order)
    {
        ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    /**
     * Метод вызывается непосредственно перед отправкой письма о заказа пользователю. Письмо будет отправлено
     *
     * @param $order
     */
    public function finalEmailOrderUser($order)
    {
        ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    /**
     * Метод вызывается непосредственно перед отправкой письма админу о новом комментарии. Письмо будет отправлено
     *
     * @param $comment
     */
    public function finalEmailCommentAdmin($comment)
    {
        ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }
    
    /**
     * Метод вызывается непосредственно перед отправкой письма о заявке на обратный завок. Письмо будет отправлено
     *
     * @param $callback
     */
    public function finalEmailCallbackAdmin($callback)
    {
        ExtenderFacade::execute(__METHOD__, $callback, func_get_args());
    }
    
    /**
     * Метод вызывается непосредственно перед отправкой письма пользователю об ответе на комментарий. Письмо будет отправлено
     *
     * @param $comment
     */
    public function finalEmailCommentAnswerToUser($comment)
    {
        ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }
    
    /**
     * Метод вызывается непосредственно перед отправкой письма пользователю о сбросе пароля. Письмо будет отправлено
     *
     * @param $user
     * @param $code
     */
    public function finalEmailPasswordRemind($user, $code)
    {
        ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }
    
    /**
     * Метод вызывается непосредственно перед отправкой письма пользователю с ответом на заявку в обратную связь. Письмо будет отправлено
     *
     * @param $feedback
     * @param $text
     */
    public function finalEmailFeedbackAnswerForUser($feedback, $text)
    {
        ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }
    
    /**
     * Метод вызывается непосредственно перед отправкой письма админу о заявке в обратную связь . Письмо будет отправлено
     *
     * @param $feedback
     */
    public function finalEmailFeedbackAdmin($feedback)
    {
        ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }
    
    /**
     * Метод вызывается непосредственно перед отправкой письма о сбросе пароля администратора. Письмо будет отправлено
     *
     * @param $email
     * @param $code
     */
    public function finalEmailPasswordRecoveryAdmin($email, $code)
    {
        ExtenderFacade::execute(__METHOD__, $email, func_get_args());
    }

    /**
     * @param $order
     * @return object
     */
    public function notSendEmailOrderAdmin($order)
    {
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    /**
     * @param $order
     * @return object
     */
    public function notSendEmailOrderUser($order)
    {
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }

    /**
     * @param $comment
     * @return object
     */
    public function notSendEmailCommentAdmin($comment)
    {
        return ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }

    /**
     * @param $callback
     * @return object
     */
    public function notSendEmailCallbackAdmin($callback)
    {
        return ExtenderFacade::execute(__METHOD__, $callback, func_get_args());
    }

    /**
     * @param $comment
     * @return object
     */
    public function notSendEmailCommentAnswerToUser($comment)
    {
        return ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }

    /**
     * @param $user
     * @param $code
     * @return object
     */
    public function notSendEmailPasswordRemind($user, $code)
    {
        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }
    
    /**
     * @param $feedback
     * @param $text
     * @return object
     */
    public function notSendEmailFeedbackAnswerForUser($feedback, $text)
    {
        return ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }

    /**
     * @param $feedback
     * @return object
     */
    public function notSendEmailFeedbackAdmin($feedback)
    {
        return ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }
    
    /**
     * @param $email
     * @param $code
     * @return object
     */
    public function notSendEmailPasswordRecoveryAdmin($email, $code)
    {
        return ExtenderFacade::execute(__METHOD__, $email, func_get_args());
    }

    /**
     * Нужно ли отправлять письмо клиенту о заказе (может понадобится если модуль отправляет свое письмо, 
     * а это нужно отменить)
     * 
     * @param $order
     * @return bool
     */
    public function needSendEmailOrderUser($order)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }

    /**
     * Нужно ли отправлять письмо клиенту о заказе (может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     * 
     * @param $order
     * @return bool
     */
    public function needSendEmailOrderAdmin($order)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }

    /**
     * Нужно ли отправлять письмо админу о комментарии (может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $comment
     * @return bool
     */
    public function needSendEmailCommentAdmin($comment)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }
    
    /**
     * Нужно ли отправлять письмо админу о заявке на обратный звонок(может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $callback
     * @return bool
     */
    public function needSendEmailCallbackAdmin($callback)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }
    
    /**
     * Нужно ли отправлять письмо клиенту о ответе на комментарий(может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $comment
     * @return bool
     */
    public function needSendEmailCommentAnswerToUser($comment)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }
    
    /**
     * Нужно ли отправлять письмо клиенту о сбросе пароля(может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $user
     * @return bool
     */
    public function needSendEmailPasswordRemind($user)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }
    
    /**
     * Нужно ли отправлять письмо клиенту письмо с ответом на заявку в обратную связь (может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $feedback
     * @return bool
     */
    public function needSendEmailFeedbackAnswerForUser($feedback)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }
    
    /**
     * Нужно ли отправлять письмо админу из формы обратной связи (может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $feedback
     * @return bool
     */
    public function needSendEmailFeedbackAdmin($feedback)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    } 
    
    /**
     * Нужно ли отправлять письмо админу из формы обратной связи (может понадобится если модуль отправляет свое письмо,
     * а это нужно отменить)
     *
     * @param $email
     * @return bool
     */
    public function needSendEmailPasswordRecoveryAdmin($email)
    {
        return ExtenderFacade::execute(__METHOD__, true, func_get_args());
    }
}