<?php

namespace Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\Client;

interface ClientInterface
{
    /**
     * @param $method
     * @param $url
     * @param $params
     * @param $config
     * @return mixed
     */
    public function request($method, $url, $params, $config);
}