<?php

namespace Okay\Modules\OkayCMS\WayForPay;

return [
    'OkayCMS_WayForPay_callback' => [
        'slug' => 'payment/OkayCMS/WayForPay/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];