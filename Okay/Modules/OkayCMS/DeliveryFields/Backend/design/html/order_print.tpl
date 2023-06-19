{foreach $deliveryFields as $field}
    {if !in_array($order->delivery_id, $field->deliveries)}
        {continue}
    {/if}
    <tr>
        <td class="td_pr_1">{$field->name|escape}:</td>
        <td class="small"><i>{$field->value|escape}</i></td>
    </tr>
{/foreach}