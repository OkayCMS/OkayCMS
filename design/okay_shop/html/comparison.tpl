{* The products comparison page *}

{* The page title *}
{$meta_title = $lang->comparison_title scope=global}

<div class="block">
    {* The page heading *}
    <div class="block__header block__header--boxed block__header--border">
        <h1 class="block__heading">
            <span data-language="comparison_header">{$lang->comparison_header}</span>
        </h1>
    </div>
    <div class="block__body block--boxed block--border">
        {if $comparison->products}
            <div class="comparison_block clearfix">
                <div class="comparison_block__left">
                    <div class="fn_resize compare_controls">
                        {* Show all/different product features *}
                        {if $comparison->products|count > 1}
                            <div class="fn_show compare_show">
                                <a href="#show_all" class="active"><span data-language="comparison_all">{$lang->comparison_all}</span></a>
                                <a href="#show_dif" class="unique"><span data-language="comparison_unique">{$lang->comparison_unique}</span></a>
                            </div>
                        {/if}
                    </div>
                    {* Rating *}
                    <div class="cprs_rating" data-use="cprs_rating">
                        <span data-language="product_rating">{$lang->product_rating}</span>
                    </div>
                    {* Feature name *}
                    {if $comparison->features}
                        {foreach $comparison->features as $id=>$cf}
                            <div class="cprs_feature_{$id} cell{if $cf->not_unique} not_unique{/if}" data-use="cprs_feature_{$id}">
                                <span data-feature="{$cf->id}">{$cf->name}</span>
                            </div>
                        {/foreach}
                    {/if}
                </div>

                <div class="fn_comparison_products comparison_block__products swiper-container">
                    <div class="swiper-wrapper">
                        {foreach $comparison->products as $id=>$product}
                            <div class="comparison_block__item swiper-slide">
                                <div class="fn_resize product_item no_hover">
                                    {include file="product_list.tpl"}
                                </div>

                                {* Rating *}
                                <div id="product_{$product->id}" class="cprs_rating">
                                    <span class="rating_starOff">
                                        <span class="rating_starOn" style="width:{$product->rating*90/5|string_format:'%.0f'}px;"></span>
                                    </span>
                                </div>

                                {* Feature value *}
                                {if $product->features}
                                    {foreach $product->features as $id=>$value}
                                        <div class="cprs_feature_{$id} cell{if $comparison->features.{$id}->not_unique} not_unique{/if}">
                                            {$value|default:"&mdash;"}
                                        </div>
                                    {/foreach}
                                {/if}

                            </div>
                        {/foreach}
                    </div>
                    {*if $comparison->products|count > 4*} 
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    {*/if*}
                </div>
            </div>
        {else}
            <div class="boxed boxed--big boxed--notify ">
                <span data-language="comparison_empty">{$lang->comparison_empty}</span>
            </div>
        {/if}
    </div>
</div>