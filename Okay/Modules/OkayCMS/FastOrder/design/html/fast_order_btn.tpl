<a class="fn_fast_order_button fast_order_button fa fa-rocket fn_is_stock"
   href="#fast_order" {if !$fastOrderProduct->variant->available_to_order}style="display: none" {/if}
   title="{$lang->fast_order}" data-language="fast_order" data-name="{$fast_order_product_name}">{$lang->fast_order}
</a>
