<?php


namespace Okay\Admin\Controllers;


use Okay\Core\Response;
use Okay\Core\Managers;
use Okay\Core\Notify;
use Okay\Core\Validator;
use Okay\Entities\LessonsEntity;
use Okay\Entities\ManagersEntity;

class AuthAdmin extends IndexAdmin
{

    public function fetch(
        Managers $managers,
        ManagersEntity $managersEntity,
        LessonsEntity $lessonsEntity,
        Notify $notify,
        Response $response,
        Validator $validator
    ) {
        /*Восстановление пароля администратора*/
        $recoveryEmail = $this->request->get('recovery_email');
        if ($this->request->get("ajax_recovery")) {
            $result = new \stdClass();
            if (!$validator->isEmail($recoveryEmail, true)) {
                $result->error = 'wrong_email';
            } elseif (!($managerToRecovery = $managersEntity->findOne(['email' => $recoveryEmail]))) {
                $result->error = 'not_admin_email';
            } else {
                $code = $this->config->token(mt_rand(1, mt_getrandmax()) . mt_rand(1, mt_getrandmax()) . mt_rand(1, mt_getrandmax()));
                $_SESSION['admin_password_recovery_code'] = $code;
                $notify->passwordRecoveryAdmin($managerToRecovery->email, $code);
                
                $result->send = true;
            }
            $this->response->setContent(json_encode($result), RESPONSE_JSON);
            $this->response->sendContent();
            exit;
        }

        if (isset($_SESSION['admin_password_recovery_code']) && $_SESSION['admin_password_recovery_code'] == $this->request->get('code')){
            $this->design->assign("recovery_mod",true);
            if ($this->request->method('post')){
                $new_login = $this->request->post('new_login');
                $new_password = $this->request->post('new_password');
                $new_password_check = $this->request->post('new_password_check');

                if ($new_password == $new_password_check) {
                    $manager = $managersEntity->get($new_login);
                    if (!$managersEntity->update($manager->id, ['password' => $new_password, 'cnt_try' => 0, 'last_try' => null])) {
                        $managersEntity->add(['login' => $new_login, 'password' => $new_password]);
                        $manager = $managersEntity->get($new_login);
                    }
                    unset($_SESSION['admin_password_recovery_code']);
                    $_SESSION['admin'] = $manager->login;

                    $allManagers = $managersEntity->order('id ASC')->find();
                    $firstManager = reset($allManagers);

                    if ($lessonsEntity->count(['not_done' => 1]) > 0 && $firstManager->id === $manager->id) {
                        $response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=LearningAdmin');
                    }
                    $response->redirectTo($this->request->getBasePathWithDomain() . '/backend/index.php');
                }
            }

        } elseif ($this->request->method('post')) {
            /*Авторизация в админ.панель*/
            $login = $this->request->post('login');
            $pass = $this->request->post('password');
            $manager = $managersEntity->get((string)$login);
            
            if ($manager) {
                /*Подсчитываем количество неправильны попыток входа*/
                $limit = 10;
                $now = date('Y-m-d');
                $last = (isset($manager->last_try) ? $manager->last_try : $now);
                if ($last != $now) {
                    $last = $now;
                    $manager->cnt_try = 1;
                } else {
                    $manager->cnt_try++;
                }

                if ($manager->cnt_try > $limit) {
                    $this->design->assign('error_message', 'limit_try');
                } elseif ($managers->checkPassword($pass, $manager->password)) {
                    /*Входим в админку*/
                    $_SESSION['admin'] = $manager->login;
                    $managersEntity->update((int)$manager->id, ['cnt_try'=>0, 'last_try'=>null]);
                    $managersEntity->updateLastActivityDate($manager->id);
                    $loginRedirectResource = (!empty($_SESSION['before_auth_url']) ? $_SESSION['before_auth_url'] : $this->request->getBasePathWithDomain() . '/backend/index.php');
                    unset($_SESSION['before_auth_url']);

                    $allManagers = $managersEntity->order('id ASC')->find();
                    $firstManager = reset($allManagers);

                    if ($lessonsEntity->count(['not_done' => 1]) > 0 && $firstManager->id === $manager->id) {
                        $response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=LearningAdmin');
                    }
                    $response->redirectTo($loginRedirectResource);
                } else {
                    /*неверный пароль менеджера*/
                    $this->design->assign('login', $login);
                    $this->design->assign('error_message', 'auth_wrong');
                    $this->design->assign('limit_cnt', $limit-$manager->cnt_try);
                    $managersEntity->update((int)$manager->id, ['cnt_try'=>$manager->cnt_try, 'last_try'=>$last]);
                }
            } else {
                /*менеджер не найден*/
                $this->design->assign('login', $login);
                $this->design->assign('error_message', 'auth_wrong');
            }
        }
        $this->response->setContent($this->design->fetch('auth.tpl'));
    }

}
