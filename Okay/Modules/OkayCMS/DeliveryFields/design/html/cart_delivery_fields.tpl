{if !empty($delivery->delivery_fields)}
    {foreach $delivery->delivery_fields as $deliveryField}
        <div class="form__group delivery_fields__form_group">
            <input class="form__input form__placeholder--focus{if $deliveryField->required} required{/if}"
                   {if $deliveryField->required}
                       data-error_text="{sprintf($lang->okaycms__delivery_fields_error|escape, $deliveryField->name)}"
                   {/if}
                   name="delivery_fields[{$delivery->id}][{$deliveryField->id}]"
                   autocomplete="off"
                   type="text"
                   value="{$request_data['delivery_fields'][$delivery->id][$deliveryField->id]|escape}"
            />
            <span class="form__placeholder">{$deliveryField->name|escape}{if $deliveryField->required}*{/if}</span>
        </div>
    {/foreach}
{/if}