{* Account page *}

{* The page title *}
{$meta_title = $lang->user_title scope=global}

<div class="block">
    <div class="tabs tabs--user clearfix">
        {* Sidebar with blog *}
        <div class="sidebar sidebar--user position_sticky d-lg-flex flex-lg-column">
            <div class="sidebar__boxed sidebar__boxed--user">
                <div class="d-flex align-items-center form__profile profile">
                    <div class="profile__image">
                        <div class="profile__icon">
                            {include file="svg.tpl" svgId="comment-user_icon"}
                        </div>
                    </div>
                    <div class="profile__information">
                        <div class="profile__name">
                            <span>{$user->name|escape}</span>
                        </div>
                        {* Logout *}
                        <div class="profile__logout hidden-md-up">
                            <a href="{url_generator route='logout'}" class="d-flex align-items-center">
                                {include file="svg.tpl" svgId="exit_icon"}
                                <span data-language="user_logout">{$lang->user_logout}</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="tabs__navigation tabs__navigation--user">
                    <a class="tabs__link{if empty($active_tab)} selected{/if}" data-history_location="{url_generator route="user"}" href="#user_info">
                        {include file="svg.tpl" svgId="user_account_icon"}
                        <span data-language="user_personal_title">{$lang->user_personal_title}</span>
                    </a>
                    {if $orders}
                        <a class="tabs__link{if $active_tab == 'orders'} selected{/if}" data-history_location="{url_generator route="user_orders"}" href="#user_orders">
                            {include file="svg.tpl" svgId="user_orders_icon"}
                            <span data-language="user_orders_title">{$lang->user_orders_title}</span>
                        </a>
                    {/if}
                    <a class="tabs__link{if $active_tab == 'comments'} selected{/if}" data-history_location="{url_generator route="user_comments"}" href="#user_comments">
                        {include file="svg.tpl" svgId="user_comments_icon"}
                        <span data-language="user_comments_title">{$lang->user_comments_title}</span>
                    </a>
                    {if $wishlist->products|count}
                        <a class="tabs__link{if $active_tab == 'favorites'} selected{/if}" data-history_location="{url_generator route="user_favorites"}" href="#user_wishlist">
                            {include file="svg.tpl" svgId="user_heart_icon"}
                            <span data-language="user_wishlist_title">{$lang->user_wishlist_title}</span>
                        </a>
                    {/if}
                    {get_browsed_products var=browsed_products limit=16}
                    {if $browsed_products}
                    <a class="tabs__link{if $active_tab == 'browsed'} selected{/if}" data-history_location="{url_generator route="user_browsed"}" href="#user_browsed">
                        {include file="svg.tpl" svgId="user_broused_icon"}
                        <span data-language="product_featuuser_browsed_titleres">{$lang->user_browsed_title}</span>
                    </a>
                    {/if}
                    {* Logout *}
                    <span onclick="document.location.href = '{url_generator route="logout"}'" class="button__logout">
                        {include file="svg.tpl" svgId="exit_icon"}
                        <span data-language="user_logout">{$lang->user_logout}</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="user_container position_sticky d-flex flex-column">
            <div class="tabs__content user_container__boxed">
                <div id="user_info" class="tab"{if empty($active_tab)} style="display: block;"{/if}>
                    <div class="fn_switch user_tab__switch active">
                        <div class="block__header block__header--boxed block__header--border">
                            <div class="block__heading h1"><span data-language="user_personal_title">{$lang->user_personal_title}</span></div>
                         </div>
                    </div>
                    <div class="block block--boxed block--border mobile_tab__content">
                        <div class="block__inner">
                            <form method="post" class="fn_validate_register">
                                {if $user_updated}
                                    <div class="message_success">
                                        {include file="svg.tpl" svgId="success_icon"}
                                        <span data-language="general_messages_success">{$lang->general_messages_success}</span>
                                    </div>
                                {/if}
                                <div class="f_row">
                                    <div class="user_personal_seperator f_col-xl-6">
                                        <div class="block form form_cart ">
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
                                                    {elseif $error == 'empty_password'}
                                                        <span data-language="form_enter_password">{$lang->form_enter_password}</span>
                                                    {elseif $error == 'user_exists'}
                                                        <span data-language="register_user_registered">{$lang->register_user_registered}</span>
                                                    {else}
                                                        {$error|escape}
                                                    {/if}
                                                </div>
                                                {/if}

                                                <div class="f_row">
                                                    <div class="f_col-md-12 f_col-lg-6 f_col-xl-12">
                                                        {* User's name *}
                                                        <div class="form__group">
                                                            <input class="form__input form__placeholder--focus" value="{$user->name|escape}" name="name" type="text" data-language="form_name" />
                                                            <span class="form__placeholder">{$lang->form_name}*</span>
                                                        </div>
                                                    </div>
                                                    <div class="f_col-md-12 f_col-lg-6 f_col-xl-12">
                                                        {* User's last name *}
                                                        <div class="form__group">
                                                            <input class="form__input form__placeholder--focus" value="{$user->last_name|escape}" name="last_name" type="text" data-language="form_name" />
                                                            <span class="form__placeholder">{$lang->form_last_name}</span>
                                                        </div>
                                                    </div>
                                                    <div class="f_col-md-12 f_col-lg-6 f_col-xl-12">
                                                        {* User's email *}
                                                        <div class="form__group">
                                                            <input class="form__input form__placeholder--focus" value="{$user->email|escape}" name="email" type="text" data-language="form_email" />
                                                            <span class="form__placeholder">{$lang->form_email}*</span>
                                                        </div>
                                                    </div>
                                                    <div class="f_col-md-12 f_col-lg-6 f_col-xl-12">
                                                        {* User's phone *}
                                                        <div class="form__group">
                                                            <input class="form__input form__placeholder--focus" value="{$user->phone|phone}" name="phone" type="text" data-language="form_phone" />
                                                            <span class="form__placeholder">{$lang->form_phone}</span>
                                                        </div>
                                                    </div>
                                                    <div class="f_col-md-12 f_col-lg-6 f_col-xl-12">
                                                        {* User's address *}
                                                        <div class="form__group">
                                                            <input class="form__input form__placeholder--focus" value="{$user->address|escape}" name="address" type="text" data-language="form_address" />
                                                            <span class="form__placeholder">{$lang->form_address}</span>
                                                        </div>
                                                    </div>
                                                    <div class="f_col-md-12">
                                                        {* User's password *}
                                                        <div class="form__group">
                                                            <p class="change_pass" onclick="$('#fn_password').toggle().prop('type', 'password').prop('name', 'password');return false;">
                                                                <span data-language="user_change_password">{$lang->user_change_password}</span>
                                                                {include file="svg.tpl" svgId="arrow_right2"}
                                                            </p>
                                                            <input class="form__input form__placeholder--focus " id="fn_password" value="" name="" type="" style="display:none;" {*placeholder="{$lang->user_change_password}"*}/>
                                                        </div>
                                                    </div>
                                                    <div class="f_col-md-12 form__group hidden-sm-down">
                                                        {* Submit button *}
                                                        <button type="submit" class="form__button button--blick hidden-md-up" name="user_save" value="{$lang->form_save}">
                                                            <span data-language="form_save">{$lang->form_save}</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="f_col-xl-6">
                                        {include 'user_deliveries.tpl'}
                                        {* Submit button *}
                                        <button type="submit" class="form__button button--blick" name="user_save" value="{$lang->form_save}">
                                            <span data-language="form_save">{$lang->form_save}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {if $orders}
                    <div id="user_orders" class="tab"{if $active_tab == 'orders'} style="display: block;"{/if}>
                        <div class="fn_switch user_tab__switch">
                            <div class="block__header block__header--boxed block__header--border">
                                <div class="block__heading h1"><span data-language="user_orders_title">{$lang->user_orders_title}</span></div>
                            </div>
                        </div>
                        <div class="block block--boxed block--border mobile_tab__content">
                            <div class="block_explanation__body">
                                <div class="table_wrapper block__description--style table_not_bg">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th></th>  
                                            <th>
                                                <span data-language="user_number_of_order">{$lang->user_number_of_order}</span>
                                            </th>
                                            <th>
                                                <span data-language="user_order_date">{$lang->user_order_date}</span>
                                            </th>
                                            <th>
                                                <span data-language="user_order_status">{$lang->user_order_status}</span>
                                            </th>
                                        </tr>
                                        </thead>
                                        {foreach $orders as $order}
                                        <tr>
                                            <td width="50px"><a class="fn_user_orders_switch" href="javascript:;"></a></td>
                                            {* Order number *}
                                            <td>
                                                <a href='{url_generator route="order" url=$order->url}'><span data-language="user_order_number">{$lang->user_order_number}</span>{$order->id}</a>
                                            </td>
    
                                            {* Order date *}
                                            <td>{$order->date|date}</td>
    
                                            {* Order status *}
                                            <td>
                                                {if $order->paid == 1}
                                                <span data-language="status_paid">{$lang->status_paid}</span>,
                                                {/if}
                                                {$orders_status[$order->status_id]->name|escape}
                                            </td>
                                        </tr>
                                        <tr class="user_orders_hidden">
                                            <td colspan="4">
                                                <div class="purchases purchases--user">
                                                    {foreach $order->purchases as $purchase}
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
                                                    {* Discount *}
                                                    {if $order->discount > 0}
                                                        <div class="purchase_detail__item">
                                                            <div class="purchase_detail__column_name">
                                                                <div class="purchase_detail__name" data-language="cart_discount">{$lang->cart_discount}:</div>
                                                            </div>
                                                            <div class="purchase_detail__column_value">
                                                                <div class="purchase_detail__price">{$order->discount}%</div>
                                                            </div>
                                                        </div>
                                                    {/if}
                        
                                                    {if $order->coupon_discount > 0}
                                                        <div class="purchase_detail__item">
                                                            <div class="purchase_detail__column_name">
                                                                <div class="purchase_detail__name" data-language="cart_coupon">{$lang->cart_coupon}:</div>
                                                            </div>
                                                            <div class="purchase_detail__column_value">
                                                                <div class="purchase_detail__price">
                                                                    &minus; {$order->coupon_discount|convert} <span class="currency">{$currency->sign|escape}</span>
                                                                </div>
                                                            </div>
                                                        </div>
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
                                            </td>
                                        </tr>
                                        {/foreach}
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}

                <div id="user_comments" class="tab"{if $active_tab == 'comments'} style="display: block;"{/if}>
                    <div class="fn_switch user_tab__switch">
                        <div class="block__header block__header--boxed block__header--border">
                            <div class="block__heading h1"><span data-language="user_comments_title">{$lang->user_comments_title}</span></div>
                        </div>
                    </div>

                    <div class="block block--boxed block--border mobile_tab__content">
                         {include 'user_comments.tpl'}
                    </div>
                </div>

                {if $wishlist->products|count}
                    <div id="user_wishlist" class="tab"{if $active_tab == 'favorites'} style="display: block;"{/if}>
                        <div class="fn_switch user_tab__switch">
                            <div class="block__header block__header--boxed block__header--border">
                                <div class="block__heading h1"><span data-language="user_wishlist_title">{$lang->user_wishlist_title}</span></div>
                            </div>
                        </div>
                        <div class="block block--boxed block--border mobile_tab__content">
                            <div class="fn_wishlist_page products_list row">
                                {* Список избранных товаров *}
                                {foreach $wishlist->products as $product}
                                    <div class="product_item no_hover col-xs-6 col-sm-4 col-md-6 col-lg-4 col-xl-3">
                                        {include "product_list.tpl"}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                {/if}
                {if $browsed_products}
                <div id="user_browsed" class="tab"{if $active_tab == 'browsed'} style="display: block;"{/if}>
                    <div class="fn_switch user_tab__switch">
                        <div class="block__header block__header--boxed block__header--border">
                            <div class="block__heading h1"><span data-language="user_browsed_title">{$lang->user_browsed_title}</span></div>
                        </div>
                    </div>
                    <div class="block block--boxed block--border mobile_tab__content">
                        <div class="products_list row">
                            {foreach $browsed_products as $product}
                                <div class="product_item no_hover col-xs-6 col-sm-4 col-md-6 col-lg-4 col-xl-3">
                                    {include "product_list.tpl"}
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
                {/if}
            </div>
        </div>
    </div>
</div>