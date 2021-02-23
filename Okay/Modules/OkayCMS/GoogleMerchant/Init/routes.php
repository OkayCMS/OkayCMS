<?php

namespace Okay\Modules\OkayCMS\GoogleMerchant;

return [
    'OkayCMS_GoogleMerchant_feed' => [
        'slug' => 'google/{$url}.xml',
        'patterns' => [
            '{$url}' => '([0-9A-z\-]+)?',
        ],
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\GoogleMerchantController',
            'method' => 'render',
        ],
    ],
];