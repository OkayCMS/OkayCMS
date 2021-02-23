{if $delivery->module_id == $np_delivery_module_id}
    <div class="novaposhta_div fn_delivery_novaposhta" style="margin-top: 15px;">
        <div class="np_preloader"></div>
        
        {if $delivery->settings['service_type'] == 'DoorsDoors' || $delivery->settings['service_type'] == 'WarehouseDoors'}
            <div class="form__group">
                <input class="city_novaposhta_for_door form__input form__placeholder--focus" name="novaposhta_city" autocomplete="on" type="text" value="{$request_data.novaposhta_city|escape}" >
                <span class="form__placeholder">{$lang->np_cart_city}*</span>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="form__group fn_search_street"> 
                        <input class="form__input fn_street form__placeholder--focus fn_np_clear" name="novaposhta_street" id="search_np_street" type="text" value="{$request_data.novaposhta_street|escape}" autocomplete="off">
                        <span class="form__placeholder">{$lang->np_cart_street}*</span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form__group fn_house">
                        <input class="form__input fn_address form__placeholder--focus fn_np_clear" name="novaposhta_house" type="text" value="{$request_data.novaposhta_house|escape}">
                        <span class="form__placeholder">{$lang->np_cart_house}*</span>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form__group fn_apartment">
                        <input class="form__input fn_address form__placeholder--focus fn_np_clear" name="novaposhta_apartment" type="text" value="{$request_data.novaposhta_apartment|escape}">
                        <span class="form__placeholder">{$lang->np_cart_apartment}</span>
                    </div>
                </div>
            </div>
            <input name="novaposhta_door_delivery" type="hidden" value="1"/>

            <input name="novaposhta_city_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_city_name}"/>
            <input name="novaposhta_area_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_area_name}"/>
            <input name="novaposhta_region_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_region_name}"/>
            <input name="novaposhta_street_name" class="fn_np_clear" type="hidden" value="{$request_data.novaposhta_street_name}"/>
            
        {else}
            <div class="form__group">
                <input class="city_novaposhta form__input form__placeholder--focus" name="novaposhta_city" autocomplete="on" type="text" value="{$request_data.novaposhta_city|escape}" >
                <span class="form__placeholder">{$lang->np_cart_city}*</span>
            </div>
            
            <div class="warehouses_novaposhta form__group">
                <select name="novaposhta_warehouses" tabindex="1" class="fn_select_warehouses_novaposhta" style="width: 100%;"></select>
            </div>

            <input name="novaposhta_delivery_warehouse_id" type="hidden" value="{$request_data.novaposhta_delivery_warehouse_id}"/>
        {/if}

        {if $np_redelivery_payments_ids}
            <div class="form__group">
                <label for="redelivery_{$delivery->id}">
                <input name="novaposhta_redelivery" id="redelivery_{$delivery->id}" value="1" type="checkbox" {if $request_data.novaposhta_redelivery == true}checked{/if} />
                {$lang->np_cart_cod} 
            </label>
            </div>
        {/if}
        
        <div class="term_novaposhta">{$lang->np_cart_term} <span></span></div>
    
        <input name="is_novaposhta_delivery" type="hidden" value="1"/>
        <input name="novaposhta_delivery_price" type="hidden" value="{$request_data.novaposhta_delivery_price}"/>
        <input name="novaposhta_delivery_term" type="hidden" value="{$request_data.novaposhta_delivery_term}"/>
        <input name="novaposhta_delivery_city_id" type="hidden" value="{$request_data.novaposhta_delivery_city_id}"/>
        
    </div>
{/if}
