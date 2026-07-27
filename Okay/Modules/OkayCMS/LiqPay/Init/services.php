<?php


namespace Okay\Modules\OkayCMS\LiqPay;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Money;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Settings;

return [
    PaymentForm::class => [
        'class' => PaymentForm::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Money::class),
            new SR(FrontTranslations::class),
            new SR(Settings::class),
        ],
    ],
];