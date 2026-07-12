<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Managers;
use PHPUnit\Framework\TestCase;

final class AdminLoginPasswordValidationTest extends TestCase
{
    public function testInvalidStoredPasswordHashFailsWithoutWarning(): void
    {
        $managers = new Managers();

        self::assertFalse($managers->checkPassword('secret', ''));
        self::assertFalse($managers->checkPassword('secret', 'not-a-hash'));
        self::assertFalse($managers->checkPassword('secret', '$broken$hash'));
        self::assertFalse($managers->checkPassword('secret', '$apr1$12345678$short'));
    }

    public function testModernPasswordHashesPassAndDoNotNeedRehash(): void
    {
        $managers = new Managers();
        $hash = $managers->hashPassword('secret');

        self::assertMatchesRegularExpression('/^\$(argon2id|2y)\$/', $hash);
        self::assertTrue($managers->checkPassword('secret', $hash));
        self::assertFalse($managers->checkPassword('wrong', $hash));
        self::assertFalse($managers->needsPasswordRehash($hash));
    }

    public function testValidApr1HashStillPasses(): void
    {
        $managers = new Managers();
        $hash = $managers->cryptApr1Md5('secret', '12345678');

        self::assertTrue($managers->checkPassword('secret', $hash));
        self::assertFalse($managers->checkPassword('wrong', $hash));
        self::assertTrue($managers->needsPasswordRehash($hash));
    }

    public function testValidLegacySaltedMd5HashStillPasses(): void
    {
        $managers = new Managers();
        $hash = md5('8e86a279d6e182b3c811c559e6b15484' . 'secret' . md5('secret'));

        self::assertTrue($managers->checkPassword('secret', $hash));
        self::assertFalse($managers->checkPassword('wrong', $hash));
        self::assertTrue($managers->needsPasswordRehash($hash));
    }

    public function testValidLegacyRawMd5HashStillPasses(): void
    {
        $managers = new Managers();
        $hash = md5('secret');

        self::assertTrue($managers->checkPassword('secret', $hash));
        self::assertFalse($managers->checkPassword('wrong', $hash));
        self::assertTrue($managers->needsPasswordRehash($hash));
    }

    public function testManagerWritesUseModernPasswordHashing(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/Okay/Entities/ManagersEntity.php');

        self::assertIsString($source);
        self::assertStringContainsString('$managersCore->hashPassword($manager->password)', $source);
        self::assertStringContainsString('rehashPasswordIfNeeded', $source);
        self::assertStringNotContainsString('$managersCore->cryptApr1Md5($manager->password)', $source);
    }

    public function testAdminPasswordChecksRehashLegacyHashesAfterSuccess(): void
    {
        $authAdmin = file_get_contents(dirname(__DIR__, 2) . '/backend/Controllers/AuthAdmin.php');
        $backendValidateHelper = file_get_contents(dirname(__DIR__, 2) . '/backend/Helpers/BackendValidateHelper.php');
        $integration1c = file_get_contents(dirname(__DIR__, 2) . '/Okay/Modules/OkayCMS/Integration1C/Integration/Integration1C.php');

        self::assertIsString($authAdmin);
        self::assertIsString($backendValidateHelper);
        self::assertIsString($integration1c);
        self::assertStringContainsString('rehashPasswordIfNeeded((int)$manager->id', $authAdmin);
        self::assertStringContainsString('rehashPasswordIfNeeded((int)$manager->id', $backendValidateHelper);
        self::assertStringContainsString('rehashPasswordIfNeeded((int)$manager1c->id', $integration1c);
    }

    public function testAdminLoginChecksValidPasswordBeforeTryLimitBlock(): void
    {
        $authAdmin = file_get_contents(dirname(__DIR__, 2) . '/backend/Controllers/AuthAdmin.php');

        self::assertIsString($authAdmin);

        $passwordCheck = strpos($authAdmin, '$passwordIsValid = $managers->checkPassword($pass, $manager->password);');
        $limitBlock = strpos($authAdmin, "} elseif (\$manager->cnt_try > \$limit) {");

        self::assertIsInt($passwordCheck);
        self::assertIsInt($limitBlock);
        self::assertLessThan($limitBlock, $passwordCheck);
    }
}
