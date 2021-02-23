{* Feedback page *}

<div class="block">
    {* The page heading *}
    <div class="block__header block__header--boxed block__header--border">
        <h1 class="block__heading">
            <span>{if $page->name_h1|escape}{$page->name_h1|escape}{else}{$page->name|escape}{/if}</span>
        </h1>
    </div>

    {* The page content *}
    <div class="block__body block--boxed block--border">
        <div class="row">
            {if $description}
                <div class="col-lg-6">
                    <div class="block">
                        <div class="block__description">{$description}</div>
                    </div>
                </div>
            {/if}
            <div class="col-lg-6">
                {* Form heading *}
                {if $message_sent}
                    <div class="form form--boxed">
                        <div class="form__header">
                            <div class="form__title">
                                {include file="svg.tpl" svgId="comment_icon"}
                                <span data-language="feedback_feedback">{$lang->feedback_feedback}</span>
                            </div>
                        </div>
                        <div class="message_success">
                            <b>{$request_data.name|escape},</b> <span data-language="feedback_message_sent">{$lang->feedback_message_sent}.</span>
                        </div>
                    </div>
                {else}
                {* Feedback form *}
                <form id="captcha_id" method="post" class="fn_validate_feedback form form--boxed">
                    {if $settings->captcha_type == "v3"}
                        <input type="hidden" class="fn_recaptcha_token fn_recaptchav3" name="recaptcha_token" />
                    {/if}

                    <div class="form__header">
                        <div class="form__title">
                            {include file="svg.tpl" svgId="comment_icon"}
                            <span data-language="feedback_feedback">{$lang->feedback_feedback}</span>
                        </div>

                        {* Form error messages *}
                        {if $error}
                            <div class="message_error">
                                {if $error=='captcha'}
                                <span data-language="form_error_captcha">{$lang->form_error_captcha}</span>
                                {elseif $error=='empty_name'}
                                <span data-language="form_enter_name">{$lang->form_enter_name}</span>
                                {elseif $error=='empty_email'}
                                <span data-language="form_enter_email">{$lang->form_enter_email}</span>
                                {elseif $error=='empty_text'}
                                <span data-language="form_enter_message">{$lang->form_enter_message}</span>
                                {/if}
                            </div>
                        {/if}
                    </div>

                    <div class="form__body">
                        {* User's name *}
                        <div class="form__group">
                            <input class="form__input form__placeholder--focus" value="{if $request_data.name}{$request_data.name|escape}{elseif $user->name}{$user->name|escape}{/if}" name="name" type="text" data-language="form_name"/>
                            <span class="form__placeholder">{$lang->form_name}*</span>
                        </div>

                        {* User's email *}
                        <div class="form__group">
                            <input class="form__input form__placeholder--focus" value="{if $request_data.email}{$request_data.email|escape}{elseif $user->email}{$user->email|escape}{/if}" name="email" type="text" data-language="form_email"/>
                            <span class="form__placeholder">{$lang->form_email}</span>
                        </div>

                        {* User's comment *}
                        <div class="form__group">
                            <textarea class="form__textarea form__placeholder--focus" rows="3" name="message" data-language="form_enter_message">{$request_data.message|escape}</textarea>
                            <span class="form__placeholder">{$lang->form_enter_message}*</span>
                        </div>
                    </div>

                    <div class="form__footer">
                        {* Captcha *}
                        {if $settings->captcha_feedback}
                            {if $settings->captcha_type == "v2"}
                            <div class="captcha" style="">
                                <div id="recaptcha1"></div>
                            </div>
                            {elseif $settings->captcha_type == "default"}
                            {get_captcha var="captcha_feedback"}
                            <div class="captcha">
                                <div class="secret_number">{$captcha_feedback[0]|escape} + ? =  {$captcha_feedback[1]|escape}</div>
                                <span class="form__captcha">
                                        <input class="form__input form__input_captcha form__placeholder--focus" type="text" name="captcha_code" value="" data-language="form_enter_captcha"/>
                                        <span class="form__placeholder">{$lang->form_enter_captcha}*</span>
                                    </span>
                            </div>
                            {/if}
                        {/if}
                        <input type="hidden" name="feedback" value="1">

                        {* Submit button *}
                        <button class="form__button button--blick g-recaptcha" type="submit" name="feedback" {if $settings->captcha_type == "invisible"}data-sitekey="{$settings->public_recaptcha_invisible}" data-badge='bottomleft' data-callback="onSubmit"{/if} value="{$lang->form_send}">
                            <span data-language="form_send">{$lang->form_send}</span>
                        </button>
                    </div>
                </form>
                {/if}
            </div>
        </div>
    </div>
</div>

{* Map *}
{if $settings->iframe_map_code}
<div class="block block--boxed block--border">
    <div class="ya_map">
        {$settings->iframe_map_code}
    </div>
</div>
{/if}

