<?php

namespace Okay\Modules\OkayCMS\Feeds\Init;

use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendGoogleMerchantAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendHotlineAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendRozetkaAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendYandexAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\GoogleMerchantAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\HotlineAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\RozetkaAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\YandexAdapter;

return [
    'modules' => [
        'okay_cms' => [
            'feeds' => [
                'presets' => [
                    'GoogleMerchant' => [
                        'backend_adapter' => BackendGoogleMerchantAdapter::class,
                        'frontend_adapter' => GoogleMerchantAdapter::class
                    ],
                    'Hotline' => [
                        'backend_adapter' => BackendHotlineAdapter::class,
                        'frontend_adapter' => HotlineAdapter::class
                    ],
                    'Rozetka' => [
                        'backend_adapter' => BackendRozetkaAdapter::class,
                        'frontend_adapter' => RozetkaAdapter::class
                    ],
                    'YML' => [
                        'backend_adapter' => BackendYandexAdapter::class,
                        'frontend_adapter' => YandexAdapter::class
                    ],
                ]
            ]
        ]
    ]
];