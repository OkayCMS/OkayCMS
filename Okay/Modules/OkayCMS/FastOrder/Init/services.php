<?php


namespace Okay\Modules\OkayCMS\FastOrder;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\Validator;
use Okay\Modules\OkayCMS\FastOrder\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\FastOrder\Helpers\ValidateHelper;

return [
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Request::class),
            new SR(ValidateHelper::class),
        ],
    ],
    ValidateHelper::class => [
        'class' => ValidateHelper::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Validator::class),
            new SR(EntityFactory::class),
            new SR(FrontTranslations::class),
            new SR(Settings::class),
        ],
    ],
];