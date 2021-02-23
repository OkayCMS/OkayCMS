<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendMenuHelper;
use Okay\Admin\Requests\BackendMenuRequest;
use Okay\Entities\MenuEntity;

class MenusAdmin extends IndexAdmin
{

    public function fetch(MenuEntity $menuEntity, BackendMenuHelper $menuHelper, BackendMenuRequest $menuRequest)
    {
        $filter = $menuHelper->buildFilter();
        
        /*Принимаем выбранные меню*/
        if ($this->request->method('post')) {
            $ids = $menuRequest->postCheck();
            if (is_array($ids)) {
                switch($menuRequest->postAction()) {
                    case 'enable': {
                        $menuHelper->enable($ids);
                        break;
                    }
                    case 'disable': {
                        $menuHelper->disable($ids);
                        break;
                    }
                    case 'delete': {
                        $menuHelper->delete($ids);
                        break;
                    }
                    default : {
                        $menuHelper->defaultAction($menuRequest->postAction(), $ids);
                    }
                }
            }

            // Сортировка
            $positions = $this->request->post('positions');
            $ids = array_keys($positions);
            sort($positions);
            foreach($positions as $i=>$position) {
                $menuEntity->update($ids[$i], ['position'=>$position]);
            }
        }

        $menus = $menuHelper->findMenus($filter);
        $this->design->assign('menus', $menus);
        $this->response->setContent($this->design->fetch('menus.tpl'));
    }

}
