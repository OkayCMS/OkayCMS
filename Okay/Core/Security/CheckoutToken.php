<?php

declare(strict_types=1);

namespace Okay\Core\Security;

final class CheckoutToken
{
    private const SESSION_KEY = 'checkout_token';
    private const FINGERPRINT_SESSION_KEY = 'checkout_submission_fingerprint';
    private const FINGERPRINT_TTL_SECONDS = 600;

    public static function get(): string
    {
        if (!empty($_SESSION[self::SESSION_KEY]) && is_string($_SESSION[self::SESSION_KEY])) {
            return $_SESSION[self::SESSION_KEY];
        }

        return self::rotate();
    }

    public static function consume(?string $token): bool
    {
        if ($token === null || !self::isToken($token)) {
            return false;
        }

        if (empty($_SESSION[self::SESSION_KEY]) || !is_string($_SESSION[self::SESSION_KEY])) {
            return false;
        }

        if (!hash_equals($_SESSION[self::SESSION_KEY], $token)) {
            return false;
        }

        unset($_SESSION[self::SESSION_KEY]);

        return true;
    }

    public static function consumeFingerprint(string $fingerprint): bool
    {
        if (!self::isToken($fingerprint)) {
            return false;
        }

        $stored = $_SESSION[self::FINGERPRINT_SESSION_KEY] ?? null;
        $now = time();
        if (
            is_array($stored)
            && isset($stored['fingerprint'], $stored['expires_at'])
            && is_string($stored['fingerprint'])
            && (int) $stored['expires_at'] >= $now
            && hash_equals($stored['fingerprint'], $fingerprint)
        ) {
            return false;
        }

        $_SESSION[self::FINGERPRINT_SESSION_KEY] = [
            'fingerprint' => $fingerprint,
            'expires_at' => $now + self::FINGERPRINT_TTL_SECONDS,
        ];

        return true;
    }

    public static function rotate(): string
    {
        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));

        return $_SESSION[self::SESSION_KEY];
    }

    private static function isToken(string $token): bool
    {
        return strlen($token) === 64 && ctype_xdigit($token);
    }
}
