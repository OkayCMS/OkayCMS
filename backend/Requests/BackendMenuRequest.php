<?php


namespace Okay\Admin\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;

class BackendMenuRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function postMenu()
    {
        $menu = new \stdClass();
        $menu->id       = $this->request->post('id', 'integer');
        $menu->group_id = trim($this->request->post('group_id', 'string'));
        $menu->name     = $this->request->post('name');
        $menu->visible  = $this->request->post('visible', 'integer');
        $menu->group_id = preg_replace("/[\s]+/ui", '', $menu->group_id);
        $menu->group_id = strtolower(preg_replace("/[^0-9a-z_]+/ui", '', $menu->group_id));

        return ExtenderFacade::execute(__METHOD__, $menu, func_get_args());
    }
    
    public function postMenuItems()
    {
        $postFields = $this->request->post('menu_items');

        if (empty($postFields)) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        foreach ($this->request->post('menu_items') as $field => $values) {
            foreach ($values as $i => $v) {
                if (empty($menuItems[$i])) {
                    $menuItems[$i] = new \stdClass();
                    $menuItems[$i]->i_tm = $i;
                }
                $menuItems[$i]->$field = $v;
            }
        }
        
        // сортируем по родителю
        usort($menuItems, function ($item1, $item2) {
            if ($item1->parent_index == $item2->parent_index) {
                return $item1->i_tm - $item2->i_tm;
            }
            return strcmp($item1->parent_index, $item2->parent_index);
        });
        $tm = [];

        $local = [trim($this->request->getRootUrl(), "/"), trim(preg_replace("~^https?://~", "", $this->request->getRootUrl()), "/")];
        foreach ($menuItems as $key => $item) {
            foreach ($local as $l) {
                $item->url = preg_replace("~^$l/?~", "", $item->url);
            }
            $tm[$item->index] = $item;
        }
        $menuItems = $tm;
        
        return ExtenderFacade::execute(__METHOD__, $menuItems, func_get_args());
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
