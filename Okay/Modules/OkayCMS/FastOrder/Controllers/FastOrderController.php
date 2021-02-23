<?php


namespace Okay\Modules\OkayCMS\FastOrder\Controllers;


use Okay\Core\Cart;
use Okay\Core\FrontTranslations;
use Okay\Core\Notify;
use Okay\Core\Phone;
use Okay\Core\Router;
use Okay\Core\Languages;
use Okay\Core\EntityFactory;
use Okay\Core\Validator;
use Okay\Helpers\CartHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Controllers\AbstractController;

class FastOrderController extends AbstractController
{
    public function createOrder(
        EntityFactory     $entityFactory,
        OrdersHelper      $ordersHelper,
        Languages         $languages,
        Notify            $notify,
        Validator         $validator,
        FrontTranslations $frontTranslations,
        CartHelper        $cartHelper,
        Cart              $cart
    ) {
        if (!$this->request->method('post')) {
            return $this->response->setContent(json_encode(['errors' => ['Request must be post']]), RESPONSE_JSON);
        }

        $order = new \stdClass();
        $order->name    = $this->request->post('name');
        $order->last_name = $this->request->post('last_name');
        $order->phone   = Phone::toSave($this->request->post('phone'));
        $order->email   = '';
        $order->address = '';
        $order->comment = 'Быстрый заказ';
        $order->lang_id = $languages->getLangId();
        $order->ip      = $_SERVER['REMOTE_ADDR'];

        $order = $ordersHelper->attachUserIfLogin($order, $this->user);

        $errors = [];
        if (!$validator->isName($order->name, true)) {
            $errors[] = $frontTranslations->getTranslation('okay_cms__fast_order__form_name_error');
        }
        
        if (!$validator->isPhone($order->phone, true)) {
            $errors[] = $frontTranslations->getTranslation('okay_cms__fast_order__form_phone_error');
        }

        $captchaCode =  $this->request->post('captcha_code', 'string');
        if ($this->settings->get('captcha_fast_order') && !$validator->verifyCaptcha('captcha_fast_order', $captchaCode)) {
            $errors[] = $frontTranslations->getTranslation('okay_cms__fast_order__form_captcha_error');
        }

        if (!empty($errors)) {
            return $this->response->setContent(json_encode(['errors' => $errors]), RESPONSE_JSON);
        }
        
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $entityFactory->get(OrdersEntity::class);
        $orderId      = $ordersEntity->add($order);

        $amount = $this->request->post('amount', 'integer');
        if ($amount <= 0) {
            $amount = 1;
        }
        $variantId = $this->request->post('variant_id');

        if ($variantId && $amount) {
            $cart->addItem($variantId, $amount);
        }

        $preparedCart = $cartHelper->prepareCart($cart, $orderId);
        $preparedCart = $cartHelper->cartToOrder($preparedCart, $orderId);
        $preparedCart = $cartHelper->prepareDiscounts($preparedCart, $orderId);
        $cartHelper->discountsToDB($preparedCart);

        $order = $ordersEntity->findOne(['id' => $orderId]);
        $ordersEntity->updateTotalPrice($orderId);
        $ordersHelper->finalCreateOrderProcedure($order);

        $notify->emailOrderUser($order->id);
        $notify->emailOrderAdmin($order->id);

        $cart->clear();

        return $this->response->setContent(json_encode([
            'success'           => 1,
            'redirect_location' => Router::generateUrl('order', ['url' => $order->url], true)
        ]), RESPONSE_JSON);
    }
}