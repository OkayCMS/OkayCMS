<?php


namespace Okay\Core;

use Okay\Admin\Requests\BackendAuthorsRequest;
use Okay\Admin\Requests\BackendBlogCategoriesRequest;
use Okay\Admin\Requests\BackendBlogRequest;
use Okay\Admin\Requests\BackendBrandsRequest;
use Okay\Admin\Requests\BackendCallbacksRequest;
use Okay\Admin\Requests\BackendCouponsRequest;
use Okay\Admin\Requests\BackendCurrenciesRequest;
use Okay\Admin\Requests\BackendDeliveriesRequest;
use Okay\Admin\Requests\BackendDiscountsRequest;
use Okay\Admin\Requests\BackendFeaturesRequest;
use Okay\Admin\Requests\BackendFeaturesValuesRequest;
use Okay\Admin\Requests\BackendFeedbacksRequest;
use Okay\Admin\Requests\BackendMenuRequest;
use Okay\Admin\Requests\BackendOrderSettingsRequest;
use Okay\Admin\Requests\BackendOrdersRequest;
use Okay\Admin\Requests\BackendCategoriesRequest;
use Okay\Admin\Requests\BackendCommentsRequest;
use Okay\Admin\Requests\BackendPagesRequest;
use Okay\Admin\Requests\BackendPaymentsRequest;
use Okay\Admin\Requests\BackendSettingsRequest;
use Okay\Admin\Requests\BackendUserGroupsRequest;
use Okay\Admin\Requests\BackendUsersRequest;
use Okay\Core\OkayContainer\Reference\ParameterReference as PR;
use Okay\Core\OkayContainer\Reference\ServiceReference as SR;
use Okay\Admin\Requests\BackendProductsRequest;
use Okay\Requests\CartRequest;
use Okay\Requests\CommonRequest;
use Okay\Requests\UserRequest;

return [
    BackendProductsRequest::class => [
        'class' => BackendProductsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendOrdersRequest::class => [
        'class' => BackendOrdersRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendCategoriesRequest::class => [
        'class' => BackendCategoriesRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendAuthorsRequest::class => [
        'class' => BackendAuthorsRequest::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Translit::class),
        ]
    ],
    BackendBlogCategoriesRequest::class => [
        'class' => BackendBlogCategoriesRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendBlogRequest::class => [
        'class' => BackendBlogRequest::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Translit::class),
        ]
    ],
    BackendBrandsRequest::class => [
        'class' => BackendBrandsRequest::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Translit::class),
        ]
    ],
    BackendSettingsRequest::class => [
        'class' => BackendSettingsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendCallbacksRequest::class => [
        'class' => BackendCallbacksRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendCommentsRequest::class => [
        'class' => BackendCommentsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendCurrenciesRequest::class => [
        'class' => BackendCurrenciesRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendCouponsRequest::class => [
        'class' => BackendCouponsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendDeliveriesRequest::class => [
        'class' => BackendDeliveriesRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendPaymentsRequest::class => [
        'class' => BackendPaymentsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendUsersRequest::class => [
        'class' => BackendUsersRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendUserGroupsRequest::class => [
        'class' => BackendUserGroupsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendFeaturesRequest::class => [
        'class' => BackendFeaturesRequest::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Translit::class),
        ]
    ],
    BackendOrderSettingsRequest::class => [
        'class' => BackendOrderSettingsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendFeedbacksRequest::class => [
        'class' => BackendFeedbacksRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendFeaturesValuesRequest::class => [
        'class' => BackendFeaturesValuesRequest::class,
        'arguments' => [
            new SR(Request::class),
            new SR(Translit::class),
        ]
    ],
    BackendPagesRequest::class => [
        'class' => BackendPagesRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendMenuRequest::class => [
        'class' => BackendMenuRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    BackendDiscountsRequest::class => [
        'class' => BackendDiscountsRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    CartRequest::class => [
        'class' => CartRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    CommonRequest::class => [
        'class' => CommonRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
    UserRequest::class => [
        'class' => UserRequest::class,
        'arguments' => [
            new SR(Request::class),
        ]
    ],
];
