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
            $comment = new \stdClass();
            $comment->name = $this->request->post('name', null, null, true);
            $comment->email = $this->request->post('email', null, null, true);
            $comment->text = $this->request->post('text', null, null, true);
        }

        return ExtenderFacade::execute(__METHOD__, $comment, func_get_args());
    }

    /**
     * @return null|(object{email: mixed, name: mixed, message: mixed, ip?: mixed, lang_id?: mixed}&\stdClass)
     */
    public function postFeedback()
    {
        $feedback = null;
        if ($this->request->post('feedback')) {
            $feedback = new \stdClass();
            $feedback->email    = $this->request->post('email', null, null, true);
            $feedback->name     = $this->request->post('name', null, null, true);
            $feedback->message  = $this->request->post('message', null, null, true);
        }

        return ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }

    public function postCallback()
    {
        $callback = null;
        if ($this->request->post('callback')) {
            $callback = new \stdClass();
            $callback->phone    = Phone::toSave($this->request->post('callback_phone', null, null, true));
            $callback->name     = $this->request->post('callback_name', null, null, true);
            $callback->url      = $this->request->getCurrentUrl();
            $callback->message  = $this->request->post('callback_message', null, null, true);
        }

        return ExtenderFacade::execute(__METHOD__, $callback, func_get_args());
    }

    public function postSubscribe()
    {
        $subscribe = null;
        if ($this->request->post('subscribe')) {
            $subscribe = new \stdClass();
            $subscribe->email = $this->request->post('subscribe_email', null, null, true);
        }

        return ExtenderFacade::execute(__METHOD__, $subscribe, func_get_args());
    }
}
