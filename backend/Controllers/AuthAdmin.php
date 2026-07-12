<?php

namespace Okay\Admin\Controllers;

use Okay\Core\Response;
use Okay\Core\Managers;
use Okay\Core\Notify;
use Okay\Core\Security\AdminSession;
use Okay\Core\Security\AdminRecoveryToken;
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
                $result->send = true;
            } else {
                /** @var object{id: int|string, email: string, password: string} $managerToRecovery */
                $recoveryToken = new AdminRecoveryToken($this->config);
                $code = $recoveryToken->create((int)$managerToRecovery->id, (string)$managerToRecovery->password);
                $notify->emailPasswordRecoveryAdmin($managerToRecovery->email, $code);

                $result->send = true;
            }
            $this->response->setContent(json_encode($result), RESPONSE_JSON);
            $this->response->sendContent();
            exit;
        }

        /** @var object{id: int|string, login: string}|null $recoveryManager */
        $recoveryManager = $this->getRecoveryManager($managersEntity);
        if ($recoveryManager) {
            $this->design->assign("recovery_mod", true);
            $this->design->assign("recovery_login", $recoveryManager->login);
            if ($this->request->method('post')) {
                $new_password = (string)$this->request->post('new_password');
                $new_password_check = (string)$this->request->post('new_password_check');

                if (trim($new_password) === '') {
                    $this->design->assign('error_message', 'password_empty');
                } elseif ($new_password !== $new_password_check) {
                    $this->design->assign('error_message', 'password_wrong');
                } else {
                    $manager = $recoveryManager;
                    $passwordUpdated = $managersEntity->update($manager->id, [
                        'password' => $new_password,
                        'cnt_try' => 0,
                        'last_try' => null,
                    ]);
                    if (!$passwordUpdated) {
                        $this->design->assign('error_message', 'auth_wrong');
                        $this->response->setContent($this->design->fetch('auth.tpl'));
                        return;
                    }

                    unset($_SESSION['admin_password_recovery_code']);
                    $_SESSION['admin'] = $manager->login;
                    AdminSession::regenerateId();

                    $allManagers = $managersEntity->order('id ASC')->find();
                    $firstManager = reset($allManagers);

                    if (
                        $firstManager
                        && $lessonsEntity->count(['not_done' => 1]) > 0
                        && $firstManager->id === $manager->id
                    ) {
                        $response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=LearningAdmin');
                    }
                    $response->redirectTo($this->request->getRootUrl() . '/backend/index.php');
                }
            }
        } elseif ($this->request->method('post')) {
            /*Авторизация в админ.панель*/
            $login = $this->request->post('login');
            $pass = $this->request->post('password');
            $manager = $managersEntity->get((string)$login);

            if ($manager) {
                $passwordIsValid = $managers->checkPassword($pass, $manager->password);

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

                if ($passwordIsValid) {
                    if (is_string($pass)) {
                        $managersEntity->rehashPasswordIfNeeded((int)$manager->id, $pass, (string)$manager->password);
                    }

                    /*Входим в админку*/
                    $_SESSION['admin'] = $manager->login;
                    AdminSession::regenerateId();
                    $managersEntity->update((int)$manager->id, ['cnt_try' => 0, 'last_try' => null]);
                    $managersEntity->updateLastActivityDate($manager->id);
                    $loginRedirectResource = (!empty($_SESSION['before_auth_url']) ? $_SESSION['before_auth_url'] : $this->request->getRootUrl() . '/backend/index.php');
                    unset($_SESSION['before_auth_url']);

                    $allManagers = $managersEntity->order('id ASC')->find();
                    $firstManager = reset($allManagers);

                    if ($lessonsEntity->count(['not_done' => 1]) > 0 && $firstManager->id === $manager->id) {
                        $response->redirectTo($this->request->getRootUrl() . '/backend/index.php?controller=LearningAdmin');
                    }
                    $response->redirectTo($loginRedirectResource);
                } elseif ($manager->cnt_try > $limit) {
                    $this->design->assign('error_message', 'limit_try');
                } else {
                    /*неверный пароль менеджера*/
                    $this->design->assign('login', $login);
                    $this->design->assign('error_message', 'auth_wrong');
                    $this->design->assign('limit_cnt', $limit - $manager->cnt_try);
                    $managersEntity->update((int)$manager->id, ['cnt_try' => $manager->cnt_try, 'last_try' => $last]);
                }
            } else {
                /*менеджер не найден*/
                $this->design->assign('login', $login);
                $this->design->assign('error_message', 'auth_wrong');
            }
        }
        $this->response->setContent($this->design->fetch('auth.tpl'));
    }

    /**
     * @return object{id: int|string, login: string}|null
     */
    private function getRecoveryManager(ManagersEntity $managersEntity): ?object
    {
        $recovery = $_SESSION['admin_password_recovery_code'] ?? null;
        if (is_array($recovery)) {
            $code = $this->request->get('code');
            if (!empty($recovery['code']) && !empty($recovery['manager_id']) && hash_equals((string)$recovery['code'], (string)$code)) {
                /** @var object{id: int|string, login: string}|null $legacyManager */
                $legacyManager = $managersEntity->get((int)$recovery['manager_id']);
                return $legacyManager;
            }
        }

        $code = (string)$this->request->get('code');
        $recoveryToken = new AdminRecoveryToken($this->config);
        $managerId = $recoveryToken->unverifiedManagerId($code);
        if ($managerId === null) {
            return null;
        }

        /** @var object{id: int|string, login: string, password: string}|null $manager */
        $manager = $managersEntity->get($managerId);
        if (!$manager || empty($manager->password)) {
            return null;
        }

        if ($recoveryToken->managerId($code, (string)$manager->password) !== (int)$manager->id) {
            return null;
        }

        return $manager;
    }
}
