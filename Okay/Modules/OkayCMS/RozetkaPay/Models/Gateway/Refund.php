<?php


namespace Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway;

use Okay\Core\EntityFactory;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\Client\HttpCurl;
use Okay\Entities\OrdersEntity;
use Okay\Core\QueryFactory;

class Refund
{
    const REFUND_URL = 'refund';

    /**
     * @var HttpCurl
     */
    protected $client;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    /**
     * @var QueryFactory
     */
    protected $queryFactory;

    /**
     * @param HttpCurl $client
     * @param EntityFactory $entityFactory
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        HttpCurl $client,
        EntityFactory $entityFactory,
        QueryFactory $queryFactory
    )
    {
        $this->client = $client;
        $this->entityFactory = $entityFactory;
        $this->queryFactory = $queryFactory;
    }

    public function refund($order)
    {
        $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);
        $paymentMethod = $paymentsEntity->get($order->payment_method_id);
        $settings = $paymentsEntity->getPaymentSettings($paymentMethod->id);
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
        $paymentCurrency = $currenciesEntity->get(intval($paymentMethod->currency_id));
        $orderArray = (array)$order;
        if($settings['rozetkapay_secretkey'] === 'XChz3J8qrr') {
            $postfix = \Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\CreatePayment::POSTFIX_FOR_TEST;
            $orderArray['id'] = $order->id . $postfix;
        }
        $orderArray['currency'] = (array)$paymentCurrency;
        $data = json_encode($this->prepareRequest($orderArray));
        return $this->client->request('post', self::REFUND_URL, $data, $settings);
    }

    private function prepareRequest($order)
    {
        $data = [
            'amount' => $this->getAmount($order['id']),
            'currency' => $order['currency']['code'],
            'external_id' => (string)$order['id']
        ];

        return $data;
    }

    public function getAmount($id)
    {
        $createDetails = $this->getPaymentDetails((int)$id, $this->queryFactory, OrdersEntity::getTable());
        return $createDetails->details->amount;
    }

    private function getPaymentDetails($id, $queryFactory, $table)
    {
        $postfix = \Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\CreatePayment::POSTFIX_FOR_TEST;
        $originalId = str_replace($postfix, '', (string)$id);
        $select = $queryFactory->newSelect();
        $data = $select->from($table)
            ->cols(['payment_details'])
            ->where('id=:id')
            ->bindValue('id', $originalId)
            ->results('payment_details');

        return json_decode($data[0]);
    }
}