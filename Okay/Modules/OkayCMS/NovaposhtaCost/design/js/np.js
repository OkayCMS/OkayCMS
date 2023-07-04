const configParamsObj = {
    //placeholder: 'Выберите город...', // Place holder text to place in the select
    minimumResultsForSearch: 3, // Overrides default of 15 set above
    width: 'resolve',
    matcher: function (params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (data.text.toLowerCase().startsWith(params.term.toLowerCase())) {
            return $.extend({}, data, true);
        }
        return null;
    }
};

const whsParams = {
    matcher: function (params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if ($.isNumeric(params.term)) {
            // Шукаємо лише по першому входженню "№...", щоб по запиту "3" знайти лише "Відділення №3",
            // а не "Відділення №1, під'їзд №3"
            let warehouseNumSearch = ~data.text.indexOf("№"+params.term);
            if (warehouseNumSearch && ~data.text.indexOf("№") === warehouseNumSearch){
                return data;
            }
        } else {
            let words = params.term.toLowerCase().split(' ');
            let isMatched = true;
            for (let wordKey in words) {
                let word = words[wordKey];
                if (!~data.text.toLowerCase().indexOf(word)) {
                    isMatched = false;
                }
            }
            if (isMatched) {
                return $.extend({}, data, true);
            } else if (~data.text.toLowerCase().indexOf(params.term.toLowerCase())) {
                return $.extend({}, data, true);
            }
        }
        return null;
    }
};

init();
$('select.city_novaposhta').select2(configParamsObj);

$(document).on('change', 'select.fn_select_warehouses_novaposhta', set_warehouse);
$(document).on('change', 'input[name="novaposhta_redelivery"]', calc_delivery_price);
$(document).on('click', '.np_delivery_types_heading a', changeDeliveryType);

function init() {

    let delivery_block = $('.fn_delivery_novaposhta').closest('.delivery__item');
    let city_ref = delivery_block.find('input[name="novaposhta_delivery_city_id"]').val();
    
    $('select.city_novaposhta').closest('.delivery_wrap').find('span.deliver_price').text('');

    $(document).on('change', 'input[name="delivery_id"]', calc_delivery_price);
    $(document).on('change', 'input[name="novaposhta_delivery_city_id"]', getWarehouses);

    if (city_ref) {
        getWarehouses();
    }
    $('.np_preloader').remove();
    update_np_payments();
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
       // if (suggestion.streets_availability) { Новая Почта перестала присылать корректный параметр у некоторых городов
        if (true) {
            $(".fn_delivery_novaposhta input.fn_street").devbridgeAutocomplete({
                serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_street'] + "?city_ref=" + suggestion.ref,
                minChars:1,
                noCache: false,
                onSearchStart: function(params) {
                    streetAutocomplete = true;
                },
                onSelect: function(suggestion){
                    delivery_block.find('input[name=novaposhta_street_name]').val(suggestion.street);
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

$('[name="delivery_id"]').on('change', function() {
    if (Number($(this).data('module_id')) !== Number(okay.np_delivery_module_id)) {
        return;
    }
    update_np_payments();
    select_first_active_payment();
});

function getWarehouses(e)
{
    let active_delivery = $('input[name="delivery_id"]:checked');
    let delivery_block = active_delivery.closest('.delivery__item');
    let warehousesBlock = delivery_block.find('.warehouses_novaposhta');
    let deliveryTypesBlock = delivery_block.find('.np_delivery_types_block');
    let deliveryTypesHeading = deliveryTypesBlock.find('.np_delivery_types_heading');
    let deliveryTypesContent = deliveryTypesBlock.find('.np_delivery_types_content');
    let cityRef = delivery_block.find('input[name="novaposhta_delivery_city_id"]').val();
    let selectedWarehouseRef = delivery_block.find('input[name="novaposhta_delivery_warehouse_id"]').val();
    let selectedDeliveryType = deliveryTypesHeading.find('a.active').data('delivery_type');

    $.ajax({
        url: okay.router['OkayCMS_NovaposhtaCost_get_warehouses'],
        data: {city: cityRef},
        dataType: 'json',
        success: function(data) {
            if (data.hasOwnProperty('success') && data.success) {

                deliveryTypesHeading.html('');
                deliveryTypesContent.html('');

                // Додаємо таби типів доставки
                for (let deliveryTypeKey in data.delivery_types) {
                    let deliveryType = data.delivery_types[deliveryTypeKey];

                    let deliveryTypeButton = $('<a href="javascript:;" data-delivery_type="fn_delivery_type_' + deliveryTypeKey + '"><span>' + deliveryType.name + '</span></a>');
                    deliveryTypeButton.appendTo(deliveryTypesHeading);
                    let warehousesSelect = $('<select name="novaposhta_warehouses" tabindex="1" class="fn_select_warehouses_novaposhta" style="width: 100%;" disabled></select>');

                    for (let warehouseKey in data.warehouses) {
                        let warehouse = data.warehouses[warehouseKey];
                        if (deliveryType.typeRefs.includes(warehouse.typeRef)) {
                            let option = $('<option value="' + warehouse.name + '" ' +
                                'data-warehouse_ref="' + warehouse.ref + '"' +
                                (selectedWarehouseRef && selectedWarehouseRef == warehouse.ref ? 'selected' : '') +
                                '>' + warehouse.name + '</option>')
                            warehousesSelect.append(option);
                            if (selectedWarehouseRef && selectedWarehouseRef == warehouse.ref) {
                                selectedDeliveryType = 'fn_delivery_type_' + deliveryTypeKey;
                            }
                        }
                    }
                    let selectWrap = $('<div class="fn_delivery_type_' + deliveryTypeKey + '"></div>')

                    selectWrap.hide();
                    warehousesSelect.attr('disabled', false);

                    warehousesSelect.appendTo(selectWrap)
                        .select2(whsParams);

                    selectWrap.appendTo(deliveryTypesContent);
                }

                // Відмічаємо активний таб типу доставки
                // Перший таб, або який був вибраний для попереднього міста, якщо для нового він теж доступний
                let selectedDeliveryTypeButton = deliveryTypesHeading.find('a[data-delivery_type="' + selectedDeliveryType + '"]');
                if (selectedDeliveryTypeButton.length > 0) {
                    selectedDeliveryTypeButton.trigger('click');
                } else {
                    deliveryTypesHeading.children('a').first().trigger('click');
                }
                if (deliveryTypesHeading.children().length > 1) {
                    deliveryTypesHeading.show();
                } else {
                    deliveryTypesHeading.hide();
                }
            } else {
                warehousesBlock.hide();
                warehousesBlock.find('.fn_select_warehouses_novaposhta')
                    .html('')
                    .attr('disabled', true);
            }

            calc_delivery_price();
        }
    });
}

function changeDeliveryType()
{
    let activeDeliveryTypeButton = $(this);
    let activeDelivery = $('input[name="delivery_id"]:checked');
    let deliveryBlock = activeDelivery.closest('.delivery__item');
    let deliveryTypesBlock = deliveryBlock.find('.np_delivery_types_block');
    let deliveryTypesHeading = deliveryTypesBlock.find('.np_delivery_types_heading');
    let deliveryTypesContent = deliveryTypesBlock.find('.np_delivery_types_content');
    $('.fn_select_warehouses_novaposhta').attr('disabled', true);
    deliveryTypesContent.children().hide();
    deliveryTypesHeading.children().removeClass('active');

    deliveryTypesContent.find('.' + activeDeliveryTypeButton.data('delivery_type'))
        .show()
        .find('select')
        .attr('disabled', false)
        .trigger('change');
    activeDeliveryTypeButton.addClass('active');

    return false;
}

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
    let delivery_id = active_delivery.val();
    let city_ref = delivery_block.find('input[name="novaposhta_delivery_city_id"]').val();

    let redelivery = 0;
    
    if (delivery_block.find('input[name="novaposhta_redelivery"]').is(':checked')){
        redelivery = delivery_block.find('input[name="novaposhta_redelivery"]').val();
    }

    if (city_ref) {
        delivery_block.find('input[name="novaposhta_delivery_city_id"]').val(city_ref);

        price_elem.text(okay.np_cart_calculate);
        $('#fn_total_delivery_price').text(okay.np_cart_calculate);
        term_elem.text('');

        delivery_block.find('input[name="novaposhta_delivery_price"]').val('');
        delivery_block.find('input[name="novaposhta_delivery_term"]').val('');
        $.ajax({
            url: okay.router['OkayCMS_NovaposhtaCost_calc'],
            data: {city: city_ref, redelivery: redelivery, delivery_id: delivery_id},
            dataType: 'json',
            success: function(data) {
                if (data.hasOwnProperty('price_response')) {
                    if (data.price_response.success) {
                        price_elem.text(data.price_response.price_formatted);
                        delivery_block.find('input[name="novaposhta_delivery_price"]').val(data.price_response.price);
                        delivery_block.find('input[name="delivery_id"]').data('total_price', data.price_response.cart_total_price)
                            .data('delivery_price', data.price_response.price);

                        okay.change_payment_method();
                    }
                }
                
                if (data.hasOwnProperty('term_response') && data.term_response.success) {
                    delivery_block.find('input[name="novaposhta_delivery_term"]').val(data.term_response.term);
                    term_elem.text(data.term_response.term);
                    term_elem.parent().show();
                } else {
                    term_elem.parent().hide();
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
        $('input[name="novaposhta_delivery_warehouse_id"]').val($(this).children(':selected').data('warehouse_ref'));
    }
}