<?php


namespace Okay\Modules\OkayCMS\RozetkaPay;


use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Money;
use Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\CreatePayment;
use Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\Client\HttpCurl;
use Okay\Modules\OkayCMS\RozetkaPay\Models\Gateway\Refund;
use Okay\Modules\OkayCMS\RozetkaPay\Backend\Controllers\RefundAdmin;
use Okay\Core\QueryFactory;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    PaymentForm::class => [
        'class' => PaymentForm::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Languages::class),
            new SR(Money::class),
            new SR(CreatePayment::class),
            new SR(QueryFactory::class),
        ],
    ],
    CreatePayment::class => [
        'class' => CreatePayment::class,
        'arguments' => [
            new SR(HttpCurl::class),
            new SR(EntityFactory::class),
        ],
    ],
    HttpCurl::class => [
        'class' => HttpCurl::class,
        'arguments' => [
        ],
    ],
    RefundAdmin::class => [
        'class' => RefundAdmin::class,
        'arguments' => [
            new SR(Refund::class),
        ],
    ],
    Refund::class => [
        'class' => Refund::class,
        'arguments' => [
            new SR(HttpCurl::class),
            new SR(EntityFactory::class),
            new SR(QueryFactory::class)
        ],
    ],
];