<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Request;
use Okay\Helpers\MainHelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class CustomerAuthHardeningTest extends TestCase
{
    public function testCustomerSessionRegeneratesOnPrivilegeTransitions(): void
    {
        $helper = file_get_contents(dirname(__DIR__, 2) . '/Okay/Helpers/UserHelper.php');
        $controller = file_get_contents(dirname(__DIR__, 2) . '/Okay/Controllers/UserController.php');

        self::assertIsString($helper);
        self::assertIsString($controller);
        self::assertStringContainsString('CustomerSession::regenerate();', $helper);
        self::assertStringContainsString('CustomerSession::regenerate();', $controller);
    }

    public function testLoginCredentialVerificationIsSinglePass(): void
    {
        $validateHelper = file_get_contents(dirname(__DIR__, 2) . '/Okay/Helpers/ValidateHelper.php');
        $userHelper = file_get_contents(dirname(__DIR__, 2) . '/Okay/Helpers/UserHelper.php');

        self::assertIsString($validateHelper);
        self::assertIsString($userHelper);
        self::assertStringNotContainsString('checkPassword($email, $password)', $validateHelper);
        self::assertSame(1, substr_count($userHelper, 'checkPassword($email, $password)'));
    }

    public function testPasswordHashingMigratesLegacyMd5OnSuccessfulCheck(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/Okay/Entities/UsersEntity.php');

        self::assertIsString($source);
        self::assertStringContainsString('password_hash($password, PASSWORD_ARGON2ID', $source);
        self::assertStringContainsString('password_verify($password, $hash)', $source);
        self::assertStringContainsString('isLegacyPasswordHash', $source);
        self::assertStringContainsString('$this->update($userId, [\'password\' => $password]);', $source);
    }

    public function testPrgRedirectRejectsExternalRedirects(): void
    {
        $source = file_get_contents(dirname(__DIR__, 2) . '/Okay/Helpers/MainHelper.php');

        self::assertIsString($source);
        self::assertStringContainsString('isSafePrgRedirect', $source);
        self::assertStringContainsString('preg_match', $source);
        self::assertStringContainsString("str_starts_with(\$decoded, '//')", $source);
        self::assertStringContainsString("str_contains(\$decoded, '\\\\')", $source);
        self::assertStringContainsString('rawurldecode($url)', $source);
    }

    public function testPrgRedirectAllowsSameOriginAbsoluteCatalogUrls(): void
    {
        $requestClass = new ReflectionClass(Request::class);
        $domain = $requestClass->getProperty('domain');
        $protocol = $requestClass->getProperty('protocol');
        $previousDomain = $domain->getValue();
        $previousProtocol = $protocol->getValue();

        Request::setDomain('okaycms.ddev.site');
        Request::setProtocol('https');

        try {
            self::assertTrue($this->isSafePrgRedirect('/catalog/mebel-dlya-doma/sort-price'));
            self::assertTrue($this->isSafePrgRedirect('catalog/mebel-dlya-doma/sort-price'));
            self::assertTrue($this->isSafePrgRedirect('https://okaycms.ddev.site/catalog/mebel-dlya-doma/sort-price'));
            self::assertFalse($this->isSafePrgRedirect('https://evil.test/catalog/mebel-dlya-doma/sort-price'));
            self::assertFalse($this->isSafePrgRedirect('https://okaycms.ddev.site.evil.test/catalog/mebel-dlya-doma/sort-price'));
            self::assertFalse($this->isSafePrgRedirect('//okaycms.ddev.site/catalog/mebel-dlya-doma/sort-price'));
        } finally {
            $domain->setValue(null, $previousDomain);
            $protocol->setValue(null, $previousProtocol);
        }
    }

    private function isSafePrgRedirect(string $url): bool
    {
        $helperClass = new ReflectionClass(MainHelper::class);
        $helper = $helperClass->newInstanceWithoutConstructor();
        $method = $helperClass->getMethod('isSafePrgRedirect');

        return $method->invoke($helper, $url);
    }
}
