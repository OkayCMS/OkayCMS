<?php


namespace Okay\Modules\OkayCMS\PayKeeper;


use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{	
    
    private $entityFactory;
    private $money;
    
    public function __construct(EntityFactory $entityFactory, Money $money)
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->money = $money;
    }

    /**
     * @inheritDoc
     */
    public function checkoutForm($orderId)
	{
	    /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var PaymentsEntity $paymentsEntity */
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);

        $order            = $ordersEntity->get((int)$orderId);
        $paymentMethod    = $paymentsEntity->get($order->payment_method_id);
        $payment_settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);

        $price = $this->money->convert($order->total_price, $paymentMethod->currency_id, false);
        
        $phone = preg_replace('/[^\d]/', '', $order->phone);
        $phone = substr($phone, -min(10, strlen($phone)), 10);

        $clientId = $order->user_id;

        $formData = [
            "phone"     => $phone,
            "clientid"  => $clientId,
            "sum"       => number_format($price, 2,'.',''),
            "orderid"   => $orderId
        ];

        //build the post string
        $postString = "";
        foreach($formData as $key => $val){
            $postString .= urlencode($key) . "=" . urlencode($val) . "&";
        }
        // strip off trailing ampersand
        $postString = substr($postString, 0, -1);

        $url = $payment_settings['PAYKEEPER_PAYMENT_FORM_URL'];

        if (function_exists( "curl_init" )) {

            $CR = curl_init();
            curl_setopt($CR, CURLOPT_URL, $url);
            curl_setopt($CR, CURLOPT_POST, 1);
            curl_setopt($CR, CURLOPT_FAILONERROR, true);
            curl_setopt($CR, CURLOPT_POSTFIELDS, $postString);
            curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec( $CR );

            $error = curl_error( $CR );
            if( !empty( $error )) {
                $html = "<br/><span class=message>"."INTERNAL ERROR:".$error."</span>";
            } else {
                $html = $result;
            }

            curl_close( $CR );

            $this->design->assign('html_form', $html);
            return $this->design->fetch('form.tpl');
        }

        $paymentParameters = http_build_query([
            "clientid" => $clientId,
            "orderid"  => $orderId,
            "sum"      => number_format($price, 2,'.',''),
            "phone"    => $phone
        ]);

        $options = [
                "http"    => [
                "method"  => "POST",
                "header"  => "Content-type: application/x-www-form-urlencoded",
                "content" => $paymentParameters
            ]
        ];

        $context = stream_context_create($options);
        $html = file_get_contents($url, false, $context);

        $this->design->assign('html_form', $html);
        return $this->design->fetch('form.tpl');
	}
}