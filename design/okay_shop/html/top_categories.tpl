{assign var=has_main_categories value = false scope="global"}
{function name=categories_has_main}
    {if $categories}
        {foreach $categories as $c}
            {if $c->visible &&  ($c->has_products || $settings->show_empty_categories)}
                {if $c->on_main}
                    {assign var=has_main_categories value = true scope="global"}
                    {break}
                {/if}
                {categories_has_main categories=$c->subcategories level=$level + 1}
            {/if}
            {if $has_main_categories}
                {break}
            {/if}
        {/foreach}
    {/if}
{/function}
{categories_has_main categories=$categories level=1}

{if $has_main_categories}
<section class="container section_top_categories"> 
    <div class="block block--boxed block--border">
        <div class="block__header block__header--promo">
            <div class="block__title">
                <span data-language="main_popular_categories">{$lang->main_popular_categories}</span>
            </div>
        </div>

        <div class="block__body">
            <div class="f_row top_categories">
                {function name=has_main_categories}
                {if $categories}
                    {foreach $categories as $c}
                        {if $c->visible &&  ($c->has_products || $settings->show_empty_categories)}
                        {if $c->on_main}
                            <div class="top_categories__item f_col-6 f_col-md-4 f_col-xl-2">
                                <a class="top_categories__preview d-flex align-items-center flex-column" href="{url_generator route='category' url=$c->url}">
                                    <div class="top_categories__image d-flex align-items-center justify-content-center">
                                        {if $c->image}
                                            {if strtolower(pathinfo($c->image, $smarty.const.PATHINFO_EXTENSION)) == 'svg'}
                                                {$c->image|read_svg:$config->original_categories_dir}
                                            {else}
                                            <picture>
                                                {if $settings->support_webp}
                                                    <source type="image/webp" data-srcset="{$c->image|resize:100:100:false:$config->resized_categories_dir|webp}">
                                                {/if}
                                                <source data-srcset="{$c->image|resize:100:100:false:$config->resized_categories_dir}">
                                                <img class="lazy" data-src="{$c->image|resize:100:100:false:$config->resized_categories_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$c->name|escape}" title="{$c->name|escape}"/>
                                            </picture>
                                            {/if}
                                        {else}
                                            <div class="top_categories__no_image d-flex align-items-center justify-content-center" title="{$c->name|escape}">
                                                {include file="svg.tpl" svgId="no_image"}
                                            </div>
                                        {/if}
                                    </div>
                                    <div class="top_categories__name">
                                        {$c->name|escape}  
                                    </div>
                                </a>
                            </div>
                        {/if}
                        {has_main_categories categories=$c->subcategories level=$level + 1}
                        {/if}
                    {/foreach}
                {/if}
            {/function}
            {has_main_categories categories=$categories level=1}
            </div>
        </div>
    </div>
</section> 
{/if}