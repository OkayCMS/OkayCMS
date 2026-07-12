<?php

namespace Okay\Controllers;

use Okay\Helpers\CartHelper;
use Okay\Helpers\CouponHelper;
use Okay\Helpers\MetadataHelpers\CartMetadataHelper;
use Okay\Requests\CartRequest;
use Okay\Core\Notify;
use Okay\Core\Router;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\CouponsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Cart;
use Okay\Core\Languages;
use Okay\Core\Security\CheckoutToken;
use Okay\Helpers\DeliveriesHelper;
use Okay\Helpers\PaymentsHelper;
use Okay\Helpers\ValidateHelper;
use Okay\Helpers\OrdersHelper;

class CartController extends AbstractController
{
    private const CART_AJAX_MUTATING_ACTIONS = [
        'update_citem' => true,
        'remove_citem' => true,
        'add_citem' => true,
        'coupon_apply' => true,
    ];

    /* Cart page */
    public function render(
        DeliveriesEntity $deliveriesEntity,
        OrdersEntity $ordersEntity,
        CouponsEntity $couponsEntity,
        CurrenciesEntity $currenciesEntity,
        Languages $languages,
        Request $request,
        Notify $notify,
        Cart $cart,
        DeliveriesHelper $deliveriesHelper,
        PaymentsHelper $paymentsHelper,
        OrdersHelper $ordersHelper,
        CartRequest $cartRequest,
        CartHelper $cartHelper,
        ValidateHelper $validateHelper,
        CouponHelper $couponHelper,
        CartMetadataHelper $cartMetadataHelper
    ) {
        $cartCsrfError = null;
        if ($request->isPost() && $this->hasAnyPostField(['variant', 'amounts', 'checkout'])) {
            $cartCsrfError = $this->getCustomerCsrfError($validateHelper);
            if ($cartCsrfError !== null) {
                $this->design->assign('error', $cartCsrfError);
            }
        }

        // Add a posted variant to the cart.
        if ($cartCsrfError === null && $request->isPost() && ($variantId = $request->post('variant', 'integer'))) {
            $cart->addItem($variantId, $request->post('amount', 'integer'));
            $this->response->redirectTo(Router::generateUrl('cart', [], true), 301);
        }

        // Update posted cart amounts.
        if ($cartCsrfError === null && ($amounts = $request->post('amounts'))) {
            foreach ($amounts as $variantId => $amount) {
                $cart->updateItem($variantId, $amount);
            }
        }

        $this->setMetadataHelper($cartMetadataHelper);

        $cart = $cart->get();
        /* Checkout */
        if ($this->hasPostField('checkout')) {
            if ($cartCsrfError !== null) {
                $this->design->assign('error', $cartCsrfError);
            } else {
                $order = $cartRequest->postOrder();
                $order = $ordersHelper->attachUserIfLogin($order, $this->user);

                if ($error = $validateHelper->getCartValidateError($order)) {
                    $this->design->assign('error', $error);
                } elseif (!$this->acceptCheckoutSubmission($order, $cart)) {
                    $this->design->assign('error', 'csrf');
                } else {
                    // Add the order to the database.
                    $order->lang_id = $languages->getLangId();
                    $preparedOrder  = $ordersHelper->prepareAdd($order);
                    $orderId        = $ordersHelper->add($preparedOrder);

                    if (isset($_SESSION['coupon_code'])) {
                        $couponHelper->registerUseIfExists($_SESSION['coupon_code']);
                    }

                    $preparedCart = $cartHelper->prepareCart($cart, $orderId);
                    $preparedCart = $cartHelper->cartToOrder($preparedCart, $orderId);
                    $preparedCart = $cartHelper->prepareDiscounts($preparedCart, $orderId);
                    $cartHelper->discountsToDB($preparedCart);

                    /** @var object{id: string|int, delivery_id?: string|int|null, total_price: string|int|float, url: string}&\stdClass $order */
                    $order = $ordersEntity->get((int) $orderId);
                    if (!empty($order->delivery_id)) {
                        $delivery          = $deliveriesEntity->get((int) $order->delivery_id);
                        $deliveryPriceInfo = $deliveriesHelper->prepareDeliveryPriceInfo($delivery, $order);
                        $deliveriesHelper->updateDeliveryPriceInfo($deliveryPriceInfo, $order);
                    }

                    $ordersEntity->updateTotalPrice($order->id);
                    $ordersHelper->finalCreateOrderProcedure($order);

                    // Send the customer notification.
                    $notify->emailOrderUser($order->id);

                    // Send the administrator notification.
                    $notify->emailOrderAdmin($order->id);

                    $cart->clear();

                    // Redirect to the order page or return the AJAX order content.
                    if ($this->request->post('ajax')) {
                        $content = $cartHelper->getAjaxOrderContent($order);
                        return $this->response->setContent(
                            json_encode($content, JSON_UNESCAPED_SLASHES),
                            RESPONSE_JSON
                        );
                    } else {
                        $this->response->redirectTo(
                            Router::generateUrl('order', ['url' => $order->url], true)
                        );
                    }
                }
            }
        } else {
            if ($cartCsrfError === null && $request->post('amounts')) {
                $couponCode = $cartRequest->postCoupon();
                if (empty($couponCode)) {
                    $cart->applyCoupon('');
                    $this->response->redirectTo(Router::generateUrl('cart', [], true));
                } else {
                    $coupon = $couponsEntity->get((string)$couponCode);
                    if (empty($coupon) || !$coupon->valid) {
                        $cart->applyCoupon($couponCode);
                        $this->design->assign('coupon_error', 'invalid');
                    } else {
                        $cart->applyCoupon($couponCode);
                        $this->response->redirectTo(Router::generateUrl('cart', [], true));
                    }
                }
            }

            // Default user data.
            $this->design->assign('request_data', $cartHelper->getDefaultCartData($this->user));
        }

        // Deliveries and payments.
        $paymentMethods = $paymentsHelper->getCartPaymentsList($cart);
        $deliveries     = $deliveriesHelper->getCartDeliveriesList($cart, $paymentMethods);
        $activeDelivery = $deliveriesHelper->getActiveDeliveryMethod($deliveries, $this->user);
        $activePayment  = $paymentsHelper->getActivePaymentMethod($paymentMethods, $activeDelivery, $this->user ?? new \stdClass());

        $this->design->assign('all_currencies', $currenciesEntity->mappedBy('id')->find());
        $this->design->assign('deliveries', $deliveries);
        $this->design->assign('payment_methods', $paymentMethods);
        $this->design->assign('active_delivery', $activeDelivery);
        $this->design->assign('active_payment', $activePayment);

        if ($couponsEntity->count(['valid' => 1]) > 0) {
            $this->design->assign('coupon_request', true);
        }

        $this->design->assign('noindex_follow', true);

        $this->response->setContent('cart.tpl');
    }

    public function cartAjax(
        CouponsEntity $couponsEntity,
        CurrenciesEntity $currenciesEntity,
        Request $request,
        Cart $cart,
        DeliveriesHelper $deliveriesHelper,
        PaymentsHelper $paymentsHelper,
        CartHelper $cartHelper,
        ValidateHelper $validateHelper
    ) {
        $action = (string)($request->isPost() ? $request->post('action') : $request->get('action'));
        $isMutation = $this->isCartAjaxMutation($action);
        if ($isMutation && !$request->isPost()) {
            return $this->rejectCartAjaxMutation(405, 'method_not_allowed');
        }

        if ($isMutation && ($error = $this->getCustomerCsrfError($validateHelper))) {
            return $this->rejectCartAjaxMutation(403, $error);
        }

        $variantId = $request->isPost()
            ? $request->post('variant_id', 'integer')
            : $request->get('variant_id', 'integer');
        $amount = $request->isPost()
            ? $request->post('amount', 'integer')
            : $request->get('amount', 'integer');

        switch ($action) {
            case 'update_citem':
                $cart->updateItem($variantId, $amount);
                break;
            case 'remove_citem':
                $cart->deleteItem($variantId);
                break;
            case 'add_citem':
                $cart->addItem($variantId, $amount);
                break;
            default:
                break;
        }

        $cart = $cart->get();
        $this->design->assign('cart', $cart);

        $this->design->assign('all_currencies', $currenciesEntity->mappedBy('id')->find());

        /* Cart items */
        if ($cart->isEmpty === false) {
            if ($request->isPost() && $this->hasPostField('coupon_code')) {
                $couponCode = trim($request->post('coupon_code', 'string'));
                if (empty($couponCode)) {
                    $cart->applyCoupon('');
                    if ($action == 'coupon_apply') {
                        $this->design->assign('coupon_error', 'empty');
                    }
                } else {
                    $coupon = $couponsEntity->get((string)$couponCode);
                    if (empty($coupon) || !$coupon->valid) {
                        $cart->applyCoupon($couponCode);
                        $this->design->assign('coupon_error', 'invalid');
                    } else {
                        $cart->applyCoupon($couponCode);
                    }
                }
            }

            if ($couponsEntity->count(['valid' => 1]) > 0) {
                $this->design->assign('coupon_request', true);
            }

            $cart = $cart->get();
        }

        $paymentMethods = $paymentsHelper->getCartPaymentsList($cart);
        $deliveries = $deliveriesHelper->getCartDeliveriesList($cart, $paymentMethods);

        $result = $cartHelper->getAjaxCartResult(
            $cart,
            $this->currency,
            $paymentMethods,
            $deliveries,
            $action,
            $variantId,
            $amount
        );

        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    /**
     * @param string $variantId Numeric route parameter.
     */
    public function removeItem(Cart $cart, ValidateHelper $validateHelper, $variantId)
    {
        if (!$this->request->isPost()) {
            $this->response->setStatusCode(405)->sendHeaders();
            return;
        }

        if ($this->getCustomerCsrfError($validateHelper) !== null) {
            $this->response->setStatusCode(403)->sendHeaders();
            return;
        }

        $cart->deleteItem((int)$variantId);
        $this->response->redirectTo(Router::generateUrl('cart', [], true));
    }

    /**
     * @param string $variantId Numeric route parameter.
     */
    public function addItem(Cart $cart, ValidateHelper $validateHelper, $variantId)
    {
        if (!$this->request->isPost()) {
            $this->response->setStatusCode(405)->sendHeaders();
            return;
        }

        if ($this->getCustomerCsrfError($validateHelper) !== null) {
            $this->response->setStatusCode(403)->sendHeaders();
            return;
        }

        $cart->addItem((int)$variantId, $this->request->post('amount', 'integer'));
        $this->response->redirectTo(Router::generateUrl('cart', [], true));
    }

    private function getCustomerCsrfError(ValidateHelper $validateHelper): ?string
    {
        return $validateHelper->getCustomerCsrfError($this->request->post('customer_csrf_token'));
    }

    private function acceptCheckoutSubmission(object $order, Cart $cart): bool
    {
        $checkoutToken = $this->request->post('checkout_token');
        if (is_string($checkoutToken) && $checkoutToken !== '') {
            return CheckoutToken::consume($checkoutToken);
        }

        return CheckoutToken::consumeFingerprint($this->getCheckoutSubmissionFingerprint($order, $cart));
    }

    private function getCheckoutSubmissionFingerprint(object $order, Cart $cart): string
    {
        $purchases = [];
        foreach ($cart->purchases as $purchase) {
            $purchases[] = [
                'variant_id' => (string) $purchase->variant_id,
                'amount' => (int) $purchase->amount,
            ];
        }

        usort(
            $purchases,
            static fn (array $first, array $second): int => $first['variant_id'] <=> $second['variant_id']
        );

        $payload = json_encode([
            'purchases' => $purchases,
            'delivery_id' => (string) ($order->delivery_id ?? ''),
            'payment_method_id' => (string) ($order->payment_method_id ?? ''),
            'name' => (string) ($order->name ?? ''),
            'last_name' => (string) ($order->last_name ?? ''),
            'email' => (string) ($order->email ?? ''),
            'phone' => (string) ($order->phone ?? ''),
            'comment' => (string) ($order->comment ?? ''),
            'total_price' => (string) $cart->total_price,
            'coupon_code' => (string) ($_SESSION['coupon_code'] ?? ''),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);

        return hash('sha256', is_string($payload) ? $payload : '');
    }

    /**
     * @param list<string> $names
     */
    private function hasAnyPostField(array $names): bool
    {
        foreach ($names as $name) {
            if ($this->hasPostField($name)) {
                return true;
            }
        }

        return false;
    }

    private function hasPostField(string $name): bool
    {
        return array_key_exists($name, $_POST);
    }

    private function hasRequestField(string $name): bool
    {
        return array_key_exists($name, $_POST) || array_key_exists($name, $_GET);
    }

    private function isCartAjaxMutation(string $action): bool
    {
        return isset(self::CART_AJAX_MUTATING_ACTIONS[$action]) || $this->hasRequestField('coupon_code');
    }

    private function rejectCartAjaxMutation(int $statusCode, string $error): Response
    {
        $this->response->setStatusCode($statusCode);
        return $this->response->setContent(json_encode([
            'result' => 0,
            'error' => $error,
        ]), RESPONSE_JSON);
    }
}
