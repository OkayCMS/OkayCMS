<?php

declare(strict_types=1);

namespace Security;

use Okay\Core\Recaptcha;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\Validator;
use PHPUnit\Framework\TestCase;

final class FrontendP1SecurityTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    public function testP1ControllersUseExistingCustomerCsrfBoundary(): void
    {
        foreach (
            [
                'Okay/Controllers/FeedbackController.php',
                'Okay/Controllers/SubscribeController.php',
                'Okay/Controllers/WishListController.php',
                'Okay/Controllers/ComparisonController.php',
                'Okay/Controllers/ProductController.php',
                'Okay/Controllers/BlogController.php',
            ] as $path
        ) {
            $source = $this->read($path);

            self::assertStringContainsString('getCustomerCsrfError', $source, $path);
            self::assertStringContainsString("post('customer_csrf_token')", $source, $path);
            self::assertStringNotContainsString('Request::checkSession', $source, $path);
            self::assertStringNotContainsString('session_id()', $source, $path);
        }

        foreach (['WishListController.php', 'ComparisonController.php'] as $controller) {
            $source = $this->read('Okay/Controllers/' . $controller);

            self::assertStringContainsString('!$this->request->isPost()', $source, $controller);
            self::assertStringNotContainsString('$this->request->get(\'id\'', $source, $controller);
            self::assertStringNotContainsString('$this->request->get(\'product\'', $source, $controller);
        }
    }

    public function testP1ThemeMutationsSubmitCustomerCsrfToken(): void
    {
        foreach (
            [
                'design/okay_shop/html/feedback.tpl',
                'design/okay_shop/html/index.tpl',
                'design/okay_shop/html/blog_sidebar.tpl',
            ] as $template
        ) {
            $source = $this->read($template);

            self::assertStringContainsString('name="customer_csrf_token"', $source, $template);
            self::assertStringContainsString('{$customer_csrf_token|escape}', $source, $template);
        }

        $okayJs = $this->read('design/okay_shop/js/okay.js');
        self::assertStringContainsString('data: withCustomerCsrfToken({ product: product, action: action })', $okayJs);
        self::assertStringContainsString('data: withCustomerCsrfToken({ id: $(this).data("id"), action: action })', $okayJs);

        $scripts = $this->read('design/okay_shop/html/scripts.tpl');
        self::assertStringContainsString(
            "'&customer_csrf_token=' + encodeURIComponent(okay.customer_csrf_token)",
            $scripts
        );
    }

    public function testHeaderInformersRemainLinksWhenEmpty(): void
    {
        $wishlist = $this->read('design/okay_shop/html/wishlist_informer.tpl');
        $comparison = $this->read('design/okay_shop/html/comparison_informer.tpl');
        $cart = $this->read('design/okay_shop/html/cart_informer.tpl');

        self::assertStringContainsString('href="{url_generator route="wishlist"}"', $wishlist);
        self::assertStringNotContainsString('<span class="header_informers__link', $wishlist);

        self::assertStringContainsString('href="{url_generator route="comparison"}"', $comparison);
        self::assertStringNotContainsString('<div class="header_informers__link', $comparison);

        self::assertStringContainsString("href=\"{url_generator route='cart'}\"", $cart);
        self::assertStringNotContainsString('<div class="header_informers__link', $cart);
    }

    public function testExactCmsVersionHeaderIsRemovedAndBaselineHtmlHeadersAreSet(): void
    {
        $response = $this->read('Okay/Core/Response.php');
        $htmlAdapter = $this->read('Okay/Core/Adapters/Response/Html.php');

        self::assertStringNotContainsString('X-Powered-CMS', $response);
        self::assertStringContainsString('X-Frame-Options: SAMEORIGIN', $htmlAdapter);
        self::assertStringContainsString('X-Content-Type-Options: nosniff', $htmlAdapter);
        self::assertStringContainsString('Referrer-Policy: strict-origin-when-cross-origin', $htmlAdapter);
        self::assertStringNotContainsString('Strict-Transport-Security', $response . $htmlAdapter);
    }

    public function testRequestPostKeepsRawDefaultAndSupportsOptionalTagStripping(): void
    {
        $previousPost = $_POST;
        $_POST = [
            'message' => '<b>Hello</b> & <script>x</script>',
        ];

        try {
            $request = new Request();

            self::assertSame('<b>Hello</b> & <script>x</script>', $request->post('message'));
            self::assertSame('Hello &amp; x', $request->post('message', null, null, true));
        } finally {
            $_POST = $previousPost;
        }
    }

    public function testValidatorTreatsIsSafeAsPlainTextGuard(): void
    {
        $validator = new Validator(
            $this->createStub(Settings::class),
            $this->createStub(Recaptcha::class)
        );

        self::assertTrue($validator->isSafe('Plain text & punctuation.', true));
        self::assertFalse($validator->isSafe('<img src=x onerror=alert(1)>', true));
        self::assertFalse($validator->isSafe('<svg onload=alert(1)>', true));
        self::assertFalse($validator->isSafe('', true));
        self::assertTrue($validator->isSafe('', false));
    }

    public function testPlainTextStorefrontRequestsOptIntoPostTagStripping(): void
    {
        $commonRequest = $this->read('Okay/Requests/CommonRequest.php');
        $cartRequest = $this->read('Okay/Requests/CartRequest.php');

        foreach (
            [
                "post('name', null, null, true)",
                "post('email', null, null, true)",
                "post('message', null, null, true)",
                "post('text', null, null, true)",
                "post('callback_message', null, null, true)",
                "post('subscribe_email', null, null, true)",
            ] as $expected
        ) {
            self::assertStringContainsString($expected, $commonRequest);
        }

        foreach (
            [
                "post('name', null, null, true)",
                "post('last_name', null, null, true)",
                "post('email', null, null, true)",
                "post('phone', null, null, true)",
                "post('comment', null, null, true)",
            ] as $expected
        ) {
            self::assertStringContainsString($expected, $cartRequest);
        }
    }

    private function read(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);

        self::assertIsString($source);
        return $source;
    }
}
