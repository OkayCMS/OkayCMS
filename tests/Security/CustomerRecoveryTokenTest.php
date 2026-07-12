<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Config;
use Okay\Core\Security\CustomerRecoveryToken;
use PHPUnit\Framework\TestCase;

final class CustomerRecoveryTokenTest extends TestCase
{
    public function testTokenIsOpaqueAndServerStoresDigest(): void
    {
        $token = new CustomerRecoveryToken($this->config());
        $plain = $token->create();
        $digest = $token->digest($plain);

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $plain);
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $digest);
        self::assertNotSame($plain, $digest);
        self::assertTrue($token->isValidFormat($plain));
    }

    public function testMalformedTokensFailFormatCheck(): void
    {
        $token = new CustomerRecoveryToken($this->config());

        self::assertFalse($token->isValidFormat(''));
        self::assertFalse($token->isValidFormat('abc'));
        self::assertFalse($token->isValidFormat(str_repeat('g', 64)));
    }

    public function testExpirationUsesRecoveryTtl(): void
    {
        $token = new CustomerRecoveryToken($this->config());

        self::assertSame('2026-05-13 01:00:00', $token->expiresAt(strtotime('2026-05-13 00:00:00')));
    }

    private function config(): Config
    {
        return new class ('', '') extends Config {
            public function __construct(string $configFile, string $configLocalFile)
            {
            }

            public function token($text): string
            {
                return hash('sha256', $text . ':test-salt');
            }
        };
    }
}
