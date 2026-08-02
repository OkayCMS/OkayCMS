<?php

declare(strict_types=1);

namespace Security;

use PHPUnit\Framework\TestCase;

final class FrontendCartCsrfTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = dirname(__DIR__, 2);
    }

    public function testProductionConfigDisablesDebugModeByDefault(): void
    {
        $config = $this->read('config/config.php');

        self::assertMatchesRegularExpression('/^debug_mode\s*=\s*false$/m', $config);
        self::assertStringContainsString('config/config.local.php', $config);
    }

    public function testCartControllerUsesExistingCustomerCsrfBoundary(): void
    {
        $source = $this->read('Okay/Controllers/CartController.php');

        self::assertStringContainsString(
            "\$validateHelper->getCustomerCsrfError(\$this->request->post('customer_csrf_token'))",
            $source
        );
        self::assertStringContainsString('CART_AJAX_MUTATING_ACTIONS', $source);
        self::assertStringContainsString('$this->response->setStatusCode(405)->sendHeaders();', $source);
        self::assertStringContainsString('return $this->rejectCartAjaxMutation(405, \'method_not_allowed\');', $source);
        self::assertStringContainsString('return $this->rejectCartAjaxMutation(403, $error);', $source);
        self::assertStringNotContainsString('Request::checkSession', $source);
        self::assertStringNotContainsString('session_id()', $source);

        $csrfPosition = strpos($source, '$cartCsrfError = $this->getCustomerCsrfError($validateHelper);');
        $orderReadPosition = strpos($source, '$order = $cartRequest->postOrder();');

        self::assertIsInt($csrfPosition);
        self::assertIsInt($orderReadPosition);
        self::assertLessThan($orderReadPosition, $csrfPosition);
    }

    public function testDefaultThemeCartFormsExposeCustomerCsrfToken(): void
    {
        foreach (['cart.tpl', 'product.tpl', 'product_list.tpl', 'pop_up_cart.tpl'] as $template) {
            $source = $this->read('design/okay_shop/html/' . $template);
            self::assertStringContainsString('name="customer_csrf_token"', $source, $template);
        }

        $mainHelper = $this->read('Okay/Helpers/MainHelper.php');
        self::assertStringContainsString("assignJsVar('customer_csrf_token', \$customerCsrfToken)", $mainHelper);
    }

    public function testCheckoutOrderCreationConsumesOneTimeTokenBeforeInsert(): void
    {
        $source = $this->read('Okay/Controllers/CartController.php');

        self::assertStringContainsString('CheckoutToken::consume', $source);
        self::assertStringContainsString('CheckoutToken::consumeFingerprint', $source);
        self::assertStringContainsString("\$this->request->post('checkout_token')", $source);
        self::assertStringContainsString('getCheckoutSubmissionFingerprint', $source);

        $tokenPosition = strpos($source, '$this->acceptCheckoutSubmission($order, $cart)');
        $orderInsertPosition = strpos($source, '$orderId        = $ordersHelper->add($preparedOrder);');

        self::assertIsInt($tokenPosition);
        self::assertIsInt($orderInsertPosition);
        self::assertLessThan($orderInsertPosition, $tokenPosition);
    }

    public function testDefaultThemeCheckoutFormExposesOneTimeToken(): void
    {
        $cart = $this->read('design/okay_shop/html/cart.tpl');
        $mainHelper = $this->read('Okay/Helpers/MainHelper.php');

        self::assertStringContainsString('name="checkout_token"', $cart);
        self::assertStringContainsString('{$checkout_token|escape}', $cart);
        self::assertStringContainsString("assign('checkout_token'", $mainHelper);
    }

    public function testDefaultThemeCartAjaxUsesPostAndCustomerCsrfToken(): void
    {
        $source = $this->read('design/okay_shop/js/okay.js');

        self::assertStringContainsString('function withCustomerCsrfToken(data)', $source);
        foreach (['add_citem', 'update_citem', 'coupon_apply', 'remove_citem'] as $action) {
            $pattern = '/type:\s+"post",\s+data:\s+withCustomerCsrfToken\(\{[^}]*action:\s+"'
                . preg_quote($action, '/')
                . '"/s';

            self::assertMatchesRegularExpression($pattern, $source, $action);
        }
    }

    public function testDefaultThemeRemoveActionsAreNoLongerGetLinks(): void
    {
        $cartPurchases = $this->read('design/okay_shop/html/cart_purchases.tpl');
        $popUpCart = $this->read('design/okay_shop/html/pop_up_cart.tpl');

        self::assertStringContainsString('class="purchase__remove" type="button"', $cartPurchases);
        self::assertStringContainsString('<form method="post"', $popUpCart);
        self::assertStringNotContainsString('href="{url_generator route="cart_remove_item"', $cartPurchases);
        self::assertStringNotContainsString('href="{url_generator route="cart_remove_item"', $popUpCart);
        self::assertStringNotContainsString('formaction="{url_generator route="cart_remove_item"', $cartPurchases);
    }

    public function testCartPageRemoveActionCannotBecomeCheckoutDefaultSubmitter(): void
    {
        $cartPurchases = $this->read('design/okay_shop/html/cart_purchases.tpl');

        self::assertStringContainsString('class="purchase__remove"', $cartPurchases);
        self::assertStringContainsString('onclick="ajax_remove({$purchase->variant->id});return false;"', $cartPurchases);
        self::assertDoesNotMatchRegularExpression(
            '/<button\b(?=[^>]*\bclass="[^"]*\bpurchase__remove\b[^"]*")(?=[^>]*\btype="submit")[^>]*>/',
            $cartPurchases
        );
        self::assertDoesNotMatchRegularExpression(
            '/<button\b(?=[^>]*\bclass="[^"]*\bpurchase__remove\b[^"]*")(?=[^>]*\bformaction=)[^>]*>/',
            $cartPurchases
        );
    }

    private function read(string $path): string
    {
        $source = file_get_contents($this->root . '/' . $path);

        self::assertIsString($source);
        return $source;
    }
}
