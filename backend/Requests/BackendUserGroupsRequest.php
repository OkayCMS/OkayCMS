<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendUserGroupsRequest
{
    /**
     * @var Request
     */
    private $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postGroup()
    {
        $group = new \stdClass;
        $group->id = $this->request->post('id', 'integer');
        $group->name = $this->request->post('name');
        $group->discount = $this->request->post('discount', 'float');

        return ExtenderFacade::execute(__METHOD__, $group, func_get_args());
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