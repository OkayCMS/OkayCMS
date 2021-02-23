<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendUsersHelper;
use Okay\Admin\Requests\BackendUsersRequest;
use Okay\Entities\UserGroupsEntity;

class UsersAdmin extends IndexAdmin
{
    
    public function fetch(
        UserGroupsEntity $userGroupsEntity,
        BackendUsersRequest $backendUsersRequest,
        BackendUsersHelper $backendUsersHelper
    ) {
        $group = null;
        $filter = $backendUsersHelper->buildFilter();
        
        if (!empty($filter['keyword'])) {
            $this->design->assign('keyword', $filter['keyword']);
        }

        if (!empty($filter['group_id'])) {
            $group = $userGroupsEntity->get((int)$filter['group_id']);
        }
        
        if ($this->request->method('post')) {
            // Действия с выбранными
            $ids = $backendUsersRequest->postCheck();
            if (is_array($ids)) {
                switch ($backendUsersRequest->postAction()) {
                    case 'delete': {
                        $backendUsersHelper->delete($ids);
                        break;
                    }
                    case 'move_to': {
                        /*Переместить пользователя в группу*/
                        $backendUsersHelper->moveToGroup($ids, $this->request->post('move_group', 'integer'));
                        break;
                    }
                }
            }
        }

        $groups = $backendUsersHelper->getAllGroups();
        $usersSort = $backendUsersHelper->getUsersSort();
        $usersCount = $backendUsersHelper->countUsers($filter);
        $users = $backendUsersHelper->findUsers($filter, $usersSort);
        
        $this->design->assign('pages_count', ceil($usersCount/$filter['limit']));
        $this->design->assign('current_page', $filter['page']);
        $this->design->assign('groups', $groups);
        $this->design->assign('group', $group);
        $this->design->assign('users', $users);
        $this->design->assign('users_count', $usersCount);
        $this->design->assign('sort', $usersSort);

        $this->response->setContent($this->design->fetch('users.tpl'));
    }
    
}
