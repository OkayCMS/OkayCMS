<?php


use Okay\Core\Design;
use Okay\Modules\OkayCMS\FastOrder\Plugins\FastOrderPlugin;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    FastOrderPlugin::class => [
        'class' => FastOrderPlugin::class,
        'arguments' => [
            new SR(Design::class)
        ],
    ],
];