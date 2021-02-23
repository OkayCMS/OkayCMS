$(document).on('click', '.fn_fast_order_button', function (e) {
    e.preventDefault();

    let variant,
        form_obj = $(this).closest("form.fn_variants");

    $("#fast_order_product_name").html($(this).data('name'));
    if (form_obj.find('input[name=variant]:checked').length > 0) {
        variant = form_obj.find('input[name=variant]:checked').val();
    }

    if (form_obj.find('select[name=variant]').length > 0) {
        variant = form_obj.find('select').val();
    }

    $("#fast_order_variant_id").val(variant);

    $.fancybox.open({
        src: '#fn_fast_order',
        type : 'inline'
    });
});

function sendAjaxFastOrderForm() {
    
    let $form      = $("#fn_fast_order"),
        action     = $form.attr('action'),
        $errorBlock = $form.find('.fn_fast_order_errors');

    $.ajax({
        url: action,
        type: 'post',
        data: $form.serialize(),
        dataType: 'json'
    }).done(function(response) {
        if (response.hasOwnProperty('success') && response.hasOwnProperty('redirect_location')) {
            window.location = response.redirect_location;
        } else if (response.hasOwnProperty('errors')) {

            if (typeof resetFastOrderCaptcha === "function") {
                resetFastOrderCaptcha();
            }
            let errorString = '';
            for (let error in response.errors) {
                errorString += '<div>' + response.errors[error] + '</div>';
            }
            $errorBlock.html(errorString).show();
            
        }
    });
    
}


