<script>
    let baseOnloadCallback = onloadCallback;
    var fastOrderRecaptcha;
    onloadCallback = function() {
        baseOnloadCallback();
        if($('#recaptcha_fast_order').length>0){
            fastOrderRecaptcha = grecaptcha.render('recaptcha_fast_order', {
                'sitekey' : "{$settings->public_recaptcha|escape}"
            });
        }
    };
    
    var resetFastOrderCaptcha = function() {
        grecaptcha.reset(fastOrderRecaptcha);
    }
    
</script>