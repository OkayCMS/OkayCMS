$(function(){
    if ($('.fn_validate_cart').length > 0) {
        $('.fn_validate_cart input[name="novaposhta_city"]').rules('add', {
            required: true,
            messages: {
                required: form_enter_novaposhta_city,
            }
        });
        $('.fn_validate_cart input[name="novaposhta_street"]').rules('add', {
            required: true,
            messages: {
                required: form_enter_novaposhta_street,
            }
        });
        $('.fn_validate_cart input[name="novaposhta_house"]').rules('add', {
            required: true,
            messages: {
                required: form_enter_novaposhta_house,
            }
        });
        $('.fn_validate_cart select[name="novaposhta_warehouses"]').rules('add', {
            required: true,
            messages: {
                required: form_enter_novaposhta_warehouses,
            }
        });
    }
});