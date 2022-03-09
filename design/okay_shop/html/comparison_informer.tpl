<!-- Сomparison informer (given by Ajax) -->
{if $comparison->products|count > 0}
    <a class="header_informers__link d-flex align-items-center"  href="{url_generator route="comparison"}">
        <i class="d-flex align-items-center fa fa-balance-scale"></i>
        <span class="compare_counter">{$comparison->products|count}</span>
    </a>
{else}
    <div class="header_informers__link d-flex align-items-center">
        <i class="d-flex align-items-center fa fa-balance-scale"></i>
    </div>
{/if}
