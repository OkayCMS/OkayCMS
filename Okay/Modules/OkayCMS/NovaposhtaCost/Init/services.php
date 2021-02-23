<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Modules\Module;
use Okay\Core\Money;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\FrontExtender;

return [
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
        ],
    ],
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(Module::class),
        ],
    ],
    NovaposhtaCost::class => [
        'class' => NovaposhtaCost::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(EntityFactory::class),
            new SR(Money::class),
            new SR(Languages::class),
        ],
    ],
];