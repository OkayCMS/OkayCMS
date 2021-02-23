<?php


namespace Okay\Modules\OkayCMS\YandexXMLVendorModel;


return [
    'OkayCMS_YandexXMLVendorModel_feed' => [
        'slug' => 'yandex-vendor-model/{$url}.xml',
        'patterns' => [
            '{$url}' => '([0-9A-z\-]+)?',
        ],
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\YandexXMLController',
            'method' => 'render',
        ],
    ],
];