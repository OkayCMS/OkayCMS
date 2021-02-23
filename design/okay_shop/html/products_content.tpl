{if $products}
    {foreach $products as $product}
        <div class="product_item col-xs-6 col-sm-4 col-md-4 col-lg-4 col-xl-3">
            {include file="product_list.tpl"}
        </div>
    {/foreach}
{else}
    <div class="col-xs-12">
        <div class="boxed boxed--big boxed--notify ">
            <span data-language="products_not_found">{$lang->products_not_found}</span>
        </div>
    </div>
{/if}