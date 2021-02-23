const configParamsObj = {
    //placeholder: 'Выберите город...', // Place holder text to place in the select
    minimumResultsForSearch: 3, // Overrides default of 15 set above
    width: 'resolve',
    matcher: function (params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
            var modifiedData = $.extend({}, data, true);
            return modifiedData;
        }
        return null;
    }
};

const whsParams = {
    matcher: function (params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if ($.isNumeric(params.term)){
            if (~data.text.indexOf("№"+params.term)){
                return data;
            }
        } else if (~data.text.toLowerCase().indexOf(params.term.toLowerCase())) {
            var modifiedData = $.extend({}, data, true);
            return modifiedData;
        }
        return null;
    }
};

init();
$('select.city_novaposhta').select2(configParamsObj);

$(document).on('change', 'select.fn_select_warehouses_novaposhta', set_warehouse);
$(document).on('change', 'input[name="novaposhta_redelivery"]', calc_delivery_price);

function init() {

    let delivery_block = $('.fn_delivery_novaposhta').closest('.delivery__item');
    let city_ref = delivery_block.find('input[name="novaposhta_delivery_city_id"]').val();
    
    $('select.city_novaposhta').closest('.delivery_wrap').find('span.deliver_price').text('');

    $(document).on('change', 'input[name="delivery_id"]', calc_delivery_price);
    $(document).on('change', 'input[name="novaposhta_delivery_city_id"]', calc_delivery_price);

    if (city_ref) {
        calc_delivery_price();
    }
    $('.np_preloader').remove();
}

$( ".fn_delivery_novaposhta input.city_novaposhta" ).devbridgeAutocomplete({
    serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_city'],
    minChars: 1,
    maxHeight: 320,
    noCache: true,
    onSelect: function(suggestion) {
        let active_delivery = $('input[name="delivery_id"]:checked');
        let delivery_block = active_delivery.closest('.delivery__item');

        delivery_block.find('input[name="novaposhta_delivery_warehouse_id"]').val('');
        delivery_block.find('select.fn_select_warehouses_novaposhta option:selected').prop('selected', false);
        delivery_block.find('input[name="novaposhta_delivery_city_id"]').val(suggestion.data.ref).trigger('change');
    },
    formatResult: function(suggestion, currentValue) {
        var reEscape = new RegExp( '(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join( '|\\' ) + ')', 'g' );
        var pattern = '(' + currentValue.replace( reEscape, '\\$1' ) + ')';
        return "<div style='text-align: left'>" + suggestion.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + "<\/div>";
    }
});

// Автокомплит адреса в корзине из справочника Новой Почты
let streetAutocomplete = false;
$( ".fn_delivery_novaposhta input.city_novaposhta_for_door" ).devbridgeAutocomplete({
    serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_city_for_door'],
    minChars:1,
    noCache: false,
    onSelect: function(suggestion) {
        let active_delivery = $('input[name="delivery_id"]:checked');
        let delivery_block = active_delivery.closest('.delivery__item');
        delivery_block.find('input[name="novaposhta_delivery_city_id"]').val(suggestion.ref).trigger('change');
        delivery_block.find('input[name=novaposhta_city_name]').val(suggestion.city);
        delivery_block.find('input[name=novaposhta_area_name]').val(suggestion.area);
        delivery_block.find('input[name=novaposhta_region_name]').val(suggestion.region);
        setDoorAddress();
        if (suggestion.streets_availability) {
            $(".fn_delivery_novaposhta input.fn_street").devbridgeAutocomplete({
                serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_street'] + "?city_ref=" + suggestion.ref,
                minChars:1,
                noCache: false,
                onSearchStart: function(params) {
                    streetAutocomplete = true;
                },
                onSelect: function(suggestion){
                    delivery_block.find('input[name=novaposhta_street_name]').val(suggestion.street);
                    setDoorAddress();
                },
                formatResult: function(suggestion, currentValue) {
                    var reEscape = new RegExp( '(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join( '|\\' ) + ')', 'g' );
                    var pattern = '(' + currentValue.replace( reEscape, '\\$1' ) + ')';
                    return "<div style='text-align: left'>" + suggestion.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + "<\/div>";
                }
            });
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

$(document).on('change', 'input[name="novaposhta_city"], input[name="novaposhta_street"], input[name="novaposhta_house"], input[name="novaposhta_apartment"]', function () {
    setDoorAddress();
});

function setDoorAddress()
{
    let deliveryBlock = $('input[name="delivery_id"]:checked').closest('.fn_delivery_item');
    let address = '';
    let val = '';
    
    
    if (val = deliveryBlock.find('input[name="novaposhta_city"]').val()) {
        address += val;
    }
    
    if (val = deliveryBlock.find('input[name="novaposhta_street"]').val()) {
        address += ', ' + val;
    }
    
    if (val = deliveryBlock.find('input[name="novaposhta_house"]').val()) {
        address += ', д. ' + val;
    }
    
    if (val = deliveryBlock.find('input[name="novaposhta_apartment"]').val()) {
        address += ', кв. ' + val;
    }
    
    if (address !== '') {
        $('input[name="address"]').val(address);
    }
}

$('[name="delivery_id"]').on('change', function() {
    if (Number($(this).data('module_id')) !== Number(okay.np_delivery_module_id)) {
        return;
    }
    update_np_payments();
    select_first_active_payment();
});

function calc_delivery_price(e) {
    if (e !== undefined && e.target.name === 'novaposhta_redelivery') {
        update_np_payments();
        select_first_active_payment();
    }

    let active_delivery = $('input[name="delivery_id"]:checked');
    if (active_delivery.data('module_id') == okay.np_delivery_module_id) {
        $('#fn_total_delivery_price').text('');
    } else {
        return false;
    }

    let delivery_block = active_delivery.closest('.delivery__item');
    let price_elem = delivery_block.find('.fn_delivery_price');
    let term_elem = delivery_block.find('.term_novaposhta span');
    let warehouses_block = delivery_block.find('.warehouses_novaposhta');
    let delivery_id = active_delivery.val();
    let city_ref = delivery_block.find('input[name="novaposhta_delivery_city_id"]').val();

    let redelivery = 0;
    
    if (delivery_block.find('input[name="novaposhta_redelivery"]').is(':checked')){
        redelivery = delivery_block.find('input[name="novaposhta_redelivery"]').val();
    }

    if (city_ref) {
        delivery_block.find('input[name="novaposhta_delivery_city_id"]').val(city_ref);
        let warehouse_ref = delivery_block.find('input[name="novaposhta_delivery_warehouse_id"]').val();
        
        price_elem.text('Вычисляем...');
        $('#fn_total_delivery_price').text('Вычисляем...');
        term_elem.text('');

        delivery_block.find('input[name="novaposhta_delivery_price"]').val('');
        delivery_block.find('input[name="novaposhta_delivery_term"]').val('');
        $.ajax({
            url: okay.router['OkayCMS_NovaposhtaCost_calc'],
            data: {city: city_ref, redelivery: redelivery, warehouse: warehouse_ref, delivery_id: delivery_id},
            dataType: 'json',
            success: function(data) {
                if (data.price_response.success) {
                    price_elem.text(data.price_response.price_formatted);
                    delivery_block.find('input[name="novaposhta_delivery_price"]').val(data.price_response.price);
                    delivery_block.find('input[name="delivery_id"]').data('total_price', data.price_response.cart_total_price)
                        .data('delivery_price', data.price_response.price );

                    okay.change_payment_method();
                }
                
                if (data.term_response.success) {
                    delivery_block.find('input[name="novaposhta_delivery_term"]').val(data.term_response.term);
                    term_elem.text(data.term_response.term);
                    term_elem.parent().show();
                } else {
                    term_elem.parent().hide();
                }
                if (data.warehouses_response.success) {
                    warehouses_block.show();
                    selected_whref = $('.fn_select_warehouses_novaposhta').find(':selected').attr('data-warehouse_ref');
                    if(!$('.fn_select_warehouses_novaposhta').find(':selected').val() || data.warehouses_response.warehouses.indexOf(selected_whref)== -1){
                    warehouses_block.find('.fn_select_warehouses_novaposhta')
                        .html(data.warehouses_response.warehouses)
                        .attr('disabled', false)
                        .select2(whsParams);
                    }
                } else {
                    warehouses_block.hide();
                    warehouses_block.find('.fn_select_warehouses_novaposhta')
                        .html('')
                        .attr('disabled', true);
                }

                update_np_payments();
            }
        });
    }
}

function update_np_payments() {
    const payment_method_ids = get_np_payment_method_ids();
    const redelivery_enabled = $('input[name="delivery_id"]:checked').closest('.fn_delivery_item').find('[name="novaposhta_redelivery"]').prop('checked');

    if (redelivery_enabled) {
        for (const payment_id of payment_method_ids) {
            if (okay.np_redelivery_payments_ids.includes(payment_id)) {
                $(`.fn_payment_method__item_${payment_id}`).show();
            } else {
                $(`.fn_payment_method__item_${payment_id}`).hide();
            }
        }
    } else {
        for (const payment_id of payment_method_ids) {
            if (okay.np_redelivery_payments_ids.includes(payment_id)) {
                $(`.fn_payment_method__item_${payment_id}`).hide();
            } else {
                $(`.fn_payment_method__item_${payment_id}`).show();
            }
        }
    }
}

function select_first_active_payment() {
    const payment_method_elements = $('[name="payment_method_id"]');
    for (const element of payment_method_elements) {
        const id = element.attributes.id.nodeValue;
        if (! $(`#${id}`).closest('.fn_payment_method__item').is(':hidden')) {
            $(`#${id}`).trigger('click');
            break;
        }
    }
}

function get_np_payment_method_ids() {
    let deliveryInput = $('input[name="delivery_id"]:checked').closest('.fn_delivery_item').find('[name="novaposhta_redelivery"]')
        .closest('.fn_delivery_item')
        .find('[name="delivery_id"]');
    
    if (deliveryInput.data('payment_method_ids') !== undefined) {
        return String(deliveryInput.data('payment_method_ids'))
            .split(',')
            .map(Number);
    } else {
        return [];
    }
}

function set_warehouse() {
    if ($(this).val() != '') {
        $('input[name="address"]').trigger('focus');
        let city_name = $('.city_novaposhta').val(),
            warehouse_name = $(this).val(),
            delivery_address = city_name + ', ' + warehouse_name;
        $('input[name="address"]').val(delivery_address);
        $('input[name="novaposhta_delivery_warehouse_id"]').val($(this).children(':selected').data('warehouse_ref'));
    }
}