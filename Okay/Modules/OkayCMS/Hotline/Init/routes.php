<?php


namespace Okay\Modules\OkayCMS\Hotline;


use Okay\Modules\OkayCMS\Hotline\Controllers\HotlineController;

return [
    'OkayCMS_Hotline_Feed' => [
        'slug' => 'hotline/{$url}.xml',
        'patterns' => [
            '{$url}' => '([0-9A-z\-]+)?',
        ],
        'params' => [
            'controller' => HotlineController::class,
            'method' => 'render',
        ],
    ],
];