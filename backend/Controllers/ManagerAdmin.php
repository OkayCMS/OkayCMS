<?php


namespace Okay\Admin\Controllers;


use Okay\Core\Languages;
use Okay\Core\ManagerMenu;
use Okay\Entities\ManagersEntity;

class ManagerAdmin extends IndexAdmin
{
    
    public function fetch(ManagerMenu $managerMenu, ManagersEntity $managersEntity, Languages $languagesCore) {
        $manager = new \stdClass();
        /*Прием информации о менеджере*/
        if ($this->request->method('post')) {
            if ($this->request->post('reset_menu')) {
                $id = $this->request->post('id', 'integer');
                $managersEntity->update($id, ['menu'=>'']);
                $this->response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=ManagerAdmin&id='.$id);
            }
            
            $manager->id = $this->request->post('id', 'integer');
            $manager->lang = $this->request->post('manager_lang');
            $manager->email = $this->request->post('email');
            $manager->comment = $this->request->post('comment');
            $manager->menu_status = $this->request->post('menu_status','integer');
            
            if ($this->request->post('unlock_manager')) {
                $managersEntity->update($manager->id, ['cnt_try'=>0]);
                $id = $this->request->get('id', 'integer');
                if (!empty($id)) {
                    $manager = $managersEntity->get($id);
                }
            } else {
                $manager->login = $this->request->post('login');

                if (empty($manager->login)) {
                    $this->design->assign('message_error', 'empty_login');
                } elseif (($m = $managersEntity->get($manager->login)) && $m->id!=$manager->id) {
                    $manager->permissions = (array)$this->request->post('permissions');
                    $this->design->assign('message_error', 'login_exists');
                } else {
                    if($this->request->post('password') != "" && $this->request->post('password') == $this->request->post('password_check')) {
                        $manager->password = $this->request->post('password');
                    } elseif($this->request->post('password') != $this->request->post('password_check')) {
                        $this->design->assign('message_error', 'password_wrong');
                    }

                    // Обновляем права только другим менеджерам
                    $currentManager = $managersEntity->get($_SESSION['admin']);
                    if ($manager->id != $currentManager->id) {
                        $targetManager  = $managersEntity->get((int) $manager->id);
                        $newPermissions = $this->request->post('permissions', null, []);

                        $manager->permissions = $this->managers->determineNewPermissions(
                            $currentManager,
                            $targetManager,
                            $newPermissions
                        );
                    }

                    /*Добавление/Обновление менеджера*/
                    if (empty($manager->id)) {
                        $manager->id = $managersEntity->add($manager);
                        $this->design->assign('message_success', 'added');
                    } else {
                        $managersEntity->update($manager->id, $manager);
                        $this->design->assign('message_success', 'updated');
                        if ($manager->lang != $m->lang) {
                            $this->response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=ManagerAdmin&id=' . $manager->id);
                        }
                    }
                    $manager = $managersEntity->get($manager->login);
                }
            }
        } else {
            $id = $this->request->get('id', 'integer');
            if(!empty($id)) {
                $manager = $managersEntity->get($id);
            }
        }

        $btr = $this->design->getVar('btr');
        $permission = $managerMenu->getPermissionMenu(
            $managersEntity->get((string) $_SESSION['admin']),
            $btr
        );

        $btrLanguages = [];
        foreach ($languagesCore->getLangList() as $label=>$l) {
            if (file_exists("backend/lang/".$label.".php")) {
                $btrLanguages[$l->name] = $l->label;
            }
        }
        
        $this->design->assign('btr_languages', $btrLanguages);
        $this->design->assign('m', $manager);
        $this->design->assign('permission', $permission);

        $this->response->setContent($this->design->fetch('manager.tpl'));
    }
    
}
