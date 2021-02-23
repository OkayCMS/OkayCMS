<?php


namespace Okay\Modules\OkayCMS\Banners;


use Okay\Core\Config;
use Okay\Core\Database;
use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Languages;
use Okay\Core\Modules\Module;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Modules\OkayCMS\Banners\Extenders\FrontExtender;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersHelper;
use Okay\Modules\OkayCMS\Banners\Helpers\BannersImagesHelper;
use Okay\Modules\OkayCMS\Banners\Requests\BannersImagesRequest;
use Okay\Modules\OkayCMS\Banners\Requests\BannersRequest;

return [
    FrontExtender::class => [
        'class' => FrontExtender::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(Module::class),
            new SR(BannersHelper::class),
        ],
    ],
    BannersImagesHelper::class => [
        'class' => BannersImagesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Config::class),
            new SR(Image::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(Request::class),
            new SR(Languages::class),
        ],
    ],
    BannersImagesRequest::class => [
        'class' => BannersImagesRequest::class,
        'arguments' => [
            new SR(Request::class),
        ],
    ],
    BannersRequest::class => [
        'class' => BannersRequest::class,
        'arguments' => [
            new SR(Request::class),
        ],
    ],
    BannersHelper::class => [
        'class' => BannersHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Design::class),
        ],
    ],
];