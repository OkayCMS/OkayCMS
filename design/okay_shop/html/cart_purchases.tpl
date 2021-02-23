{*NOTICE: Обратите внимание, data-total_purchases_price хранится в основной валюте сайта*}
<div class="fn_purchases_wrap" data-total_purchases_price="{$cart->total_price}">
{foreach $cart->purchases as $purchase}
    <div class="purchase__item d-flex align-items-start">
        {* Product image *}
        <div class="purchase__image d-flex">
            <a href="{url_generator route="product" url=$purchase->product->url}">
                {if $purchase->product->image}
                    <img src="{$purchase->product->image->filename|resize:70:70}" alt="{$purchase->product->name|escape}" title="{$purchase->product->name|escape}"/>
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
                <a class="purchase__name_link" href="{url_generator route="product" url=$purchase->product->url}">{$purchase->product->name|escape}</a>
                <i>{$purchase->variant->name|escape}</i>
                {if $purchase->variant->stock == 0}<span class="preorder_label">{$lang->product_pre_order}</span>{/if}
            </div>
            <div class="purchase__group">
                {* Price per unit *}
                <div class="purchase__price">
                    <div class="purchase__group_title hidden-xs-down">
                        <span data-language="cart_head_price">{$lang->cart_head_price}</span>
                    </div>
                    <div class="purchase__group_content{if $purchase->discounts} price--red{/if}">
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
                    <div class="fn_product_amount purchase__group_content{if $settings->is_preorder} fn_is_preorder{/if} amount">
                        <span class="fn_minus amount__minus">&minus;</span>
                        <input class="amount__input" type="text" data-id="{$purchase->variant->id}" name="amounts[{$purchase->variant->id}]" value="{$purchase->amount}" onblur="ajax_change_amount(this, {$purchase->variant->id});" data-max="{$purchase->variant->stock}">
                        <span class="fn_plus amount__plus">&plus;</span>
                    </div>
                </div>
                <div class="purchase__price_total">
                    <div class="purchase__group_title hidden-xs-down">
                        <span data-language="cart_head_total">{$lang->cart_head_total}</span>
                    </div>
                    <div class="purchase__group_content">{$purchase->meta->total_price|convert} <span class="currency">{$currency->sign}</span></div>
                </div>
            </div>
            {* Remove button *}
            <a class="purchase__remove" href="{url_generator route="cart_remove_item" variantId=$purchase->variant->id}" onclick="ajax_remove({$purchase->variant->id});return false;" title="{$lang->cart_remove}">
                {include file='svg.tpl' svgId='remove_icon'}
            </a>
        </div>
    </div>
{/foreach}
</div>