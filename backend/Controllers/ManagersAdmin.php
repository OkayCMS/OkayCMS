<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\ManagersEntity;

class ManagersAdmin extends IndexAdmin
{
    
    public function fetch(ManagersEntity $managersEntity)
    {
        if ($this->request->method('post')) {
            // Действия с выбранными
            $ids = $this->request->post('check');
            if(is_array($ids)) {
                switch($this->request->post('action')) {
                    case 'delete': {
                        /*Удалить менеджера*/
                        $managersEntity->delete($ids);
                        break;
                    }
                }
            }
        }
        
        $managers = $managersEntity->find();
        $managersCount = $managersEntity->count();
        $this->design->assign('managers', $managers);
        $this->design->assign('managers_count', $managersCount);
        $this->response->setContent($this->design->fetch('managers.tpl'));
    }
    
}
