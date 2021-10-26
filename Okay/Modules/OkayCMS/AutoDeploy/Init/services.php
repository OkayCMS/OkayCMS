<?php


namespace Okay\Modules\OkayCMS\AutoDeploy;


use Okay\Core\Database;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Modules;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
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
            new SR(TranslationsHelper::class),
            new SR(Settings::class),
        ],
    ],
    TranslationsHelper::class => [
        'class' => TranslationsHelper::class,
        'arguments' => [
            new SR(FrontTemplateConfig::class),
            new SR(Settings::class),
            new SR(EntityFactory::class),
            new SR(Modules::class),
        ],
    ],
];