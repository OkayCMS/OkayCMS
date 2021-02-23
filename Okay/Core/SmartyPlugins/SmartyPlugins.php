<?php


namespace Okay\Core\SmartyPlugins;


use Okay\Core\BrowsedProducts;
use Okay\Core\Modules\Module;
use Okay\Core\Modules\Modules;
use Okay\Core\Money;
use Okay\Core\Design;
use Okay\Core\Config;
use Okay\Core\Request;
use Okay\Core\Languages;
use Okay\Core\Router;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Settings;
use Okay\Core\DesignBlocks;
use Okay\Core\TemplateConfig\BackendTemplateConfig;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Helpers\BlogHelper;
use Okay\Helpers\BrandsHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\ProductsHelper;
use Okay\Core\Phone as PhoneCore;

$DI = include 'Okay/Core/config/container.php';

$plugins = [
    Plugins\GetCaptcha::class => [
        'class' => Plugins\GetCaptcha::class,
    ],
    Plugins\GetBrands::class => [
        'class' => Plugins\GetBrands::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(BrandsHelper::class),
        ],
    ],
    Plugins\GetPosts::class => [
        'class' => Plugins\GetPosts::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(BlogHelper::class),
        ],
    ],
    Plugins\GetFeaturedProducts::class => [
        'class' => Plugins\GetFeaturedProducts::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(ProductsHelper::class),
        ],
    ],
    Plugins\GetBrowsedProducts::class => [
        'class' => Plugins\GetBrowsedProducts::class,
        'arguments' => [
            new SR(BrowsedProducts::class),
        ],
    ],
    Plugins\GetNewProducts::class => [
        'class' => Plugins\GetNewProducts::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(ProductsHelper::class),
        ],
    ],
    Plugins\GetDiscountedProducts::class => [
        'class' => Plugins\GetDiscountedProducts::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(ProductsHelper::class),
        ],
    ],
    Plugins\Convert::class => [
        'class' => Plugins\Convert::class,
        'arguments' => [
            new SR(Money::class),
        ],
    ],
    Plugins\Phone::class => [
        'class' => Plugins\Phone::class,
        'arguments' => [
            new SR(PhoneCore::class),
        ],
    ],
    Plugins\Resize::class => [
        'class' => Plugins\Resize::class,
        'arguments' => [
            new SR(Image::class),
        ],
    ],
    Plugins\Token::class => [
        'class' => Plugins\Token::class,
        'arguments' => [
            new SR(Config::class),
        ],
    ],
    Plugins\Plural::class => [
        'class' => Plugins\Plural::class,
    ],
    Plugins\Url::class => [
        'class' => Plugins\Url::class,
        'arguments' => [
            new SR(Request::class),
        ],
    ],
    Plugins\Furl::class => [
        'class' => Plugins\Furl::class,
        'arguments' => [
            new SR(Router::class),
            new SR(FilterHelper::class),
        ],
    ],
    Plugins\First::class => [
        'class' => Plugins\First::class,
    ],
    Plugins\FirstLetter::class => [
        'class' => Plugins\FirstLetter::class,
    ],
    Plugins\Cut::class => [
        'class' => Plugins\Cut::class,
    ],
    Plugins\Date::class => [
        'class' => Plugins\Date::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Languages::class),
        ],
        'calls' => [
            [
                'method' => 'setDateFormat',
                'arguments' => [
                    new PR('plugins.date.date_format'),
                ]
            ],
        ]
    ],
    Plugins\Time::class => [
        'class' => Plugins\Time::class,
    ],
    Plugins\Balance::class => [
        'class' => Plugins\Balance::class,
    ],
    Plugins\GetTheme::class => [
        'class' => Plugins\GetTheme::class,
        'arguments' => [
            new SR(FrontTemplateConfig::class),
        ],
    ],
    Plugins\CssFile::class => [
        'class' => Plugins\CssFile::class,
        'arguments' => [
            new SR(FrontTemplateConfig::class),
            new SR(BackendTemplateConfig::class),
        ],
    ],
    Plugins\JsFile::class => [
        'class' => Plugins\JsFile::class,
        'arguments' => [
            new SR(FrontTemplateConfig::class),
            new SR(BackendTemplateConfig::class),
        ],
    ],
    Plugins\UrlGenerator::class => [
        'class' => Plugins\UrlGenerator::class,
        'arguments' => [
            new SR(Router::class),
            new SR(EntityFactory::class),
            new SR(Languages::class),
        ],
    ],
    Plugins\CheckoutPaymentForm::class => [
        'class' => Plugins\CheckoutPaymentForm::class,
        'arguments' => [
            new SR(Design::class),
            new SR(Module::class),
            new SR(Modules::class),
            new PR('root_dir'),
        ],
    ],
    Plugins\BackendCompactProductList::class => [
        'class' => Plugins\BackendCompactProductList::class,
        'arguments' => [
            new SR(Design::class),
            new SR(Config::class),
            new SR(Settings::class),
            new PR('root_dir'),
        ],
    ],
    Plugins\GetDesignBlock::class => [
        'class' => Plugins\GetDesignBlock::class,
        'arguments' => [
            new SR(DesignBlocks::class),
            new SR(Design::class),
            new SR(Config::class),
        ],
    ],
    Plugins\ReadSvg::class => [
        'class' => Plugins\ReadSvg::class,
        'arguments' => [
            new SR(Config::class),
        ],
    ],
];

$DI->bindServices($plugins);

// Регистрируем все плагины
foreach ($plugins as $plugin) {
    $p = $DI->get($plugin['class']);
    $p->register($DI->get(Design::class), $DI->get(Module::class));
}

