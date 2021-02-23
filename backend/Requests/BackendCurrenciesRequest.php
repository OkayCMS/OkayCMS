<?php


namespace Okay\Admin\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;

class BackendCurrenciesRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postCurrencies()
    {
        $currencies = [];
        foreach ($this->request->post('currency') as $n=>$va) {
            foreach ($va as $i=>$v) {
                if(empty($currencies[$i])) {
                    $currencies[$i] = new \stdClass;
                }
                $currencies[$i]->$n = $v;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $currencies, func_get_args());
    }

    public function postRecalculatePrices()
    {
        $recalculate = $this->request->post('recalculate');
        return ExtenderFacade::execute(__METHOD__, $recalculate, func_get_args());
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postActionId()
    {
        $id = $this->request->post('action_id');
        return ExtenderFacade::execute(__METHOD__, $id, func_get_args());
    }
}