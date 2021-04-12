<?php


namespace Okay\Modules\OkayCMS\Banners;


use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\TemplateConfig;
use Okay\Modules\OkayCMS\AutoDeploy\Extenders\BackendExtender;
use Okay\Modules\OkayCMS\AutoDeploy\Helpers\DeployHelper;
use Okay\Modules\OkayCMS\AutoDeploy\Helpers\TranslationsHelper;

return [
    DeployHelper::class => [
        'class' => DeployHelper::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Settings::class),
            new SR(Database::class),
            new SR(EntityFactory::class),
        ],
    ],
    BackendExtender::class => [
        'class' => BackendExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(TemplateConfig::class),
            new SR(TranslationsHelper::class),
            new SR(Settings::class),
        ],
    ],
    TranslationsHelper::class => [
        'class' => TranslationsHelper::class,
        'arguments' => [
            new SR(TemplateConfig::class),
            new SR(Settings::class),
        ],
    ],
];