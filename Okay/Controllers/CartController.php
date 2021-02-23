<?php


namespace Okay\Controllers;


use Okay\Core\SmartyPlugins\Plugins\CheckoutPaymentForm;
use Okay\Entities\PaymentsEntity;
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
use Okay\Core\Cart;
use Okay\Core\Languages;
use Okay\Helpers\DeliveriesHelper;
use Okay\Helpers\PaymentsHelper;
use Okay\Helpers\ValidateHelper;
use Okay\Helpers\OrdersHelper;

class CartController extends AbstractController
{
    /*Отображение заказа*/
    public function render(
        DeliveriesEntity   $deliveriesEntity,
        OrdersEntity       $ordersEntity,
        CouponsEntity      $couponsEntity,
        CurrenciesEntity   $currenciesEntity,
        Languages          $languages,
        Request            $request,
        Notify             $notify,
        Cart               $cart,
        DeliveriesHelper   $deliveriesHelper,
        PaymentsHelper     $paymentsHelper,
        OrdersHelper       $ordersHelper,
        CartRequest        $cartRequest,
        CartHelper         $cartHelper,
        ValidateHelper     $validateHelper,
        CouponHelper       $couponHelper,
        CartMetadataHelper $cartMetadataHelper
    ) {

        // Если передан id варианта, добавим его в корзину
        if ($variantId = $request->get('variant', 'integer')) {
            $cart->addItem($variantId, $request->get('amount', 'integer'));
            $this->response->redirectTo(Router::generateUrl('cart', [], true), 301);
        }

        // Если нам запостили amounts, обновляем их
        if ($amounts = $request->post('amounts')) {
            foreach ($amounts as $variantId => $amount) {
                $cart->updateItem($variantId, $amount);
            }
        }
        
        $this->setMetadataHelper($cartMetadataHelper);
        
        $cart = $cart->get();
        /*Оформление заказа*/
        if (isset($_POST['checkout'])) {
            $order = $cartRequest->postOrder();
            $order = $ordersHelper->attachUserIfLogin($order, $this->user);

            if ($error = $validateHelper->getCartValidateError($order)) {
                $this->design->assign('error', $error);
            } else {
                // Добавляем заказ в базу
                $order->lang_id = $languages->getLangId();
                $preparedOrder  = $ordersHelper->prepareAdd($order);
                $orderId        = $ordersHelper->add($preparedOrder);

                if (isset($_SESSION['coupon_code'])){
                    $couponHelper->registerUseIfExists($_SESSION['coupon_code']);
                }

                $preparedCart = $cartHelper->prepareCart($cart, $orderId);
                $preparedCart = $cartHelper->cartToOrder($preparedCart, $orderId);
                $preparedCart = $cartHelper->prepareDiscounts($preparedCart, $orderId);
                $cartHelper->discountsToDB($preparedCart);

                $order = $ordersEntity->get((int) $orderId);
                if (!empty($order->delivery_id)) {
                    $delivery          = $deliveriesEntity->get((int) $order->delivery_id);
                    $deliveryPriceInfo = $deliveriesHelper->prepareDeliveryPriceInfo($delivery, $order);
                    $deliveriesHelper->updateDeliveryPriceInfo($deliveryPriceInfo, $order);
                }

                $ordersEntity->updateTotalPrice($order->id);
                $ordersHelper->finalCreateOrderProcedure($order);
                
                // Отправляем письмо пользователю
                $notify->emailOrderUser($order->id);

                // Отправляем письмо администратору
                $notify->emailOrderAdmin($order->id);

                $cart->clear();

                // Перенаправляем на страницу заказа или отправляем форму для автосабмита или урл заказа
                if ($this->request->post('ajax')) {
                    $content = $cartHelper->getAjaxOrderContent($order);
                    return $this->response->setContent(json_encode($content, JSON_UNESCAPED_SLASHES), RESPONSE_JSON);
                } else {
                    $this->response->redirectTo(Router::generateUrl('order', ['url' => $order->url], true));
                }
            }
        } else {
            
            if ($request->post('amounts')) {
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

            // Данные пользователя по умолчанию
            $this->design->assign('request_data', $cartHelper->getDefaultCartData($this->user));
        }

        // Способы доставки и оплаты
        $paymentMethods = $paymentsHelper->getCartPaymentsList($cart);
        $deliveries     = $deliveriesHelper->getCartDeliveriesList($cart, $paymentMethods);
        $activeDelivery = $deliveriesHelper->getActiveDeliveryMethod($deliveries, $this->user);
        $activePayment  = $paymentsHelper->getActivePaymentMethod($paymentMethods, $activeDelivery, $this->user);

        $this->design->assign('all_currencies', $currenciesEntity->mappedBy('id')->find());
        $this->design->assign('deliveries', $deliveries);
        $this->design->assign('payment_methods', $paymentMethods);
        $this->design->assign('active_delivery', $activeDelivery);
        $this->design->assign('active_payment', $activePayment);
        
        if ($couponsEntity->count(['valid'=>1])>0) {
            $this->design->assign('coupon_request', true);
        }

        $this->design->assign('noindex_follow', true);
        
        $this->response->setContent('cart.tpl');
    }
    
    public function cartAjax(
        CouponsEntity    $couponsEntity,
        CurrenciesEntity $currenciesEntity,
        Request          $request,
        Cart             $cart,
        DeliveriesHelper $deliveriesHelper,
        PaymentsHelper   $paymentsHelper,
        CartHelper       $cartHelper
    ) {
        $action     = $request->get('action');
        $variantId  = $request->get('variant_id', 'integer');
        $amount     = $request->get('amount', 'integer');
        
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

        /*Рабтаем с товарами в корзине*/
        if ($cart->isEmpty === false) {
            if (isset($_GET['coupon_code'])) {
                $couponCode = trim($request->get('coupon_code', 'string'));
                if (empty($couponCode)) {
                    $cart->applyCoupon('');
                    if ($this->request->get('action') == 'coupon_apply') {
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

            if ($couponsEntity->count(['valid'=>1])>0) {
                $this->design->assign('coupon_request', true);
            }

            $cart = $cart->get();
        }

        $paymentMethods = $paymentsHelper->getCartPaymentsList($cart);
        $deliveries = $deliveriesHelper->getCartDeliveriesList($cart, $paymentMethods);
        
        $result = $cartHelper->getAjaxCartResult($cart, $this->currency, $paymentMethods, $deliveries, $action, $variantId, $amount);
        
        $this->response->setContent(json_encode($result), RESPONSE_JSON);
    }

    public function removeItem(Cart $cart, $variantId)
    {
        $cart->deleteItem($variantId);
        $this->response->redirectTo(Router::generateUrl('cart', [], true));
    }

    public function addItem(Cart $cart, $variantId)
    {
        $cart->addItem($variantId);
        $this->response->redirectTo(Router::generateUrl('cart', [], true));
    }
    
}