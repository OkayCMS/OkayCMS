{* The list of the brands *}
{if $brands}
    <div class="brand f_row">
        {foreach $brands as $b}
            <div class="brand__item f_col-xs-6 f_col-sm-4 f_col-lg-3">
                <div class="brand__preview">
                    <a class="d-flex align-items-center justify-content-center brand__link" data-brand="{$b->id}" href="{url_generator route='brand' url=$b->url filtersUrl=$filtersUrl keyword=$keyword}">
                        {if $b->image}
                            <div class="brand__image">
                                <picture>
                                    {if $settings->support_webp}
                                        <source type="image/webp" data-srcset="{$b->image|resize:120:100:false:$config->resized_brands_dir}.webp">
                                    {/if}
                                    <source data-srcset="{$b->image|resize:120:100:false:$config->resized_brands_dir}">
                                    <img class="brand_img lazy" data-src="{$b->image|resize:120:100:false:$config->resized_brands_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$b->name|escape}" title="{$b->name|escape}"/>
                                </picture>
                            </div>
                        {else}
                            <div class="brand__name"><span>{$b->name|escape}</span></div>
                        {/if}
                    </a>
                </div>
            </div>
        {/foreach}
    </div>
{else}
    <div class="brand f_row">
        <div class="col-xs-12">
            <div class="boxed boxed--big boxed--notify ">
                <span data-language="products_not_found">{$lang->brands_not_found}</span>
            </div>
        </div>
    </div>
{/if}