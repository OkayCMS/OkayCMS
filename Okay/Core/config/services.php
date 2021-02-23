<?php


namespace Okay\Core;


use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\RotatingFileHandler;
use Okay\Core\Entity\UrlUniqueValidator;
use Okay\Core\Modules\ModuleDesign;
use Okay\Core\Modules\ModulesEntitiesFilters;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;

use Monolog\Logger;
use Okay\Core\Routes\RouteFactory;
use Okay\Core\TemplateConfig\BackendTemplateConfig;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Helpers\MainHelper;
use Okay\Helpers\DiscountsHelper;
use Okay\Helpers\NotifyHelper;
use Okay\Core\TplMod\TplMod;
use Okay\Helpers\OrdersHelper;
use Psr\Log\LoggerInterface;
use Bramus\Router\Router as BRouter;
use Smarty;
use Mobile_Detect;
use Aura\SqlQuery\QueryFactory as AuraQueryFactory;
use Aura\Sql\ExtendedPdo;
use Okay\Core\Import as ImportCore;
use PHPMailer\PHPMailer\PHPMailer;
use Okay\Helpers\ProductsHelper;
use Okay\Helpers\MoneyHelper;
use Okay\Core\Modules\Module;
use Okay\Core\Modules\Modules;
use Okay\Core\Modules\Installer;
use Okay\Core\Modules\SqlPresentor;
use Okay\Core\Modules\EntityMigrator;
use Okay\Core\Modules\UpdateObject;
use Okay\Core\Modules\Extender\ChainExtender;
use Okay\Core\Modules\Extender\QueueExtender;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\UserReferer\UserReferer;
use Snowplow\RefererParser\Parser;
use Okay\Core\TplMod\Parser as TplParser;

$services = [
    BRouter::class => [
        'class' => BRouter::class,
    ],
    PHPMailer::class => [
        'class' => PHPMailer::class,
    ],
    Smarty::class => [
        'class' => Smarty::class,
    ],
    Mobile_Detect::class => [
        'class' => Mobile_Detect::class,
    ],
    Router::class => [
        'class' => Router::class,
        'arguments' => [
            new SR(BRouter::class),
            new SR(Request::class),
            new SR(Response::class),
            new SR(EntityFactory::class),
            new SR(Languages::class),
            new SR(RouteFactory::class),
            new SR(Modules::class),
        ],
    ],
    Config::class => [
        'class' => Config::class,
        'arguments' => [
            new PR('config.config_file'),
            new PR('config.config_local_file'),
        ],
    ],
    Database::class => [
        'class' => Database::class,
        'arguments' => [
            new SR(ExtendedPdo::class),
            new SR(LoggerInterface::class),
            new PR('db'),
            new SR(QueryFactory::class),
        ],
    ],
    AuraQueryFactory::class => [
        'class' => AuraQueryFactory::class,
        'arguments' => [
            new PR('db.driver'),
        ],
    ],
    QueryFactory::class => [
        'class' => QueryFactory::class,
        'arguments' => [
            new SR(AuraQueryFactory::class),
        ],
    ],
    ExtendedPdo::class => [
        'class' => ExtendedPdo::class,
        'arguments' => [
            new PR('db.dsn'),
            new PR('db.user'),
            new PR('db.password'),
        ],
    ],
    EntityFactory::class => [
        'class' => EntityFactory::class,
        'arguments' => [
            new SR(LoggerInterface::class),
        ],
    ],
    Request::class => [
        'class' => Request::class,
    ],
    Response::class => [
        'class' => Response::class,
        'arguments' => [
            new SR(Adapters\Response\AdapterManager::class),
            new PR('config.version'),
        ],
    ],
    Languages::class => [
        'class' => Languages::class,
        'arguments' => [
            new SR(Database::class),
            new SR(Request::class),
            new SR(QueryFactory::class),
        ],
    ],
    Validator::class => [
        'class' => Validator::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Recaptcha::class),
        ],
    ],
    Settings::class => [
        'class' => Settings::class,
        'arguments' => [
            new SR(Database::class),
            new SR(Languages::class),
            new SR(QueryFactory::class),
        ],
    ],
    FrontTemplateConfig::class => [
        'class' => FrontTemplateConfig::class,
        'arguments' => [
            new SR(Modules::class),
            new SR(Module::class),
            new SR(Settings::class),
            new SR(Config::class),
            new PR('root_dir'),
            new PR('template_config.scripts_defer'),
            new PR('template_config.them_settings_filename'),
            new PR('template_config.compile_css_dir'),
            new PR('template_config.compile_js_dir'),
        ],
    ],
    BackendTemplateConfig::class => [
        'class' => BackendTemplateConfig::class,
        'arguments' => [
            new SR(Modules::class),
            new SR(Module::class),
            new PR('root_dir'),
            new PR('template_config.scripts_defer'),
            new PR('template_config.them_settings_filename'),
            new PR('template_config.compile_css_dir'),
            new PR('template_config.compile_js_dir'),
        ],
    ],
    Design::class => [
        'class' => Design::class,
        'arguments' => [
            new SR(Smarty::class),
            new SR(Mobile_Detect::class),
            new SR(FrontTemplateConfig::class),
            new SR(Module::class),
            new SR(Modules::class),
            new SR(TplMod::class),
            new PR('design.smarty_cache_lifetime'),
            new PR('design.smarty_compile_check'),
            new PR('design.smarty_html_minify'),
            new PR('design.smarty_debugging'),
            new PR('design.smarty_security'),
            new PR('design.smarty_caching'),
            new PR('design.smarty_force_compile'),
            new PR('root_dir'),
        ],
    ],
    Image::class => [
        'class' => Image::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Config::class),
            new SR(Adapters\Resize\AdapterManager::class),
            new SR(Request::class),
            new SR(Response::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(EntityFactory::class),
            new PR('root_dir'),
        ],
    ],
    Notify::class => [
        'class' => Notify::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Languages::class),
            new SR(EntityFactory::class),
            new SR(Design::class),
            new SR(FrontTemplateConfig::class),
            new SR(OrdersHelper::class),
            new SR(BackendTranslations::class),
            new SR(FrontTranslations::class),
            new SR(PHPMailer::class),
            new SR(LoggerInterface::class),
            new SR(NotifyHelper::class),
            new PR('root_dir'),
        ],
    ],
    Money::class => [
        'class' => Money::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
        'calls' => [
            [
                'method' => 'configure',
                'arguments' => [
                    new PR('money.decimals_point'),
                    new PR('money.thousands_separator'),
                ]
            ],
        ]
    ],
    ChromePHPHandler::class => [
        'class' => ChromePHPHandler::class,
        'arguments' => [
            Logger::DEBUG,
        ],
    ],
    RotatingFileHandler::class => [
        'class' => RotatingFileHandler::class,
        'arguments' => [
            new PR('logger.file'),
            new PR('logger.max_files_rotation'),
            Logger::DEBUG,
        ],
    ],
    LoggerInterface::class => [
        'class' => Logger::class,
        'arguments' => [ 'channel-name' ],
        'calls' => [
            [
                'method' => 'pushHandler',
                'arguments' => [
                    new SR(ChromePHPHandler::class),
                ]
            ],
            [
                'method' => 'pushHandler',
                'arguments' => [
                    new SR(RotatingFileHandler::class),
                ]
            ],
        ]
    ],
    Recaptcha::class => [
        'class' => Recaptcha::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Request::class),
        ],
    ],
    Managers::class => [
        'class' => Managers::class,
    ],
    Translit::class => [
        'class' => Translit::class,
    ],
    ManagerMenu::class => [
        'class' => ManagerMenu::class,
        'arguments' => [
            new SR(Managers::class),
            new SR(Module::class),
            new PR('manager_menu.dev_mode'),
        ],
    ],
    BackendTranslations::class => [
        'class' => BackendTranslations::class,
        'arguments' => [
            new SR(LoggerInterface::class),
            new SR(Modules::class),
            new PR('design.debug_translation'),
        ],
    ],
    FrontTranslations::class => [
        'class' => FrontTranslations::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Languages::class),
            new SR(Modules::class),
            new PR('design.debug_translation'),
        ],
    ],
    JsSocial::class => [
        'class' => JsSocial::class,
    ],
    DataCleaner::class => [
        'class' => DataCleaner::class,
        'arguments' => [
            new SR(Database::class),
            new SR(Config::class),
            new SR(QueryFactory::class),
        ],
    ],
    ImportCore::class => [
        'class' => ImportCore::class
    ],
    Cart::class => [
        'class' => Cart::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Settings::class),
            new SR(ProductsHelper::class),
            new SR(MoneyHelper::class),
            new SR(MainHelper::class),
            new SR(Discounts::class),
            new SR(DiscountsHelper::class),
        ],
        'calls' => [
            [
                'method' => 'init'
            ]
        ]
    ],
    Comparison::class => [
        'class' => Comparison::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Settings::class),
            new SR(MoneyHelper::class),
            new SR(MainHelper::class),
        ],
    ],
    WishList::class => [
        'class' => WishList::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Settings::class),
            new SR(MoneyHelper::class),
            new SR(MainHelper::class),
        ],
    ],
    BrowsedProducts::class => [
        'class' => BrowsedProducts::class,
        'arguments' => [
            new SR(ProductsHelper::class),
            new SR(MainHelper::class),
            new SR(EntityFactory::class),
        ],
    ],
    ModulesEntitiesFilters::class => [
        'class' => ModulesEntitiesFilters::class,
    ],
    Module::class => [
        'class' => Module::class,
        'arguments' => [
            new SR(LoggerInterface::class),
        ],
    ],
    ModuleDesign::class => [
        'class' => ModuleDesign::class,
        'arguments' => [
            new SR(Module::class),
            new SR(FrontTemplateConfig::class),
            new SR(Config::class),
        ],
    ],
    Modules::class => [
        'class' => Modules::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Module::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(Config::class),
            new SR(Smarty::class),
            new SR(Smarty::class),
        ],
    ],
    Installer::class => [
        'class' => Installer::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Module::class),
        ],
    ],
    Support::class => [
        'class' => Support::class,
        'arguments' => [
            new SR(Config::class),
            new SR(Settings::class),
            new SR(EntityFactory::class),
        ],
    ],
    EntityMigrator::class => [
        'class' => EntityMigrator::class,
        'arguments' => [
            new SR(Database::class),
            new SR(QueryFactory::class),
            new SR(SqlPresentor::class),
        ],
    ],
    SqlPresentor::class => [
        'class' => SqlPresentor::class,
    ],
    UpdateObject::class => [
        'class' => UpdateObject::class,
    ],
    QueueExtender::class => [
        'class' => QueueExtender::class
    ],
    ChainExtender::class => [
        'class' => ChainExtender::class
    ],
    ExtenderFacade::class => [
        'class' => ExtenderFacade::class,
        'arguments' => [
            new SR(QueueExtender::class),
            new SR(ChainExtender::class),
        ],
    ],
    DesignBlocks::class => [
        'class' => DesignBlocks::class,
        'arguments' => [
            new SR(Design::class),
            new SR(EntityFactory::class),
        ],
    ],
    UrlUniqueValidator::class => [
        'class' => UrlUniqueValidator::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
    RouteFactory::class => [
        'class' => RouteFactory::class,
        'arguments' => [],
    ],
    BackendPostRedirectGet::class => [
        'class' => BackendPostRedirectGet::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Response::class),
        ],
    ],
    Parser::class => [
        'class' => Parser::class,
        'arguments' => [
            UserReferer::createConfigReader(),
        ],
    ],
    UserReferer::class => [
        'class' => UserReferer::class,
        'arguments' => [
            new SR(Parser::class),
        ],
    ],
    Phone::class => [
        'class' => Phone::class,
        'arguments' => [
            new SR(Settings::class),
        ],
    ],
    TplParser::class => [
        'class' => TplParser::class,
    ],
    TplMod::class => [
        'class' => TplMod::class,
        'arguments' => [
            new SR(TplParser::class),
            new SR(Config::class),
        ],
    ],
    TemplateConfig::class => [
        'class' => TemplateConfig::class,
        'arguments' => [
            new SR(FrontTemplateConfig::class),
        ],
    ],
    Discounts::class => [
        'class' => Discounts::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
];

$adapters = include __DIR__ . '/../Adapters/adapters.php';
$requestContainers = include __DIR__ . '/requests.php';
$helpers           = include __DIR__ . '/helpers.php';

return array_merge($services, $adapters, $requestContainers, $helpers);
