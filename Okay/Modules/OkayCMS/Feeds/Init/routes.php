<?php

namespace Okay\Modules\OkayCMS\Feeds\Init;

use Okay\Modules\OkayCMS\Feeds\Controllers\FeedController;

return [
    'OkayCMS.Feeds.Feed' => [
        'slug' => '/feeds/{$url}.xml',
        'patterns' => [
            '{$url}' => '([0-9A-z\-]+)?',
        ],
        'params' => [
            'controller' => FeedController::class,
            'method' => 'render',
        ],
        'to_front' => false
    ],
];