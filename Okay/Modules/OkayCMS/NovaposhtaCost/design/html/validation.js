$(function(){
    if ($('.fn_validate_cart').length > 0) {
        $('.fn_validate_cart input[name="novaposhta_city"]').rules('add', {
            required: true
        });
        $('.fn_validate_cart input[name="novaposhta_street"]').rules('add', {
            required: true
        });
        $('.fn_validate_cart input[name="novaposhta_house"]').rules('add', {
            required: true
        });
        $('.fn_validate_cart select[name="novaposhta_warehouses"]').rules('add', {
            required: true
        });
    }
});