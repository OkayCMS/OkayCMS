{if $catalog_categories}
    <div class="sidebar__boxed hidden-md-down">
        <div class="filters filters_catalog">
            <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                <span data-language="features_catalog">{$lang->features_catalog}</span>
                <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
            </div>
            <div class="filter__group">
                {function name=categories_tree_sidebar}
                    {if $categories}
                        <div class="level_{$level} {if $level == 1}filter__catalog_menu {else}filter__subcatalog {/if}">
                            {foreach $categories as $c}
                                {if $c->visible && ($c->has_products || $settings->show_empty_categories)}
                                    <div class="filter__catalog_item has_child">
                                        <{if $c->id == $category->id && !$keyword}b{else}a{/if}
                                                class="filter__catalog_link{if $c->subcategories} sub_cat{/if}{if $category->id == $c->id} selected{/if}"
                                                {if $route_name === 'products'}
                                                    href="{url_generator route="category" url=$c->url filtersUrl=['brand' => $brand->url] keyword=$keyword}"
                                                {else}
                                                    href="{url_generator route="category" url=$c->url filtersUrl=['brand' => $brand->url]}"
                                                {/if}
                                                data-category="{$c->id}"
                                        >
                                            {if $c->image}
                                                <picture>
                                                    {if $settings->support_webp}
                                                        <source type="image/webp" data-srcset="{$c->image|resize:20:20:false:$config->resized_categories_dir}.webp">
                                                    {/if}
                                                    <source data-srcset="{$c->image|resize:20:20:false:$config->resized_categories_dir}">
                                                    <img class="lazy" data-src="{$c->image|resize:20:20:false:$config->resized_categories_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$c->name|escape}" title="{$c->name|escape}"/>
                                                </picture>
                                            {else}
                                                <div class="filter_catalog__no_image d-flex align-items-center justify-content-center" title="{$c->name|escape}">
                                                    {include file="svg.tpl" svgId="no_image"}
                                                </div>
                                            {/if}
                                            <span>{$c->name|escape}</span>
                                            {if $c->id != $category->id}
                                                {include file="svg.tpl" svgId="arrow_right2"}
                                            {/if}
                                        </{if $c->id == $category->id && !$keyword}b{else}a{/if}>
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                    {/if}
                {/function}
                {categories_tree_sidebar categories=$catalog_categories level=1}
            </div>
        </div>
    </div>
{/if}

{* Filters *}
{if ($catalog_brands || ($catalog_prices->min != $catalog_prices->max) || $catalog_features)}
    <div class="sidebar__boxed">
        <div class="filters">
            {* Ajax Price filter *}
            {if $catalog_prices->min != '' && $catalog_prices->max != '' && $catalog_prices->min != $catalog_prices->max}
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_price">{$lang->features_price}</span>
                    <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                </div>

                <div class="filter__group">
                    {* Price range *}
                    <div class="d-flex align-items-center justify-content-between price_range">
                        <div class="d-flex align-items-center price_label">
                            <input class="min_input" id="fn_slider_min" aria-label="{$catalog_prices->min}" name="p[min]" value="{($selected_catalog_prices['min']|default:$catalog_prices->min)|escape}" data-price="{$catalog_prices->min}" type="text">
                        </div>
                        <div class="separator">-</div>
                        <div class="d-flex align-items-center price_label max_price">
                            <input class="max_input" id="fn_slider_max" name="p[max]" aria-label="{$catalog_prices->max}" value="{($selected_catalog_prices['max']|default:$catalog_prices->max)|escape}" data-price="{$catalog_prices->max}" type="text">
                        </div>
                        <div class="price_currency">
                            <span>{$currency->sign|escape}</span>
                        </div>
                    </div>
                    {* Price slider *}
                    <div id="fn_slider_price" data-href="{furl params=[price=>['min'=>'min', 'max'=>'max'], page=>null, sort=>null, route=>$furlRoute]}"></div>
                </div>
            {/if}

            {* Other filters *}
            {if $catalog_other_filters}
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_other_filter">{$lang->features_other_filter}</span>
                    <span class="d-flex align-items-center filter__name_arrow icon fa fa-chevron-down"></span>
                </div>
                <div class="filter__group">
                    {* Display all brands *}
                    <div class="filter__item">
                        <form method="post">
                            {$furl = {furl params=[filter=>null, page=>null, route=>$furlRoute]}}
                            <button type="submit" name="prg_seo_hide" class="filter__link {if !$selected_catalog_other_filters} checked{/if}" value="{$furl|escape}">
                                <span class="filter__checkbox">
                                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                    </svg>
                                </span>
                                <span class="filter__label" data-language="features_all">{$lang->features_all}</span>
                            </button>
                        </form>
                    </div>
                    {* Other filter list *}
                    {foreach $catalog_other_filters as $f}
                        <div class="filter__item">
                            {$furl = {furl params=[filter=>$f->url, page=>null, route=>$furlRoute]}}
                            {if $seo_hide_filter || ($selected_catalog_other_filters && in_array($f->url, $selected_catalog_other_filters))}
                                <form method="post">
                                    <button type="submit" name="prg_seo_hide" class="filter__link{if $selected_catalog_other_filters && in_array($f->url, $selected_catalog_other_filters)} checked{/if}" value="{$furl|escape}">
                                        <span class="filter__checkbox">
                                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                            </svg>
                                        </span>
                                        <span class="filter__label" data-language="{$f->translation}">{$f->name|escape}</span>
                                    </button>
                                </form>
                            {else}
                                <a class="filter__link{if $selected_catalog_other_filters && in_array($f->url, $selected_catalog_other_filters)} checked{/if}" href="{$furl}">
                                    <span class="filter__checkbox">
                                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                        </svg>
                                    </span>
                                    <span class="filter__label" data-language="{$f->translation}">{$f->name|escape}</span>
                                </a>
                            {/if}
                        </div>
                    {/foreach}
                </div>
            {/if}

            {* Brand filter *}
            {if $catalog_brands}
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_manufacturer">{$lang->features_manufacturer}</span>
                    <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                </div>
                
                <div class="fn_view_content filter__group feature_content">
                    {* Display all brands *}
                    <div class="filter__item">
                        <form method="post">
                            {$furl = {furl params=[brand=>null, page=>null, route=>$furlRoute]}}
                            <button type="submit" name="prg_seo_hide" class="filter__link {if !$brand->id && !$selected_catalog_brands_ids} checked{/if}" value="{$furl|escape}">
                                <span class="filter__checkbox">
                                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                    </svg>
                                </span>
                                <span class="filter__label" data-language="features_all">{$lang->features_all}</span>
                            </button>
                        </form>
                    </div>
                    {* Brand list *}
                    {foreach $catalog_brands as $b}
                        {$b_count = $b_count+1}
                        <div class="filter__item {if $b && $b_count > 4} {if $brand->id == $b->id || $selected_catalog_brands_ids && in_array($b->id,$selected_catalog_brands_ids)}opened{else}closed{/if}{/if}">
                            {$furl = {furl params=[brand=>$b->url, page=>null, route=>$furlRoute]}}
                            {if $seo_hide_filter || ($brand->id == $b->id || $selected_catalog_brands_ids && in_array($b->id,$selected_catalog_brands_ids))}
                                <form method="post">
                                    <button type="submit" name="prg_seo_hide" class="filter__link{if $brand->id == $b->id || $selected_catalog_brands_ids && in_array($b->id,$selected_catalog_brands_ids)} checked{/if}" value="{$furl|escape}">
                                        <span class="filter__checkbox">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                            </svg>
                                        </span>
                                        <span class="filter__label">{$b->name|escape}</span>
                                    </button>
                                </form>
                            {else}
                                <a class="filter__link{if $brand->id == $b->id || $selected_catalog_brands_ids && in_array($b->id,$selected_catalog_brands_ids)} checked{/if}" href="{$furl}">
                                    <span class="filter__checkbox">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                        </svg>
                                    </span>
                                    <span class="filter__label">{$b->name|escape}</span>
                                </a>
                            {/if}
                        </div>
                    {/foreach}
                    {if $b_count > 4}
                        <div class="box_view_all_feature">
                            <a class="fn_view_all view_all_feature" href="">{$lang->filter_view_show|escape}</a>
                        </div>
                    {/if}
                </div>
            {/if}

            {* Features filter *}
            {if $catalog_features}
                {foreach $catalog_features as $key=>$f}
                    <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                        <span data-feature="{$f->id}">{$f->name|escape}</span>
                        <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                    </div>
                    <div class="fn_view_content filter__group feature_content">
                        {* Display all features *}
                        <div class="filter__item">
                            <form method="post">
                                {$furl = {furl params=[$f->url=>null, page=>null, route=>$furlRoute]}}
                                <button type="submit" name="prg_seo_hide" class="filter__link {if !isset($selected_catalog_features[$f->id])} checked{/if}" value="{$furl|escape}">
                                    <span class="filter__checkbox">
                                        <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                        </svg>
                                    </span>
                                    <span class="filter__label" data-language="features_all">{$lang->features_all}</span>
                                </button>
                            </form>
                        </div>
                        
                        {* Feture value *}
                        {$f_count = 0}
                        {foreach $f->features_values as $fv}
                            {$f_count = $f_count+1}
                            <div class="filter__item {if $fv && $f_count > 4} {if $selected_catalog_features[$f->id] && isset($selected_catalog_features[$f->id][$fv->id])}opened{else}closed{/if}{/if}">
                                {$furl = {furl params=[$f->url=>$fv->translit, page=>null, route=>$furlRoute]}}
                                {if !$fv->to_index || $seo_hide_filter || ($selected_catalog_features[$f->id] && isset($selected_catalog_features[$f->id][$fv->id]))}
                                    <form method="post">
                                        <button type="submit" name="prg_seo_hide" class="filter__link{if $selected_catalog_features[$f->id] && isset($selected_catalog_features[$f->id][$fv->id])} checked{/if}" value="{$furl|escape}">
                                            <span class="filter__checkbox">
                                                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                    <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                                </svg>
                                            </span>
                                            <span class="filter__label">{$fv->value|escape}</span>
                                        </button>
                                    </form>
                                {else}
                                    <a class="filter__link{if $smarty.get.{$f@key} && in_array($fv->translit,$smarty.get.{$f@key},true)} checked{/if}" href="{$furl}">
                                        <span class="filter__checkbox">
                                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                            </svg>
                                        </span>
                                        <span class="filter__label">{$fv->value|escape}</span>
                                    </a>
                                {/if}
                            </div>
                        {/foreach}
                        {if $f_count > 4}
                        <div class="box_view_all_feature">
                            <a class="fn_view_all view_all_feature" href="">{$lang->filter_view_show|escape}</a>
                        </div>
                    {/if}
                    </div>
                {/foreach}
            {/if}
        </div>
    </div>
{/if}
