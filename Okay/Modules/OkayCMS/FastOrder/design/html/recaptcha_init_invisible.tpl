<script>
    let baseOnloadReCaptchaInvisible = onloadReCaptchaInvisible;
    var fastOrderRecaptcha;
    onloadReCaptchaInvisible = function() {
        baseOnloadReCaptchaInvisible();
        if($('#recaptcha_fast_order').length>0){
            fastOrderRecaptcha = grecaptcha.render('recaptcha_fast_order', {
                'sitekey' : "{$settings->public_recaptcha_invisible|escape}",
                "callback": "sendAjaxFastOrderForm",
                "size":"invisible"
            });
        }
    };

    var resetFastOrderCaptcha = function() {
        grecaptcha.reset(fastOrderRecaptcha);
    }
    
</script>