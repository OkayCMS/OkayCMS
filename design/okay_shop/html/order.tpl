{* Order page *}

<div class="block">
    <div class="block__header block__header--boxed block__header--border">
        <div class="block__title block__title--order">
            {include file="svg.tpl" svgId="success_icon"}
            <span data-language="order_greeting">{$lang->order_greeting}</span>
            <span class="order_number">№ {$order->id}</span>
            <span data-language="order_success_issued">{$lang->order_success_issued}</span>
        </div>
    </div>

    <div class="block__body">
        <div class="f_row flex-column flex-lg-row" data-sticky-container>
            <div class="sticky f_col f_col-lg-6 f_col-xl-5">
                <div class="fn_cart_sticky block--cart_purchases block--boxed block--border" data-margin-top="15" data-sticky-for="1024" data-sticky-class="is-sticky">
                    <div class="order_boxeded">
                        <div class="h6" data-language="cart_purchase_title">{$lang->cart_purchase_title}</div>

                        <div class="purchase">
                            {foreach $purchases as $purchase}
                                <div class="purchase__item d-flex align-items-start">
                                    {* Product image *}
                                    <div class="purchase__image d-flex">
                                        <a href="{url_generator route='product' url=$purchase->product->url}">
                                            {if $purchase->product->image}
                                            <picture>
                                                {if $settings->support_webp}
                                                    <source type="image/webp" data-srcset="{$purchase->product->image->filename|resize:70:70}.webp">
                                                {/if}
                                                <source data-srcset="{$purchase->product->image->filename|resize:70:70}">
                                                <img class="lazy" data-src="{$purchase->product->image->filename|resize:70:70}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$purchase->product->name|escape}" title="{$purchase->product->name|escape}"/>
                                            </picture>
                                            {else}
                                                <div class="purchase__no_image d-flex align-items-start">
                                                    {include file="svg.tpl" svgId="no_image"}
                                                </div>
                                            {/if}
                                        </a>
                                    </div>
                                    <div class="purchase__content">
                                        {* Product name *}
                                        <div class="purchase__name">
                                            <a class="purchase__name_link" href="{url_generator route="product" url=$purchase->product->url}">{$purchase->product_name|escape}</a>
                                            <i>{$purchase->variant_name|escape}</i>
                                            {if $purchase->variant->stock == 0}<span class="preorder_label">{$lang->product_pre_order}</span>{/if}

                                        </div>
                                        <div class="purchase__group">
                                            {* Price per unit *}
                                            <div class="purchase__price">
                                                <div class="purchase__group_title hidden-xs-down">
                                                    <span data-language="cart_head_price">{$lang->cart_head_price}</span>
                                                </div>
                                                <div class="purchase__group_content {if $purchase->discounts} price--red{/if}">
                                                    <span class="hidden-xs-down">{($purchase->price)|convert} </span>
                                                    <span class="currency hidden-xs-down">{$currency->sign}</span> 
                                                    {if $purchase->variant->units}<span class="hidden-xs-down">/ {$purchase->variant->units|escape}</span>{/if}
                                                    {if $purchase->discounts}
                                                        <a href="javascript:;" class="discount_tooltip" title="{$lang->purchase_discount__tooltip}" data-src="#fn_purchase_discount_detail_{$purchase->variant->id}" data-fancybox="hello_{$purchase->variant->id}">{include file="svg.tpl" svgId="sale_icon"}</a>
                                                    {/if}
                                                </div>
                                                <div class="hidden">
                                                    <div id="fn_purchase_discount_detail_{$purchase->variant->id}" class="purchase_discount_detail popup popup_animated">
                                                        {* The form heading *}
                                                        <div class="form__header">
                                                            <div class="form__title">
                                                                {include file="svg.tpl" svgId="sale_icon"}
                                                                <span data-language="purchase_discount__popup_title">{$lang->purchase_discount__popup_title}</span>
                                                            </div>
                                                        </div>
                                                        <div class="form__body">
                                                            {foreach $purchase->discounts as $discount}
                                                                <div class="purchase_discount__item">
                                                                    <div class="purchase_discount__name">
                                                                        <span>{$discount->name}</span>
                                                                    </div>
                                                                    <div class="purchase_discount__group">
                                                                        <div class="purchase_discount__price_before">
                                                                            <div class="purchase_discount__title">
                                                                                <span data-language="purchase_discount__price">{$lang->purchase_discount__price}</span>
                                                                            </div>
                                                                            <div class="purchase_discount__group_content">
                                                                                <span>{$discount->priceBeforeDiscount}</span>
                                                                                <span class="currency">{$currency->sign|escape}</span>
                                                                            </div>   
                                                                        </div>
                                                                        <div class="purchase_discount__discount">
                                                                            <div class="purchase_discount__title">
                                                                                <span data-language="purchase_discount__discount">{$lang->purchase_discount__discount}</span>
                                                                            </div>
                                                                            <div class="purchase_discount__group_content purchase_detail__price">
                                                                                <i>{$discount->percentDiscount|string_format:"%.2f"} %</i>
                                                                                &minus; {$discount->absoluteDiscount|convert} <span class="currency">{$currency->sign|escape}</span>
                                                                            </div>
                                                                        </div>
                                                                        <div class="purchase_discount__price_after">
                                                                            <div class="purchase__group_title">
                                                                                <span data-language="purchase_discount__total">{$lang->purchase_discount__total}</span>
                                                                            </div>
                                                                            <div class="purchase_discount__group_content">
                                                                                <span>{$discount->priceAfterDiscount}</span>
                                                                                <span class="currency">{$currency->sign|escape}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            {/foreach}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="purchase__amount">
                                                <div class="purchase__group_title hidden-xs-down">
                                                    <span data-language="cart_head_amoun">{$lang->cart_head_amoun}</span>
                                                </div>
                                                <div class="fn_product_amount purchase__group_content d-flex justify-content-center align-items-center">
                                                    <span class="order_purchase_count">x{$purchase->amount|escape}</span>
                                                </div>
                                            </div>
                                            <div class="purchase__price_total">
                                                <div class="purchase__group_title hidden-xs-down">
                                                    <span data-language="cart_head_total">{$lang->cart_head_total}</span>
                                                </div>
                                                <div class="purchase__group_content">{($purchase->price*$purchase->amount)|convert} <span class="currency">{$currency->sign}</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>

                        <div class="purchase_detail">
                            {* Discounts *}
                            {if $discounts}
                                {foreach $discounts as $discount}
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

                            {if !$delivery->hide_front_delivery_price && ($order->separate_delivery || !$order->separate_delivery && $order->delivery_price > 0)}
                                <div class="purchase_detail__item">
                                    <div class="purchase_detail__column_name">
                                        <div class="purchase_detail__name">{$delivery->name|escape}:</div>
                                    </div>
                                    <div class="purchase_detail__column_value">
                                        <div class="purchase_detail__price">
                                            <span>{$order->delivery_price|convert} <span class="currency"> {$currency->sign|escape}</span></span>
                                        </div>
                                    </div>
                                </div>
                            {/if}

                            <div class="purchase_detail__item">
                                <div class="purchase_detail__column_name">
                                    <div class="purchase_detail__name purchase_detail__name--total" data-language="cart_total_price">{$lang->cart_total_price}:</div>
                                </div>
                                <div class="purchase_detail__column_value">
                                    <div class="purchase_detail__price purchase_detail__price--total">
                                        <span>{$order->total_price|convert} <span class="currency">{$currency->sign|escape}</span></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky f_col f_col-lg-6 f_col-xl-7 flex-lg-first">
                <div class="fn_cart_sticky block--boxed block--border d-flex justify-content-center" data-margin-top="15" data-sticky-for="1024" data-sticky-class="is-sticky">
                    <div class="order_boxeded">
                        {if !$order->paid}
                            {if $payment_methods && !$payment_method && $order->total_price>0}
                                <div class="block form--boxed form form_cart">
                                    {* Payments *}
                                    <div class="h6">
                                        <span data-language="order_payment_details">{$lang->order_payment_details}</span>
                                    </div>
                                    <div class="delivery padding block">
                                        <form method="post">
                                            <div class="delivery form__group">
                                                {foreach $payment_methods as $payment_method}
                                                    <div class="delivery__item">
                                                        <label class="checkbox delivery__label{if $payment_method@first} active{/if}">
                                                            <input class="checkbox__input delivery__input"  type="radio" name="payment_method_id"{if $payment_method@first} checked{/if} value="{$payment_method->id}" {if $delivery@first && $payment_method@first} checked{/if} id="payment_{$delivery->id}_{$payment_method->id}">

                                                            <svg class="checkbox__icon" viewBox="0 0 20 20">
                                                                <path class="checkbox__mark" fill="none" d="M4 10 l5 4 8-8.5"></path>
                                                            </svg>

                                                            <div class="delivery__name">
                                                                {$payment_method->name|escape} {$lang->cart_deliveries_to_pay}
                                                                <span class="delivery__name_price">{$order->total_price|convert:$payment_method->currency_id} {$all_currencies[$payment_method->currency_id]->sign}</span>
                                                            </div>

                                                            {if $payment_method->image}
                                                                <div class="delivery__image">
                                                                    <picture>
                                                                        {if $settings->support_webp}
                                                                            <source type="image/webp" srcset="{$payment_method->image|resize:80:30:false:$config->resized_payments_dir}.webp">
                                                                        {/if}
                                                                        <source srcset="{$payment_method->image|resize:80:30:false:$config->resized_payments_dir}">
                                                                        <img class="lazy" data-src="{$payment_method->image|resize:80:30:false:$config->resized_payments_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$purchase->product->name|escape}" title="{$purchase->product->name|escape}"/>
                                                                    </picture>
                                                                </div>
                                                            {/if}
                                                        </label>

                                                        {if $payment_method->description}
                                                            <div class="delivery__description">
                                                                {$payment_method->description}
                                                            </div>
                                                        {/if}
                                                    </div>
                                                {/foreach}
                                            </div>

                                            <input type="submit" data-language="cart_checkout" value="{$lang->cart_checkout}" name="checkout" class="form__button">
                                        </form>
                                    </div>
                                </div>
                            {elseif $payment_method}
                                <div class="block form--boxed form form_cart">

                                    {* Payments *}
                                    <div class="h6">
                                        <span data-language="order_payment_details">{$lang->order_payment_details}</span>
                                    </div>
                                    {* Selected payment *}
                                    <div class="block_selected_payment">
                                        <div class="order_payment">
                                            <div class="order_payment__title">
                                                <span data-language="order_payment">{$lang->order_payment}:</span>
                                                <span class="order_payment__name">{$payment_method->name|escape}</span>
                                            </div>
                                            <form class="order_payment__form" method="post">
                                                <input class="order_payment__button" type=submit name='reset_payment_method' data-language="order_change_payment" value='{$lang->order_change_payment}'/>
                                            </form>
                                            {if $payment_method->description}
                                                <div class="order_payment__description">
                                                    {$payment_method->description}
                                                </div>
                                            {/if}

                                            <div class="order_payment__checkout">
                                                {*Payment form is generated by payment module*}
                                                {*payment's form HTML code is in the /payment/ModuleName/form.tpl*}
                                                {checkout_payment_form order_id=$order->id module=$payment_method->module}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/if}

                        {/if}
                        <div class="block form form_cart">
                            <div class="h6" data-language="order_details">{$lang->order_details}</div>
                            {* Order details *}
                            <div class="block padding block__description--style">
                                <table class="order_details">
                                    <tr>
                                        <td>
                                            <span data-language="user_order_status">{$lang->user_order_status}</span>
                                        </td>
                                        <td>
                                            {$order_status->name|escape}
                                            {if $order->paid == 1}
                                                , <span data-language="status_paid">{$lang->status_paid}</span>
                                            {/if}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span data-language="order_date">{$lang->order_date}</span>
                                        </td>
                                        <td>{$order->date|date} <span data-language="order_time">{$lang->order_time}</span> {$order->date|time}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span data-language="order_number_text">{$lang->order_number_text}</span>
                                        </td>
                                        <td>№ {$order->id}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span data-language="order_name">{$lang->order_name}</span>
                                        </td>
                                        <td>{$order->name|escape} {$order->last_name|escape}</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span data-language="order_email">{$lang->order_email}</span>
                                        </td>
                                        <td>{$order->email|escape}</td>
                                    </tr>
                                    {if $order->phone}
                                        <tr>
                                            <td>
                                                <span data-language="order_phone">{$lang->order_phone}</span>
                                            </td>
                                            <td>{$order->phone|phone}</td>
                                        </tr>
                                    {/if}
                                    {if $order->address}
                                        <tr>
                                            <td>
                                                <span data-language="order_address">{$lang->order_address}</span>
                                            </td>
                                            <td>{$order->address|escape}</td>
                                        </tr>
                                    {/if}
                                    {if $order->comment}
                                        <tr>
                                            <td>
                                                <span data-language="order_comment">{$lang->order_comment}</span>
                                            </td>
                                            <td>{$order->comment|escape|nl2br}</td>
                                        </tr>
                                    {/if}
                                    {if $delivery}
                                        <tr>
                                            <td>
                                                <span data-language="order_delivery">{$lang->order_delivery}</span>
                                            </td>
                                            <td>{$delivery->name|escape}</td>
                                        </tr>
                                    {/if}
                                </table>
                            </div>
                        </div>

                        <div class="block form form_cart">
                            <div class="o_notify_v2_content">
                                <div class="o_notify_v2_content_inner" data-language="order_success_text">
                                    <p><strong>{$order->name|escape}</strong>, {$lang->order_success_text}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>