<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class CustomerPasswordRecoveryTest extends TestCase
{
    public function testRecoveryLinkCreatesResetStateInsteadOfCustomerSession(): void
    {
        $source = $this->userControllerSource();

        self::assertStringContainsString('handleRecoveryLink', $source);
        self::assertStringContainsString("self::RECOVERY_SESSION_KEY", $source);
        self::assertStringContainsString("self::RECOVERY_COOKIE_KEY", $source);
        self::assertStringContainsString('storeRecoveryState($token)', $source);
        self::assertStringContainsString("Router::generateUrl('password_remind', [], true)", $source);
        self::assertStringNotContainsString("\$_SESSION['user_id'] = \$user->id;", $source);
        self::assertStringNotContainsString("find(['remind_code' => \$code", $source);
    }

    public function testRecoveryStateCanSurviveCleanUrlRedirectAcrossSessionBoundary(): void
    {
        $source = $this->userControllerSource();

        self::assertStringContainsString('readRecoveryCookieState', $source);
        self::assertStringContainsString('normalizeRecoveryState($_SESSION[self::RECOVERY_SESSION_KEY] ?? null)', $source);
        self::assertStringContainsString('$_SESSION[self::RECOVERY_SESSION_KEY] = $state;', $source);
        self::assertStringContainsString('getActiveById($state[\'token_id\'], $state[\'user_id\'])', $source);
    }

    public function testRecoveryPasswordSubmitRequiresPasswordAndConsumesTokenBeforeLogin(): void
    {
        $source = $this->userControllerSource();

        self::assertStringContainsString("trim(\$newPassword) === ''", $source);
        self::assertStringContainsString("\$newPassword !== \$newPasswordCheck", $source);
        self::assertStringContainsString("\$recoveryTokensEntity->consume(\$state['token_id'], \$state['user_id'])", $source);

        $consume = strpos($source, "\$recoveryTokensEntity->consume(\$state['token_id'], \$state['user_id'])");
        $login = strpos($source, "\$_SESSION['user_id'] = \$state['user_id'];");

        self::assertIsInt($consume);
        self::assertIsInt($login);
        self::assertLessThan($login, $consume);
    }

    public function testRecoveryRequestDoesNotEnumerateCustomerAccounts(): void
    {
        $source = $this->userControllerSource();
        $template = file_get_contents(dirname(__DIR__, 2) . '/design/okay_shop/html/password_remind.tpl');

        self::assertIsString($template);
        self::assertStringContainsString("\$this->design->assign('email_sent', true);", $source);
        self::assertStringContainsString('password_remind_email_sent_generic', $template);
        self::assertStringNotContainsString('password_remind_user_not_found', $template);
    }

    public function testRecoveryUsesDedicatedTokenTableInsteadOfLegacyUserColumns(): void
    {
        $source = $this->userControllerSource();

        self::assertStringContainsString('UserPasswordRecoveryTokensEntity', $source);
        self::assertStringContainsString('$recoveryTokensEntity->createToken', $source);
        self::assertStringContainsString("'remind_code' => null", $source);
        self::assertStringNotContainsString('bin2hex(random_bytes(16))', $source);
    }

    private function userControllerSource(): string
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/Okay/Controllers/UserController.php');

        self::assertIsString($source);
        return $source;
    }
}
