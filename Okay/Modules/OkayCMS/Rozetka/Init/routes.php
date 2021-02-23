<?php


namespace Okay\Modules\OkayCMS\Rozetka;


return [
    'OkayCMS_Rozetka_feed' => [
        'slug' => 'rozetka/{$url}.xml',
        'patterns' => [
            '{$url}' => '([0-9A-z\-]+)?',
        ],
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\RozetkaController',
            'method' => 'render',
        ],
    ],
];