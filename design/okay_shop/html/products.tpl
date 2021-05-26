{* The Categories page *}

<div class="clearfix">
    {* Sidebar with filters *}
    <div class="fn_mobile_toogle sidebar d-lg-flex flex-lg-column">
        <div class="fn_mobile_toogle sidebar__header sidebar__boxed hidden-lg-up">
            <div class="fn_switch_mobile_filter sidebar__header--close">
                {include file="svg.tpl" svgId="remove_icon"}
                <span data-language="mobile_filter_close">{$lang->mobile_filter_close}</span>
            </div>
            {if $category}
                <div class="sidebar__header--reset">
                    <form method="post">
                        <button type="submit" name="prg_seo_hide" class="fn_filter_reset mobile_filter__reset" value="{url_generator route="category" url=$category->url absolute=1}">
                            {include file="svg.tpl" svgId="reset_icon"}
                            <span>{$lang->mobile_filter_reset}</span>
                        </button>
                    </form>
                </div>
            {elseif $brand}
                <div class="sidebar__header--reset">
                    <form method="post">
                        <button type="submit" name="prg_seo_hide" class="fn_filter_reset mobile_filter__reset" value="{url_generator route="brand" url=$brand->url absolute=1}">
                            {include file="svg.tpl" svgId="reset_icon"}
                            <span>{$lang->mobile_filter_reset}</span>
                        </button>
                    </form>
                </div>
            {/if}
        </div>

        <div class="fn_selected_features">
            {if !($controller == 'CategoryController' && $settings->deferred_load_features)}
                {include file='selected_features.tpl'}
            {/if}
        </div>

        <div class="fn_features">
            {if !($controller == 'CategoryController' && $settings->deferred_load_features)}
                {include file='features.tpl'}
            {else}
                {* Deferred load features *}
                <div class='fn_skeleton_load'>
                    {section name=foo start=1 loop=7 step=1}
                        <div class='skeleton_load__item skeleton_load__item--{$smarty.section.foo.index}'></div>
                    {/section}
                </div>
            {/if}
        </div>

        {* Browsed products *}
        <div class="browsed products">
            {include file='browsed_products.tpl'}
        </div>
    </div>

    <div class="products_container d-flex flex-column">
        <div class="products_container__boxed">
            <h1 class="h1"{if $category} data-category="{$category->id}"{/if}{if $brand} data-brand="{$brand->id}"{/if}>{$h1|escape}</h1>

            {if !empty($annotation)}
                <div class="boxed boxed--big">
                    <div class="">
                        <div class="fn_readmore">
                            <div class="block__description">
                                {$annotation}
                            </div>
                        </div>
                    </div>
                </div>
            {/if}

            {if $products}
                <div class="products_container__sort d-flex align-items-center justify-content-between">
                    {* Product Sorting *}
                    <div class="fn_products_sort">
                        {include file="products_sort.tpl"}
                    </div>
                    {* Mobile button filters *}
                    <div class="fn_switch_mobile_filter switch_mobile_filter hidden-lg-up">
                        {include file="svg.tpl" svgId="filter_icon"}
                        <span data-language="filters">{$lang->filters}</span>
                    </div>
                </div>
            {/if}

            {* Product list *}
            <div id="fn_products_content" class="fn_categories products_list row">
                {include file="products_content.tpl"}
            </div>

            {if $products}
                {* Friendly URLs Pagination *}
                <div class="fn_pagination products_pagination">
                    {include file='chpu_pagination.tpl'}
                </div>
            {/if}

            {if $description}
                <div class="boxed boxed--big">
                    <div class="">
                        <div class="fn_readmore">
                            <div class="block__description">{$description}</div>
                        </div>
                    </div>
                </div>
            {/if}
        </div>
    </div>
</div>
