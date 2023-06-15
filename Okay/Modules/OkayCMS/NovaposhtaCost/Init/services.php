<?php


namespace Okay\Modules\OkayCMS\NovaposhtaCost;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Languages;
use Okay\Core\Modules\Module;
use Okay\Core\Money;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Helpers\NPBackendHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Backend\Requests\NPBackendRequest;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Extenders\FrontExtender;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPApiHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCacheHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPCalcHelper;
use Okay\Modules\OkayCMS\NovaposhtaCost\Helpers\NPDeliveryDataHelper;
use Psr\Log\LoggerInterface;

return [
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
            new SR(FrontTranslations::class),
            new SR(Design::class),
            new SR(NPDeliveryDataHelper::class),
        ],
    ],
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(Module::class),
            new SR(Settings::class),
            new SR(NPDeliveryDataHelper::class),
        ],
    ],
    NPApiHelper::class => [
        'class' => NPApiHelper::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(LoggerInterface::class),
        ],
    ],
    NPCacheHelper::class => [
        'class' => NPCacheHelper::class,
        'arguments' => [
            new SR(NPApiHelper::class),
            new SR(EntityFactory::class),
            new SR(Languages::class),
            new SR(Settings::class),
        ],
    ],
    NPBackendRequest::class => [
        'class' => NPBackendRequest::class,
        'arguments' => [
            new SR(Request::class),
        ],
    ],
    NPBackendHelper::class => [
        'class' => NPBackendHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
    NPCalcHelper::class => [
        'class' => NPCalcHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(NPApiHelper::class),
            new SR(Settings::class),
            new SR(Money::class),
        ],
    ],
    NPDeliveryDataHelper::class => [
        'class' => NPDeliveryDataHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
];