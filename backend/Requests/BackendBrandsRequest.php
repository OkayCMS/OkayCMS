<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Translit;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendBrandsRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Translit
     */
    private $translit;

    public function __construct(Request $request, Translit $translit)
    {
        $this->request = $request;
        $this->translit = $translit;
    }

    public function postBrand()
    {
        $brand = new \stdClass();
        $brand->id               = $this->request->post('id', 'integer');
        $brand->name             = $this->request->post('name');
        $brand->annotation       = $this->request->post('annotation');
        $brand->description      = $this->request->post('description');
        $brand->visible          = $this->request->post('visible', 'boolean');
        $brand->url              = trim($this->request->post('url', 'string'));
        $brand->meta_title       = $this->request->post('meta_title');
        $brand->meta_keywords    = $this->request->post('meta_keywords');
        $brand->meta_description = $this->request->post('meta_description');

        return ExtenderFacade::execute(__METHOD__, $brand, func_get_args());
    }

    public function postDeleteImage()
    {
        $deleteImage = $this->request->post('delete_image');
        return ExtenderFacade::execute(__METHOD__, $deleteImage, func_get_args());
    }

    public function fileImage()
    {
        $image = $this->request->files('image');
        return ExtenderFacade::execute(__METHOD__, $image, func_get_args());
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