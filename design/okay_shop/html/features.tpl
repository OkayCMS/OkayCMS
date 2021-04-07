{if ($category->subcategories && $category->count_children_visible) ||
    ($category->path[$category->level_depth-2]->subcategories && $category->path[$category->level_depth-2]->count_children_visible) || $brand->categories}
        <div class="sidebar__boxed hidden-md-down">
        {if ($category->subcategories && $category->count_children_visible) ||
        ($category->path[$category->level_depth-2]->subcategories && $category->path[$category->level_depth-2]->count_children_visible)}
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
                                            <{if $c->id == $category->id}b{else}a{/if} class="filter__catalog_link{if $c->subcategories} sub_cat{/if}{if $category->id == $c->id} selected{/if}" href="{url_generator route="category" url=$c->url}" data-category="{$c->id}">
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
                                            </{if $c->id == $category->id}b{else}a{/if}>
                                        </div>
                                    {/if}
                                {/foreach}
                            </div>
                        {/if}
                    {/function}
                    {if $category->subcategories && $category->count_children_visible}
                        {categories_tree_sidebar categories=$category->subcategories level=1}
                    {elseif $category->path[$category->level_depth-2]->subcategories && $category->path[$category->level_depth-2]->count_children_visible}
                        {categories_tree_sidebar categories=$category->path[$category->level_depth-2]->subcategories level=1}
                    {/if}
                </div>
            </div>
        {/if}

        {if $brand->categories}
            <div class="filters filters_catalog">
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_catalog">{$lang->features_catalog}</span>
                    <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                </div>

                <div class="filter__group">
                    <div class="level_1 filter__catalog_menu">
                        {foreach $brand->categories as $c}
                            <div class="filter__catalog_item has_child">
                                <a class="filter__catalog_link" href="{url_generator route='category' url=$c->url filtersUrl='/brand-'|cat:$brand->url}" data-category="{$c->id}">
                                    <span>{$c->name|escape}</span>
                                    {include file="svg.tpl" svgId="arrow_right2"}
                                </a>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        {/if}
    </div>
 {/if}

{* Filters *}
{if ($category->brands || ($prices->range->min != '' && $prices->range->max != '') || $features)}
    <div class="sidebar__boxed">
        <div class="filters">
            {* Ajax Price filter *}
            {if $prices->range->min != '' && $prices->range->max != '' && $prices->range->min != $prices->range->max}
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_price">{$lang->features_price}</span>
                    <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                </div>

                <div class="filter__group">
                    {* Price range *}
                    <div class="d-flex align-items-center justify-content-between price_range">
                        <div class="d-flex align-items-center price_label">
                            <input class="min_input" id="fn_slider_min" aria-label="{$prices->range->min}" name="p[min]" value="{($prices->current->min|default:$prices->range->min)|escape}" data-price="{$prices->range->min}" type="text">
                        </div>
                        <div class="separator">-</div>
                        <div class="d-flex align-items-center price_label max_price">
                            <input class="max_input" id="fn_slider_max" name="p[max]" aria-label="{$prices->range->max}" value="{($prices->current->max|default:$prices->range->max)|escape}" data-price="{$prices->range->max}" type="text">
                        </div>
                        <div class="price_currency">
                            <span>{$currency->sign|escape}</span>
                        </div>
                    </div>
                    {* Price slider *}
                    <div id="fn_slider_price"></div>
                </div>
            {/if}

            {* Other filters *}
            {if $other_filters}
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_other_filter">{$lang->features_other_filter}</span>
                    <span class="d-flex align-items-center filter__name_arrow icon fa fa-chevron-down"></span>
                </div>
                <div class="filter__group">
                    {* Display all brands *}
                    <div class="filter__item">
                        <form method="post">
                            {$furl = {furl params=[filter=>null, page=>null, route=>$furlRoute]}}
                            <button type="submit" name="prg_seo_hide" class="filter__link {if !$selected_other_filters} checked{/if}" value="{$furl|escape}">
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
                    {foreach $other_filters as $f}
                        <div class="filter__item">
                            {$furl = {furl params=[filter=>$f->url, page=>null, route=>$furlRoute]}}
                            {if $seo_hide_filter || ($selected_other_filters && in_array($f->url, $selected_other_filters))}
                                <form method="post">
                                    <button type="submit" name="prg_seo_hide" class="filter__link{if $selected_other_filters && in_array($f->url, $selected_other_filters)} checked{/if}" value="{$furl|escape}">
                                        <span class="filter__checkbox">
                                            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                            </svg>
                                        </span>
                                        <span class="filter__label" data-language="{$f->translation}">{$f->name|escape}</span>
                                    </button>
                                </form>
                            {else}
                                <a class="filter__link{if $selected_other_filters && in_array($f->url, $selected_other_filters)} checked{/if}" href="{$furl}">
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
            {if $category->brands}
                <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                    <span data-language="features_manufacturer">{$lang->features_manufacturer}</span>
                    <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                </div>
                
                <div class="fn_view_content filter__group feature_content">
                    {* Display all brands *}
                    <div class="filter__item">
                        <form method="post">
                            {$furl = {furl params=[brand=>null, page=>null, route=>$furlRoute]}}
                            <button type="submit" name="prg_seo_hide" class="filter__link {if !$brand->id && !$selected_brands_ids} checked{/if}" value="{$furl|escape}">
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
                    {foreach $category->brands as $b}
                        {$b_count = $b_count+1}
                        <div class="filter__item {if $b && $b_count > 4} {if $brand->id == $b->id || $selected_brands_ids && in_array($b->id,$selected_brands_ids)}opened{else}closed{/if}{/if}">
                            {$furl = {furl params=[brand=>$b->url, page=>null, route=>$furlRoute]}}
                            {if $seo_hide_filter || ($brand->id == $b->id || $selected_brands_ids && in_array($b->id,$selected_brands_ids))}
                                <form method="post">
                                    <button type="submit" name="prg_seo_hide" class="filter__link{if $brand->id == $b->id || $selected_brands_ids && in_array($b->id,$selected_brands_ids)} checked{/if}" value="{$furl|escape}">
                                        <span class="filter__checkbox">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                                <path class="checkmark-path" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                            </svg>
                                        </span>
                                        <span class="filter__label">{$b->name|escape}</span>
                                    </button>
                                </form>
                            {else}
                                <a class="filter__link{if $brand->id == $b->id || $selected_brands_ids && in_array($b->id,$selected_brands_ids)} checked{/if}" href="{$furl}">
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
            {if $features}
                {foreach $features as $key=>$f}
                    <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                        <span data-feature="{$f->id}">{$f->name|escape}</span>
                        <span class="d-flex align-items-center filter__name_arrow fa fa-chevron-down"></span>
                    </div>
                    <div class="fn_view_content filter__group feature_content">
                        {* Display all features *}
                        <div class="filter__item">
                            <form method="post">
                                {$furl = {furl params=[$f->url=>null, page=>null, route=>$furlRoute]}}
                                <button type="submit" name="prg_seo_hide" class="filter__link {if !isset($selected_filters[$f->id])} checked{/if}" value="{$furl|escape}">
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
                            <div class="filter__item {if $fv && $f_count > 4} {if $selected_filters[$f->id] && isset($selected_filters[$f->id][$fv->id])}opened{else}closed{/if}{/if}"> 
                                {$furl = {furl params=[$f->url=>$fv->translit, page=>null, route=>$furlRoute]}}
                                {if !$fv->to_index || $seo_hide_filter || ($selected_filters[$f->id] && isset($selected_filters[$f->id][$fv->id]))}
                                    <form method="post">
                                        <button type="submit" name="prg_seo_hide" class="filter__link{if $selected_filters[$f->id] && isset($selected_filters[$f->id][$fv->id])} checked{/if}" value="{$furl|escape}">
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
