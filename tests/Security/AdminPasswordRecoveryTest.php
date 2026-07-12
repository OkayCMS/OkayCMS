<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class AdminPasswordRecoveryTest extends TestCase
{
    public function testRecoveryUsesBoundManagerAndCannotCreateManagers(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/backend/Controllers/AuthAdmin.php');

        self::assertIsString($source);
        self::assertStringContainsString('new AdminRecoveryToken($this->config)', $source);
        self::assertStringContainsString('$recoveryToken->create((int)$managerToRecovery->id', $source);
        self::assertStringNotContainsString("\$this->request->post('new_login')", $source);
        self::assertStringNotContainsString("\$managersEntity->add(['login'", $source);
    }

    public function testRecoveryDoesNotEnumerateKnownAdminEmails(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/backend/Controllers/AuthAdmin.php');

        self::assertIsString($source);
        self::assertStringContainsString("\$result->send = true;", $source);
        self::assertStringNotContainsString("\$result->error = 'not_admin_email';", $source);
    }

    public function testRecoveryRequiresNonEmptyConfirmedPasswordBeforeLogin(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/backend/Controllers/AuthAdmin.php');

        self::assertIsString($source);
        self::assertStringContainsString("trim(\$new_password) === ''", $source);
        self::assertStringContainsString("\$this->design->assign('error_message', 'password_empty');", $source);
        self::assertStringContainsString("\$new_password !== \$new_password_check", $source);
        self::assertStringContainsString("\$this->design->assign('error_message', 'password_wrong');", $source);

        $emptyPasswordGuard = strpos($source, "trim(\$new_password) === ''");
        $sessionLogin = strpos($source, "\$_SESSION['admin'] = \$manager->login;");

        self::assertIsInt($emptyPasswordGuard);
        self::assertIsInt($sessionLogin);
        self::assertLessThan($sessionLogin, $emptyPasswordGuard);
    }

    public function testRecoveryAndLoginRedirectsDoNotAppendBackendToCurrentRecoveryUrl(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/backend/Controllers/AuthAdmin.php');

        self::assertIsString($source);
        self::assertStringContainsString(
            "\$response->redirectTo(\$this->request->getRootUrl() . '/backend/index.php');",
            $source
        );
        self::assertStringContainsString(
            "\$_SESSION['before_auth_url'] : \$this->request->getRootUrl() . '/backend/index.php'",
            $source
        );
        self::assertStringNotContainsString(
            "\$this->request->getBasePathWithDomain() . '/backend/index.php'",
            $source
        );
    }

    public function testRecoveryLinkIsNotBypassedByExistingAdminSession(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/backend/index.php');

        self::assertIsString($source);
        self::assertStringContainsString(
            "\$hasRecoveryCode = \$backendControllerName == 'AuthAdmin' && (string)\$request->get('code') !== '';",
            $source
        );
        self::assertStringContainsString(
            "if (\$manager && \$backendControllerName == 'AuthAdmin' && !\$hasRecoveryCode) {",
            $source
        );
    }

    public function testRecoverySuccessTextDoesNotClaimMessageWasSentToUnknownEmail(): void
    {
        $template = file_get_contents(dirname(__DIR__, 2) . '/backend/design/html/auth.tpl');

        self::assertIsString($template);
        self::assertStringContainsString('Если указанный email принадлежит администратору', $template);
        self::assertStringNotContainsString('Сообщение отправлено на емейл администратору', $template);
    }

    public function testRecoveryTemplateShowsPasswordValidationErrors(): void
    {
        $template = file_get_contents(dirname(__DIR__, 2) . '/backend/design/html/auth.tpl');

        self::assertIsString($template);
        self::assertStringContainsString("error_message == 'password_empty'", $template);
        self::assertStringContainsString('Введите новый пароль.', $template);
        self::assertStringContainsString("error_message == 'password_wrong'", $template);
        self::assertStringContainsString('Введенные пароли не совпадают.', $template);
        self::assertStringContainsString('Сменить пароль', $template);
    }
}
