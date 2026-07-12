<?php

declare(strict_types=1);

namespace Okay\Core\Security;

final class AdminSession
{
    public const SESSION_NAME = 'okay_admin_sid';

    /**
     * @param array<string, mixed> $server
     */
    public static function isSecureRequest(array $server): bool
    {
        if ((int)($server['SERVER_PORT'] ?? 0) === 443) {
            return true;
        }

        if (in_array((string)($server['HTTPS'] ?? ''), ['on', '1'], true)) {
            return true;
        }

        if (($server['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
            return true;
        }

        return ($server['HTTP_X_FORWARDED_SSL'] ?? '') === 'on';
    }

    /**
     * @param array<string, mixed> $server
     */
    public static function configureCookieParams(array $server): void
    {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => self::isSecureRequest($server),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }

    public static function regenerateId(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
            $_SESSION['id'] = session_id();
        }
    }

    /**
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $server
     */
    public static function syncFrontendAdmin(array $cookies, array $server): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        $adminLogin = self::adminLoginFromCookie($cookies, $server);
        if ($adminLogin === null) {
            unset($_SESSION['admin']);
            return;
        }

        $_SESSION['admin'] = $adminLogin;
    }

    /**
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $server
     */
    public static function destroyFromCookie(array $cookies, array $server): void
    {
        $sessionId = self::sessionIdFromCookies($cookies);
        if ($sessionId === null) {
            self::deleteCookie($server);
            return;
        }

        $previousSession = self::suspendCurrentSession();
        try {
            session_name(self::SESSION_NAME);
            self::configureCookieParams($server);
            session_id($sessionId);
            session_start();
            $_SESSION = [];
            session_destroy();
            self::deleteCookie($server);
        } finally {
            self::restoreSession($previousSession);
        }
    }

    /**
     * @param array<string, mixed> $cookies
     * @param array<string, mixed> $server
     */
    private static function adminLoginFromCookie(array $cookies, array $server): ?string
    {
        $sessionId = self::sessionIdFromCookies($cookies);
        if ($sessionId === null) {
            return null;
        }

        $previousSession = self::suspendCurrentSession();
        try {
            session_name(self::SESSION_NAME);
            self::configureCookieParams($server);
            session_id($sessionId);
            session_start(['read_and_close' => true]);

            $adminLogin = $_SESSION['admin'] ?? null;
            return is_string($adminLogin) && $adminLogin !== '' ? $adminLogin : null;
        } finally {
            self::restoreSession($previousSession);
        }
    }

    /**
     * @param array<string, mixed> $cookies
     */
    private static function sessionIdFromCookies(array $cookies): ?string
    {
        $sessionId = $cookies[self::SESSION_NAME] ?? null;
        if (!is_string($sessionId) || $sessionId === '') {
            return null;
        }

        if (!preg_match('/^[A-Za-z0-9,-]{16,128}$/', $sessionId)) {
            return null;
        }

        return $sessionId;
    }

    /**
     * @return array{active: bool, name: string, id: string}
     */
    private static function suspendCurrentSession(): array
    {
        $sessionName = session_name();
        $sessionId = session_id();
        $session = [
            'active' => session_status() === PHP_SESSION_ACTIVE,
            'name' => is_string($sessionName) ? $sessionName : '',
            'id' => is_string($sessionId) ? $sessionId : '',
        ];

        if ($session['active']) {
            session_write_close();
        }

        return $session;
    }

    /**
     * @param array{active: bool, name: string, id: string} $session
     */
    private static function restoreSession(array $session): void
    {
        if (!$session['active']) {
            return;
        }

        session_name($session['name']);
        if ($session['id'] !== '') {
            session_id($session['id']);
        }
        session_start();
    }

    /**
     * @param array<string, mixed> $server
     */
    private static function deleteCookie(array $server): void
    {
        setcookie(self::SESSION_NAME, '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => self::isSecureRequest($server),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
