<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class CustomerRecoveryTemplateTest extends TestCase
{
    public function testRecoveryTemplateHasRequestAndResetStates(): void
    {
        $template = $this->template();

        self::assertStringContainsString('{if $recovery_mode}', $template);
        self::assertStringContainsString('name="new_password"', $template);
        self::assertStringContainsString('name="new_password_check"', $template);
        self::assertStringContainsString('name="reset_password"', $template);
        self::assertStringContainsString('autocomplete="new-password"', $template);
    }

    public function testRecoveryTemplateUsesCustomerCsrfAndAccessibleErrors(): void
    {
        $template = $this->template();

        self::assertStringContainsString('name="customer_csrf_token"', $template);
        self::assertStringContainsString('{$customer_csrf_token|escape}', $template);
        self::assertStringContainsString('role="alert"', $template);
    }

    public function testRecoveryTemplateDoesNotExposeAccountExistenceOrToken(): void
    {
        $template = $this->template();

        self::assertStringContainsString('password_remind_email_sent_generic', $template);
        self::assertStringNotContainsString('$code', $template);
        self::assertStringNotContainsString('user_not_found', $template);
        self::assertStringNotContainsString('$email|escape', $template);
    }

    private function template(): string
    {
        $template = file_get_contents(dirname(__DIR__, 2) . '/design/okay_shop/html/password_remind.tpl');

        self::assertIsString($template);
        return $template;
    }
}
