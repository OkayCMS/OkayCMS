{* The blog sidebar template *}

{* Mobile close menu *}
<div class="fn_mobile_toogle sidebar__header sidebar__boxed hidden-lg-up">
    <div class="fn_switch_mobile_filter sidebar__header--close">
        {include file="svg.tpl" svgId="remove_icon"}
        <span data-language="mobile_filter_close">{$lang->mobile_filter_close}</span>
    </div>
</div>

{* blog category *}
<div class="sidebar__boxed">
    <div class="fn_switch sidebar_heading d-flex align-items-center justify-content-between">
        <span data-language="blog_catalog">{$lang->blog_catalog}</span>
        <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
    </div>
    <nav class="blog_catalog">
        {function name=categories_article}
            {if $categories}
                <ul class="blog_catalog__list level_{$level}{if $level > 1} blog_catalog__list--inner{/if}">
                    {foreach $categories as $c}
                        {if $c->visible && ($c->has_posts || $settings->show_empty_categories)}
                            {if $c->subcategories && $c->count_children_visible && $level < 3}
                                <li class="blog_catalog__item parent">
                                    <a class="blog_catalog__link {if $category->id == $c->id} selected{/if}" href="{url_generator route='blog_category' url=$c->url}" data-blog_category="{$c->id}">
                                        {if $c->image}
                                            <picture>
                                                {if $settings->support_webp}
                                                    <source type="image/webp" data-srcset="{$c->image|resize:20:20:false:$config->resized_blog_categories_dir}.webp">
                                                {/if}
                                                <source data-srcset="{$c->image|resize:20:20:false:$config->resized_blog_categories_dir}">
                                                <img class="lazy" data-src="{$c->image|resize:20:20:false:$config->resized_blog_categories_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$c->name|escape}" title="{$c->name|escape}"/>
                                            </picture>
                                        {else}
                                            <span class="blog_catalog__no_image d-flex align-items-center justify-content-center" title="{$c->name|escape}">
                                                {include file="svg.tpl" svgId="no_image"}
                                            </span>
                                        {/if}
                                        <span class="blog_catalog__name">{$c->name|escape}</span>
                                    </a>
                                    <span class="fn_switch blog_catalog__switch">{include file='svg.tpl' svgId='arrow_right3'}</span>
                                    {categories_article categories=$c->subcategories level=$level + 1}
                                </li>
                            {else}
                                <li class="blog_catalog__item">
                                    <a class="blog_catalog__link {if $category->id == $c->id} selected{/if}" href="{url_generator route='blog_category' url=$c->url}" data-blog_category="{$c->id}">
                                        {if $c->image}
                                        <picture>
                                            {if $settings->support_webp}
                                                <source type="image/webp" data-srcset="{$c->image|resize:20:20:false:$config->resized_blog_categories_dir}.webp">
                                            {/if}
                                            <source data-srcset="{$c->image|resize:20:20:false:$config->resized_blog_categories_dir}">
                                            <img class="lazy" data-src="{$c->image|resize:20:20:false:$config->resized_blog_categories_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$c->name|escape}" title="{$c->name|escape}"/>
                                        </picture>
                                        {else}
                                            <span class="blog_catalog__no_image d-flex align-items-center justify-content-center" title="{$c->name|escape}">
                                                {include file="svg.tpl" svgId="no_image"}
                                            </span>
                                        {/if}
                                        <span class="blog_catalog__name">{$c->name|escape}</span>
                                    </a>
                                </li>
                            {/if}
                        {/if}
                    {/foreach}
                </ul>
            {/if}
        {/function}
        {categories_article categories=$blog_categories level=1}
    </nav>
</div>

{* Subscribing *}
<div class="sidebar__boxed sidebar__boxed--subscribe hidden-md-down">
    <div class="sidebar_subscribe">
        <div class="sidebar_subscribe__title">
            <span data-language="subscribe_promotext_post">{$lang->subscribe_promotext_post}</span>
        </div>
        <form class="sidebar_subscribe__form fn_validate_subscribe" method="post">
            <div class="sidebar_subscribe__group">
                <input type="hidden" name="subscribe" value="1"/>
                <input class="form__input form__input--aside_subscribe" aria-label="subscribe" type="email" name="subscribe_email" value="" data-format="email" placeholder="{$lang->form_email}"/>
            </div>
            <button class="button button--basic button--aside_subscribe" type="submit" title="{$lang->subscribe_button}">{include file='svg.tpl' svgId='subscribe_image'}</button>
        </form>
    </div>
</div>

{if $controller != "AuthorsController" && !$post}
    {* Featured products *}
    {get_featured_products var=featured_products limit=3}
    {if $featured_products}
    <div class="sidebar__boxed">
        <div class="fn_switch sidebar_heading d-flex align-items-center justify-content-between">
            <span data-language="main_recommended_products">{$lang->main_recommended_products}</span>
            <span class="d-flex align-items-center sidebar_heading_arrow icon fa fa-chevron-down"></span>
        </div>
        <div class="sidebar_card f_row">
            {foreach $featured_products as $product}
            <div class="sidebar_card__item f_col-12">
                <a class="d-flex align-items-center justify-content-center sidebar_card__link" href="{url_generator route='product' url=$product->url}">
                    <div class="sidebar_card__image">
                        {if $product->image->filename}
                        <picture>
                            {if $settings->support_webp}
                                <source type="image/webp" data-srcset="{$product->image->filename|resize:60:60}.webp">
                            {/if}
                            <source data-srcset="{$product->image->filename|resize:60:60}">
                            <img class="lazy" data-src="{$product->image->filename|resize:60:60}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$product->name|escape}" title="{$product->name|escape}"/>
                        </picture>
                        {else}
                        <div class="sidebar_card__no_image d-flex align-items-center justify-content-center" title="{$product->name|escape}">
                            {include file="svg.tpl" svgId="no_image"}
                        </div>
                        {/if}
                    </div>
                    <div class="sidebar_card__content">
                        <div class="sidebar_card__title">{$product->name|escape}</div>
                        <div class="sidebar_card__prices">
                            <div class="d-flex align-items-center">
                                <div class="old_price {if !$product->variant->compare_price} hidden-xs-up{/if}">
                                    <span class="fn_old_price">{$product->variant->compare_price|convert}</span>
                                </div>
                                <div class="price {if $product->variant->compare_price} price--red{/if}">
                                    <span class="fn_price">{$product->variant->price|convert}</span> <span class="currency">{$currency->sign|escape}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            {/foreach}
            <a class="sidebar_card__more d-flex align-items-center f_col-12" href="{url_generator route='bestsellers'}">
                <span data-language="main_look_all">{$lang->main_look_all}</span>{include file="svg.tpl" svgId="arrow_right2"}
            </a>
        </div>
    </div>
    {/if}
{/if}