<?php


namespace Okay\Core;


use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    Adapters\Resize\AdapterManager::class => [
        'class' => Adapters\Resize\AdapterManager::class,
        'arguments' => [
            new PR('adapters.resize.default_adapter'),
        ],
        'calls' => [
            [
                'method' => 'configure',
                'arguments' => [
                    new PR('adapters.resize.watermark'),
                    new PR('adapters.resize.watermark_offset_x'),
                    new PR('adapters.resize.watermark_offset_y'),
                ]
            ],
        ]
    ],
    Adapters\Response\AdapterManager::class => [
        'class' => Adapters\Response\AdapterManager::class,
        'arguments' => [
            new PR('adapters.response.default_adapter'),
        ],
    ],
];