{* Password remind page *}

{* The page title *}
{$meta_title = $lang->password_remind_title scope=global}

<div class="block">
    {* The page heading *}
    <div class="block__header block__header--boxed block__header--border">
        <h1 class="block__heading"><span data-language="password_remind_header">{$lang->password_remind_header}</span></h1>
    </div>

    <div class="block block--boxed block--border">
        {if $email_sent}
            <div>
                <span data-language="password_remind_on">{$lang->password_remind_on}</span> <b>{$email|escape}</b> <span data-language="password_remind_letter_sent">{$lang->password_remind_letter_sent}.</span>
            </div>
        {else}
        <div class="f_row flex-lg-row align-items-md-start">

        </div>
            <div class="form_wrap f_col-lg-6">
                <form method="post" class="form form--boxed">
                    <div class="form__header">
                        <div class="form__title">
                            <span class="label_block" data-language="password_remind_enter_your_email">{$lang->password_remind_enter_your_email}</span>
                        </div>
                    </div>
                    <div class="form__body">
                        {* Form error messages *}
                        {if $error}
                            <div class="message_error">
                                {if $error == 'user_not_found'}
                                    <span data-language="password_remind_user_not_found">{$lang->password_remind_user_not_found}</span>
                                {else}
                                    {$error|escape}
                                {/if}
                            </div>
                        {/if}
                        <div class="form__group">
                            <input id="password_remind" class="form__input form__placeholder--focus" type="text" name="email" value="{$request_data.email|escape}" data-language="form_email" required>
                            <span class="form__placeholder">{$lang->form_email}*</span>
                        </div>
                    </div>
                    <div class="form__footer">
                        {* Submit button *}
                        <button type="submit" class="form__button button--blick" value="{$lang->password_remind_remember}">
                            <span data-language="password_remind_remember">{$lang->password_remind_remember}</span>
                        </button>
                    </div>
                </form>
            </div>
        {/if}
    </div>
</div>