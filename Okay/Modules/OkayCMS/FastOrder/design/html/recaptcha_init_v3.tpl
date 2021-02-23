<script>
    var resetFastOrderCaptcha = function() {
        grecaptcha.execute('{$settings->public_recaptcha_v3|escape}', { action: 'cart' })
            .then(function (token) {
                let capture = document.getElementById('fn_fast_order_recaptcha_token');
                capture.value = token;
            });
    };
    
</script>