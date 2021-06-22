<?php

namespace Okay\Modules\OkayCMS\AutoDeploy;

return [
    'OkayCMS_AutoDeploy_build' => [
        'slug' => '/build_project/{$channel}/{$buildKey}',
        'patterns' => [
            '{$buildKey}' => '([a-f0-9]{32})',
        ],
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\BuildController',
            'method' => 'build',
        ],
        'always_active' => true,
    ],
];