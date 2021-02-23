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
     * @param $order
     * @return false|mixed|void|null
     */
    public function notSendEmailOrderAdmin($order)
    {
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
    }
    
    public function notSendEmailOrderUser($order)
    {
        return ExtenderFacade::execute(__METHOD__, $order, func_get_args());
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
}