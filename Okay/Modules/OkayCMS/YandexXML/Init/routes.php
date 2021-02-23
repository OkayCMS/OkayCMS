<?php


namespace Okay\Modules\OkayCMS\YandexXML;


return [
    'OkayCMS_YandexXML_feed' => [
        'slug' => 'yandex/{$url}.xml',
        'patterns' => [
            '{$url}' => '([0-9A-z\-]+)?',
        ],
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\YandexXMLController',
            'method' => 'render',
        ],
    ],
];