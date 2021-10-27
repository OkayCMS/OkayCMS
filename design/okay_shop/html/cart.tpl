{* The cart page template *}
<div class="cart d-flex flex-column">
    {* The cart header *}
    <div class="cart_header">
        <div class="container">
            <div class="cart_header__inner d-flex align-items-center flex-nowrap justify-content-between">
                {* Logo *}
                {if !empty({$settings->site_logo})}
                <a class="cart_header__logo " href="{url_generator route='main'}">
                    {if strtolower(pathinfo($settings->site_logo, $smarty.const.PATHINFO_EXTENSION)) == 'svg'}
                        {$settings->site_logo|read_svg:$config->design_images}
                    {else}
                        <img src="{$rootUrl}/{$config->design_images|escape}{$settings->site_logo|escape}?v={$settings->site_logo_version|escape}" alt="{$settings->site_name|escape}"/>
                    {/if}
                </a>
                {/if}
                {if $settings->site_phones}
                    {foreach $settings->site_phones as $phone}
                        {if $phone@first}
                        <a class="cart_header__phone" href="tel:{preg_replace('~[^0-9\+]~', '', $phone)}">
                            <span>{$phone|escape}</span>
                        </a>
                        {/if}
                    {/foreach}
                {/if}
            </div>
        </div>
    </div>

    {* The cart content *}
    <div class="cart_body">
        <div class="container">
            {if $cart->isEmpty === false}
                <div class="block">
                    <div class="block__header block__header--boxed block__header--border">
                        <h1 class="block__heading">
                            <span data-language="cart_header">{$lang->cart_header}</span>
                        </h1>
                    </div>

                    {if $description}
                        <div class="block block--boxed block--border">
                            {$description}
                        </div>
                    {/if}
                    
                    <div class="block__body">
                        <form id="captcha_id" method="post" name="cart" class="fn_validate_cart">
                            <div class="f_row flex-column align-items-start flex-lg-row">
                                <div class="position_sticky f_col f_col-lg-6 f_col-xl-5">
                                    {* The list of products in the cart *}
                                    <div class="block--cart_purchases block--boxed block--border">
                                        <div class="block__inner">
                                            <div class="h6" data-language="cart_purchase_title">{$lang->cart_purchase_title}</div>

                                            <div id="fn_purchases" class="purchase">
                                                {include file='cart_purchases.tpl'}
                                            </div>

                                            <div class="purchase_detail">

                                                <div id="fn_cart_coupon">
                                                    {include file="cart_coupon.tpl"}
                                                </div>

                                                <div class="purchase_detail__item">
                                                    <div class="purchase_detail__column_name">
                                                        <div class="purchase_detail__name" data-language="cart_order_price">{$lang->cart_order_price}:</div>
                                                    </div>
                                                    <div class="purchase_detail__column_value">
                                                        <div id="fn_total_purchases_price" class="purchase_detail__price">{$cart->total_price|convert} {$currency->sign|escape}</div>
                                                    </div>
                                                </div>

                                                <div id="fn_total_delivery_price_block" class="purchase_detail__item">
                                                    <div class="purchase_detail__column_name">
                                                        <div class="purchase_detail__name" data-language="cart_discount">
                                                            <span data-language="cart_delivery_order_price">{$lang->cart_delivery_order_price}</span>
                                                            <span id="fn_total_separate_delivery"{if !$active_delivery->separate_payment || $active_delivery->is_free_delivery === true} style="display: none;" {/if}> ({$lang->cart_paid_separate})</span>:
                                                        </div>
                                                    </div>
                                                    <div class="purchase_detail__column_value">
                                                        <div id="fn_total_delivery_price" class="purchase_detail__price"{if $active_delivery->is_free_delivery === true} style="display: none;"{/if}>
                                                            {$active_delivery->price|convert} {$currency->sign|escape}
                                                        </div>
                                                        <div id="fn_total_free_delivery" class="purchase_detail__price" data-language="cart_free"{if $active_delivery->is_free_delivery === false} style="display: none;"{/if}>
                                                            {$lang->cart_free}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="purchase_detail__item">
                                                    <div class="purchase_detail__column_name">
                                                        <div class="purchase_detail__name purchase_detail__name--total" data-language="cart_total_price">{$lang->cart_total_price}:</div>
                                                    </div>
                                                    <div class="purchase_detail__column_value">
                                                        <div class="purchase_detail__price purchase_detail__price--total">
                                                            {*Итоговую стоимость выводим с активной доставки*}
                                                            <span><span id="fn_cart_total_price">{$active_delivery->total_price_with_delivery|convert}</span> <span class="currency">{$currency->sign|escape}</span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="position_sticky f_col f_col-lg-6 f_col-xl-7 flex-lg-first">
                                    <div class="block--boxed block--border d-flex justify-content-center">
                                        {if $settings->captcha_type == "v3"}
                                            <input type="hidden" class="fn_recaptcha_token fn_recaptchav3" name="recaptcha_token" />
                                        {/if}
                                        <div class="block__inner">
                                            <div class="h6" data-language="cart_title">{$lang->cart_title}</div>

                                            <div class="block form form_cart form--boxed">
                                                <div class="form__header">
                                                    {* The form heading *}
                                                    <div class="form__title">
                                                        {include file="svg.tpl" svgId="comment_icon"}
                                                        <span data-language="cart_form_header">{$lang->cart_form_header}</span>
                                                    </div>
                                                </div>

                                                <div class="form__body">
                                                    {* Form error messages *}
                                                    {if $error}
                                                        <div class="message_error">
                                                            {if $error == 'empty_name'}
                                                                <span data-language="form_enter_name">{$lang->form_enter_name}</span>
                                                            {elseif $error == 'empty_email'}
                                                                <span data-language="form_enter_email">{$lang->form_enter_email}</span>
                                                            {elseif $error == 'captcha'}
                                                                <span data-language="form_error_captcha">{$lang->form_error_captcha}</span>
                                                            {elseif $error == 'empty_phone'}
                                                                <span data-language="form_error_phone">{$lang->form_error_phone} {$lang->form_error_phone_example} {$phone_example}</span>
                                                            {else}
                                                                <span>{$error|escape}</span>
                                                            {/if}
                                                        </div>
                                                    {/if}
                                                    <div class="f_row">
                                                        {* User's name *}
                                                        <div class="f_col-md-6 f_col-lg-12 f_col-xl-6">
                                                            <div class="form__group ">
                                                                <input class="form__input form__placeholder--focus" name="name" type="text" value="{$request_data.name|escape}" data-language="form_name" >
                                                                <span class="form__placeholder">{$lang->form_name}*</span>
                                                            </div>
                                                        </div>

                                                        {* User's name *}
                                                        <div class="f_col-md-6 f_col-lg-12 f_col-xl-6">
                                                            <div class="form__group ">
                                                                <input class="form__input form__placeholder--focus" name="last_name" type="text" value="{$request_data.last_name|escape}" data-language="form_name" >
                                                                <span class="form__placeholder">{$lang->form_last_name}</span>
                                                            </div>
                                                        </div>

                                                        {* User's phone *}
                                                        <div class="f_col-md-6 f_col-lg-12 f_col-xl-6">
                                                            <div class="form__group">
                                                                <input class="form__input form__placeholder--focus" name="phone" type="text" value="{$request_data.phone|escape}" data-language="form_phone" >
                                                                <span class="form__placeholder">{$lang->form_phone}</span>
                                                            </div>
                                                        </div>

                                                        {* User's email *}
                                                        <div class="f_col-md-6 f_col-lg-12 f_col-xl-6">
                                                            <div class="form__group">
                                                                <input class="form__input form__placeholder--focus" name="email" type="text" value="{$request_data.email|escape}" data-language="form_email" >
                                                                <span class="form__placeholder">{$lang->form_email}*</span>
                                                            </div>
                                                        </div>

                                                        {* User's address *}
                                                        <div class="f_col-md-6 f_col-lg-12 f_col-xl-6">
                                                            <div class="form__group">
                                                                <input class="form__input form__placeholder--focus" name="address" type="text" value="{$request_data.address|escape}" data-language="form_address" >
                                                                <span class="form__placeholder">{$lang->form_address}</span>
                                                            </div>
                                                        </div>

                                                        {* User's message *}
                                                        <div class="f_col-xl-12">
                                                            <div class="form__group form__group--last">
                                                                <textarea class="form__textarea form__placeholder--focus" rows="3" name="comment" data-language="cart_order_comment">{$request_data.comment|escape}</textarea>
                                                                <span class="form__placeholder">{$lang->cart_order_comment}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {* Delivery and Payment *}
                                            <div id="fn_ajax_deliveries">
                                                {include file='cart_deliveries.tpl'}
                                            </div>

                                            <div class="block form form_cart form--boxed">
                                                <div class="form__footer">
                                                    {* Captcha *}
                                                    {if $settings->captcha_cart}
                                                        {if $settings->captcha_type == "v2"}
                                                            <div class="captcha">
                                                                <div id="recaptcha1"></div>
                                                            </div>
                                                        {elseif $settings->captcha_type == "default"}
                                                            {get_captcha var="captcha_cart"}
                                                            <div class="captcha">
                                                                <div class="secret_number">{$captcha_cart[0]|escape} + ? =  {$captcha_cart[1]|escape}</div>
                                                                <div class="form__captcha">
                                                                    <input class="form__input form__input_captcha form__placeholder--focus" type="text" name="captcha_code" value="" />
                                                                    <span class="form__placeholder">{$lang->form_enter_captcha}*</span>
                                                                </div>
                                                            </div>
                                                        {/if}
                                                    {/if}

                                                    <input type="hidden" name="checkout" value="1">
                                                    {* Submit button *}
                                                    <button class="form__button button--blick g-recaptcha" type="submit" name="checkout" {if $settings->captcha_type == "invisible"}data-sitekey="{$settings->public_recaptcha_invisible}" data-badge='bottomleft' data-callback="onSubmit"{/if} value="{$lang->cart_checkout}">
                                                        <span data-language="cart_button">{$lang->cart_button}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            {else}
                <div class="block">
                    {* The page heading *}
                    <h1 class="h1"><span data-language="cart_header">{$lang->cart_header}</span></h1>

                    <p class="block padding" data-language="cart_empty">{$lang->cart_empty}</p>
                </div>
            {/if}
        </div>
    </div>

    {* The cart footer *}
    <div class="cart_footer">
        <div class="container">
            <div class="cart_footer__copyright f_col-md flex-md-first d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <span>© {$smarty.now|date_format:"%Y"}</span>
                    <span data-language="index_copyright">{$lang->index_copyright}</span>
                </div>
                <a href="https://okay-cms.com" rel="noreferrer" target="_blank" title="OkayCms">{include file="svg.tpl" svgId="okaycms"}</a>
            </div>
        </div>
    </div>
</div>
