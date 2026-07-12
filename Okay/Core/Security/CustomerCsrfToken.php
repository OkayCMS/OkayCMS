<?php

declare(strict_types=1);

namespace Okay\Core\Security;

final class CustomerCsrfToken
{
    private const SESSION_KEY = 'customer_csrf_token';
    private const COOKIE_KEY = 'okay_customer_csrf';

    public static function get(): string
    {
        if (!empty($_SESSION[self::SESSION_KEY]) && is_string($_SESSION[self::SESSION_KEY])) {
            return $_SESSION[self::SESSION_KEY];
        }

        $cookieToken = $_COOKIE[self::COOKIE_KEY] ?? null;
        if (is_string($cookieToken) && self::isToken($cookieToken)) {
            $_SESSION[self::SESSION_KEY] = $cookieToken;

            return $_SESSION[self::SESSION_KEY];
        }

        return self::rotate();
    }

    public static function check(?string $token): bool
    {
        if ($token === null || !self::isToken($token)) {
            return false;
        }

        if (!empty($_SESSION[self::SESSION_KEY]) && is_string($_SESSION[self::SESSION_KEY])) {
            return hash_equals($_SESSION[self::SESSION_KEY], $token);
        }

        $cookieToken = $_COOKIE[self::COOKIE_KEY] ?? null;
        if (is_string($cookieToken) && self::isToken($cookieToken)) {
            return hash_equals($cookieToken, $token);
        }

        return false;
    }

    public static function rotate(): string
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        self::setCookie($_SESSION[self::SESSION_KEY]);

        return $_SESSION[self::SESSION_KEY];
    }

    private static function isToken(string $token): bool
    {
        return strlen($token) === 64 && ctype_xdigit($token);
    }

    private static function setCookie(string $token): void
    {
        if (headers_sent()) {
            $_COOKIE[self::COOKIE_KEY] = $token;
            return;
        }

        setcookie(self::COOKIE_KEY, $token, [
            'expires' => 0,
            'path' => '/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::COOKIE_KEY] = $token;
    }
}
