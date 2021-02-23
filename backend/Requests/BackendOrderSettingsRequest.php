<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendOrderSettingsRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postNewStatuses()
    {
        $newStatusNames = (array) $this->request->post('new_name');
        //$newParams      = (array) $this->request->post('new_is_close');
        $newColors      = (array) $this->request->post('new_color');

        $newStatuses = [];
        foreach ($newStatusNames as $id => $name) {
            if (!empty($name)) {
                $status = new \stdClass();
                $status->name     = $name;
                //$status->is_close = $newParams[$id];
                $status->color    = $newColors[$id];
                $newStatuses[$id] = $status;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $newStatuses, func_get_args());
    }

    public function postStatuses()
    {
        $postFields = $this->request->post('statuses');

        if (empty($postFields)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $statuses = [];
        foreach ($postFields as $n=>$va) {
            foreach ($va as $i=>$v) {
                if (empty($statuses[$i])) {
                    $statuses[$i] = new \stdClass();
                }
                if (empty($v) && in_array($n, ['id'])) {
                    $v = null;
                }
                $statuses[$i]->$n = $v;
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $statuses, func_get_args());
    }

    public function postStatus()
    {
        $status = $this->request->post('status');
        return ExtenderFacade::execute(__METHOD__, $status, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
    }

    public function postCheck()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postLabels()
    {
        $postFields = $this->request->post('labels');

        if (empty($postFields)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }
        
        $labels = [];
        foreach ($postFields as $n=>$va) {
            foreach ($va as $i=>$v) {
                if (empty($labels[$i])) {
                    $labels[$i] = new \stdClass();
                }
                if (empty($v) && in_array($n, ['id'])) {
                    $v = null;
                }
                $labels[$i]->$n = $v;
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $labels, func_get_args());
    }
}