{* Callback form *}
<div class="hidden">
    <form id="fn_callback" class="form form--boxed popup popup_animated fn_validate_callback" method="post">

        {if $settings->captcha_type == "v3"}
            <input type="hidden" class="fn_recaptcha_token fn_recaptchav3" name="recaptcha_token" />
        {/if}

        {* The form heading *}
        <div class="form__header">
            <div class="form__title">
                {include file="svg.tpl" svgId="support_icon"}
                <span data-language="callback_header">{$lang->callback_header}</span>
            </div>
        </div>

        <div class="form__body">
            {if $call_error}
            <div class="message_error">
                {if $call_error=='captcha'}
                    <span data-language="form_error_captcha">{$lang->form_error_captcha}</span>
                {elseif $call_error=='empty_name'}
                    <span data-language="form_enter_name">{$lang->form_enter_name}</span>
                {elseif $call_error=='empty_phone'}
                    <span data-language="form_enter_phone">{$lang->form_enter_phone}</span>
                {elseif $call_error=='empty_comment'}
                    <span data-language="form_enter_comment">{$lang->form_enter_comment}</span>
                {else}
                    <span>{$call_error|escape}</span>
                {/if}
            </div>
            {/if}

            {* User's name *}
            <div class="form__group">
                <input class="form__input form__placeholder--focus" type="text" name="callback_name" value="{if $request_data.callback_name}{$request_data.callback_name|escape}{elseif $user->name}{$user->name|escape}{/if}" data-language="form_name">
                <span class="form__placeholder">{$lang->form_name}*</span>
            </div>

            {* User's phone *}
            <div class="form__group">
                <input class="form__input form__placeholder--focus" type="text" name="callback_phone" value="{if $request_data.callback_phone}{$request_data.callback_phone|escape}{elseif $user->phone}{$user->phone|phone}{/if}" data-language="form_phone">
                <span class="form__placeholder">{$lang->form_phone}*</span>
            </div>

            {* User's message *}
            <div class="form__group">
                <textarea class="form__textarea form__placeholder--focus" rows="3" name="callback_message" data-language="form_enter_message">{$request_data.callback_message|escape}</textarea>
                <span class="form__placeholder">{$lang->form_enter_message}</span>
            </div>
        </div>

        <div class="form__footer">
            {* Captcha *}
            {if $settings->captcha_callback}
            {if $settings->captcha_type == "v2"}
                <div class="captcha">
                    <div id="recaptcha2"></div>
                </div>
            {elseif $settings->captcha_type == "default"}
                {get_captcha var="captcha_callback"}
                <div class="captcha">
                    <div class="secret_number">{$captcha_callback[0]|escape} + ? =  {$captcha_callback[1]|escape}</div>
                    <span class="form__captcha">
                        <input class="form__input form__input_captcha form__placeholder--focus" type="text" name="captcha_code" value="" >
                        <span class="form__placeholder">{$lang->form_enter_captcha}*</span>
                    </span>
                </div>
            {/if}
            {/if}
            <input name="callback" type="hidden" value="1">
            {* Submit button *}
            <button class="form__button button--blick g-recaptcha" type="submit" name="callback" {if $settings->captcha_type == "invisible"}data-sitekey="{$settings->public_recaptcha_invisible}" data-badge='bottomleft' data-callback="onSubmitCallback"{/if} value="{$lang->callback_order}">
                <span data-language="callback_order">{$lang->callback_order}</span>
            </button>
        </div>
    </form>
</div>

{* The modal window after submitting *}
{if $call_sent}
    <div class="hidden">
        <div id="fn_callback_sent" class="popup">
            <div class="popup__heading">
                {include file="svg.tpl" svgId="success_icon"}
                <span data-language="callback_sent_header">{$lang->callback_sent_header}</span>
            </div>
            <div class="popup__description">
                <span data-language="callback_sent_text">{$lang->callback_sent_text}</span>
            </div>
        </div>
    </div>
{/if}
