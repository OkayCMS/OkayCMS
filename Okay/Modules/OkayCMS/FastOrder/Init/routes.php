<?php

use Okay\Modules\OkayCMS\FastOrder\Controllers\FastOrderController;

return [
    'OkayCMS.FastOrder.CreateOrder' => [
        'slug' => '/okay-cms/fast-order/create-order',
        'params' => [
            'controller' => FastOrderController::class,
            'method' => 'createOrder',
        ],
    ],
];