<?php


namespace Okay\Modules\OkayCMS\YooKassa;


use Okay\Core\EntityFactory;
use Okay\Core\Languages;
use Okay\Core\Money;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Core\Notify;
use Okay\Core\Database;
use Okay\Core\Settings;
use Okay\Core\QueryFactory;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

require __DIR__.'/../vendor/autoload.php';

return [
    PaymentForm::class => [
        'class' => PaymentForm::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Languages::class),
            new SR(Money::class),
        ],
    ],
    YooMoneyCallbackHandler::class => [
        'class' => YooMoneyCallbackHandler::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Response::class),
            new SR(Notify::class),
            new SR(Database::class),
            new SR(QueryFactory::class),
        ],
    ],
    PaymentForm::class => [
        'class' => PaymentForm::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Response::class),
            new SR(Request::class),
            new SR(Money::class),
            new SR(Settings::class),
        ],
    ],
];