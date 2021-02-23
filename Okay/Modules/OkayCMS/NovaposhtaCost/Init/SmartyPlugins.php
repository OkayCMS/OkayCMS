<?php


use Okay\Core\Design;
use Okay\Modules\OkayCMS\NovaposhtaCost\Plugins\NewpostCityPlugin;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    NewpostCityPlugin::class => [
        'class' => NewpostCityPlugin::class,
        'arguments' => [
            new SR(Design::class),
            new SR(\Okay\Core\EntityFactory::class),
        ],
    ],
];