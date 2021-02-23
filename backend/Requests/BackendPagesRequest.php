<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendPagesRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postPage()
    {
        $page = new \stdClass;
        $page->id               = $this->request->post('id', 'integer');
        $page->name             = $this->request->post('name');
        $page->name_h1          = $this->request->post('name_h1');
        $page->url              = trim($this->request->post('url'));
        $page->visible          = $this->request->post('visible', 'boolean');
        $page->meta_title       = $this->request->post('meta_title');
        $page->meta_keywords    = $this->request->post('meta_keywords');
        $page->meta_description = $this->request->post('meta_description');
        $page->description      = $this->request->post('description');

        return ExtenderFacade::execute(__METHOD__, $page, func_get_args());
    }

    public function getId()
    {
        $id = $this->request->get('id', 'integer');
        return ExtenderFacade::execute(__METHOD__, $id, func_get_args());
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

    public function postAction()
    {
        $action = $this->request->post('action');
        return ExtenderFacade::execute(__METHOD__, $action, func_get_args());
    }
}