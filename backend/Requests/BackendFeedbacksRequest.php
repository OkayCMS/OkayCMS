<?php
/**
 * Created by PhpStorm.
 * User: marielle
 * Date: 07.11.19
 * Time: 14:16
 */

namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendFeedbacksRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postCheck()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postFeedback()
    {
        $newFeedback = new \stdClass();
        $newFeedback->message   = $this->request->post('text');
        $newFeedback->parent_id = $this->request->post('feedback_id', 'integer');
        return ExtenderFacade::execute(__METHOD__, $newFeedback, func_get_args());
    }

    public function postFeedbackAnswer()
    {
        $feedbackAnswer = $this->request->post('feedback_answer');
        return ExtenderFacade::execute(__METHOD__, $feedbackAnswer, func_get_args());
    }
}