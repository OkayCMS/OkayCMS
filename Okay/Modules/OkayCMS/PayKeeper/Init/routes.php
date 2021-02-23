<?php

namespace Okay\Modules\OkayCMS\PayKeeper;

return [
    'OkayCMS_PayKeeper_callback' => [
        'slug' => 'payment/OkayCMS/PayKeeper/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];