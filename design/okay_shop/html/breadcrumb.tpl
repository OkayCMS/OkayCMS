{* Breadcrumb navigation *}
{$level = 1}
{if $controller != "MainController"}
    <ol itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumbs d-flex flex-wrap align-items-center">
        {* The link to the homepage *}
        <li itemprop="itemListElement" itemscope
            itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
            <a itemprop="item" href="{url_generator route='main'}">
                <span itemprop="name" data-language="breadcrumb_home" title="{$lang->breadcrumb_home}">{$lang->breadcrumb_home}</span>
            </a>
            <meta itemprop="position" content="{$level++}" />
        </li>

        {* Categories page *}
        {if $controller == "CategoryController"}
            {if $category}
                {foreach from=$category->path item=cat}
                    {if !$cat@last}
                        {if $cat->visible}
                            <li itemprop="itemListElement" itemscope
                                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                                <a itemprop="item" href="{url_generator route='category' url=$cat->url}">
                                    <span itemprop="name">{$cat->name|escape}</span>
                                </a>
                                <meta itemprop="position" content="{$level++}" />
                            </li>
                        {/if}
                    {else}
                        <li itemprop="itemListElement" itemscope
                            itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                            <span itemprop="name">{$cat->name|escape}</span>
                            <meta itemprop="position" content="{$level++}" />
                        </li>
                    {/if}
                {/foreach}
            {/if}

        {* Products list page *}
        {elseif $controller == "ProductsController"}
            {if !empty($keyword)}
                <li itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                    <span itemprop="name" data-language="general_search">{$lang->general_search}</span>
                    <meta itemprop="position" content="{$level++}" />
                </li>
            {else}
                <li itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                    <span itemprop="name">{$h1|escape}</span>
                    <meta itemprop="position" content="{$level++}" />
                </li>
            {/if}
            
        {* Brand list page *}
        {elseif $controller == "BrandController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <a itemprop="item" href="{url_generator route='brands'}">
                    <span itemprop="name" data-language="breadcrumb_brands">{$lang->breadcrumb_brands}</span>
                </a>
                <meta itemprop="position" content="{$level++}" />
            </li>
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name">{$brand->name|escape}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Brand list page *}
        {elseif $controller == "BrandsController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name">{$page->name|escape}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Product page *}
        {elseif $controller == "ProductController"}
            {foreach from=$category->path item=cat}
                {if $cat->visible}
                    <li itemprop="itemListElement" itemscope
                        itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                        <a itemprop="item" href="{url_generator route='category' url=$cat->url}">
                            <span itemprop="name">{$cat->name|escape}</span>
                        </a>
                        <meta itemprop="position" content="{$level++}" />
                    </li>
                {/if}
            {/foreach}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name">{$product->name|escape}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Page *}
        {elseif $controller == "FeedbackController" || $controller == "PageController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name">{$page->name|escape}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Cart page *}
        {elseif $controller == "CartController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumb_cart">{$lang->breadcrumb_cart}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Order page *}
        {elseif $controller == "OrderController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name">{$lang->breadcrumb_order} {$order->id}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Password remind page *}
        {elseif $controller == "LoginController" && $smarty.get.action == "password_remind"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumbs_password_remind">{$lang->breadcrumbs_password_remind}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Login page *}
        {elseif $controller == "LoginController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumbs_enter">{$lang->breadcrumbs_enter}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Register page *}
        {elseif $controller == "RegisterController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumbs_registration">{$lang->breadcrumbs_registration}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* User account page *}
        {elseif $controller == "UserController"}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumbs_user">{$lang->breadcrumbs_user}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>

        {* Blog page *}
        {elseif $controller == "BlogController"}

            {if $category}
                {foreach from=$category->path item=cat}
                    {if !$cat@last || !empty($post)}
                        {if $cat->visible}
                            <li itemprop="itemListElement" itemscope
                                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                                <a itemprop="item" href="{url_generator route='blog_category' url=$cat->url}">
                                    <span itemprop="name">{$cat->name|escape}</span>
                                </a>
                                <meta itemprop="position" content="{$level++}" />
                            </li>
                        {/if}
                    {else}
                        <li itemprop="itemListElement" itemscope
                            itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                            <span itemprop="name">{$cat->name|escape}</span>
                            <meta itemprop="position" content="{$level++}" />
                        </li>
                    {/if}
                {/foreach}

                {if $post}
                    <li itemprop="itemListElement" itemscope
                        itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                        <span itemprop="name">{$post->name|escape}</span>
                        <meta itemprop="position" content="{$level++}" />
                    </li>
                {/if}
            {else}
                <li itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                    <span itemprop="name" data-language="breadcrumbs_blog">{$lang->breadcrumbs_blog}</span>
                    <meta itemprop="position" content="{$level++}" />
                </li>
            {/if}
        {elseif $controller == "AuthorsController"}
            {if $author}
                <li itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                    <a itemprop="item" href="{url_generator route='authors'}">
                        <span itemprop="name">{$lang->breadcrumbs_authors}</span>
                    </a>
                    <meta itemprop="position" content="{$level++}" />
                </li>
                <li itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                    <span itemprop="name">{$author->name|escape}</span>
                    <meta itemprop="position" content="{$level++}" />
                </li>
            {else}
                <li itemprop="itemListElement" itemscope
                    itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                    <span itemprop="name">{$lang->breadcrumbs_authors}</span>
                    <meta itemprop="position" content="{$level++}" />
                </li>
            {/if}
        {elseif $controller == 'ComparisonController'}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumb_comparison">{$lang->breadcrumb_comparison}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>
        {elseif $controller == 'WishListController'}
            <li itemprop="itemListElement" itemscope
                itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                <span itemprop="name" data-language="breadcrumb_wishlist">{$lang->breadcrumb_wishlist}</span>
                <meta itemprop="position" content="{$level++}" />
            </li>
        {elseif !empty($breadcrumbs) && is_array($breadcrumbs)}
            {foreach $breadcrumbs as $url => $name}
                {if !$name@last}
                    <li itemprop="itemListElement" itemscope
                        itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                        <a itemprop="item" href="{$url|escape}">
                            <span itemprop="name">{$name|escape}</span>
                        </a>
                        <meta itemprop="position" content="{$level++}" />
                    </li>
                {else}
                    <li itemprop="itemListElement" itemscope
                        itemtype="https://schema.org/ListItem" class="d-inline-flex align-items-center breadcrumbs__item">
                        <span itemprop="name">{$name|escape}</span>
                        <meta itemprop="position" content="{$level++}" />
                    </li>
                {/if}
            {/foreach}
        {/if}
    </ol>
{/if}
