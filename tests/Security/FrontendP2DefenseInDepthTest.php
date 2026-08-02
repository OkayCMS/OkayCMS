<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class FrontendP2DefenseInDepthTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    public function testSupportEndpointUsesCompatiblePostJsonAndTokenHardening(): void
    {
        $controller = $this->read('Okay/Controllers/SupportController.php');
        $supportClient = $this->read('Okay/Core/Support.php');
        $attempts = $this->read('Okay/Entities/UserAuthAttemptsEntity.php');

        self::assertStringContainsString('$this->request->isPost()', $controller);
        self::assertStringContainsString('isJsonRequest()', $controller);
        self::assertStringContainsString('json_last_error() !== JSON_ERROR_NONE', $controller);
        self::assertStringContainsString('hash_equals((string)$info->temp_key, $tempKey)', $controller);
        self::assertStringContainsString('hash_equals((string)$info->public_key, $key)', $controller);
        self::assertStringContainsString('error_log($message)', $controller);
        self::assertStringContainsString('ACTION_SUPPORT_ENDPOINT', $controller);
        self::assertStringContainsString('private_key', $controller);
        self::assertStringNotContainsString('private_key .', $controller);
        self::assertStringNotContainsString('public_key .', $controller);
        self::assertStringNotContainsString('temp_key .', $controller);

        self::assertStringContainsString("'Content-Type: application/json; charset=UTF-8'", $supportClient);
        self::assertStringContainsString("public const ACTION_SUPPORT_ENDPOINT = 'support_endpoint';", $attempts);
    }

    public function testShoppingCartCookieHasExplicitSecureFlagsAndIsNotReadByStorefrontJavascript(): void
    {
        $cart = $this->read('Okay/Core/Cart.php');

        self::assertStringContainsString("setcookie('shopping_cart', \$value, [", $cart);
        self::assertStringContainsString("'secure' => \$this->isHttpsRequest()", $cart);
        self::assertStringContainsString("'httponly' => true", $cart);
        self::assertStringContainsString("'samesite' => 'Lax'", $cart);
        self::assertStringContainsString('$this->deleteShoppingCartCookie();', $cart);

        foreach ($this->storefrontScriptFiles() as $file) {
            self::assertStringNotContainsString('shopping_cart', $this->read($file), $file);
        }
    }

    public function testLegacyPreferenceCookiesUseExplicitSecurityFlags(): void
    {
        $cookieSources = [
            'Okay/Core/WishList.php' => [
                "setcookie('wishlist', \$value, [",
                "'path' => '/'",
                "'secure' => \$this->isHttpsRequest()",
            ],
            'Okay/Core/Comparison.php' => [
                "setcookie('comparison', \$value, [",
                "'path' => '/'",
                "'secure' => \$this->isHttpsRequest()",
            ],
            'Okay/Core/BrowsedProducts.php' => [
                "setcookie('browsed_products', \$value, [",
                "'path' => '/'",
                "'secure' => \$this->isHttpsRequest()",
            ],
            'Okay/Helpers/UserHelper.php' => [
                "setcookie('browsed_products', '', [",
                "'path' => '/'",
                "'secure' => \$this->isHttpsRequest()",
            ],
            'Okay/Core/UserReferer/UserReferer.php' => [
                "setcookie('userReferer', base64_encode(\$encodedReferer), [",
                "'path' => '/'",
                "'secure' => Request::getProtocol() === 'https'",
            ],
            'backend/Controllers/IndexAdmin.php' => [
                "setcookie('admin_login', \$value, [",
                "'path' => '/'",
                "'secure' => \$this->isHttpsRequest()",
            ],
            'index.php' => [
                "setcookie('admin_login', '', [",
                "'path' => '/'",
                "'secure' => !empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off'",
            ],
            'backend/design/js/filemanager/dialog.php' => [
                "setcookie('last_position', \$subdir, [",
                "'path' => '/backend/design/js/filemanager'",
                "'secure' => !empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off'",
            ],
        ];

        foreach ($cookieSources as $path => $expectedStrings) {
            $source = $this->read($path);

            foreach ($expectedStrings as $expectedString) {
                self::assertStringContainsString($expectedString, $source, $path);
            }

            self::assertStringContainsString("'httponly' => true", $source, $path);
            self::assertStringContainsString("'samesite' => 'Lax'", $source, $path);
        }
    }

    public function testSessionNamesAreStableAndFrontendAdminNamesAreDistinct(): void
    {
        $frontend = $this->read('index.php');
        self::assertStringContainsString("session_name('okay_sid');", $frontend);
        self::assertStringContainsString('AdminSession::syncFrontendAdmin($_COOKIE, $_SERVER);', $frontend);
        self::assertStringContainsString('AdminSession::destroyFromCookie($_COOKIE, $_SERVER);', $frontend);

        foreach (
            [
                'backend/index.php',
                'backend/ajax/configure.php',
                'backend/files/index.php',
            ] as $path
        ) {
            self::assertStringContainsString('session_name(AdminSession::SESSION_NAME);', $this->read($path), $path);
        }

        foreach (
            [
                'backend/design/js/admintooltip/admintooltip.php',
                'backend/design/js/filemanager/include/okay_access.php',
                'backend/design/js/filemanager/config/config.php',
                'backend/design/js/filemanager/UploadHandler.php',
            ] as $path
        ) {
            self::assertStringContainsString("session_name('okay_admin_sid')", $this->read($path), $path);
        }

        $adminSession = $this->read('Okay/Core/Security/AdminSession.php');
        self::assertStringContainsString("public const SESSION_NAME = 'okay_admin_sid';", $adminSession);
        self::assertStringContainsString("session_start(['read_and_close' => true]);", $adminSession);
        self::assertStringContainsString('setcookie(self::SESSION_NAME, \'\', [', $adminSession);

        foreach (['index.php', 'backend', 'Okay'] as $path) {
            self::assertStringNotContainsString("session_name(md5(\$_SERVER['HTTP_USER_AGENT']))", $this->readTree($path));
        }
    }

    public function testRecaptchaInvalidSecretFailsClosed(): void
    {
        $recaptcha = $this->read('Okay/Core/Recaptcha.php');

        self::assertStringContainsString("in_array('invalid-input-secret'", $recaptcha);
        self::assertStringContainsString("error_log('[OkayCMS] Recaptcha: invalid secret key configured')", $recaptcha);
        self::assertStringContainsString('return false;', $recaptcha);
        self::assertStringNotContainsString("reset(\$response['error-codes']) == 'invalid-input-secret') {\n            return true", $recaptcha);
    }

    public function testResizePathHandlingRejectsTraversalAndKeepsPathsInsideImageDirectories(): void
    {
        $controller = $this->read('Okay/Controllers/ResizeController.php');
        $image = $this->read('Okay/Core/Image.php');

        self::assertStringContainsString('hasUnsafeResizePath', $controller);
        self::assertStringContainsString("\$segment === '..'", $controller);
        self::assertStringContainsString('buildPathWithinDirectory', $image);
        self::assertStringContainsString('hasUnsafeImagePath', $image);
        self::assertStringContainsString('hasParentDirectorySegment', $image);
        self::assertStringContainsString('isNotHttpsSource($sourceFile)', $image);
        self::assertStringContainsString('!str_starts_with($resolvedTarget, $basePath)', $image);
        self::assertStringContainsString('!str_starts_with($resolvedParent, $basePath)', $image);
    }

    private function read(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);

        self::assertIsString($source);
        return $source;
    }

    private function readTree(string $path): string
    {
        $fullPath = $this->root . '/' . $path;
        if (is_file($fullPath)) {
            return $this->read($path);
        }

        $contents = '';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fullPath));
        foreach ($iterator as $fileInfo) {
            if (!$fileInfo instanceof SplFileInfo || !$fileInfo->isFile() || $fileInfo->getExtension() !== 'php') {
                continue;
            }

            $source = file_get_contents($fileInfo->getPathname());
            self::assertIsString($source);
            $contents .= $source;
        }

        return $contents;
    }

    /**
     * @return list<string>
     */
    private function storefrontScriptFiles(): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->root . '/design/okay_shop'));
        foreach ($iterator as $fileInfo) {
            if (
                !$fileInfo instanceof SplFileInfo
                || !$fileInfo->isFile()
                || !in_array($fileInfo->getExtension(), ['js', 'tpl'], true)
            ) {
                continue;
            }

            $files[] = substr($fileInfo->getPathname(), strlen($this->root) + 1);
        }

        return $files;
    }
}
