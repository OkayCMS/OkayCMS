<!-- selected filters -->
{if $is_filter_page}
    <div class="sidebar__boxed">
        <div class="filter">
            <div class="fn_switch filter__name d-flex align-items-center justify-content-between">
                <span data-language="selected_features_heading">{$lang->selected_features_heading}</span>
                <span class="d-flex align-items-center filter__name_arrow icon icon-keyboard-arrow-down"></span>
            </div>
            <div class="filter__group">
                <div class="filter__selected_features d-flex align-items-center flex-wrap">
                    {if $selected_catalog_prices}
                        <div class="filter__selected_feature">
                            <form class="filter__selected_feature_item" method="post">
                                <button type="submit" name="prg_seo_hide" class="fn_filter_reset d-flex align-items-center filter__sf_link checked" value="{furl params=[price=>null, route=>$furlRoute]}">
                                    <span>{$lang->features_price}: <i>{$selected_catalog_prices['min']|escape} - {$selected_catalog_prices['max']|escape}</i></span>
                                    {include file="svg.tpl" svgId="remove_icon"}
                                </button>
                            </form>
                        </div>
                    {/if}

                    {* Other filters *}
                    {if $catalog_other_filters && $selected_catalog_other_filters}
                        {foreach $catalog_other_filters as $f}
                            {if in_array($f->url, $selected_catalog_other_filters)}
                                {$furl = {furl params=[filter=>$f->url, page=>null, route=>$furlRoute]}}
                                <div class="filter__selected_feature">
                                    <form class="filter__selected_feature_item" method="post">
                                        <button type="submit" name="prg_seo_hide" class="d-flex align-items-center filter__sf_link checked" value="{$furl|escape}">
                                            <span data-language="{$f->translation}">{$f->name}</span>
                                            {include file="svg.tpl" svgId="remove_icon"}
                                        </button>
                                    </form>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}

                    {* Brand filter *}
                    {if $catalog_brands && $selected_catalog_brands_ids}
                        {foreach $catalog_brands as $b}
                            {if $brand->id == $b->id || in_array($b->id, $selected_catalog_brands_ids)}
                                {$furl = {furl params=[brand=>$b->url, page=>null, route=>$furlRoute]}}
                                <div class="filter__selected_feature">
                                    <form class="filter__selected_feature_item" method="post">
                                        <button type="submit" name="prg_seo_hide" class="d-flex align-items-center filter__sf_link checked" value="{$furl|escape}">
                                            <span><i>{$b->name|escape}</i></span>
                                            {include file="svg.tpl" svgId="remove_icon"}
                                        </button>
                                    </form>
                                </div>
                            {/if}
                        {/foreach}
                    {/if}

                    {* Features filter *}
                    {if $catalog_features}
                        {foreach $catalog_features as $key=>$f}
                            {if $selected_catalog_features[$f->id]}
                                {foreach $f->features_values as $fv}
                                    {if isset($selected_catalog_features[$f->id][$fv->id])}
                                        {$furl = {furl params=[$f->url=>$fv->translit, page=>null, route=>$furlRoute]}}
                                        <div class="filter__selected_feature">
                                            <form class="filter__selected_feature_item" method="post">
                                                <button type="submit" name="prg_seo_hide" class="d-flex align-items-center filter__sf_link checked" value="{$furl|escape}">
                                                    <span>{$f->name|escape}: <i>{$fv->value|escape}</i></span>
                                                    {include file="svg.tpl" svgId="remove_icon"}
                                                </button>
                                            </form>
                                        </div>
                                    {/if}
                                {/foreach}
                            {/if}
                        {/foreach}
                    {/if}
                </div>

                {if $category}
                    <div class="filter__selected_feature_reset">
                        <form method="post">
                            <button type="submit" name="prg_seo_hide" class="fn_filter_reset  filter__sf_reset" value="{url_generator route="category" url=$category->url}">
                                {$lang->selected_features_reset}
                            </button>
                        </form>
                    </div>
                {elseif $brand}
                    <div class="filter__selected_feature_reset">
                        <form method="post">
                            <button type="submit" name="prg_seo_hide" class="fn_filter_reset filter__sf_reset" value="{url_generator route="brand" url=$brand->url}">
                                {$lang->selected_features_reset}
                            </button>
                        </form>
                    </div>
                {elseif $routeName = 'products'}
                    <div class="filter__selected_feature_reset">
                        <form method="post">
                            <button type="submit" name="prg_seo_hide" class="fn_filter_reset filter__sf_reset" value="{url_generator route="products"}">
                                {$lang->selected_features_reset}
                            </button>
                        </form>
                    </div>
                {/if}
            </div>
        </div>
    </div>
{/if}
