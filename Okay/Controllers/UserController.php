<?php


namespace Okay\Controllers;


use Okay\Core\Notify;
use Okay\Core\Response;
use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\UsersEntity;
use Okay\Core\Router;
use Okay\Helpers\CommentsHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Helpers\UserHelper;
use Okay\Helpers\ValidateHelper;
use Okay\Requests\UserRequest;

class UserController extends AbstractController
{
    
    public function render(
        UsersEntity $usersEntity,
        ValidateHelper $validateHelper,
        UserRequest $userRequest,
        OrdersEntity $ordersEntity,
        OrderStatusEntity $orderStatusEntity,
        UserHelper $userHelper,
        CommentsHelper $commentsHelper,
        OrdersHelper $ordersHelper
    ) {
        if (empty($this->user->id)) {
            $this->response->redirectTo(Router::generateUrl('login', [], true));
        }

        if ($user = $userRequest->postProfileUser()) {
            /*Валидация данных*/
            if ($error = $validateHelper->getUserError($user, $this->user->id)) {
                $this->design->assign('error', $error);
            } elseif($usersEntity->update($this->user->id, $user)) {
                $this->user = $usersEntity->get((int)$this->user->id);
                $this->design->assign('user', $this->user);
                $this->design->assign('user_updated', true, true);
            } else {
                $this->design->assign('error', 'unknown error');
            }

            if ($password = $this->request->post('password')) {
                $usersEntity->update($this->user->id, ['password'=>$password]);
            }
        }

        /*Выборка истории заказов клиента*/
        $orders = $ordersEntity->mappedBy('id')->find(['user_id'=>$this->user->id]);
        
        foreach ($orders as $order) {
            $order->purchases = $ordersHelper->getOrderPurchasesList(intval($order->id));

            // Скидки
            $order->discounts = $ordersHelper->getDiscounts($order->id);
        }

        $allStatuses = $orderStatusEntity->mappedBy('id')->find();

        $paymentMethods = $userHelper->getPaymentMethodsListForUser();
        $deliveries = $userHelper->getDeliveriesListForUser($paymentMethods);
        $this->design->assign('payment_methods', $paymentMethods);
        $this->design->assign('deliveries', $deliveries);
        
        if (!empty($this->user->preferred_payment_method_id) && isset($paymentMethods[$this->user->preferred_payment_method_id])) {
            $this->design->assign('active_payment', $paymentMethods[$this->user->preferred_payment_method_id]);
        }
        
        if (!empty($this->user->preferred_delivery_id) && isset($deliveries[$this->user->preferred_delivery_id])) {
            $activeDelivery = $deliveries[$this->user->preferred_delivery_id];
        } else {
            $activeDelivery = reset($deliveries);
        }
        $this->design->assign('active_delivery', $activeDelivery);

        $userComments = $commentsHelper->getList(['user_id' => $this->user->id]);
        $userComments = $commentsHelper->attachTargetEntitiesToComments($userComments);
        $userComments = $commentsHelper->attachAnswers($userComments);
        $this->design->assign('user_comments', $userComments);
        
        $this->design->assign('orders_status', $allStatuses);
        $this->design->assign('orders', $orders);

        $activeTab = null;
        switch (Router::getCurrentRouteName()) {
            case 'user_orders':
                $activeTab = 'orders';
                break;
            case 'user_comments':
                $activeTab = 'comments';
                break;
            case 'user_favorites':
                $activeTab = 'favorites';
                break;
            case 'user_browsed':
                $activeTab = 'browsed';
                break;
        }
        
        $this->design->assign('active_tab', $activeTab);
        $this->design->assign('meta_title', $this->user->name);
        
        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('user', [], true));
        
        $this->response->setContent('user.tpl');
    }
    
    public function register(UserHelper $userHelper, UserRequest $userRequest, ValidateHelper $validateHelper)
    {
        if (!empty($this->user->id)) {
            $this->response->redirectTo(Router::generateUrl('user', [], true));
        }

        if ($this->request->method('post') && ($user = $userRequest->postRegisterUser())) {
            /*Валидация данных клиента*/
            if ($error = $validateHelper->getUserRegisterError($user)) {
                $this->design->assign('error', $error);
            } elseif ($userId = $userHelper->register($user)) {
                $this->response->redirectTo(Router::generateUrl('user', [], true));
            } else {
                $this->design->assign('error', 'unknown error');
            }
        }

        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('register', [], true));
        
        $this->response->setContent('register.tpl');
    }

    public function login(UserHelper $userHelper, ValidateHelper $validateHelper)
    {
        if (!empty($this->user->id)) {
            $this->response->redirectTo(Router::generateUrl('user', [], true));
        }

        if ($this->request->method('post')) {
            $email    = $this->request->post('email');
            $password = $this->request->post('password');
            $this->design->assign('email', $email);

            if ($error = $validateHelper->getUserLoginError($email, $password)) {
                $this->design->assign('error', $error);
            } elseif ($userId = $userHelper->login($email, $password)) {
                $this->response->redirectTo(Router::generateUrl('user', [], true));
            } else {
                $this->design->assign('error', 'unknown error');
            }
        }

        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('login', [], true));
        
        $this->response->setContent('login.tpl');
    }
    
    public function logout(UserHelper $userHelper)
    {
        $userHelper->logout();
        $this->response->redirectTo(Router::generateUrl('main', [], true));
        return;
    }
    
    public function passwordRemind(UsersEntity $usersEntity, Notify $notify, UserHelper $userHelper, $code = '')
    {
        
        if (!empty($code)) {

            // Выбераем пользователя из базы
            $users = $usersEntity->find(['remind_code'=>$code, 'limit'=>1]); // todo переделать когда будут методы getByField()
            if (empty($users)) {
                return false;
            }

            $user = reset($users);

            $usersEntity->update($user->id, ['remind_code'=>null, 'remind_expire'=>null]);
            if (date('Y-m-d H:i:s') > $user->remind_expire) {
                return false;
            }

            // Залогиниваемся под пользователем и переходим в кабинет для изменения пароля
            $_SESSION['user_id'] = $user->id;
            
            $userHelper->mergeCart();
            $userHelper->mergeWishlist();
            $userHelper->mergeComparison();
            $userHelper->mergeBrowsedProducts();
            
            $this->response->redirectTo(Router::generateUrl('user', [], true));
        }
        
        // Если запостили email
        if ($this->request->method('post') && $this->request->post('email')) {
            $email = $this->request->post('email');
            $this->design->assign('email', $email);
            
            // Выбираем пользователя из базы
            $user = $usersEntity->get($email);
            if (!empty($user->id)) {
                // Генерируем секретный код и запишем в базу с датой до которой он будет активен (+5 минут от текущей)
                $code = md5(uniqid($this->config->salt, true));
                
                $usersEntity->update($user->id, ['remind_code'=>$code, 'remind_expire'=>date('Y-m-d H:i:s', time()+300)]);

                // Отправляем письмо пользователю для восстановления пароля
                $notify->emailPasswordRemind($user->id, $code);
                $this->design->assign('email_sent', true);
            } else {
                $this->design->assign('error', 'user_not_found');
            }
        }

        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('password_remind', [], true));
        
        $this->response->setContent('password_remind.tpl');
    }
 
    public function wellKnownChangePassword()
    {
        Response::redirectTo(Router::generateUrl('user', [], true), 302);
    }
    
}
