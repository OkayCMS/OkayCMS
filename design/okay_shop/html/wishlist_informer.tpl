<!-- Информер избранного (отдаётся аяксом) -->
<a class="header_informers__link d-flex align-items-center" href="{url_generator route="wishlist"}">
    <i class="d-flex align-items-center fa fa-heart-o"></i>
    {if $wishlist->products|count > 0}
        {*<span class="informer_name tablet-hidden" data-language="wishlist_header">{$lang->wishlist_header}</span> <span class="informer_counter">({$wished_products|count})</span>*}
        <span class="wishlist_counter">{$wishlist->products|count}</span>
    {else}
        {*<span class="informer_name tablet-hidden" data-language="wishlist_header">{$lang->wishlist_header}</span>*}
    {/if}
</a>
