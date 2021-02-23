<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendUsersHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendUsersRequest;

class UserAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendUsersRequest   $backendUsersRequest,
        BackendValidateHelper $backendValidateHelper,
        BackendUsersHelper    $backendUsersHelper
    ) {
        
        /*Прием данных о пользователе*/
        if ($this->request->method('post')) {
            $user = $backendUsersRequest->postUser();
            
            /*Не допустить одинаковые email пользователей*/
            if ($error = $backendValidateHelper->getUsersValidateError($user)) {
                $this->design->assign('message_error', $error);
            } else {
                $preparedUser = $backendUsersHelper->prepareUpdate($user);
                $backendUsersHelper->update($preparedUser->id, $preparedUser);

                $this->postRedirectGet->redirect();
            }
        }

        $userId = $this->request->get('id', 'integer');
        $user   = $backendUsersHelper->getUser($userId);
        $groups = $backendUsersHelper->getAllGroups();

        $this->design->assign('groups', $groups);
        $this->design->assign('user',   $user);

        $this->response->setContent($this->design->fetch('user.tpl'));
    }
    
}
