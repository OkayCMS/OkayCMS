<?php

namespace Okay\Modules\OkayCMS\Integration1C;

return [
    'integration_1c' => [
        'slug' => 'cml/1c_exchange.php',
        'params' => [
            'controller' => __NAMESPACE__ . '\Controllers\Integration1cController',
            'method' => 'runIntegration',
        ],
        'always_active' => true,
    ],
];