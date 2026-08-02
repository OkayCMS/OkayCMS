<?php

declare(strict_types=1);

namespace Okay\Core\Security;

use Okay\Core\Config;

final class CustomerRecoveryToken
{
    public const TTL_SECONDS = 3600;
    public const TOKEN_BYTES = 32;

    public function __construct(private Config $config)
    {
    }

    public function create(): string
    {
        return bin2hex(random_bytes(self::TOKEN_BYTES));
    }

    public function isValidFormat(string $token): bool
    {
        return strlen($token) === self::TOKEN_BYTES * 2 && ctype_xdigit($token);
    }

    public function digest(string $token): string
    {
        return hash_hmac('sha256', strtolower($token), $this->config->token('customer-recovery-token'));
    }

    public function expiresAt(?int $issuedAt = null): string
    {
        $issuedAt ??= time();

        return date('Y-m-d H:i:s', $issuedAt + self::TTL_SECONDS);
    }
}
