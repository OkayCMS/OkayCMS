<?php


namespace Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway;

use Okay\Core\EntityFactory;
use Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\Client\HttpCurl;

class CreatePayment
{
    const CREATE_PAYMENT = 'new';

    const POSTFIX_FOR_TEST = '20249OKAY';

    private $client;

    /** @var EntityFactory */
    protected $entityFactory;

    /**
     * @param HttpCurl $client
     * @param EntityFactory $entityFactory
     * @return void
     */
    public function __construct(
        HttpCurl $client,
        EntityFactory $entityFactory
    )
    {
        $this->client = $client;
        $this->entityFactory = $entityFactory;
    }

    public function createPayment($order)
    {
        $data = $this->prepareRequest($order);
        if($order['settings']['rozetkapay_secretkey'] === 'XChz3J8qrr') {
            $data['external_id'] = $data['external_id'] .'-'.date('YmdHis'). self::POSTFIX_FOR_TEST;
        }
        $data = json_encode($data);
        return $this->client->request('post', 'new', $data, $order['settings']);
    }

    private function prepareRequest($order): array
    {
        return [
            'amount' => $order['total_price'],
            'callback_url' => $order['callback_url'],
            'result_url' => $order['result_url'],
            'currency' => $order['currency']['code'],
            'external_id' => (string)$order['id'],
            'mode' => 'hosted'
        ];
    }
}