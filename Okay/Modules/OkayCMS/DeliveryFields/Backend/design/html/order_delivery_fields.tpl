{foreach $deliveryFields as $field}
    <div class="mb-1 fn_delivery_field{foreach $field->deliveries as $deliveryId} fn_delivery_field_{$deliveryId}{/foreach}"
        {if !empty($field->deliveries) && !in_array($order->delivery_id, $field->deliveries)}style="display: none"{/if}
    >
        <div class="heading_label">
            {$field->name|escape}
            {if $field->value}
                <a href="https://www.google.com/maps/search/{$field->value|escape}?hl=ru" target="_blank">
                    <i class="fa fa-map-marker"></i> {$btr->df_order_on_map|escape}
                </a>
            {/if}
        </div>
        <input name="delivery_fields[{$field->id}]"
               class="form-control"
               type="text"
               value="{$field->value|escape}"
               {if !empty($field->deliveries) && !in_array($order->delivery_id, $field->deliveries)}disabled{/if}
        />
        {if $field->value_id}
            <input name="delivery_fields_values_ids[{$field->id}]"
                   value="{$field->value_id|escape}"
                   type="hidden"
                   {if !empty($field->deliveries) && !in_array($order->delivery_id, $field->deliveries)}disabled{/if}
            />
        {/if}
    </div>
{/foreach}

<script>
    $(document).on('change', 'select[name="delivery_id"]', function (){
        let deliveryId = $(this).children(':selected').val();
        $('.fn_delivery_field').hide().children('input').prop('disabled', true);
        if (deliveryId) {
            $('.fn_delivery_field_' + deliveryId).show().children('input').prop('disabled', false);
        }
    });
</script>