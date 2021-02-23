<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendCommentsRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postCommentAnswer()
    {
        $commentAnswer = $this->request->post('comment_answer', 'boolean');
        return ExtenderFacade::execute(__METHOD__, $commentAnswer, func_get_args());
    }

    public function postCheck()
    {
        $check = (array) $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }
}