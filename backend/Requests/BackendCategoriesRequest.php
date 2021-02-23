<?php


namespace Okay\Admin\Requests;


use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendCategoriesRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postCategory()
    {
        $category = new \stdClass;
        $category->id               = $this->request->post('id', 'integer');
        $category->parent_id        = $this->request->post('parent_id', 'integer');
        $category->name             = $this->request->post('name');
        $category->name_h1          = $this->request->post('name_h1');
        $category->visible          = $this->request->post('visible', 'boolean');
        $category->url              = trim($this->request->post('url', 'string'));
        $category->meta_title       = $this->request->post('meta_title');
        $category->meta_keywords    = $this->request->post('meta_keywords');
        $category->meta_description = $this->request->post('meta_description');
        $category->annotation       = $this->request->post('annotation');
        $category->description      = $this->request->post('description');

        return ExtenderFacade::execute(__METHOD__, $category, func_get_args());
    }

    public function getCategoryId()
    {
        $categoryId = $this->request->get('category_id', 'integer');
        return ExtenderFacade::execute(__METHOD__, $categoryId, func_get_args());
    }

    public function postCheckedIds()
    {
        $check = $this->request->post('check');
        return ExtenderFacade::execute(__METHOD__, $check, func_get_args());
    }

    public function postPositions()
    {
        $positions = $this->request->post('positions');
        return ExtenderFacade::execute(__METHOD__, $positions, func_get_args());
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
}