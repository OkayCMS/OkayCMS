<?php


namespace Okay\Admin\Requests;


use Okay\Core\Phone;
use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendUsersRequest
{
    /**
     * @var Request
     */
    private $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postUser()
    {
        $user = new \stdClass;
        $user->id = $this->request->post('id', 'integer');
        $user->name = $this->request->post('name');
        $user->last_name = $this->request->post('last_name');
        $user->email = $this->request->post('email');
        $user->phone = Phone::toSave($this->request->post('phone'));
        $user->address = $this->request->post('address');
        $user->group_id = $this->request->post('group_id');
    
        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }
    
    public function postCheck()
    {
        $check = (array) $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }
}