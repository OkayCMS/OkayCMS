<?php

namespace Okay\Modules\OkayCMS\RozetkaPay;

return [
    'RozetkaPay_callback' => [
        'slug' => 'payment/rozetkapay/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];