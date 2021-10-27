<?php

namespace Okay\Modules\OkayCMS\Feeds\Init;

use Okay\Core\EntityFactory;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Helpers\ProductsHelper;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\BackendPresetAdapterFactory;
use Okay\Modules\OkayCMS\Feeds\Backend\Helpers\BackendFeedsHelper;
use Okay\Modules\OkayCMS\Feeds\Backend\Requests\BackendFeedsRequest;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\PresetAdapterFactory;
use Okay\Modules\OkayCMS\Feeds\Helpers\FeedsHelper;

return [
    BackendFeedsRequest::class => [
        'class' => BackendFeedsRequest::class,
        'arguments' => [
            new SR(Request::class),
            new SR(BackendPresetAdapterFactory::class)
        ]
    ],
    BackendFeedsHelper::class => [
        'class' => BackendFeedsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(ProductsHelper::class),
            new SR(BackendPresetAdapterFactory::class),
            new SR(QueryFactory::class),
            new SR(Request::class)
        ]
    ],
    BackendPresetAdapterFactory::class => [
        'class' => BackendPresetAdapterFactory::class,
        'arguments' => [
            new PR('modules.okay_cms.feeds.presets')
        ]
    ],
    FeedsHelper::class => [
        'class' => FeedsHelper::class,
        'arguments' => [
            new SR(PresetAdapterFactory::class),
        ]
    ],
    PresetAdapterFactory::class => [
        'class' => PresetAdapterFactory::class,
        'arguments' => [
            new PR('modules.okay_cms.feeds.presets')
        ]
    ],
];