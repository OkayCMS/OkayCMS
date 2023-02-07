<script>
    let baseOnloadCallback = onloadCallback;
    var fastOrderRecaptcha;
    onloadCallback = function() {
        baseOnloadCallback();
        if(document.querySelector("#recaptcha_fast_order") !== null){
            fastOrderRecaptcha = grecaptcha.render('recaptcha_fast_order', {
                'sitekey' : "{$settings->public_recaptcha|escape}"
            });
        }
    };
    
    var resetFastOrderCaptcha = function() {
        grecaptcha.reset(fastOrderRecaptcha);
    }
    
</script>