<?php

namespace Okay\Controllers;

use Okay\Core\Notify;
use Okay\Core\Response;
use Okay\Core\Security\CustomerRecoveryToken;
use Okay\Core\Security\CustomerSession;
use Okay\Entities\UserAuthAttemptsEntity;
use Okay\Entities\UserPasswordRecoveryTokensEntity;
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
    private const RECOVERY_SESSION_KEY = 'customer_password_recovery';
    private const RECOVERY_COOKIE_KEY = 'okay_customer_recovery';
    private const RECOVERY_SESSION_TTL = 900;

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
            return;
        }

        $currentUser = $this->user;
        $currentUserId = (int)$currentUser->id;

        if ($user = $userRequest->postProfileUser()) {
            /*Валидация данных*/
            if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
                $this->design->assign('error', $error);
            } elseif ($error = $validateHelper->getUserError($user, $currentUser->id)) {
                $this->design->assign('error', $error);
            } elseif ($error = $this->getProfilePasswordError($usersEntity, $currentUser)) {
                $this->design->assign('error', $error);
            } elseif ($usersEntity->update($currentUser->id, $user)) {
                $newPassword = (string)$this->request->post('new_password');
                if ($newPassword !== '') {
                    $usersEntity->update($currentUser->id, ['password' => $newPassword]);
                    CustomerSession::regenerate();
                }
                $this->user = $usersEntity->get($currentUserId);
                $this->design->assign('user', $this->user);
                $this->design->assign('user_updated', true, true);
            } else {
                $this->design->assign('error', 'unknown error');
            }
        }

        /*Выборка истории заказов клиента*/
        $orders = $ordersEntity->mappedBy('id')->find(['user_id' => $this->user->id]);

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
            default:
                $activeTab = $userHelper->defaultActiveTab(Router::getCurrentRouteName());
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
            if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
                $this->design->assign('error', $error);
            } elseif ($error = $validateHelper->getUserRegisterError($user)) {
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

    public function login(
        UserHelper $userHelper,
        ValidateHelper $validateHelper,
        UserAuthAttemptsEntity $userAuthAttemptsEntity
    ) {
        if (!empty($this->user->id)) {
            $this->response->redirectTo(Router::generateUrl('user', [], true));
        }

        if ($this->request->method('post')) {
            $email    = $this->request->post('email');
            $password = $this->request->post('password');
            $this->design->assign('email', $email);
            $ip = $this->clientIp();

            if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
                $this->design->assign('error', $error);
            } elseif ($error = $validateHelper->getUserLoginError($email, $password)) {
                $this->design->assign('error', $error);
            } elseif ($userAuthAttemptsEntity->isBlocked(UserAuthAttemptsEntity::ACTION_LOGIN, (string)$email, $ip)) {
                $this->design->assign('error', 'rate_limit');
            } elseif ($userId = $userHelper->login($email, $password)) {
                $userAuthAttemptsEntity->clear(UserAuthAttemptsEntity::ACTION_LOGIN, (string)$email, $ip);
                $this->response->redirectTo(Router::generateUrl('user', [], true));
            } else {
                $userAuthAttemptsEntity->registerFailure(UserAuthAttemptsEntity::ACTION_LOGIN, (string)$email, $ip);
                $this->design->assign('error', 'login_incorrect');
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

    /**
     * @param string $code
     */
    public function passwordRemind(
        UsersEntity $usersEntity,
        Notify $notify,
        UserHelper $userHelper,
        UserPasswordRecoveryTokensEntity $recoveryTokensEntity,
        UserAuthAttemptsEntity $userAuthAttemptsEntity,
        ValidateHelper $validateHelper,
        $code = ''
    ) {
        $recoveryToken = new CustomerRecoveryToken($this->config);

        if (!empty($code)) {
            $this->handleRecoveryLink($code, $recoveryToken, $recoveryTokensEntity);
        }

        if ($this->request->method('post') && $this->request->post('reset_password')) {
            $this->handleRecoveryPasswordSubmit($usersEntity, $userHelper, $recoveryTokensEntity, $validateHelper);
        } elseif ($this->request->method('post') && $this->request->post('email')) {
            $email = $this->request->post('email');
            $this->design->assign('email', $email);
            $ip = $this->clientIp();

            if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
                $this->design->assign('error', $error);
            } elseif (!filter_var((string)$email, FILTER_VALIDATE_EMAIL)) {
                $this->design->assign('error', 'empty_email');
            } else {
                if (!$userAuthAttemptsEntity->isBlocked(UserAuthAttemptsEntity::ACTION_PASSWORD_REMIND, (string)$email, $ip)) {
                    if ($user = $usersEntity->get((string)$email)) {
                        $token = $recoveryToken->create();
                        $recoveryTokensEntity->createToken(
                            (int)$user->id,
                            $recoveryToken->digest($token),
                            $recoveryToken->expiresAt(),
                            $ip
                        );
                        $usersEntity->update($user->id, ['remind_code' => null, 'remind_expire' => null]);
                        $notify->emailPasswordRemind($user->id, $token);
                    }
                    $userAuthAttemptsEntity->registerFailure(
                        UserAuthAttemptsEntity::ACTION_PASSWORD_REMIND,
                        (string)$email,
                        $ip
                    );
                }

                $this->design->assign('email_sent', true);
            }
        }

        $this->assignRecoveryState($recoveryTokensEntity);

        $this->design->assign('noindex_follow', true);
        $this->design->assign('canonical', Router::generateUrl('password_remind', [], true));

        $this->response->setContent('password_remind.tpl');
    }

    public function wellKnownChangePassword()
    {
        Response::redirectTo(Router::generateUrl('user', [], true), 302);
    }

    private function handleRecoveryLink(
        string $code,
        CustomerRecoveryToken $recoveryToken,
        UserPasswordRecoveryTokensEntity $recoveryTokensEntity
    ): void {
        $this->clearRecoveryState();

        if ($recoveryToken->isValidFormat($code)) {
            $token = $recoveryTokensEntity->getActiveByDigest($recoveryToken->digest($code));
            if ($token !== false) {
                /** @var object{id: int|string, user_id: int|string, expires_at: string} $token */
                $this->storeRecoveryState($token);

                header('Referrer-Policy: no-referrer');
                $this->response->redirectTo(Router::generateUrl('password_remind', [], true));
                return;
            }
        }

        $this->design->assign('error', 'password_remind_invalid_token');
    }

    private function handleRecoveryPasswordSubmit(
        UsersEntity $usersEntity,
        UserHelper $userHelper,
        UserPasswordRecoveryTokensEntity $recoveryTokensEntity,
        ValidateHelper $validateHelper
    ): void {
        $state = $this->getRecoveryState($recoveryTokensEntity);
        $newPassword = (string)$this->request->post('new_password');
        $newPasswordCheck = (string)$this->request->post('new_password_check');

        if ($error = $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'))) {
            $this->design->assign('error', $error);
        } elseif ($state === null) {
            $this->design->assign('error', 'password_remind_invalid_token');
        } elseif (trim($newPassword) === '') {
            $this->design->assign('error', 'empty_password');
        } elseif ($newPassword !== $newPasswordCheck) {
            $this->design->assign('error', 'password_wrong');
        } elseif (!$recoveryTokensEntity->consume($state['token_id'], $state['user_id'])) {
            $this->clearRecoveryState();
            $this->design->assign('error', 'password_remind_invalid_token');
        } else {
            $usersEntity->update($state['user_id'], ['password' => $newPassword, 'remind_code' => null, 'remind_expire' => null]);
            $this->clearRecoveryState();

            if (!empty($this->user->id) && (int)$this->user->id !== $state['user_id']) {
                unset($_SESSION['user_id']);
                CustomerSession::regenerate();
                $this->response->redirectTo(Router::generateUrl('login', [], true));
                return;
            }

            CustomerSession::regenerate();
            $_SESSION['user_id'] = $state['user_id'];
            $userHelper->mergeCart();
            $userHelper->mergeWishlist();
            $userHelper->mergeComparison();
            $userHelper->mergeBrowsedProducts();
            $this->response->redirectTo(Router::generateUrl('user', [], true));
        }
    }

    private function assignRecoveryState(UserPasswordRecoveryTokensEntity $recoveryTokensEntity): void
    {
        $this->design->assign('recovery_mode', $this->getRecoveryState($recoveryTokensEntity) !== null);
    }

    /**
     * @return array{token_id: int, user_id: int}|null
     */
    private function getRecoveryState(UserPasswordRecoveryTokensEntity $recoveryTokensEntity): ?array
    {
        $state = $this->normalizeRecoveryState($_SESSION[self::RECOVERY_SESSION_KEY] ?? null);
        if ($state === null) {
            $state = $this->readRecoveryCookieState();
            if ($state === null) {
                $this->clearRecoveryState();
                return null;
            }

            $_SESSION[self::RECOVERY_SESSION_KEY] = $state;
        }

        if ($state['expires_at'] < time()) {
            $this->clearRecoveryState();
            return null;
        }

        $token = $recoveryTokensEntity->getActiveById($state['token_id'], $state['user_id']);
        if ($token === false) {
            $this->clearRecoveryState();
            return null;
        }

        return [
            'token_id' => $state['token_id'],
            'user_id' => $state['user_id'],
        ];
    }

    /**
     * @param object{id: int|string, user_id: int|string, expires_at: string} $token
     */
    private function storeRecoveryState(object $token): void
    {
        $tokenExpiresAt = strtotime((string)$token->expires_at);
        if ($tokenExpiresAt === false) {
            $tokenExpiresAt = time();
        }

        $state = [
            'token_id' => (int)$token->id,
            'user_id' => (int)$token->user_id,
            'expires_at' => min($tokenExpiresAt, time() + self::RECOVERY_SESSION_TTL),
        ];

        $_SESSION[self::RECOVERY_SESSION_KEY] = $state;
        $this->setRecoveryCookie($this->encodeRecoveryState($state), $state['expires_at']);
    }

    /**
     * @return array{token_id: int, user_id: int, expires_at: int}|null
     */
    private function readRecoveryCookieState(): ?array
    {
        $cookie = $_COOKIE[self::RECOVERY_COOKIE_KEY] ?? null;
        if (!is_string($cookie)) {
            return null;
        }

        $separator = strrpos($cookie, '.');
        if ($separator === false) {
            return null;
        }

        $payload = substr($cookie, 0, $separator);
        $signature = substr($cookie, $separator + 1);
        if (!hash_equals($this->recoveryStateSignature($payload), $signature)) {
            return null;
        }

        $decodedPayload = $this->base64UrlDecode($payload);
        if ($decodedPayload === null) {
            return null;
        }

        $state = json_decode($decodedPayload, true);
        return $this->normalizeRecoveryState($state);
    }

    private function clearRecoveryState(): void
    {
        unset($_SESSION[self::RECOVERY_SESSION_KEY], $_COOKIE[self::RECOVERY_COOKIE_KEY]);

        if (!headers_sent()) {
            setcookie(self::RECOVERY_COOKIE_KEY, '', [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => $this->isHttps(),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }

    /**
     * @param array{token_id: int, user_id: int, expires_at: int} $state
     */
    private function encodeRecoveryState(array $state): string
    {
        $json = json_encode($state);
        if (!is_string($json)) {
            return '';
        }

        $payload = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');

        return $payload . '.' . $this->recoveryStateSignature($payload);
    }

    /**
     * @param mixed $state
     * @return array{token_id: int, user_id: int, expires_at: int}|null
     */
    private function normalizeRecoveryState(mixed $state): ?array
    {
        if (!is_array($state)) {
            return null;
        }

        $tokenId = filter_var($state['token_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $userId = filter_var($state['user_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $expiresAt = filter_var($state['expires_at'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        if ($tokenId === false || $userId === false || $expiresAt === false) {
            return null;
        }

        return [
            'token_id' => $tokenId,
            'user_id' => $userId,
            'expires_at' => $expiresAt,
        ];
    }

    private function setRecoveryCookie(string $value, int $expiresAt): void
    {
        if ($value === '') {
            return;
        }

        $_COOKIE[self::RECOVERY_COOKIE_KEY] = $value;

        if (headers_sent()) {
            return;
        }

        setcookie(self::RECOVERY_COOKIE_KEY, $value, [
            'expires' => $expiresAt,
            'path' => '/',
            'secure' => $this->isHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    private function recoveryStateSignature(string $payload): string
    {
        return $this->config->token('customer-recovery-state:' . $payload);
    }

    private function base64UrlDecode(string $payload): ?string
    {
        $padding = strlen($payload) % 4;
        if ($padding > 0) {
            $payload .= str_repeat('=', 4 - $padding);
        }

        $decodedPayload = base64_decode(strtr($payload, '-_', '+/'), true);
        return is_string($decodedPayload) ? $decodedPayload : null;
    }

    private function isHttps(): bool
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }

    private function getProfilePasswordError(UsersEntity $usersEntity, object $currentUser): ?string
    {
        $currentPassword = (string)$this->request->post('current_password');
        $newPassword = (string)$this->request->post('new_password');
        $newPasswordCheck = (string)$this->request->post('new_password_check');
        $passwordHash = isset($currentUser->password) ? (string)$currentUser->password : '';

        if ($currentPassword === '' && $newPassword === '' && $newPasswordCheck === '') {
            return null;
        }

        if ($currentPassword === '' || !$usersEntity->verifyPassword($currentPassword, $passwordHash)) {
            return 'password_current_wrong';
        }

        if (trim($newPassword) === '') {
            return 'empty_password';
        }

        if ($newPassword !== $newPasswordCheck) {
            return 'password_wrong';
        }

        return null;
    }

    private function clientIp(): string
    {
        return (string)($_SERVER['REMOTE_ADDR'] ?? '');
    }
}
