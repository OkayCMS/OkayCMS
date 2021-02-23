{if $settings->newpost_use_volume}
    <div class="okay_list_boding variants_item_price">
        <div class="heading_label">{$btr->product_np_volume}</div>
        <input class="variant_input" name="variants[volume][]" type="text" value="{$variant->volume|escape}"/>
    </div>
{/if}