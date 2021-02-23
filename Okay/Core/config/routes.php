<?php

/**
 *
 * 'имя_роута' => [
 *     'slug' => 'урл_роута{$именованный_параметр}', - если в слаге роута имеется именованный параметр,  то его в последствии можно будет поймать в качестве  аргумента
 *                                                     метода класса контроллера. Имя именованного параметра и переменной, которая используется в аргументе контроллера
 *                                                     должны совпадать. Если же вам необходим необязательный именованный параметр в роуте, то вы можете описать его на
 *                                                     уровне регуляроного выражения в блоке "patterns"  и отлавливать  в  методе  контроллера при помощи переменной со
 *                                                     значением по-умолчанию. Реализацию необязательного параметра вы можете увидеть на примере роута "search" ниже
 *     'patterns' => [
 *         '{$именованный_параметр}' => 'регулярное_выражение_параметра' - указание паттернов не является обязательным. В случае если вы не указываете паттерн для конкретного
 *                                                                         параметра, то подставится регулярное выражение по-умолчанию следующего вида "([^/]+)"
 *     ],
 *     'params' => [
 *         'controller' => 'имя_используемого_класса_контроллера', - если имя класса указано без пространств имен, то будет использоваться пространство имен по-умолчанию
 *                                                                   следующего вида "\Okay\Controllers\"
 *         'method'     => 'имя_вызываемого_метода_контроллера',
 *     ],
 *     'defaults' => [
 *         '{$имя_параметра_по-умолчанию}' => 'значение_параметра_по-умолчанию', - в этом блоке указываются параметры, которые не попадают в слаг роута, но используются внутри метода класса
 *                                                                                 контроллера  для  различных  целей.  Данные  параметры  можно  отловить аналогично  именованному параметру,
 *                                                                                 который   используется   в  слаге,  а именно по  имени  параметра и переменной в  аргументе  метода класса
 *                                                                                 контроллера, которые должны совпадать
 *     ],
 *     'to_front' => true|false, - нужен ли будет этот роут как JS переменная на фронте. В JS его можно будет видеть как okay.router['<route_name>']
 *     'overwrite' => true|false, - может ли этот роут переопределять роут, расположенный выше, при совпадающих названиях
 *     'always_active' => true|false, - всегда активен. Когда установлено в true, даже при выключении сайта данный роут будет активен
 * ],
 *
 */

use Okay\Core\Routes\ProductRoute;
use Okay\Core\Routes\CategoryRoute;
use Okay\Core\Routes\BrandRoute;
use Okay\Core\Routes\BlogCategoryRoute;
use Okay\Core\Routes\AllBlogRoute;
use Okay\Core\Routes\AllBrandsRoute;
use Okay\Core\Routes\PageRoute;
use Okay\Core\Routes\PostRoute;

$productRouteParams   = (new ProductRoute())->generateRouteParams();
$categoryRouteParams  = (new CategoryRoute())->generateRouteParams();
$brandRouteParams     = (new BrandRoute())->generateRouteParams();
$blogCategoryRouteParams = (new BlogCategoryRoute())->generateRouteParams();
$postRouteParams      = (new PostRoute())->generateRouteParams();
$allBlogRouteParams   = (new AllBlogRoute())->generateRouteParams();
$allBrandsRouteParams = (new AllBrandsRoute())->generateRouteParams();
$pageRouteParams      = (new PageRoute())->generateRouteParams();

return [
    'main' => [
        'slug' => '/',
        'params' => [
            'controller' => 'MainController',
            'method' => 'render',
        ],
    ],
    'contact' => [
        'slug' => '/contact',
        'params' => [
            'controller' => 'FeedbackController',
            'method' => 'render',
        ],
    ],
    'cart' => [
        'slug' => '/cart',
        'params' => [
            'controller' => 'CartController',
            'method' => 'render',
        ],
    ],
    'cart_remove_item' => [
        'slug' => '/cart/remove/{$variantId}',
        'params' => [
            'controller' => 'CartController',
            'method' => 'removeItem',
        ],
        'patterns' => [
            '{$variantId}' => '([0-9]+)',
        ],
    ],
    'cart_add_item' => [
        'slug' => '/cart/{$variantId}',
        'params' => [
            'controller' => 'CartController',
            'method' => 'addItem',
        ],
        'patterns' => [
            '{$variantId}' => '([0-9]+)',
        ],
    ],
    'cart_ajax' => [
        'slug' => '/ajax/cart_ajax.php',
        'params' => [
            'controller' => 'CartController',
            'method' => 'cartAjax',
        ],
        'to_front' => true,
    ],
    'ajax_subscribe' => [
        'slug' => '/ajax/subscribe',
        'params' => [
            'controller' => 'SubscribeController',
            'method' => 'ajaxSubscribe',
        ],
        'to_front' => true,
    ],
    'wishlist' => [
        'slug' => '/wishlist',
        'params' => [
            'controller' => 'WishListController',
            'method' => 'render',
        ],
    ],
    'wishlist_ajax' => [
        'slug' => '/ajax/wishlist.php',
        'params' => [
            'controller' => 'WishListController',
            'method' => 'ajaxUpdate',
        ],
        'to_front' => true,
    ],
    'comparison' => [
        'slug' => 'comparison',
        'params' => [
            'controller' => 'ComparisonController',
            'method' => 'render',
        ],
    ],
    'comparison_ajax' => [
        'slug' => '/ajax/comparison.php',
        'params' => [
            'controller' => 'ComparisonController',
            'method' => 'ajaxUpdate',
        ],
        'to_front' => true,
    ],
    'ajax_product_rating' => [
        'slug' => '/ajax/rating',
        'params' => [
            'controller' => 'ProductController',
            'method' => 'rating',
        ],
    ],
    'ajax_post_rating' => [
        'slug' => '/ajax/post_rating',
        'params' => [
            'controller' => 'BlogController',
            'method' => 'rating',
        ],
    ],
    'search' => [
        'slug' => '/all-products{$filtersUrl}',
        'patterns' => [
            '{$filtersUrl}' => '/?(.*)',
        ],
        'params' => [
            'controller' => 'ProductsController',
            'method' => 'render',
        ],
    ],
    'ajax_search' => [
        'slug' => '/ajax/search_products',
        'params' => [
            'controller' => 'ProductsController',
            'method' => 'ajaxSearch',
        ],
        'to_front' => true,
    ],
    'discounted' => [
        'slug' => '/discounted{$filtersUrl}',
        'patterns' => [
            '{$filtersUrl}' => '/?(.*)',
        ],
        'params' => [
            'controller' => 'ProductsController',
            'method' => 'render',
        ],
    ],
    'bestsellers' => [
        'slug' => '/bestsellers{$filtersUrl}',
        'patterns' => [
            '{$filtersUrl}' => '/?(.*)',
        ],
        'params' => [
            'controller' => 'ProductsController',
            'method' => 'render',
        ],
    ],
    'order' => [
        'slug' => 'order/{$url}',
        'params' => [
            'controller' => 'OrderController',
            'method' => 'render',
        ],
    ],
    'sitemap' => [
        'slug' => 'sitemap.xml',
        'params' => [
            'controller' => 'SiteMapController',
            'method' => 'renderXml',
        ],
    ],
    'opensearch' => [
        'slug' => 'opensearch.xml',
        'params' => [
            'controller' => 'OpenSearchController',
            'method' => 'renderXml',
        ],
    ],
    'opensearch_ajax' => [
        'slug' => 'ajax/opensearch',
        'params' => [
            'controller' => 'OpenSearchController',
            'method' => 'liveSearch',
        ],
    ],
    'user' => [
        'slug' => 'user',
        'params' => [
            'controller' => 'UserController',
            'method' => 'render',
        ],
    ],
    'user_orders' => [
        'slug' => 'user/orders',
        'params' => [
            'controller' => 'UserController',
            'method' => 'render',
        ],
    ],
    'user_comments' => [
        'slug' => 'user/comments',
        'params' => [
            'controller' => 'UserController',
            'method' => 'render',
        ],
    ],
    'user_favorites' => [
        'slug' => 'user/favorites',
        'params' => [
            'controller' => 'UserController',
            'method' => 'render',
        ],
    ],
    'user_browsed' => [
        'slug' => 'user/browsed',
        'params' => [
            'controller' => 'UserController',
            'method' => 'render',
        ],
    ],
    'well_known_change_password' => [
        'slug' => '.well-known/change-password',
        'params' => [
            'controller' => 'UserController',
            'method' => 'wellKnownChangePassword',
        ],
    ],
    'login' => [
        'slug' => '/user/login',
        'params' => [
            'controller' => 'UserController',
            'method' => 'login',
        ],
    ],
    'register' => [
        'slug' => 'user/register',
        'params' => [
            'controller' => 'UserController',
            'method' => 'register',
        ],
    ],
    'password_remind' => [
        'slug' => 'user/password_remind{$code}',
        'params' => [
            'controller' => 'UserController',
            'method' => 'passwordRemind',
        ],
        'patterns' => [
            '{$code}' => '([0-9a-z]+)?',
        ],
    ],
    'logout' => [
        'slug' => 'user/logout',
        'params' => [
            'controller' => 'UserController',
            'method' => 'logout',
        ],
    ],
    'resize' => [
        'slug' => 'files/resized/{$object}/{$filename}',
        'patterns' => [
            '{$object}' => '(.+)',
            '{$filename}' => '(.+)',
        ],
        'params' => [
            'controller' => 'ResizeController',
            'method' => 'resize',
        ],
    ],
    'dynamic_js' => [
        'slug' => 'dynamic_js/{$fileId}.js',
        'params' => [
            'controller' => 'DynamicJsController',
            'method' => 'getJs',
        ],
    ],
    'common_js' => [
        'slug' => 'common_js/{$fileId}.js',
        'params' => [
            'controller' => 'DynamicJsController',
            'method' => 'getCommonJs',
        ],
    ],
    'support' => [
        'slug' => '/support.php',
        'params' => [
            'controller' => 'SupportController',
            'method' => 'checkDomain',
        ],
    ],
    'brands' => [
        'slug' => $allBrandsRouteParams->getSlug(),
        'patterns' => $allBrandsRouteParams->getPatterns(),
        'params' => [
            'controller' => 'BrandsController',
            'method' => 'render',
        ],
        'defaults' => $allBrandsRouteParams->getDefaults(),
    ],
    'product' => [
        'slug' => $productRouteParams->getSlug(),
        'patterns' => $productRouteParams->getPatterns(),
        'params' => [
            'controller' => 'ProductController',
            'method' => 'render',
        ],
        'defaults' => $productRouteParams->getDefaults(),
    ],
    'category_features' => [
        'slug' => 'category_features/' . $categoryRouteParams->getSlug(),
        'patterns' => $categoryRouteParams->getPatterns(),
        'params' => [
            'controller' => 'CategoryController',
            'method' => 'getFilter',
        ],
        'defaults' => $categoryRouteParams->getDefaults()
    ],
    'category' => [
        'slug' => $categoryRouteParams->getSlug(),
        'patterns' => $categoryRouteParams->getPatterns(),
        'params' => [
            'controller' => 'CategoryController',
            'method' => 'render',
        ],
        'defaults' => $categoryRouteParams->getDefaults()
    ],
    'brand' => [
        'slug' => $brandRouteParams->getSlug(),
        'patterns' => $brandRouteParams->getPatterns(),
        'params' => [
            'controller' => 'BrandController',
            'method' => 'render',
        ],
        'defaults' => $brandRouteParams->getDefaults()
    ],
    'author' => [
        'slug' => 'authors/{$url}',
        'params' => [
            'controller' => 'AuthorsController',
            'method' => 'render',
        ],
    ],
    'authors' => [
        'slug' => 'authors',
        'params' => [
            'controller' => 'AuthorsController',
            'method' => 'authorsList',
        ],
    ],
    'blog_category' => [ // Блог с фильтром по категории
        'slug' => $blogCategoryRouteParams->getSlug(),
        'patterns' => $blogCategoryRouteParams->getPatterns(),
        'params' => [
            'controller' => 'BlogController',
            'method' => 'fetchBlog',
        ],
        'defaults' => $blogCategoryRouteParams->getDefaults()
    ],
    'blog' => [ // общий раздел блога, без выбранной категории
        'slug' => $allBlogRouteParams->getSlug(),
        'patterns' => $allBlogRouteParams->getPatterns(),
        'params' => [
            'controller' => 'BlogController',
            'method' => 'fetchBlog',
        ],
        'defaults' => $allBlogRouteParams->getDefaults(),
    ],
    'post' => [
        'slug' => $postRouteParams->getSlug(),
        'patterns' => $postRouteParams->getPatterns(),
        'params' => [
            'controller' => 'BlogController',
            'method' => 'fetchPost',
        ],
        'defaults' => $postRouteParams->getDefaults()
    ],
    'page' => [
        'slug' => $pageRouteParams->getSlug(),
        'patterns' => $pageRouteParams->getPatterns(),
        'params' => [
            'controller' => 'PageController',
            'method' => 'render',
        ],
        'defaults' => $pageRouteParams->getDefaults(),
    ],
];