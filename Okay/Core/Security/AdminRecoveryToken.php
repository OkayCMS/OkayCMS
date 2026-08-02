<?php

declare(strict_types=1);

namespace Okay\Core\Security;

use Okay\Core\Config;

final class AdminRecoveryToken
{
    private const TTL_SECONDS = 3600;

    public function __construct(private Config $config)
    {
    }

    public function create(int $managerId, string $passwordHash, ?int $issuedAt = null): string
    {
        $issuedAt ??= time();
        $nonce = bin2hex(random_bytes(16));
        $payload = $managerId . ':' . $issuedAt . ':' . $nonce;

        return $this->encode($payload . ':' . $this->signature($payload, $passwordHash));
    }

    public function managerId(string $token, string $passwordHash, ?int $now = null): ?int
    {
        $parts = $this->parts($token);
        if ($parts === null) {
            return null;
        }

        [$managerId, $issuedAt, $nonce, $signature] = $parts;
        if (!ctype_digit($issuedAt) || $nonce === '') {
            return null;
        }

        $now ??= time();
        if ((int)$issuedAt > $now || $now - (int)$issuedAt > self::TTL_SECONDS) {
            return null;
        }

        $payload = $managerId . ':' . $issuedAt . ':' . $nonce;
        if (!hash_equals($this->signature($payload, $passwordHash), $signature)) {
            return null;
        }

        return (int)$managerId;
    }

    public function unverifiedManagerId(string $token): ?int
    {
        $parts = $this->parts($token);

        return $parts === null ? null : (int)$parts[0];
    }

    private function signature(string $payload, string $passwordHash): string
    {
        return $this->config->token($payload . ':' . $passwordHash);
    }

    private function encode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function decode(string $value): ?string
    {
        $value .= str_repeat('=', (4 - strlen($value) % 4) % 4);
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return is_string($decoded) ? $decoded : null;
    }

    /**
     * @return array{string, string, string, string}|null
     */
    private function parts(string $token): ?array
    {
        $decoded = $this->decode($token);
        if ($decoded === null) {
            return null;
        }

        $parts = explode(':', $decoded);
        if (count($parts) !== 4 || !ctype_digit($parts[0])) {
            return null;
        }

        return [$parts[0], $parts[1], $parts[2], $parts[3]];
    }
}
