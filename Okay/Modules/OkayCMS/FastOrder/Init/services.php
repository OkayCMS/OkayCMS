<?php


namespace Okay\Modules\OkayCMS\FastOrder;


use Okay\Core\FrontTranslations;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\Validator;
use Okay\Modules\OkayCMS\FastOrder\Extenders\BackendExtender;

return [
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Request::class),
        ],
    ],
];