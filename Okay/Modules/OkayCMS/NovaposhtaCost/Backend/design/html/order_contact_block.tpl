<div class="fn_delivery_novaposhta"{if $delivery->module_id != $novaposhta_module_id} style="display: none;"{/if}>
    <input name="novaposhta_city_id" type="hidden" value="{$novaposhta_delivery_data->city_id|escape}" />
    <input name="novaposhta_delivery_term" type="hidden" value="{$novaposhta_delivery_data->delivery_term|escape}" />
    {if $delivery->settings['service_type'] == 'DoorsDoors' || $delivery->settings['service_type'] == 'WarehouseDoors'}
        <div class="mb-1">
            <div class="heading_label">{$btr->order_np_city}</div>
            <input type="text" name="novaposhta_city" class="fn_newpost_city_name form-control" autocomplete="off" value="{$novaposhta_delivery_data->city_name}">
        </div>
        <div class="mb-1">
            <div class="heading_label">{$btr->order_np_street}</div>
            <input type="text" name="novaposhta_street" class="fn_newpost_street form-control" autocomplete="off" value="{$novaposhta_delivery_data->street}">
        </div>
        <div class="mb-1">
            <div class="heading_label">{$btr->order_np_house}</div>
            <input type="text" name="novaposhta_house" class="form-control" autocomplete="off" value="{$novaposhta_delivery_data->house}">
        </div>
        <div class="mb-1">
            <div class="heading_label">{$btr->order_np_apartment}</div>
            <input type="text" name="novaposhta_apartment" class="form-control" autocomplete="off" value="{$novaposhta_delivery_data->apartment}">
        </div>

        <input name="novaposhta_city_name" class="fn_np_clear" type="hidden" value="{$novaposhta_delivery_data->city_name}"/>
        <input name="novaposhta_area_name" class="fn_np_clear" type="hidden" value="{$novaposhta_delivery_data->area_name}"/>
        <input name="novaposhta_region_name" class="fn_np_clear" type="hidden" value="{$novaposhta_delivery_data->region_name}"/>
        <input name="novaposhta_street_name" class="fn_np_clear" type="hidden" value="{$novaposhta_delivery_data->street}"/>

        <input name="novaposhta_door_delivery" type="hidden" value="1"/>
        
    {else}
        
        <input name="novaposhta_warehouse_id" type="hidden" value="{$novaposhta_delivery_data->warehouse_id|escape}" />
        
        <div class="mb-1">
            <div class="heading_label">{$btr->order_np_city}</div>
            <input type="text" class="fn_newpost_city_name form-control" autocomplete="off" value="{$novaposhta_delivery_data->city_id|newpost_city}">
        </div>
        <div class="mb-1">
            <div class="heading_label">{$btr->order_np_warehouse}</div>
            <select name="novaposhta_warehouse" tabindex="1" class="selectpicker form-control warehouses_novaposhta"></select>
        </div>
    {/if}
    
    <div class="mb-1">
        <div class="heading_label">
            <input type="checkbox" id="novaposhta_redelivery" name="novaposhta_redelivery" value="1" {if $novaposhta_delivery_data->redelivery}checked{/if}/>
            <label for="novaposhta_redelivery">{$btr->order_np_redelivery}</label>
        </div>
        
    </div>
    <div class="mb-1">
        <div class="heading_label">
            <span class="fn_np_term"{if !$novaposhta_delivery_data->delivery_term} style="display: none;"{/if}>{$btr->order_np_term}: <span>{$novaposhta_delivery_data->delivery_term}</span></span>
            <a href="#" class="fn_np_recalc_price">{$btr->order_np_calc}</a>
        </div>
    </div>
</div>

{literal}
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>

<script>

    toastr.options = {
        closeButton: true,
        newestOnTop: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        preventDuplicates: false,
        onclick: null
    };
    
    $('.fn_np_recalc_price').on('click', function(e) {
        e.preventDefault();
        let selected_city = $('input[name="novaposhta_city_id"]').val();
        let warehouse_ref = $('input[name="novaposhta_warehouse_id"]').val();
        let delivery_id = $('select[name="delivery_id"]').children(':selected').val();
        let redelivery = $('input[name="novaposhta_redelivery"]').is(':checked') ? 1 : 0;
        $.ajax({
            url: okay.router['OkayCMS_NovaposhtaCost_calc'],
            data: {
                city: selected_city,
                redelivery: redelivery,
                warehouse: warehouse_ref,
                delivery_id: delivery_id,
                currency: '{/literal}{$currency->id}{literal}',
                order_id: '{/literal}{$order->id}{literal}'
            },
            dataType: 'json',
            success: function(data) {
                
                if (data.price_response.success) {
                    toastr.success('', "{/literal}{$btr->toastr_success|escape}{literal}");
                    $('input[name="delivery_price"]').val(data.price_response.price);
                }

                if (data.term_response.success) {
                    $('input[name="novaposhta_delivery_term"]').val(data.term_response.term);
                    $('.fn_np_term').show().children('span').text(data.term_response.term);
                } else {
                    $('.fn_np_term').parent().hide();
                }
            }
        });
    });
    
    $('select[name="delivery_id"]').on('change', function () {
        if ($(this).children(':selected').data('module_id') == '{/literal}{$novaposhta_module_id}{literal}') {
            $('.fn_delivery_novaposhta').show();
        } else {
            $('.fn_delivery_novaposhta').hide();
        }
    });

    {/literal}
    {if $delivery->settings['service_type'] == 'DoorsDoors' || $delivery->settings['service_type'] == 'WarehouseDoors'}
    {literal}

    setStreetAutocomplete({/literal}'{$novaposhta_delivery_data->city_id|escape}'{literal});
    
    // Автокомплит адреса в корзине из справочника Новой Почты
    let streetAutocomplete = false;
    $( ".fn_newpost_city_name" ).devbridgeAutocomplete({
        serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_city_for_door'],
        minChars:1,
        noCache: false,
        onSelect: function(suggestion) {
            $('input[name="novaposhta_city_id"]').val(suggestion.ref).trigger('change');
            $('input[name=novaposhta_city_name]').val(suggestion.city);
            $('input[name=novaposhta_area_name]').val(suggestion.area);
            $('input[name=novaposhta_region_name]').val(suggestion.region);
            setDoorAddress()
            if (suggestion.streets_availability) {
                setStreetAutocomplete(suggestion.ref);
            } else {
                if(streetAutocomplete) {
                    $(".fn_delivery_novaposhta input.fn_street").devbridgeAutocomplete().disable();
                    streetAutocomplete = false;
                }
            }
        },
        formatResult: function(suggestion, currentValue) {
            var reEscape = new RegExp( '(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join( '|\\' ) + ')', 'g' );
            var pattern = '(' + currentValue.replace( reEscape, '\\$1' ) + ')';
            return "<div style='text-align: left'>" + suggestion.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + "<\/div>";
        }
    });

    function setStreetAutocomplete(cityRef)
    {
        $(".fn_newpost_street").devbridgeAutocomplete({
            serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_street'] + "?city_ref=" + cityRef,
            minChars:1,
            noCache: false,
            onSearchStart: function(params) {
                streetAutocomplete = true;
            },
            onSelect: function(suggestion){
                $('input[name=novaposhta_street_name]').val(suggestion.street);
                setDoorAddress()
            },
            formatResult: function(suggestion, currentValue) {
                var reEscape = new RegExp( '(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join( '|\\' ) + ')', 'g' );
                var pattern = '(' + currentValue.replace( reEscape, '\\$1' ) + ')';
                return "<div style='text-align: left'>" + suggestion.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + "<\/div>";
            }
        });
    }

    function setDoorAddress()
    {
        let address = '';
        let val = '';
        
        if (val = $('input[name="novaposhta_city"]').val()) {
            address += val;
        }

        if (val = $('input[name="novaposhta_street"]').val()) {
            address += ', ' + val;
        }

        if (val = $('input[name="novaposhta_house"]').val()) {
            address += ', д. ' + val;
        }

        if (val = $('input[name="novaposhta_apartment"]').val()) {
            address += ', кв. ' + val;
        }

        if (address !== '') {
            $('[name="address"]').val(address);
        }
    }
    
    {/literal}
    {else}
    {literal}
    
    $( ".fn_newpost_city_name" ).devbridgeAutocomplete( {
        serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_city'],
        minChars: 1,
        maxHeight: 320,
        noCache: true,
        onSelect: function(suggestion) {
            $('input[name="novaposhta_city_id"]').val(suggestion.data.ref);
            showWarehouses(suggestion.data.ref);
        },
        formatResult: function(suggestion, currentValue) {
            var reEscape = new RegExp( '(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join( '|\\' ) + ')', 'g' );
            var pattern = '(' + currentValue.replace( reEscape, '\\$1' ) + ')';
            return "<span>" + suggestion.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + "<\/span>";
        }
    } );

    {/literal}
    {/if}
    {literal}
    
    {/literal}
    {if !empty($novaposhta_delivery_data->city_id)}
    showWarehouses('{$novaposhta_delivery_data->city_id}');
    {/if}
    {literal}
    
    function showWarehouses(cityRef) {
        let selected_warehouse = $('input[name="novaposhta_warehouse_id"]').val();
        $.ajax({
            url: okay.router['OkayCMS_NovaposhtaCost_get_warehouses'],
            data: {city: cityRef, warehouse: selected_warehouse},
            dataType: 'json',
            success: function(data) {
                if (data.warehouses_response.success) {
                    $('select.warehouses_novaposhta').html(data.warehouses_response.warehouses).show();
                    $('select.warehouses_novaposhta').selectpicker('refresh');
                } else {
                    $('select.warehouses_novaposhta').html('').hide();
                }
            }
        });
    }
    
    $('select.warehouses_novaposhta').on('change', function() {
        if($(this).val() != ''){
            let city_name = $('.fn_newpost_city_name').val(),
                warehouse_name = $(this).val(),
                delivery_address = city_name + ', ' + warehouse_name;
            $('textarea[name="address"]').text(delivery_address);
            $('input[name="novaposhta_warehouse_id"]').val($(this).children(':selected').data('warehouse_ref'));
            let new_href = 'https://www.google.com/maps/search/'+ delivery_address +'?hl=ru';
            $("a#google_map").attr("href", new_href);
        }
    });
</script>
{/literal}