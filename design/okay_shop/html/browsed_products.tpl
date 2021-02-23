{get_browsed_products var=browsed_products limit=6}

{if $browsed_products}
    <div class="sidebar__boxed browsed">
        <div class="fn_switch sidebar_heading d-flex align-items-center justify-content-between">
            <span data-language="features_browsed">{$lang->features_browsed}</span>
            <span class="d-flex align-items-center sidebar_heading_arrow icon fa fa-chevron-down"></span>
        </div>

        <div class="browsed__content f_row">
            {foreach $browsed_products as $browsed_product}
                <div class="browsed__item f_col-4">
                    <a class="d-flex align-items-center justify-content-center browsed__link" href="{url_generator route='product' url=$browsed_product->url}">
                        {if $browsed_product->image->filename}
                            <picture>
                                {if $settings->support_webp}
                                    <source type="image/webp" data-srcset="{$browsed_product->image->filename|resize:70:70}.webp">
                                {/if}
                                <source data-srcset="{$browsed_product->image->filename|resize:70:70}">
                                <img class="lazy" data-src="{$browsed_product->image->filename|resize:70:70}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$browsed_product->name|escape}" title="{$browsed_product->name|escape}"/>
                            </picture>
                        {else}
                            <div class="browsed__no_image d-flex align-items-center justify-content-center" title="{$browsed_product->name|escape}">
                                {include file="svg.tpl" svgId="no_image"}
                            </div>
                        {/if}
                    </a>
                </div>
            {/foreach}
        </div>
    </div>
{/if}