<?php

namespace Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\Client;

class HttpCurl implements ClientInterface
{
    public const LINK = 'https://api.rozetkapay.com/api/payments/v1/';

    /**
     * @var array<int, bool|int|string>
     */
    private $options = [
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_HEADER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 60,
        CURLOPT_USERAGENT => 'rozetkapay-php-sdk',
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_TIMEOUT => 60
    ];

    /**
     * @param string $method
     * @param string $url
     * @param string $params
     * @param array<string, string> $config
     * @return mixed|string
     * @throws HttpClientException
     */
    public function request($method, $url, $params, $config)
    {
        $headers = [
            'Authorization: ' . $this->setCredentials($config),
            'Content-Type: application/json',
            'Content-Length: ' . strlen($params),

        ];
        $method = strtoupper($method);
        if ($method === '') {
            throw new HttpClientException('The method is empty.');
        }
        $link = self::LINK . $url;
        if (!$this->curlEnabled()) {
            throw new HttpClientException('Curl not enabled.');
        }
        if (empty($link)) {
            throw new HttpClientException('The url is empty.');
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        foreach ($this->options as $option => $value) {
            curl_setopt($ch, $option, $value);
        }
        curl_setopt($ch, CURLOPT_URL, $link);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POST, 1);
        if ($params) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return json_decode(is_string($response) ? $response : '');
    }

    public function setCredentials($config)
    {
        $val = base64_encode("{$config['rozetkapay_merchant']}:{$config['rozetkapay_secretkey']}");
        return 'Basic ' . $val;
    }

    /**
     * @return bool
     */
    private function curlEnabled()
    {
        return function_exists('curl_init');
    }
}
