<?php

namespace Okay\Modules\OkayCMS\Feeds\Init;

use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendFacebookAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendGoogleMerchantAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendHotlineAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendPriceUaAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendPromUaAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendRozetkaAdapter;
use Okay\Modules\OkayCMS\Feeds\Backend\Core\Presets\Adapters\BackendYmlAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\FacebookAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\GoogleMerchantAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\HotlineAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\PriceUaAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\PromUaAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\RozetkaAdapter;
use Okay\Modules\OkayCMS\Feeds\Core\Presets\Adapters\YmlAdapter;

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
                        'backend_adapter' => BackendYmlAdapter::class,
                        'frontend_adapter' => YmlAdapter::class
                    ],
                    'Facebook' => [
                        'backend_adapter' => BackendFacebookAdapter::class,
                        'frontend_adapter' => FacebookAdapter::class
                    ],
                    'Price.ua' => [
                        'backend_adapter' => BackendPriceUaAdapter::class,
                        'frontend_adapter' => PriceUaAdapter::class
                    ],
                    'Prom.ua' => [
                        'backend_adapter' => BackendPromUaAdapter::class,
                        'frontend_adapter' => PromUaAdapter::class
                    ],
                ]
            ]
        ]
    ]
];