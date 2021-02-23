<?php


namespace Okay\Core;


use Okay\Admin\Helpers\BackendAuthorsHelper;
use Okay\Admin\Helpers\BackendBlogCategoriesHelper;
use Okay\Admin\Helpers\BackendBlogHelper;
use Okay\Admin\Helpers\BackendCallbacksHelper;
use Okay\Admin\Helpers\BackendCategoryStatsHelper;
use Okay\Admin\Helpers\BackendCommentsHelper;
use Okay\Admin\Helpers\BackendCouponsHelper;
use Okay\Admin\Helpers\BackendDeliveriesHelper;
use Okay\Admin\Helpers\BackendFeaturesValuesHelper;
use Okay\Admin\Helpers\BackendFeedbacksHelper;
use Okay\Admin\Helpers\BackendImportHelper;
use Okay\Admin\Helpers\BackendMainHelper;
use Okay\Admin\Helpers\BackendManagersHelper;
use Okay\Admin\Helpers\BackendMenuHelper;
use Okay\Admin\Helpers\BackendModulesHelper;
use Okay\Admin\Helpers\BackendNotifyHelper;
use Okay\Admin\Helpers\BackendOrderHistoryHelper;
use Okay\Admin\Helpers\BackendOrderSettingsHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Helpers\BackendPagesHelper;
use Okay\Admin\Helpers\BackendPaymentsHelper;
use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Admin\Helpers\BackendUserGroupsHelper;
use Okay\Admin\Helpers\BackendUsersHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendOrdersRequest;
use Okay\Core\Modules\Module;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Admin\Helpers\BackendProductsHelper;
use Okay\Admin\Helpers\BackendVariantsHelper;
use Okay\Admin\Helpers\BackendFeaturesHelper;
use Okay\Admin\Helpers\BackendBrandsHelper;
use Okay\Admin\Helpers\BackendCategoriesHelper;
use Okay\Admin\Helpers\BackendSpecialImagesHelper;
use Okay\Admin\Helpers\BackendCurrenciesHelper;
use Okay\Core\SmartyPlugins\Plugins\CheckoutPaymentForm;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Helpers\AuthorsHelper;
use Okay\Helpers\BlogHelper;
use Okay\Helpers\BrandsHelper;
use Okay\Helpers\CanonicalHelper;
use Okay\Helpers\CartHelper;
use Okay\Helpers\ComparisonHelper;
use Okay\Helpers\CouponHelper;
use Okay\Helpers\CommentsHelper;
use Okay\Helpers\DeliveriesHelper;
use Okay\Helpers\DiscountsHelper;
use Okay\Helpers\MainHelper;
use Okay\Helpers\MetadataHelpers\AllProductsMetadataHelper;
use Okay\Helpers\MetadataHelpers\BestsellersMetadataHelper;
use Okay\Helpers\MetadataHelpers\BlogCategoryMetadataHelper;
use Okay\Helpers\MetadataHelpers\BrandMetadataHelper;
use Okay\Helpers\MetadataHelpers\CartMetadataHelper;
use Okay\Helpers\MetadataHelpers\CategoryMetadataHelper;
use Okay\Helpers\MetadataHelpers\CommonMetadataHelper;
use Okay\Helpers\MetadataHelpers\DiscountedMetadataHelper;
use Okay\Helpers\MetadataHelpers\OrderMetadataHelper;
use Okay\Helpers\MetadataHelpers\PostMetadataHelper;
use Okay\Helpers\MetadataHelpers\ProductMetadataHelper;
use Okay\Helpers\MetadataHelpers\AuthorMetadataHelper;
use Okay\Helpers\MetaRobotsHelper;
use Okay\Helpers\NotifyHelper;
use Okay\Helpers\PaymentsHelper;
use Okay\Helpers\RelatedProductsHelper;
use Okay\Helpers\CommonHelper;
use Okay\Helpers\ResizeHelper;
use Okay\Helpers\SiteMapHelper;
use Okay\Helpers\UserHelper;
use Okay\Helpers\ValidateHelper;
use Okay\Helpers\XmlFeedHelper;
use Okay\Requests\CommonRequest;
use Psr\Log\LoggerInterface;
use Okay\Helpers\ProductsHelper;
use Okay\Helpers\CatalogHelper;
use Okay\Helpers\OrdersHelper;
use Okay\Helpers\FilterHelper;
use Okay\Helpers\MoneyHelper;
use Okay\Core\Entity\UrlUniqueValidator;
use Okay\Admin\Helpers\BackendExportHelper;

return [
    BackendMainHelper::class => [
        'class' => BackendMainHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(ManagerMenu::class),
            new SR(Design::class),
        ]
    ],
    BackendProductsHelper::class => [
        'class' => BackendProductsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(Image::class),
            new SR(Config::class),
            new SR(Request::class),
        ]
    ],
    BackendExportHelper::class => [
        'class' => BackendExportHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ]
    ],
    BackendVariantsHelper::class => [
        'class' => BackendVariantsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BackendFeaturesHelper::class => [
        'class' => BackendFeaturesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(QueryFactory::class),
            new SR(Translit::class),
            new SR(Database::class),
            new SR(Request::class),
        ]
    ],
    BackendFeaturesValuesHelper::class => [
        'class' => BackendFeaturesValuesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(QueryFactory::class),
            new SR(Translit::class),
            new SR(Database::class),
            new SR(Request::class),
        ]
    ],
    BackendSpecialImagesHelper::class => [
        'class' => BackendSpecialImagesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BackendOrdersHelper::class => [
        'class' => BackendOrdersHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(MoneyHelper::class),
            new SR(Request::class),
            new SR(Settings::class),
            new SR(QueryFactory::class),
            new SR(DiscountsHelper::class),
        ]
    ],
    BackendOrderHistoryHelper::class => [
        'class' => BackendOrderHistoryHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(BackendOrdersRequest::class),
            new SR(Request::class),
            new SR(BackendTranslations::class),
            new SR(QueryFactory::class),
        ]
    ],
    BackendCategoriesHelper::class => [
        'class' => BackendCategoriesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Image::class),
            new SR(Config::class),
        ]
    ],
    BackendBlogCategoriesHelper::class => [
        'class' => BackendBlogCategoriesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Image::class),
            new SR(Config::class),
        ]
    ],
    BackendAuthorsHelper::class => [
        'class' => BackendAuthorsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Config::class),
            new SR(Image::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(Request::class),
        ]
    ],
    BackendBrandsHelper::class => [
        'class' => BackendBrandsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Config::class),
            new SR(Image::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(Request::class),
        ]
    ],
    BackendCategoryStatsHelper::class => [
        'class' => BackendCategoryStatsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ]
    ],
    BackendFeedbacksHelper::class => [
        'class' => BackendFeedbacksHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Settings::class),
        ]
    ],
    BackendNotifyHelper::class => [
        'class' => BackendNotifyHelper::class,
        'arguments' => [
            new SR(Notify::class),
        ]
    ],
    BackendPagesHelper::class => [
        'class' => BackendPagesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BackendOrderSettingsHelper::class => [
        'class' => BackendOrderSettingsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BackendCurrenciesHelper::class => [
        'class' => BackendCurrenciesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(QueryFactory::class),
            new SR(Database::class),
            new SR(Request::class),
        ]
    ],
    BackendManagersHelper::class => [
        'class' => BackendManagersHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BackendSettingsHelper::class => [
        'class' => BackendSettingsHelper::class,
        'arguments' => [
            new SR(Settings::class),
            new SR(Request::class),
            new SR(Config::class),
            new SR(EntityFactory::class),
            new SR(DataCleaner::class),
            new SR(Managers::class),
            new SR(FrontTemplateConfig::class),
            new SR(QueryFactory::class),
            new SR(Languages::class),
            new SR(JsSocial::class),
            new SR(Image::class),
        ]
    ],
    BackendValidateHelper::class => [
        'class' => BackendValidateHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Settings::class),
            new SR(Request::class),
            new SR(UrlUniqueValidator::class),
            new SR(Managers::class),
        ]
    ],
    BackendBlogHelper::class => [
        'class' => BackendBlogHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Config::class),
            new SR(Image::class),
            new SR(Settings::class),
            new SR(QueryFactory::class),
            new SR(ProductsHelper::class),
        ]
    ],
    BackendCallbacksHelper::class => [
        'class' => BackendCallbacksHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ]
    ],
    BackendCommentsHelper::class => [
        'class' => BackendCommentsHelper::class,
        'arguments' => [
            new SR(Request::class),
            new SR(EntityFactory::class),
            new SR(Settings::class),
            new SR(Notify::class),
        ]
    ],
    BackendCouponsHelper::class => [
        'class' => BackendCouponsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ]
    ],
    BackendDeliveriesHelper::class => [
        'class' => BackendDeliveriesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Config::class),
            new SR(Image::class),
        ]
    ],
    BackendPaymentsHelper::class => [
        'class' => BackendPaymentsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
            new SR(Config::class),
            new SR(Image::class),
        ]
    ],
    BackendUsersHelper::class => [
        'class' => BackendUsersHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ]
    ],
    BackendUserGroupsHelper::class => [
        'class' => BackendUserGroupsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Request::class),
        ]
    ],
    BackendMenuHelper::class => [
        'class' => BackendMenuHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BackendImportHelper::class => [
        'class' => BackendImportHelper::class,
        'arguments' => [
            new SR(Import::class),
            new SR(QueryFactory::class),
            new SR(Languages::class),
            new SR(EntityFactory::class),
            new SR(Image::class),
        ]
    ],
    BackendModulesHelper::class => [
        'class' => BackendModulesHelper::class,
        'arguments' => [
            new SR(Config::class),
        ]
    ],
    MainHelper::class => [
        'class' => MainHelper::class,
    ],
    DeliveriesHelper::class => [
        'class' => DeliveriesHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Module::class),
            new SR(LoggerInterface::class),
        ]
    ],
    PaymentsHelper::class => [
        'class' => PaymentsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Module::class),
            new SR(LoggerInterface::class),
        ]
    ],
    ValidateHelper::class => [
        'class' => ValidateHelper::class,
        'arguments' => [
            new SR(Validator::class),
            new SR(Settings::class),
            new SR(Request::class),
            new SR(FrontTranslations::class),
        ]
    ],
    CartHelper::class => [
        'class' => CartHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Money::class),
            new SR(FrontTemplateConfig::class),
            new SR(LoggerInterface::class),
            new SR(Design::class),
            new SR(CheckoutPaymentForm::class),
            new SR(Cart::class),
            new SR(Languages::class),
            new SR(DiscountsHelper::class),
        ]
    ],
    DiscountsHelper::class => [
        'class' => DiscountsHelper::class,
        'arguments' => [
            new SR(Discounts::class),
            new SR(FrontTranslations::class),
            new SR(Languages::class),
            new SR(EntityFactory::class),
            new SR(Settings::class),
        ]
    ],
    ProductsHelper::class => [
        'class' => ProductsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(MoneyHelper::class),
            new SR(Settings::class),
            new SR(MainHelper::class),
        ],
    ],
    CatalogHelper::class => [
        'class' => CatalogHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Money::class),
            new SR(Settings::class),
            new SR(Request::class),
        ],
    ],
    OrdersHelper::class => [
        'class' => OrdersHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(ProductsHelper::class),
            new SR(MoneyHelper::class),
            new SR(DiscountsHelper::class),
        ],
    ],
    FilterHelper::class => [
        'class' => FilterHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Settings::class),
            new SR(Languages::class),
            new SR(Request::class),
            new SR(Router::class),
            new SR(Design::class),
            new SR(Money::class),
        ],
    ],
    MoneyHelper::class => [
        'class' => MoneyHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ],
    ],
    CouponHelper::class => [
        'class' => CouponHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    CommonHelper::class => [
        'class' => CommonHelper::class,
        'arguments' => [
            new SR(ValidateHelper::class),
            new SR(Notify::class),
            new SR(Design::class),
            new SR(CommonRequest::class),
            new SR(EntityFactory::class),
            new SR(UserHelper::class),
        ]
    ],
    CommentsHelper::class => [
        'class' => CommentsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(CommonRequest::class),
            new SR(ValidateHelper::class),
            new SR(Design::class),
            new SR(Notify::class),
            new SR(MainHelper::class),
            new SR(Languages::class),
        ]
    ],
    RelatedProductsHelper::class => [
        'class' => RelatedProductsHelper::class,
        'arguments' => [
            new SR(ProductsHelper::class),
        ]
    ],
    BlogHelper::class => [
        'class' => BlogHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(AuthorsHelper::class),
        ]
    ],
    AuthorsHelper::class => [
        'class' => AuthorsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    BrandsHelper::class => [
        'class' => BrandsHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
        ]
    ],
    ResizeHelper::class => [
        'class' => ResizeHelper::class,
        'arguments' => [
            new SR(Image::class),
            new SR(Config::class),
        ]
    ],
    UserHelper::class => [
        'class' => UserHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Cart::class),
            new SR(WishList::class),
            new SR(Comparison::class),
            new SR(BrowsedProducts::class),
        ]
    ],
    SiteMapHelper::class => [
        'class' => SiteMapHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Response::class),
            new SR(MainHelper::class),
            new SR(Settings::class),
        ]
    ],
    ComparisonHelper::class => [
        'class' => ComparisonHelper::class,
        'arguments' => [
            new SR(EntityFactory::class),
            new SR(Design::class),
        ]
    ],
    ProductMetadataHelper::class => [
        'class' => ProductMetadataHelper::class,
    ],
    CommonMetadataHelper::class => [
        'class' => CommonMetadataHelper::class,
    ],
    CartMetadataHelper::class => [
        'class' => CartMetadataHelper::class,
    ],
    OrderMetadataHelper::class => [
        'class' => OrderMetadataHelper::class,
    ],
    CategoryMetadataHelper::class => [
        'class' => CategoryMetadataHelper::class,
    ],
    BrandMetadataHelper::class => [
        'class' => BrandMetadataHelper::class,
    ],
    PostMetadataHelper::class => [
        'class' => PostMetadataHelper::class,
    ],
    DiscountedMetadataHelper::class => [
        'class' => DiscountedMetadataHelper::class,
    ],
    BestsellersMetadataHelper::class => [
        'class' => BestsellersMetadataHelper::class,
    ],
    AllProductsMetadataHelper::class => [
        'class' => AllProductsMetadataHelper::class,
    ],
    BlogCategoryMetadataHelper::class => [
        'class' => BlogCategoryMetadataHelper::class,
    ],
    AuthorMetadataHelper::class => [
        'class' => AuthorMetadataHelper::class,
    ],
    XmlFeedHelper::class => [
        'class' => XmlFeedHelper::class,
        'arguments' => [
            new SR(Languages::class),
            new SR(Settings::class),
            new SR(EntityFactory::class),
        ]
    ],
    NotifyHelper::class => [
        'class' => NotifyHelper::class,
        'arguments' => [
        ]
    ],
    CanonicalHelper::class => [
        'class' => CanonicalHelper::class,
        'calls' => [
            [
                'method' => 'setParams',
                'arguments' => [
                    new PR('seo.canonical.catalog_pagination'),
                    new PR('seo.canonical.catalog_page_all'),
                    new PR('seo.canonical.category_brand'),
                    new PR('seo.canonical.category_features'),
                    new PR('seo.canonical.catalog_other_filter'),
                    new PR('seo.canonical.catalog_filter_pagination'),
                    new PR('seo.filter.max_brands_filter_depth'),
                    new PR('seo.filter.max_other_filter_depth'),
                    new PR('seo.filter.max_features_filter_depth'),
                    new PR('seo.filter.max_features_values_filter_depth'),
                    new PR('seo.filter.max_filter_depth'),
                ]
            ],
        ]
    ],
    MetaRobotsHelper::class => [
        'class' => MetaRobotsHelper::class,
        'calls' => [
            [
                'method' => 'setParams',
                'arguments' => [
                    new PR('seo.robots.catalog_pagination'),
                    new PR('seo.robots.catalog_page_all'),
                    new PR('seo.robots.category_brand'),
                    new PR('seo.robots.category_features'),
                    new PR('seo.robots.catalog_other_filter'),
                    new PR('seo.robots.catalog_filter_pagination'),
                    new PR('seo.filter.max_brands_filter_depth'),
                    new PR('seo.filter.max_other_filter_depth'),
                    new PR('seo.filter.max_features_filter_depth'),
                    new PR('seo.filter.max_features_values_filter_depth'),
                    new PR('seo.filter.max_filter_depth'),
                ]
            ],
        ]
    ],
];

