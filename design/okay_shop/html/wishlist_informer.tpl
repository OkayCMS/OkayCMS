{* Информер избранного (отдаётся аяксом) *}
{if $wishlist->products|count > 0}
    <a class="header_informers__link d-flex align-items-center" href="{url_generator route="wishlist"}">
        <i class="d-flex align-items-center fa fa-heart-o"></i>
        {*<span class="informer_name tablet-hidden" data-language="wishlist_header">{$lang->wishlist_header}</span> <span class="informer_counter">({$wished_products|count})</span>*}
        <span class="wishlist_counter">{$wishlist->products|count}</span>
    </a>
{else}
    <span class="header_informers__link d-flex align-items-center">
        <i class="d-flex align-items-center fa fa-heart-o"></i>
        {*<span class="informer_name tablet-hidden" data-language="wishlist_header">{$lang->wishlist_header}</span>*}
    </span>
{/if}
