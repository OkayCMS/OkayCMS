<?php


namespace Okay\Modules\OkayCMS\GoogleMerchant;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Money;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Helpers\ProductsHelper;
use Okay\Helpers\XmlFeedHelper;
use Okay\Modules\OkayCMS\GoogleMerchant\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\GoogleMerchant\Helpers\BackendGoogleMerchantHelper;
use Okay\Modules\OkayCMS\GoogleMerchant\Helpers\GoogleMerchantHelper;

return [
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class)
        ],
    ],
    GoogleMerchantHelper::class => [
        'class' => GoogleMerchantHelper::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Languages::class),
            new SR(QueryFactory::class),
            new SR(XmlFeedHelper::class),
            new SR(EntityFactory::class),
            new SR(Image::class),
            new SR(Money::class),
        ],
    ],
    BackendGoogleMerchantHelper::class => [
        'class' => BackendGoogleMerchantHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(QueryFactory::class),
            new SR(Request::class),
            new SR(ProductsHelper::class)
        ],
    ],
];