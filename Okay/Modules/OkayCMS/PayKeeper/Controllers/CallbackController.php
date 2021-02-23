<?php


namespace Okay\Modules\OkayCMS\PayKeeper\Controllers;


use Okay\Controllers\AbstractController;
use Okay\Core\Money;
use Okay\Core\Notify;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;

class CallbackController extends AbstractController
{
    public function payOrder(
        PaymentsEntity $paymentsEntity,
        VariantsEntity $variantsEntity,
        OrdersEntity $ordersEntity,
        PurchasesEntity $purchasesEntity,
        Notify $notify,
        Money $money
    ) {
        $this->response->setContentType(RESPONSE_TEXT);

        if (empty($_POST)) {
            die('Request doesn\'t contain POST elements.');
        }

        $theId       =  $this->request->post('id');
        $theSum      =  $this->request->post('sum');
        $theClientId =  $this->request->post('clientid');
        $theOrderId  =  $this->request->post('orderid', 'integer');
        $theKey      =  $this->request->post('key');
        
        if (empty($theOrderId) || strlen($theOrderId) > 50) {
            die('Missing or invalid order ID');
        }
        
        $theOrder = $ordersEntity->get((int) $theOrderId);
        if(empty($theOrder)) {
            die('Order not found');
        }
        
        if($theOrder->paid) {
            die('Order has been paid already');
        }

        $method = $paymentsEntity->get((int) $theOrder->payment_method_id);
        if(empty($method)) {
            die("Unknown payment method");
        }

        $ourPrice = $money->convert($theOrder->total_price, $method->currency_id, false);
        $ourPrice = number_format($ourPrice, 2, '.', '');

        $settings = unserialize($method->settings);

        $TMGCO_SECRET_KEY = $settings['PAYKEEPER_SECRET'];

        $ourCustomerId = $theOrder->user_id;
        
        if (!isset($theClientId) || strlen($theClientId) > 50) {
            die('Missing or invalid client ID');
        }
        
        if ($ourCustomerId != $theClientId) {
            die('Client not found');
        }

        if ($ourPrice != $theSum) {
            die('Incorrect amount');
        }

        $ourKey = $TMGCO_SECRET_KEY;

        $ourSignature = md5($theId . $theSum . $theClientId . $theOrderId . $ourKey);
        
        if($theKey != $ourSignature) {
            die('Message digest incorrect');
        }

        $purchases = $purchasesEntity->find(['order_id'=> (int) $theOrder->id]);
        foreach($purchases as $purchase) {
            $variant = $variantsEntity->get((int) $purchase->variant_id);
            if(empty($variant) || (!$variant->infinity && $variant->stock < $purchase->amount)) {
                die("Low stock of $purchase->product_name $purchase->variant_name");
            }
        }
        
        $ordersEntity->update((int) $theOrder->id, ['paid'=>1]);
        $ordersEntity->close(intval($theOrder->id));
        $notify->emailOrderUser(intval($theOrder->id));
        $notify->emailOrderAdmin(intval($theOrder->id));
        
        $ourHash = md5($theId . $ourKey);
        die("OK $ourHash");
    }
}
