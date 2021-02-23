{* Login page *}

{* The page title *}
{$meta_title = $lang->login_title scope=global}

<div class="block">
    {* The page heading *}
    <div class="block__header block__header--boxed block__header--border">
    	<h1 class="block__heading"><span data-language="login_enter">{$lang->login_enter}</span></h1>
    </div>
    <div class="block block--boxed block--border">
        <div class="f_row flex-lg-row align-items-md-start">
            <div class="form_wrap f_col-lg-6">
                <form method="post" class="fn_validate_login form form--boxed">
                    <div class="form__header">
                        <div class="form__title">
                            {include file="svg.tpl" svgId="note_icon"}
                            <span data-language="login_title_form">{$lang->login_title_form}</span>
                        </div>
                    </div>
                    
                    <div class="form__body">
                        {* Form error messages *}
                        {if $error}
                        <div class="message_error">
                            {if $error == 'login_incorrect'}
                            <span data-language="login_error_pass">{$lang->login_error_pass}</span>
                            {elseif $error == 'user_disabled'}
                            <span data-language="login_pass_not_active">{$lang->login_pass_not_active}</span>
                            {else}
                            {$error|escape}
                            {/if}
                        </div>
                        {/if}

                        {* User's email *}
                        <div class="form__group">
                            <input class="form__input form__placeholder--focus" type="text" name="email" value="{$request_data.email|escape}"  data-language="form_email" />
                            <span class="form__placeholder">{$lang->form_email}*</span>
                        </div>
                        {* User's password *}
                        <div class="form__group">
                            <input class="form__input form__placeholder--focus" type="password" name="password" value="" data-language="form_password" />
                            <span class="form__placeholder">{$lang->form_password}*</span>
                            {*  Remind password link *}
                            <a class="password_remind" href="{url_generator route="password_remind"}">
                                <span data-language="login_remind">{$lang->login_remind}</span>
                                {include file="svg.tpl" svgId="arrow_right2"}
                            </a>
                        </div>
                    </div>
                    <div class="form__footer">
                        {* Submit button *}
                        <button type="submit" class="form__button button--blick" name="login" value="{$lang->login_sign_in}">
                            <span data-language="login_sign_in">{$lang->login_sign_in}</span>
                        </button>

                        <div id="uLogin" data-ulogin="display=panel;theme=flat;fields=first_name,last_name,email;providers=facebook,google;"></div>
                    </div>
                </form>
            </div>
            <div class="f_col-lg-6">
                <div class="block_explanation">
                    <div class="block_explanation__header">
                        <span data-language="login_text">{$lang->login_text}</span>
                    </div>
                    <div class="block_explanation__body block__description">
                        {$description}
                    </div>
                    {* Link to registration *}
                    <div class="form__footer">
                        <a href="{url_generator route="register"}" class="form__button button--blick" data-language="login_registration">{$lang->login_registration}</a>
                    </div>
                </div>
             </div>
        </div>
    </div>
</div>
