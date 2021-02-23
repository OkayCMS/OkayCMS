{* Coupon *}
{if $coupon_request}
    <div class="coupon">
        <div class="fn_switch coupon__title" data-language="cart_coupon">{$lang->cart_coupon}</div>
        {* Coupon error messages *}
        {if $coupon_error}
            <div class="message_error">
                {if $coupon_error == 'invalid'}
                    {$lang->cart_coupon_error}
                {elseif $coupon_error == 'empty'}
                    {$lang->cart_empty_coupon_error}
                {/if}
            </div>
        {/if}

        {if $cart->coupon->min_order_price > 0}
            <div class="message_success">
                {$lang->cart_coupon} {$cart->coupon->code|escape} {$lang->cart_coupon_min} {$cart->coupon->min_order_price|convert} {$currency->sign|escape}
            </div>
        {/if}

        <div class="d-flex align-items-center coupon__group">
            <div class="form__group form__group--coupon {if !$coupon_error}filled{/if}">
                <input class="fn_coupon form__input form__input--coupon form__placeholder--focus" type="text" name="coupon_code" value="{$smarty.session.coupon_code|escape}">
                <span class="form__placeholder">{$lang->cart_coupon}</span>
            </div>
            <input class="form__button form__button--coupon fn_sub_coupon" type="button" value="{$lang->cart_purchases_coupon_apply}">
        </div>
    </div>

    {if !empty($cart->discounts)}
        {foreach $cart->discounts as $discount}
            <div class="purchase_detail__item">
                <div class="purchase_detail__column_name">
                    <div class="purchase_detail__name">{$discount->name}</div>
                </div>
                <div class="purchase_detail__column_value">
                    <div class="purchase_detail__price">
                        <i>{$discount->percentDiscount|string_format:"%.2f"} %</i>
                        &minus; {$discount->absoluteDiscount|convert} <span class="currency">{$currency->sign|escape}</span>
                    </div>
                </div>
            </div>
        {/foreach}
    {/if}
{/if}
