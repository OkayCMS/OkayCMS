<?php

namespace Okay\Modules\OkayCMS\WayForPay;

return [
    'OkayCMS_WayForPay_callback' => [
        'slug' => 'payment/okaycms/wayforpay/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];