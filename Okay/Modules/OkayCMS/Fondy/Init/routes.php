<?php

namespace Okay\Modules\OkayCMS\Fondy;

return [
    'OkayCMS_Fondy_callback' => [
        'slug' => 'payment/OkayCMS/Fondy/callback',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\CallbackController',
            'method' => 'payOrder',
        ],
    ],
];