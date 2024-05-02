<?php

namespace Okay\Modules\OkayCMS\RozetkaPay;

use Okay\Core\EntityFactory;
use Okay\Core\Modules\AbstractModule;
use Okay\Core\Modules\Interfaces\PaymentFormInterface;
use Okay\Core\Money;
use Okay\Core\Router;
use Okay\Core\Languages;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\CreatePayment;
use Okay\Core\QueryFactory;

class PaymentForm extends AbstractModule implements PaymentFormInterface
{

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var Languages
     */
    private $languages;

    /**
     * @var Money
     */
    private $money;

    /**
     * @var CreatePayment
     */
    private $createPayment;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @param EntityFactory $entityFactory
     * @param Languages $languages
     * @param Money $money
     * @param CreatePayment $createPayment
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        EntityFactory $entityFactory,
        Languages $languages,
        Money $money,
        CreatePayment $createPayment,
        QueryFactory $queryFactory
    )
    {
        parent::__construct();
        $this->entityFactory = $entityFactory;
        $this->languages     = $languages;
        $this->money         = $money;
        $this->createPayment = $createPayment;
        $this->queryFactory  = $queryFactory;
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

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);

        $order = $ordersEntity->get((int)$orderId);

        $paymentMethod = $paymentsEntity->get($order->payment_method_id);
        $createDetails = $this->getPaymentDetails((int)$orderId, $this->queryFactory, OrdersEntity::getTable());
        if(empty($createDetails)) {
            $settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);
            $paymentCurrency = $currenciesEntity->get(intval($paymentMethod->currency_id));
            $orderArray = (array)$order;
            $orderArray['currency'] = (array)$paymentCurrency;
            $orderArray['callback_url'] = Router::generateUrl('RozetkaPay_callback', [], true);
            $orderArray['result_url'] = Router::generateUrl('order', ['url' => $order->url], true);
            $orderArray['settings'] = $settings;
            $apiResult = $this->createPayment->createPayment($orderArray);
            $details = json_encode($apiResult);
            $ordersEntity->update((int)$order->id, ['payment_details' => $details]);
        } else {
            $apiResult = $createDetails;
        }

        $this->design->assign('rozetkaPayUrl', $apiResult->action->value);

        return $this->design->fetch('form.tpl');
    }

    private function getPaymentDetails($id, $queryFactory, $table)
    {
        $select = $queryFactory->newSelect();
        $data = $select->from($table)
            ->cols(['payment_details'])
            ->where('id=:id')
            ->bindValue('id', $id)
            ->results('payment_details');

        return json_decode($data[0]);
    }
}