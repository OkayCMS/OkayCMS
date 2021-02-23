<?php


namespace Okay\Modules\OkayCMS\Rozetka;


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
use Okay\Modules\OkayCMS\Rozetka\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\Rozetka\Helpers\BackendRozetkaHelper;
use Okay\Modules\OkayCMS\Rozetka\Helpers\RozetkaHelper;

return [
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class)
        ],
    ],
    RozetkaHelper::class => [
        'class' => RozetkaHelper::class,
        'arguments' => [
            new SR(Image::class),
            new SR(Money::class),
            new SR(Settings::class),
            new SR(QueryFactory::class),
            new SR(Languages::class),
            new SR(EntityFactory::class),
            new SR(XmlFeedHelper::class),
        ],
    ],
    BackendRozetkaHelper::class => [
        'class' => BackendRozetkaHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(QueryFactory::class),
            new SR(Request::class),
            new SR(ProductsHelper::class)
        ],
    ],
];