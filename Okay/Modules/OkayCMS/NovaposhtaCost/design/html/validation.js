function isActiveNovaPoshtaField(element)
{
    return $(element).closest('.delivery__item').find('input[name="delivery_id"]').is(':checked');
}

function attachNovaPoshtaWarehouseValidation(select)
{
    select.rules('add', {
        required: { depends: isActiveNovaPoshtaField },
        messages: { required: form_enter_novaposhta_warehouses }
    });
}

$(function () {
    if ($('.fn_validate_cart').length > 0) {
        $('.fn_validate_cart [data-np-field="door_city"], .fn_validate_cart [data-np-field="warehouse_city"]').rules('add', {
            required: { depends: isActiveNovaPoshtaField },
            messages: { required: form_enter_novaposhta_city }
        });

        $('.fn_validate_cart [data-np-field="street"]').rules('add', {
            required: { depends: isActiveNovaPoshtaField },
            messages: { required: form_enter_novaposhta_street }
        });

        $('.fn_validate_cart [data-np-field="house"]').rules('add', {
            required: { depends: isActiveNovaPoshtaField },
            messages: { required: form_enter_novaposhta_house }
        });

        $('.fn_validate_cart [data-np-field="warehouses"]').each(function () {
            attachNovaPoshtaWarehouseValidation($(this));
        });
    }
});
