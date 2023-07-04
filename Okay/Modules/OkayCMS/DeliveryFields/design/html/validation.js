$(function(){
    if ($('.fn_validate_cart').length > 0) {
        $('.fn_validate_cart input[name^="delivery_fields"].required').each(function () {
            let errorText = $(this).data('error_text');
            $(this).rules('add', {
                required: true,
                messages: {
                    required: errorText,
                }
            });
        });
    }
});