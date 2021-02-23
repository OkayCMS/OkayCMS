<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendUserGroupsHelper;
use Okay\Admin\Requests\BackendUserGroupsRequest;

class UserGroupsAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendUserGroupsHelper $backendUserGroupsHelper,
        BackendUserGroupsRequest $backendUserGroupsRequest
    ) {
        if ($this->request->method('post')) {
            // Действия с выбранными
            $ids = $backendUserGroupsRequest->postCheck();
            if (is_array($ids)) {
                switch ($backendUserGroupsRequest->postAction()){
                    case 'delete': {
                        /*Удаление группы пользователей*/
                        $backendUserGroupsHelper->delete($ids);
                        break;
                    }
                }
            }
        }
        $filter = $backendUserGroupsHelper->buildFilter();
        $groups = $backendUserGroupsHelper->findGroups($filter);
        
        $this->design->assign('groups', $groups);
        $this->response->setContent($this->design->fetch('user_groups.tpl'));
    }
    
}
