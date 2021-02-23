<?php

use Okay\Modules\OkayCMS\YooKassa\Controllers\CallbackController;
use Okay\Modules\OkayCMS\YooKassa\Controllers\RequestController;

return [
    'OkayCMS.YooKassa.Callback' => [
        'slug' => 'payment/OkayCMS/YooKassa/callback',
        'params' => [
            'controller' => CallbackController::class,
            'method' => 'payOrder',
        ],
    ],
    'OkayCMS.YooKassa.SendPaymentRequest' => [
        'slug' => 'payment/OkayCMS/YooKassa/sendPaymentRequest',
        'params' => [
            'controller' => RequestController::class,
            'method' => 'sendPaymentRequest',
        ],
    ],
];