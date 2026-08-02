{if $delivery->module_id == $np_delivery_module_id}
    <div class="novaposhta_div fn_delivery_novaposhta" data-delivery-id="{$delivery->id}" style="margin-top: 15px;">
        <div class="np_preloader"></div>
        
        {if $delivery->settings['service_type'] == 'DoorsDoors' || $delivery->settings['service_type'] == 'WarehouseDoors'}
            <div class="form__group">
                <input class="city_novaposhta_for_door form__input form__placeholder--focus" name="novaposhta[{$delivery->id}][door_city]" data-np-field="door_city" autocomplete="section-np-door shipping address-level2" type="text" value="{if $request_data.novaposhta_door_city}{$request_data.novaposhta_door_city|escape}{elseif $request_data.novaposhta_city}{$request_data.novaposhta_city|escape}{/if}" data-selected-city-value="{if $request_data.novaposhta_door_city}{$request_data.novaposhta_door_city|escape}{elseif $request_data.novaposhta_city}{$request_data.novaposhta_city|escape}{/if}" >
                <span class="form__placeholder">{$lang->np_cart_city}*</span>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form__group fn_search_street"> 
                        <input class="form__input fn_street form__placeholder--focus fn_np_clear" name="novaposhta[{$delivery->id}][street]" data-np-field="street" id="search_np_street_{$delivery->id}" type="text" value="{$request_data.novaposhta_street|escape}" autocomplete="off">
                        <span class="form__placeholder">{$lang->np_cart_street}*</span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form__group fn_house">
                        <input class="form__input fn_address form__placeholder--focus fn_np_clear" name="novaposhta[{$delivery->id}][house]" data-np-field="house" type="text" value="{$request_data.novaposhta_house|escape}">
                        <span class="form__placeholder">{$lang->np_cart_house}*</span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form__group fn_apartment">
                        <input class="form__input fn_address form__placeholder--focus fn_np_clear" name="novaposhta[{$delivery->id}][apartment]" data-np-field="apartment" type="text" value="{$request_data.novaposhta_apartment|escape}">
                        <span class="form__placeholder">{$lang->np_cart_apartment}</span>
                    </div>
                </div>
            </div>
            <input name="novaposhta[{$delivery->id}][door_delivery]" data-np-field="door_delivery" type="hidden" value="1"/>

            <input name="novaposhta[{$delivery->id}][door_settlement_ref]" data-np-field="door_settlement_ref" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_door_settlement_ref|escape}"/>
            <input name="novaposhta[{$delivery->id}][city_name]" data-np-field="city_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_city_name|escape}"/>
            <input name="novaposhta[{$delivery->id}][area_name]" data-np-field="area_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_area_name|escape}"/>
            <input name="novaposhta[{$delivery->id}][region_name]" data-np-field="region_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_region_name|escape}"/>
            <input name="novaposhta[{$delivery->id}][street_name]" data-np-field="street_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_street_name|escape}"/>
            
        {else}
            <div class="form__group">
                <input class="city_novaposhta form__input form__placeholder--focus" name="novaposhta[{$delivery->id}][warehouse_city]" data-np-field="warehouse_city" autocomplete="section-np-warehouse shipping address-level2" type="text" value="{if $request_data.novaposhta_warehouse_city}{$request_data.novaposhta_warehouse_city|escape}{elseif $request_data.novaposhta_city}{$request_data.novaposhta_city|escape}{/if}" data-selected-city-value="{if $request_data.novaposhta_warehouse_city}{$request_data.novaposhta_warehouse_city|escape}{elseif $request_data.novaposhta_city}{$request_data.novaposhta_city|escape}{/if}" >
                <span class="form__placeholder">{$lang->np_cart_city}*</span>
            </div>

            <div class="np_delivery_types_block">
                <div class="np_delivery_types_heading"></div>
                <div class="np_delivery_types_content"></div>
            </div>

            <input name="novaposhta[{$delivery->id}][warehouse_city_ref]" data-np-field="warehouse_city_ref" type="hidden" value="{$request_data.novaposhta_warehouse_city_ref|escape}"/>
            <input name="novaposhta[{$delivery->id}][warehouse_city_name]" data-np-field="warehouse_city_name" type="hidden" value="{$request_data.novaposhta_warehouse_city_name|escape}"/>
            <input name="novaposhta[{$delivery->id}][delivery_warehouse_id]" data-np-field="delivery_warehouse_id" type="hidden" value="{$request_data.novaposhta_delivery_warehouse_id|escape}"/>
        {/if}

        {if $np_redelivery_payments_ids && $cart->total_price > 0}
            <div class="form__group">
                <label for="redelivery_{$delivery->id}">
                    <input name="novaposhta[{$delivery->id}][redelivery]" type="hidden" value="0"/>
                    <input name="novaposhta[{$delivery->id}][redelivery]" data-np-field="redelivery" id="redelivery_{$delivery->id}" value="1" type="checkbox" {if $request_data.novaposhta_redelivery == true}checked{/if} />
                    {$lang->np_cart_cod}
                </label>
            </div>
        {/if}
        
        <div class="term_novaposhta">{$lang->np_cart_term} <span></span></div>
    
        <input name="novaposhta[{$delivery->id}][delivery_price]" data-np-field="delivery_price" type="hidden" value="{$request_data.novaposhta_delivery_price|escape}"/>
        <input name="novaposhta[{$delivery->id}][delivery_term]" data-np-field="delivery_term" type="hidden" value="{$request_data.novaposhta_delivery_term|escape}"/>
        
    </div>

    <script>
        var form_enter_novaposhta_city = "{$lang->np_form_enter_city|escape}";
        var form_enter_novaposhta_street = "{$lang->np_form_enter_street|escape}";
        var form_enter_novaposhta_house = "{$lang->np_form_novaposhta_house|escape}";
        var form_enter_novaposhta_warehouses = "{$lang->np_form_novaposhta_warehouses|escape}";

    </script>
{/if}
