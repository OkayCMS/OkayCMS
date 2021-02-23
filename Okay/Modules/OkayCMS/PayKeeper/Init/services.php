<?php


namespace Okay\Modules\OkayCMS\PayKeeper;


use Okay\Core\EntityFactory;
use Okay\Core\Money;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

return [
    PaymentForm::class => [
        'class' => PaymentForm::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Money::class),
        ],
    ],
];