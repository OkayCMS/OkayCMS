<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Security\CheckoutToken;
use PHPUnit\Framework\TestCase;

final class CheckoutTokenTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testCheckoutTokenIsOneTimeUse(): void
    {
        $token = CheckoutToken::get();

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
        self::assertTrue(CheckoutToken::consume($token));
        self::assertFalse(CheckoutToken::consume($token));
        self::assertNotSame($token, CheckoutToken::get());
    }

    public function testCheckoutTokenFailsClosedForMissingOrMalformedToken(): void
    {
        CheckoutToken::get();

        self::assertFalse(CheckoutToken::consume(null));
        self::assertFalse(CheckoutToken::consume('wrong'));
    }

    public function testCheckoutFingerprintFallbackRejectsImmediateDuplicate(): void
    {
        $fingerprint = hash('sha256', 'same checkout payload');

        self::assertTrue(CheckoutToken::consumeFingerprint($fingerprint));
        self::assertFalse(CheckoutToken::consumeFingerprint($fingerprint));
    }
}
