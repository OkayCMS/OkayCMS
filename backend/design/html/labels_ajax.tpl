{*Вывод меток*}
{if $smarty.get.controller == "OrderAdmin"}
    {foreach $order_labels as $l}
        <span class="tag font-xs" style="background-color:#{$l->color};" >{$l->name|escape}</span>
        <input type="hidden" name="order_labels[]" value="{$l->id}">
    {/foreach}
{else}
    {if $order->labels}
        {foreach $order->labels as $l}
            <span class="tag" style="background-color:#{$l->color};" >{$l->name|escape}</span>
        {/foreach}
    {/if}
{/if}