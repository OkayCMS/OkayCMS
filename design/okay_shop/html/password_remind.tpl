<!-- Password remind page -->

{* The page title *}
{$meta_title = $lang->password_remind_title scope=global}

<div class="block">
    {* The page heading *}
    <div class="block__header block__header--boxed block__header--border">
        <h1 class="block__heading"><span data-language="password_remind_header">{$lang->password_remind_header}</span></h1>
    </div>

    <div class="block block--boxed block--border">
        <div class="f_row flex-lg-row align-items-md-start">
            <div class="form_wrap f_col-lg-6">
                {if $recovery_mode}
                    <form method="post" class="form form--boxed">
                        <input type="hidden" name="customer_csrf_token" value="{$customer_csrf_token|escape}">
                        <div class="form__header">
                            <div class="form__title">
                                {include file="svg.tpl" svgId="note_icon"}
                                <span data-language="password_remind_reset_title">{$lang->password_remind_reset_title}</span>
                            </div>
                        </div>
                        <div class="form__body">
                            {if $error}
                                <div class="message_error" role="alert">
                                    {if $error == 'empty_password'}
                                        <span data-language="form_enter_password">{$lang->form_enter_password}</span>
                                    {elseif $error == 'password_wrong'}
                                        <span data-language="password_remind_password_wrong">{$lang->password_remind_password_wrong}</span>
                                    {elseif $error == 'csrf'}
                                        <span data-language="form_error_csrf">{$lang->form_error_csrf}</span>
                                    {else}
                                        {$error|escape}
                                    {/if}
                                </div>
                            {/if}
                            <div class="form__group">
                                <input class="form__input form__placeholder--focus" type="password" name="new_password" value="" autocomplete="new-password" data-language="password_remind_new_password" required>
                                <span class="form__placeholder">{$lang->password_remind_new_password}*</span>
                            </div>
                            <div class="form__group">
                                <input class="form__input form__placeholder--focus" type="password" name="new_password_check" value="" autocomplete="new-password" data-language="password_remind_new_password_check" required>
                                <span class="form__placeholder">{$lang->password_remind_new_password_check}*</span>
                            </div>
                        </div>
                        <div class="form__footer">
                            <button type="submit" class="form__button button--blick" name="reset_password" value="1">
                                <span data-language="password_remind_set_password">{$lang->password_remind_set_password}</span>
                            </button>
                        </div>
                    </form>
                {else}
                    <form method="post" class="form form--boxed">
                        <input type="hidden" name="customer_csrf_token" value="{$customer_csrf_token|escape}">
                        <div class="form__header">
                            <div class="form__title">
                                {include file="svg.tpl" svgId="note_icon"}
                                <span class="label_block" data-language="password_remind_enter_your_email">{$lang->password_remind_enter_your_email}</span>
                            </div>
                        </div>
                        <div class="form__body">
                            {if $email_sent}
                                <div class="message_success">
                                    {include file="svg.tpl" svgId="success_icon"}
                                    <span data-language="password_remind_email_sent_generic">{$lang->password_remind_email_sent_generic}</span>
                                </div>
                            {/if}
                            {if $error}
                                <div class="message_error" role="alert">
                                    {if $error == 'empty_email'}
                                        <span data-language="form_enter_email">{$lang->form_enter_email}</span>
                                    {elseif $error == 'password_remind_invalid_token'}
                                        <span data-language="password_remind_invalid_token">{$lang->password_remind_invalid_token}</span>
                                    {elseif $error == 'csrf'}
                                        <span data-language="form_error_csrf">{$lang->form_error_csrf}</span>
                                    {else}
                                        {$error|escape}
                                    {/if}
                                </div>
                            {/if}
                            <div class="form__group">
                                <input id="password_remind" class="form__input form__placeholder--focus" type="text" name="email" value="{$request_data.email|escape}" autocomplete="email" data-language="form_email" required>
                                <span class="form__placeholder">{$lang->form_email}*</span>
                            </div>
                        </div>
                        <div class="form__footer">
                            <button type="submit" class="form__button button--blick" value="{$lang->password_remind_remember}">
                                <span data-language="password_remind_remember">{$lang->password_remind_remember}</span>
                            </button>
                        </div>
                    </form>
                {/if}
            </div>
            <div class="f_col-lg-6">
                <div class="block_explanation">
                    <div class="block_explanation__header">
                        <span data-language="login_text">{$lang->login_text}</span>
                    </div>
                    <div class="form__footer">
                        <a href="{url_generator route="login"}" class="form__button button--blick" data-language="login_sign_in">{$lang->login_sign_in}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
