{foreach $deliveryFields as $field}
    {foreach $field->deliveries as $deliveryId}
        {if $order->delivery_id != $deliveryId}
            {continue}
        {/if}
    {/foreach}
    <tr>
        <td class="td_pr_1">{$field->name|escape}:</td>
        <td class="small"><i>{$field->value|escape}</i></td>
    </tr>
{/foreach}