<?php


namespace Okay\Modules\OkayCMS\DeliveryFields\Init;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\OkayContainer\Reference\ParameterReference AS PR;
use Okay\Core\OkayContainer\Reference\ServiceReference AS SR;
use Okay\Core\Request;
use Okay\Core\Validator;
use Okay\Modules\OkayCMS\DeliveryFields\Backend\Helpers\BackendDeliveryFieldsHelper;
use Okay\Modules\OkayCMS\DeliveryFields\Backend\Requests\BackendDeliveryFieldsRequest;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\DeliveryFieldsExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\OrdersHelperExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Extenders\ValidateHelperExtender;
use Okay\Modules\OkayCMS\DeliveryFields\Helpers\DeliveryFieldsHelper;

return [
    BackendDeliveryFieldsHelper::class => [
        'class' => BackendDeliveryFieldsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
    BackendDeliveryFieldsRequest::class => [
        'class' => BackendDeliveryFieldsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ],
    ],
    DeliveryFieldsHelper::class => [
        'class' => DeliveryFieldsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
    DeliveryFieldsExtender::class => [
        'class' => DeliveryFieldsExtender::class,
        'arguments' => [
            new SR(DeliveryFieldsHelper::class),
        ],
    ],
    ValidateHelperExtender::class => [
        'class' => ValidateHelperExtender::class,
        'arguments' => [
            new SR(FrontTranslations::class),
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Validator::class),
            new SR(Design::class),
        ],
    ],
    OrdersHelperExtender::class => [
        'class' => OrdersHelperExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Design::class),
            new SR(DeliveryFieldsHelper::class),
        ],
    ],
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Design::class),
            new SR(DeliveryFieldsHelper::class),
        ],
    ],
];