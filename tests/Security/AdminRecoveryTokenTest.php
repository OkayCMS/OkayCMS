<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Config;
use Okay\Core\Security\AdminRecoveryToken;
use PHPUnit\Framework\TestCase;

final class AdminRecoveryTokenTest extends TestCase
{
    public function testTokenCarriesManagerIdentityWithoutBrowserSession(): void
    {
        $token = new AdminRecoveryToken($this->config());
        $code = $token->create(15, 'current-password-hash', 1000);

        self::assertSame(15, $token->unverifiedManagerId($code));
        self::assertSame(15, $token->managerId($code, 'current-password-hash', 1200));
    }

    public function testTokenFailsAfterPasswordHashChanges(): void
    {
        $token = new AdminRecoveryToken($this->config());
        $code = $token->create(15, 'old-password-hash', 1000);

        self::assertNull($token->managerId($code, 'new-password-hash', 1200));
    }

    public function testTokenExpires(): void
    {
        $token = new AdminRecoveryToken($this->config());
        $code = $token->create(15, 'current-password-hash', 1000);

        self::assertNull($token->managerId($code, 'current-password-hash', 5000));
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
