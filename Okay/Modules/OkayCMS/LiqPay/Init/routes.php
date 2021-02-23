<?php

namespace Okay\Modules\OkayCMS\LiqPay;

return [
    'OkayCMS_LiqPay_callback' => [
        'slug' => 'payment/OkayCMS/LiqPay/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];