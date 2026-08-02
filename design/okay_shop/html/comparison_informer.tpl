<!-- Сomparison informer (given by Ajax) -->
<a class="header_informers__link d-flex align-items-center" href="{url_generator route="comparison"}">
    <i class="d-flex align-items-center fa fa-balance-scale"></i>
    {if $comparison->products|count > 0}
        <span class="compare_counter">{$comparison->products|count}</span>
    {/if}
</a>
