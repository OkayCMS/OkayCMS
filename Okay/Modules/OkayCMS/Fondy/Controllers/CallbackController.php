<?php


namespace Okay\Modules\OkayCMS\Fondy\Controllers;


use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Entities\OrdersEntity;
use Okay\Controllers\AbstractController;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\Fondy\Helpers\FondyHelper;

class CallbackController extends AbstractController
{
    
    public function payOrder(Money $money, Notify $notify)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        if (empty($_POST)){
            $callback = json_decode(file_get_contents("php://input"));
            $_POST = array();
            foreach ($callback as $key=>$val){
                $_POST[$key] =  $val ;
            }
            if (!$_POST['order_id']){
                die('go away');
            }
        }

        list($order_id,) = explode(FondyHelper::ORDER_SEPARATOR, $_POST['order_id']);
        $order = $ordersEntity->get(intval($order_id));
        $payment_method = $paymentsEntity->get((int)$order->payment_method_id);
        $settings = $paymentsEntity->getPaymentSettings($payment_method->id);

        $options = array(
            'merchant' => $settings['fondy_merchantid'],
            'secretkey' => $settings['fondy_secret'],
        );
        $paymentInfo = FondyHelper::isPaymentValid($options, $_POST);

        if (!$order->paid) {

            if ($_POST['amount'] / 100 >= round($money->convert($order->total_price, $payment_method->currency_id, false), 2)) {
                if ($paymentInfo === true) {
                    if ($_POST['order_status'] == FondyHelper::ORDER_APPROVED) {

                        // Установим статус оплачен
                        $ordersEntity->update(intval($order->id), array('paid' => 1));

                        // Отправим уведомление на email
                        $notify->emailOrderUser(intval($order->id));
                        $notify->emailOrderAdmin(intval($order->id));

                        // Спишем товары
                        $ordersEntity->close(intval($order->id));
                        Response::redirectTo(Router::generateUrl('order', ['url' => $order->url], true));

                    } else {
                        $ordersEntity->update(intval($order->id), array('paid' => 0));

                        Response::redirectTo(Router::generateUrl('order', ['url' => $order->url], true));
                    }
                }
            }
        }
        
        Response::redirectTo(Router::generateUrl('order', ['url' => $order->url], true));
    }

}