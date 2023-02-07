<script>
    let baseOnloadReCaptchaInvisible = onloadReCaptchaInvisible;
    var fastOrderRecaptcha;
    onloadReCaptchaInvisible = function() {
        baseOnloadReCaptchaInvisible();
        if(document.querySelector("#recaptcha_fast_order") !== null){
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