<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Security\CustomerCsrfToken;
use PHPUnit\Framework\TestCase;

final class CustomerCsrfGuardTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        $_COOKIE = [];
    }

    public function testCustomerCsrfTokenIsNotThePhpSessionId(): void
    {
        $token = CustomerCsrfToken::get();

        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);
        self::assertNotSame(session_id(), $token);
        self::assertTrue(CustomerCsrfToken::check($token));
    }

    public function testCustomerCsrfTokenFailsClosedAndRotates(): void
    {
        $token = CustomerCsrfToken::get();

        self::assertFalse(CustomerCsrfToken::check(null));
        self::assertFalse(CustomerCsrfToken::check('wrong'));
        self::assertNotSame($token, CustomerCsrfToken::rotate());
    }

    public function testCustomerCsrfTokenCanValidateFromSameSiteCookieWhenSessionNamespaceChanges(): void
    {
        $token = CustomerCsrfToken::get();
        $_SESSION = [];

        self::assertTrue(CustomerCsrfToken::check($token));
    }

    public function testCustomerAuthFormsIncludeCsrfToken(): void
    {
        $root = dirname(__DIR__, 2);

        foreach (['login.tpl', 'register.tpl', 'password_remind.tpl', 'user.tpl'] as $template) {
            $source = file_get_contents($root . '/design/okay_shop/html/' . $template);
            self::assertIsString($source);
            self::assertStringContainsString('name="customer_csrf_token"', $source, $template);
        }
    }
}
