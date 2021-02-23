<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Translit;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendFeaturesValuesRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Translit
     */
    private $translit;

    public function __construct(
        Request  $request,
        Translit $translit
    ){
        $this->request  = $request;
        $this->translit = $translit;
    }

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }

    public function postCheck()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postTargetPage()
    {
        $targetPage = $this->request->post('target_page', 'integer');
        return ExtenderFacade::execute(__METHOD__, $targetPage, func_get_args());
    }

    public function postToIndexAllValues()
    {
        $toIndexAllValues = isset($_POST['to_index_all_values']) ? $_POST['to_index_all_values'] : null;
        return ExtenderFacade::execute(__METHOD__, $toIndexAllValues, func_get_args());
    }
}