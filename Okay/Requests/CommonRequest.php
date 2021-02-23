<?php


namespace Okay\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Phone;
use Okay\Core\Request;

class CommonRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return null|object
     */
    public function postComment()
    {
        $comment = null;
        if ($this->request->post('comment')) {
            $comment = new \stdClass;
            $comment->name = $this->request->post('name');
            $comment->email = $this->request->post('email');
            $comment->text = $this->request->post('text');
        }

        return ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }

    public function postFeedback()
    {
        $feedback = null;
        if ($this->request->post('feedback')) {
            $feedback = new \stdClass;
            $feedback->email    = $this->request->post('email');
            $feedback->name     = $this->request->post('name');
            $feedback->message  = $this->request->post('message');
        }

        return ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }

    public function postCallback()
    {
        $callback = null;
        if ($this->request->post('callback')) {
            $callback = new \stdClass;
            $callback->phone    = Phone::toSave($this->request->post('callback_phone'));
            $callback->name     = $this->request->post('callback_name');
            $callback->url      = $this->request->getCurrentUrl();
            $callback->message  = $this->request->post('callback_message');
        }

        return ExtenderFacade::execute(__METHOD__, $callback, func_get_args());
    }

    public function postSubscribe()
    {
        $subscribe = null;
        if ($this->request->post('subscribe')) {
            $subscribe = new \stdClass;
            $subscribe->email = $this->request->post('subscribe_email');
        }

        return ExtenderFacade::execute(__METHOD__, $subscribe, func_get_args());
    }

    public function uLoginToken()
    {
        if (!$token = $this->request->post('token')) {
            $token = null;
        }

        return ExtenderFacade::execute(__METHOD__, $token, func_get_args());
    }
}