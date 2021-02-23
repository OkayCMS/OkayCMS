<div class="hidden">
    <form id="fn_fast_order" class="form form--boxed popup popup_animated fn_validate_fast_order" method="post" action="{url_generator route="OkayCMS.FastOrder.CreateOrder" absolute=1}"
          {if $settings->captcha_type == "invisible"}
              onsubmit="grecaptcha.execute(window.fastOrderRecaptcha); return false"
          {else}
              onsubmit="sendAjaxFastOrderForm(); return false"
          {/if}
    >
        {* The form heading *}
        <div class="form__header">
            <div class="form__title">
                <span data-language="fast_order">{$lang->fast_order}</span>
            </div>
        </div>

        {if $settings->captcha_type == "v3"}
            <input id="fn_fast_order_recaptcha_token" class="fn_recaptchav3" type="hidden" name="recaptcha_token" />
        {/if}

        <div class="form__body">
            <input id="fast_order_variant_id" value="" name="variant_id" type="hidden"/>
            <input value="" name="amount" type="hidden"/>
            <input type="hidden" name="IsFastOrder" value="true">

            <div class="message_error fn_fast_order_errors" style="display: none"></div>

            <div id="fast_order_product_name" class="h6"></div>

            <div class="form__group">
                <input class="fn_validate_fast_name form__input form__placeholder--focus" type="text" name="name" value="" />
                <span class="form__placeholder" data-language="form_name">{$lang->form_name}*</span>
            </div>

            <div class="form__group">
                <input class="fn_validate_fast_name form__input form__placeholder--focus" type="text" name="last_name" value="" />
                <span class="form__placeholder" data-language="form_name">{$lang->form_last_name}</span>
            </div>

            <div class="form__group">
                <input  class="fn_validate_fast_phone form__input form__placeholder--focus" type="text" name="phone" value="" />
                <span class="form__placeholder" data-language="form_phone">{$lang->form_phone}*</span>
            </div>
         </div>

        <div class="form__footer">
            {* Captcha *}
            {if $settings->captcha_fast_order}
                {if $settings->captcha_type == "v2" || $settings->captcha_type == "invisible"}
                    <div class="captcha">
                        <div id="recaptcha_fast_order"></div>
                    </div>
                {elseif $settings->captcha_type == "default"}
                    {get_captcha var="captcha_fast_order"}
                    <div class="captcha">
                        <div class="secret_number">{$captcha_fast_order[0]|escape} + ? =  {$captcha_fast_order[1]|escape}</div>
                        <span class="form__captcha">
                        <input class="form__input form__input_captcha form__placeholder--focus" type="text" name="captcha_code" value="" >
                        <span class="form__placeholder">{$lang->form_enter_captcha}*</span>
                    </span>
                    </div>
                {/if}
            {/if}

            <input class="form__button button--blick fn_fast_order_submit" type="submit" name="checkout" data-language="callback_order" value="{$lang->callback_order}"/>
        </div>
     </form>
</div>