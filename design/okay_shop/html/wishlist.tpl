{* page title *}
{$meta_title = $lang->wishlist_title scope=global}

<div class="block">
    {* Page heading *}
    <div class="block__header block__header--boxed block__header--border">
        <h1 class="block__heading">
            <span data-language="wishlist_header">{$lang->wishlist_header}</span>
        </h1>
    </div>
    <div class="block__body block--boxed block--border">
        {if $description}
            <div class="block">
                {$description}
            </div>
        {/if}

        {if $wishlist->products|count}
            <div class="fn_wishlist_page products_list row">
                {* Список избранных товаров *}
                {foreach $wishlist->products as $product}
                    <div class="product_item no_hover col-xs-6 col-sm-4 col-md-3 col-xl-25">
                        {include "product_list.tpl"}
                    </div>
                {/foreach}
            </div>
        {else}
            <div class="block">
                <span data-language="wishlist_empty">{$lang->wishlist_empty}</span>
            </div>
        {/if}
    </div>
</div>