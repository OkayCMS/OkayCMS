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
use Okay\Entities\VariantsEntity;
use Okay\Helpers\CartHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Controllers\AbstractController;
use Okay\Modules\OkayCMS\FastOrder\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\FastOrder\Helpers\ValidateHelper;

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
        VariantsEntity    $variantsEntity,
        Cart              $cart,
        BackendExtender   $validateExtend
    ) {
        if (!$this->request->method('post')) {
            return $this->response->setContent(json_encode(['errors' => ['Request must be post']]), RESPONSE_JSON);
        }

        $order = new \stdClass();
        $order->name    = $this->request->post('name');
        $order->last_name = $this->request->post('last_name');
        $order->phone   = Phone::toSave($this->request->post('phone'));
        $order->email   = '';
        $order->comment = $frontTranslations->getTranslation('fast_order');
        $order->lang_id = $languages->getLangId();
        $order->ip      = $_SERVER['REMOTE_ADDR'];
        $variantId = $this->request->post('variant_id');

        $order = $ordersHelper->attachUserIfLogin($order, $this->user);

        $errors = $validateExtend->ValidateFastOrder($order,$variantId);

        if (!empty($errors)) {
            return $this->response->setContent(json_encode(['errors' => $errors]), RESPONSE_JSON);
        }
        
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $entityFactory->get(OrdersEntity::class);
        $preparedOrder = $ordersHelper->prepareAdd($order);
        $orderId       = $ordersEntity->add($preparedOrder);

        $amount = $this->request->post('amount', 'integer');
        if ($amount <= 0) {
            $amount = 1;
        }

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
