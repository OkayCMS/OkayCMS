{if $products|count > 0}
    <div class="fn_ajax_buttons d-flex flex-wrap align-items-center products_sort">
        <span class="product_sort__title hidden-sm-down" data-language="products_sort_by">{$lang->products_sort_by}:</span>

        <form class="product_sort__form" method="post">
            <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link{if $sort=='position'} active_up{/if} no_after" value="{furl sort=position page=null absolute=1}">
                <span data-language="products_by_default">{$lang->products_by_default}</span>
            </button>
        </form>

        <form class="product_sort__form" method="post">
            <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link{if $sort=='price'} active_up{elseif $sort=='price_desc'} active_down{/if}" value="{if $sort=='price'}{furl sort=price_desc page=null absolute=1}{else}{furl sort=price page=null absolute=1}{/if}">
                <span data-language="products_by_price">{$lang->products_by_price}</span>
                {include file="svg.tpl" svgId="sort_icon"}
            </button>
        </form>

        <form class="product_sort__form" method="post">
            <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link{if $sort=='name'} active_up{elseif $sort=='name_desc'} active_down{/if}" value="{if $sort=='name'}{furl sort=name_desc page=null absolute=1}{else}{furl sort=name page=null absolute=1}{/if}">
                <span data-language="products_by_name">{$lang->products_by_name}</span>
                {include file="svg.tpl" svgId="sort_icon"}
            </button>
        </form>

        <form class="product_sort__form" method="post">
            <button type="submit" name="prg_seo_hide" class="d-inline-flex align-items-center product_sort__link {if $sort=='rating'} active_up{elseif $sort=='rating_desc'} active_down{/if}" value="{if $sort=='rating'}{furl sort=rating_desc page=null absolute=1}{else}{furl sort=rating page=null absolute=1}{/if}">
                <span data-language="products_by_rating">{$lang->products_by_rating}</span>
                {include file="svg.tpl" svgId="sort_icon"}
            </button>
        </form>
    </div>
{/if}
