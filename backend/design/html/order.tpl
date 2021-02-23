{if $order->id}
    {$meta_title = "`$btr->general_order_number` `$order->id`" scope=global}
{else}
    {$meta_title = $btr->order_new scope=global}
{/if}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}

<form method="post" enctype="multipart/form-data" class="fn_fast_button">
    <input type="hidden" name="session_id" value="{$smarty.session.id}">
    <input name="id" type="hidden" value="{$order->id|escape}"/>

    <div class="main_header">
        <div class="main_header__item">
            <div class="fn_step-1 main_header__inner order_toolbar">
                <div class="box_heading heading_page order_toolbar__heading">
                    {if $order->id}
                        {$btr->general_order_number|escape} {$order->id|escape}
                    {else}
                        {$btr->order_new|escape}
                    {/if}
                </div>
                {*Статус заказа*}
                <div class="order_toolbar__status">
                    <select class="selectpicker form-control" name="status_id">
                        {foreach $all_status as $status_item}
                            <option value="{$status_item->id}" {if $order->status_id == $status_item->id}selected=""{/if} {if $hasVariantNotInStock && !$order->closed && $status_item->is_close} disabled{/if} >{$status_item->name|escape}</option>
                        {/foreach}
                    </select>
                </div>
                {if $order->id && !empty($order->url)}
                    <a data-hint="{$btr->general_open|escape}" class="hint-bottom-middle-t-info-s-small-mobile  hint-anim ml-h site_block_icon" target="_blank"  href="{url_generator route='order' url=$order->url absolute=1}" >
                        {include file='svg_icon.tpl' svgId='eye'}
                    </a>
                {/if}
                <a data-hint="{$btr->order_print|escape}" href="{url view=print id=$order->id return=null}" target="_blank" title="{$btr->order_print|escape}" class="hint-bottom-middle-t-info-s-small-mobile  hint-anim ml-h print_block_icon">
                    {include file='svg_icon.tpl' svgId='print'}
                </a>
                {*Метки заказа*}
                <div class="box_btn_heading ml-h hidden-xs-down order_toolbar__markers">
                    <div class="add_order_marker form-control">
                        <span class="fn_ajax_label_wrapper">
                            <span class="fn_labels_show box_labels_show box_btn_heading ml-h">{include file='svg_icon.tpl' svgId='tag'} <span>{$btr->general_select_label|escape}</span> </span>

                            <div class='fn_labels_hide box_labels_hide'>
                                <span class="heading_label">{$btr->orders_choose|escape} <i class="fn_delete_labels_hide btn_close delete_labels_hide">{include file='svg_icon.tpl' svgId='delete'}</i></span>
                                <ul class="option_labels_box">
                                    {foreach $labels as $l}
                                        <li class="fn_ajax_labels" data-order_id="{$order->id}"  style="background-color: #{$l->color|escape}">
                                            <input id="l{$order->id}_{$l->id}" type="checkbox" class="hidden_check_1" name="order_labels[]"  value="{$l->id}" {if in_array($l->id, array_keys($order_labels)) && is_array($order_labels)}checked=""{/if} />
                                            <label   for="l{$order->id}_{$l->id}" class="label_labels"><span>{$l->name|escape}</span></label>
                                        </li>
                                    {/foreach}
                                </ul>
                            </div>
                            <div class="fn_order_labels orders_labels box_btn_heading ml-h">
                                {include file="labels_ajax.tpl"}
                            </div>
                        </span>
                    </div>
                </div>

            </div>
        </div>
        {if $neighbors_orders}
            <div class="main_header__item neighbors_orders hidden-md-down">
                <div class="main_header__inner">
                {if $neighbors_orders['prev']->id}
                    <span>
                        <a title="{$btr->order_prev}" class="prev_order ml-h" href="{url id=$neighbors_orders.prev->id}">
                        {include file='svg_icon.tpl' svgId='prev'}
                        </a>
                    </span>
                {/if}
                {if $neighbors_orders['next']}
                    <span>
                        <a title="{$btr->order_next}" class="next_order ml-h" href="{url id=$neighbors_orders.next->id}">
                        {include file='svg_icon.tpl' svgId='next'}
                        </a>
                    </span>
                {/if}
                </div>
            </div>
        {/if}
    </div>


    {if $hasVariantNotInStock && !$order->closed}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="boxed boxed_warning">
                    <div class="">
                        {$btr->order_not_in_stock|escape}
                    </div>
                </div>
            </div>
        </div>
    {/if}

    {*Вывод ошибок*}
    {if $message_error}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert--center alert--icon alert--error">
                    <div class="alert__content">
                        <div class="alert__title">
                        {if $message_error=='error_closing'}
                            {$btr->order_shortage|escape}
                        {elseif $message_error == 'empty_purchase'}
                            {$btr->general_empty_purchases|escape}
                        {else}
                            {$message_error|escape}
                        {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {*Вывод успешных сообщений*}
    {elseif $message_success}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert--center alert--icon alert--success">
                    <div class="alert__content">
                        <div class="alert__title">
                            {if $message_success=='updated'}
                                {$btr->order_updated|escape}
                            {elseif $message_success=='added'}
                                {$btr->order_added|escape}
                            {else}
                                {$message_success|escape}
                            {/if}
                        </div>
                    </div>
                    {if $smarty.get.return}
                        <a class="alert__button" href="{$smarty.get.return}">
                            {include file='svg_icon.tpl' svgId='return'}
                            <span>{$btr->general_back|escape}</span>
                        </a>
                    {/if}
                </div>
            </div>
        </div>
    {/if}

    <div class="row">
        {*left_column*}
        <div class="col-xl-8 break_1300_12  pr-0">
            <div class="boxed fn_toggle_wrap min_height_230px fn_step-2 tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="heading_box tab_navigation_link">
                            {$btr->order_content|escape}
                        </a>
                        {if $order->id}
                            <a href="#tab2" class="heading_box tab_navigation_link">
                                {$btr->order_history|escape}
                            </a>
                            {if $match_orders}
                            <a href="#tab3" class="fn_match_orders_tab_title heading_box tab_navigation_link {if $match_orders_tab_active}selected{/if}">
                                {$btr->order_match_orders|escape}
                            </a>
                            {/if}
                        {/if}
                    </div>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <div id="fn_purchase" class="okay_list">
                                {*Шапка таблицы*}
                                <div class="okay_list_head">
                                    <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                                    <div class="okay_list_heading okay_list_order_name">{$btr->order_name_option|escape} </div>
                                    <div class="okay_list_heading okay_list_price">{$btr->general_price|escape} {$currency->sign|escape}</div>
                                    <div class="okay_list_heading okay_list_count">{$btr->order_qty|escape}
                                    </div>
                                    <div class="okay_list_heading okay_list_order_amount_price">{$btr->general_sales_amount}</div>
                                </div>
                                {*Список покупок*}
                                <div class="okay_list_body">
                                    {foreach $purchases as $purchase}
                                        <div class="fn_row okay_list_body_item purchases">
                                            <div class="okay_list_row">
                                                <input type=hidden name=purchases[id][{$purchase->id}] value='{$purchase->id}'>

                                                <div class="okay_list_boding okay_list_photo">
                                                    {if $purchase->variant}
                                                        <img class=product_icon src="{$purchase->product->image->filename|resize:50:50}">
                                                    {else}
                                                        <img width="50" src="design/images/no_image.png"/>
                                                    {/if}
                                                </div>
                                                <div class="okay_list_boding okay_list_order_name">
                                                    <div class="boxes_inline">
                                                        {if $purchase->product}
                                                            <a class="text_600 {if $purchase->variant->stock == 0}hint-bottom-middle-t-info-s-small-mobile  hint-anim text_500 text_warning{/if}" {if $purchase->variant->stock == 0}data-hint="{$btr->product_out_stock|escape}"{/if} href="{url controller=ProductAdmin id=$purchase->product->id}">{$purchase->product_name|escape}</a>
                                                            {if $purchase->variant_name}
                                                                <div class="mt-q font_12"><span class="text_grey">{$btr->order_option|escape}</span> {$purchase->variant_name|escape}</div>
                                                            {/if}
                                                            {if $purchase->sku}
                                                                <div class="mt-q font_12"><span class="text_grey">{$btr->general_sku|escape}:</span> {$purchase->sku|default:"&mdash;"}</div>
                                                            {/if}
                                                        {else}
                                                            <div class="text_grey text_600">{$purchase->product_name|escape}</div>
                                                            {if $purchase->variant_name}
                                                                <div class="mt-q font_12"><span class="text_grey">{$btr->order_option|escape}</span> {$purchase->variant_name|escape}</div>
                                                            {/if}
                                                            {if $purchase->sku}
                                                                <div class="mt-q font_12"><span class="text_grey">{$btr->general_sku|escape}:</span> {$purchase->sku|default:"&mdash;"}</div>
                                                            {/if}
                                                        {/if}
                                                        <div class="hidden-lg-up mt-q">
                                                            <span class="text_primary text_600 {if $purchase->discounts}text_warning{/if}">{$purchase->price}</span>
                                                            <span class="hidden-md-up text_500">
                                                            {$purchase->amount} {if $purchase->units}{$purchase->units|escape}{else}{$settings->units|escape}{/if}</span>
                                                        </div>
                                                        {get_design_block block="order_purchase_name" vars=['purchase'=>$purchase]}
                                                    </div>

                                                    {if !$purchase->variant}
                                                        <input class="form-control " type="hidden" name="purchases[variant_id][{$purchase->id}]" value="" />
                                                    {else}
                                                        <div class="boxes_inline mt-h">
                                                            <select name="purchases[variant_id][{$purchase->id}]" class="selectpicker form-control {if $purchase->product->variants|count == 1}hidden{/if} fn_purchase_variant">
                                                                {foreach $purchase->product->variants as $v}
                                                                    <option data-price="{$v->price}" data-units="{if $v->units}{$v->units|escape}{else}{$settings->units|escape}{/if}" data-amount="{$v->stock}" value="{$v->id}" {if $v->id == $purchase->variant_id}selected{/if} >
                                                                        {if $v->name}
                                                                            {$v->name|escape}
                                                                        {else}
                                                                            #{$v@iteration}
                                                                        {/if}
                                                                    </option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                    {/if}
                                                    <div class="mt-h">
                                                        <span class="tag {if $purchase->discounts}tag-danger{else}tag-default{/if} fn_discounted_toggle">
                                                            <span>{$btr->general_discounts|escape}</span>
                                                            <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_price">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control fn_purchase_price" name="purchases[undiscounted_price][{$purchase->id}]" value="{$purchase->undiscounted_price}">
                                                        <span class="input-group-addon">{$currency->code}</span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_count">
                                                    <div class="input-group">
                                                        <input class="form-control fn_purchase_amount" type="text" name="purchases[amount][{$purchase->id}]" value="{$purchase->amount}"/>
                                                        <span class="input-group-addon p-0 fn_purchase_units">
                                                             {if $purchase->units}{$purchase->units|escape}{else}{$settings->units|escape}{/if}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_order_amount_price">
                                                    <div class="text_dark {if $purchase->discounts}text_warning text_600{/if}">
                                                        <span class="font_16">{($purchase->price) * ($purchase->amount)}</span>
                                                        <span class="font_12">{$currency->sign}</span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_close">
                                                    {*delete*}
                                                    <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim" >
                                                        {include file='svg_icon.tpl' svgId='trash'}
                                                    </button>
                                                </div>
                                            </div>

                                            {include 'order_purchase_discount.tpl'}
                                        </div>
                                    {/foreach}
                                </div>
                                <div class="okay_list_body fn_new_purchase" style="display: none">
                                    <div class="fn_row okay_list_body_item " >
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding okay_list_photo">
                                                <input type="hidden" name="purchases[id][]" value="" />
                                                <img class="fn_new_image" src="">
                                            </div>
                                            <div class="okay_list_boding okay_list_order_name">
                                                <div class="boxes_inline">
                                                    <a class="fn_new_product" href=""></a>
                                                    <div class="fn_new_variant_name"></div>
                                                    {get_design_block block="order_new_purchase_name"}
                                                </div>
                                                <div class="boxes_inline">
                                                    <select name="purchases[variant_id][]" class="fn_new_variant"></select>
                                                </div>
                                                <div class="mt-h">
                                                    <span class="tag tag-default fn_discounted_toggle">
                                                        <span>{$btr->general_discounts|escape}</span>
                                                        <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_price">
                                                <div class="input-group">
                                                    <input type="text" class="form-control fn_purchase_price" name=purchases[undiscounted_price][] value="">
                                                    <span class="input-group-addon">{$currency->code|escape}</span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_count">
                                                <div class="input-group">
                                                    <input class="form-control fn_purchase_amount" type="text" name="purchases[amount][]" value="1"/>
                                                    <span class="input-group-addon p-0 fn_purchase_units">
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_order_amount_price">
                                                <div class="text_dark">
                                                    <span></span>
                                                    <span class=""></span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_close">
                                                {*delete*}
                                                <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                    {include file='svg_icon.tpl' svgId='trash'}
                                                </button>
                                            </div>
                                        </div>

                                        {include 'order_purchase_discount.tpl' purchase=null}
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-2 mb-1">
                                <div class="col-lg-6 col-md-12">
                                    <div class="autocomplete_arrow">
                                        <input type="text" name="new_purchase" id="fn_add_purchase" class="form-control" placeholder="{$btr->general_add_product|escape}">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    {if $purchases}
                                    <div class="order_prices__total text-xs-right">
                                        <span class="text_grey text_500 font_16 mr-1">{$btr->order_sum|escape}</span>
                                        <span class="text_dark text_600 font_24">{$subtotal}</span>
                                        <span class="text_dark text_400 font_18 ml-q">{$currency->sign|escape}</span>
                                    </div>
                                    {/if}
                                </div>
                            </div>
                            {get_design_block block="order_purchases"}
                        </div>

                        {if $order->id}
                            <div id="tab2" class="tab">
                                {include 'order_history.tpl'}
                                <div class="mt-2">
                                    <textarea name="history_comment" class="editor_small"></textarea>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 mt-1">
                                            <button type="submit" class="btn btn_small btn_blue float-sm-right">
                                                {include file='svg_icon.tpl' svgId='checked'}
                                                <span>{$btr->general_apply|escape}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {if $match_orders}
                            <div id="tab3" class="tab fn_match_order_container">
                                {include 'match_orders.tpl'}
                            </div>
                            {/if}
                        {/if}
                    </div>
                </div>
            </div>

            {*Скидки к заказу*}
            <div class="boxed fn_toggle_wrap boxed-discound_flex">
                <div class="heading_box heading_box--discound_flex">
                    <div class="pr-q">{$btr->order_discount_title|escape}</div>

                    <div class="boxed-discound_activity text_400 opensans">
                        <div class="activity_of_switch activity_of_switch--left" style="display: inline-block;">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->order_show_order_discounts}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" type="checkbox" {if $discounts}checked{/if} onchange="$('.fn_order_discounts_block').toggle(0)">
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    {include 'order_discount.tpl'}
                </div>
            </div>

            {*Информация по заказу*}
            <div class="boxed fn_toggle_wrap min_height_230px fn_step-3">
                <div class="heading_box">
                    {$btr->order_parameters|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="">
                        <div class="">
                            <div class="okay_list">
                                <div class="okay_list_body">
                                    <div class="okay_list_body_item">
                                        <div class="okay_list_row  d_flex">
                                            <div class="okay_list_boding okay_list_ordfig_name">
                                                <div class="text_600 text_dark boxes_inline">{$btr->general_shipping|escape}</div>
                                                <div class="boxes_inline">
                                                    <select name="delivery_id" class="selectpicker form-control">
                                                        <option value="0">{$btr->order_not_selected|escape}</option>
                                                        {foreach $deliveries as $d}
                                                            <option value="{$d->id}" {if $d->id==$delivery->id}selected{/if} data-module_id="{$d->module_id}">{$d->name|escape}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                                {get_design_block block="order_delivery_info"}
                                            </div>
                                            <div class="okay_list_boding okay_list_ordfig_val">
                                                <div class="input-group">
                                                    <input type=text name=delivery_price class="form-control" value='{$order->delivery_price}'>
                                                    <span class="input-group-addon p-0">{$currency->code|escape}</span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_ordfig_price">
                                                <div class="input-group"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="okay_list_body_item">
                                        <div class="okay_list_row  d_flex">
                                            <div class="okay_list_boding okay_list_ordfig_name">
                                                <div class="text_600 text_dark boxes_inline">{$btr->order_payment_selected|escape}</div>
                                                <div class="boxes_inline">
                                                    <select name="payment_method_id" class="selectpicker form-control">
                                                        <option value="0">{$btr->order_not_selected|escape}</option>
                                                        {foreach $payment_methods as $pm}
                                                        <option value="{$pm->id}" {if $pm->id==$payment_method->id}selected{/if} data-module="{$pm->module}">{$pm->name|escape}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                                {get_design_block block="order_payment_info"}
                                            </div>
                                            <div class="okay_list_boding okay_list_ordfig_val"></div>
                                            <div class="okay_list_boding okay_list_ordfig_price">
                                                <div class="text_dark">
                                                    <span>{$order->total_price} </span>
                                                    <span class="">{$currency->sign}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {get_design_block block="order_common_info"}
                            </div>
                            <div class="order_prices mt-1">
                                <div class="order_prices__item my-1">
                                    <div class="">
                                        <div class="okay_switch">
                                            <label class="switch_label boxes_inline">{$btr->order_paid|escape}</label>
                                            <label class="switch switch-default switch-pill switch-primary-outline-alt boxes_inline">
                                                <input class="switch-input" name="paid" value='1' type="checkbox" id="paid" {if $order->paid}checked{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="order_prices__item my-1">
                                    <div class="">
                                        {if $payment_method}
                                            <div class="order_prices__total">
                                                <span class="text_grey text_500 font_18 mr-1">{$btr->order_to_pay|escape}</span>
                                                <span class="text_dark text_600 font_26">{$order->total_price|convert:$payment_currency->id}</span>
                                                <span class="text_dark text_400 font_18 ml-q">{$payment_currency->sign}</span>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                         </div>
                    </div>
                </div>
            </div>
            {$block = {get_design_block block="order_custom_block"}}
            {if !empty($block)}
                <div class="boxed fn_toggle_wrap">
                    {$block}
                </div>
            {/if}
        </div>
        {*right_column*}
        {*Информация о заказчике/детали заказа*}
        <div class="col-xl-4 break_1300_12">
            <div class="boxed fn_toggle_wrap min_height_230px fn_step-4">
                <div class="heading_box">
                    {$btr->order_buyer_information|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="box_border_buyer fn_contact_info">
                        <div class="mb-1">
                            <div class="heading_label boxes_inline">{$btr->order_date|escape}</div>
                            <div class="boxes_inline text_dark text_600">{$order->date|date} {$order->date|time}</div>
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">{$btr->index_name|escape}</div>
                            <input name="name" class="form-control" type="text" value="{$order->name|escape}" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">{$btr->index_last_name|escape}</div>
                            <input name="last_name" class="form-control" type="text" value="{$order->last_name|escape}" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">{$btr->general_phone|escape}</div>
                            <input name="phone" class="form-control" type="text" value="{$order->phone|phone}" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">E-mail</div>
                            <input name="email" class="form-control" type="text" value="{$order->email|escape}" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">{$btr->general_adress|escape} <a href="https://www.google.com/maps/search/{$order->address|escape}?hl=ru" target="_blank"><i class="fa fa-map-marker"></i> {$btr->order_on_map|escape}</a></div>
                            <textarea name="address" class="form-control short_textarea">{$order->address|escape}</textarea>
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">{$btr->general_comment|escape}</div>
                            <textarea name="comment" class="form-control short_textarea">{$order->comment|escape}</textarea>
                        </div>
                         <div class="mb-1">
                            <div class="heading_label boxes_inline">{$btr->order_ip|escape} {if $order->id}<a href="https://who.is/whois-ip/ip-address/{$order->ip}" target="_blank"><i class="fa fa-map-marker"></i> whois</a>{/if}</div>
                            <div class="boxes_inline text_dark text_600">{$order->ip|escape}</div>
                        </div>
                        {if $order->referer_channel}
                            <div class="mb-1">
                                <div class="heading_label boxes_inline">{$btr->order_referer_channel|escape}:</div>
                                <div class="boxes_inline text_dark">
                                    {if $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_EMAIL}
                                        <span class="tag tag-chanel_email" title="{$order->referer_source|escape}">
                                            {include file='svg_icon.tpl' svgId='tag_email'} {$order->referer_channel}
                                        </span>
                                    {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SEARCH}
                                        <span class="tag tag-chanel_search" title="{$order->referer_source|escape}">
                                            {include file='svg_icon.tpl' svgId='tag_search'} {$order->referer_channel}
                                        </span>
                                    {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SOCIAL}
                                        <a href="{$order->referer_source|escape}" target="_blank" class="tag tag-chanel_social" title="{$order->referer_source|escape}">
                                            {include file='svg_icon.tpl' svgId='tag_social'} {$order->referer_channel}
                                        </a>
                                    {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_REFERRAL}
                                        <a href="{$order->referer_source|escape}" target="_blank" class="tag tag-chanel_referral" title="{$order->referer_source|escape}">
                                            {include file='svg_icon.tpl' svgId='tag_referral'} {$order->referer_channel}
                                        </a>
                                    {else}
                                        <span class="tag tag-chanel_unknown" title="{$order->referer_source|escape}">
                                            {include file='svg_icon.tpl' svgId='tag_unknown'} {$order->referer_channel}
                                        </span>
                                    {/if}
                                </div>
                            </div>
                        {/if}
                        {get_design_block block="order_contact"}
                    </div>
                    <div class="box_border_buyer">
                        <div class="mb-1">
                            <div style="position:relative;">
                                {if !$user}
                                    <div class="heading_label">
                                        {$btr->order_buyer_not_registred|escape}
                                    </div>
                                    <div style="position:relative;">
                                        <input type="hidden" name="user_id" value="{$user->id}" />

                                        <input type="text" class="fn_user_complite form-control" placeholder="{$btr->order_user_select|escape}" />
                                    </div>
                                {else}
                                    <div class="fn_user_row">
                                        <input type="hidden" name="user_id" value="{$user->id}" />
                                        <div class="heading_label boxes_inline">
                                            {$btr->order_buyer|escape}
                                            <a href="{url controller=UserAdmin id=$user->id}" target=_blank>
                                                 {$user->name|escape} {$user->last_name|escape}
                                            </a>
                                        </div>
                                        <a href="javascript:;" data-hint="{$btr->users_delete|escape}" class="btn_close delete_grey fn_delete_user hint-bottom-right-t-info-s-small-mobile  hint-anim boxes_inline" >
                                            {include file='svg_icon.tpl' svgId='delete'}
                                        </a>
                                        {if $user->group_id > 0}
                                            <div class="text_grey">{$user->group->name|escape}</div>
                                        {else}
                                            <div class="text_grey">{$btr->order_not_in_group|escape}</div>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        </div>
                        {get_design_block block="order_user_info"}
                    </div>
                    <div class="box_border_buyer">
                        <div class="mb-1">
                            <div class="heading_label">{$btr->order_language|escape}</div>
                            <select name="entity_lang_id" class="selectpicker form-control">
                                {foreach $languages as $l}
                                    <option value="{$l->id}" {if $l->id == $order->lang_id}selected=""{/if}>{$l->name|escape}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="">
                            <div class="form-group">
                                <div class="heading_label">{$btr->order_note|escape}</div>
                                <textarea name="note" class="form-control short_textarea">{$order->note|escape}</textarea>
                            </div>
                        </div>
                        {get_design_block block="order_additional_info"}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-2">
            <button type="submit" class="btn btn_small btn_blue float-sm-right">
                {include file='svg_icon.tpl' svgId='checked'}
                <span>{$btr->general_apply|escape}</span>
            </button>
            <div class="checkbox_email float-sm-right text_dark mr-1 fn_step-5">
                <input id="order_to_email" name="notify_user" type="checkbox" class="hidden_check_1"  value="1" />
                <label for="order_to_email" class="checkbox_label mr-h"></label>
                <span>{$btr->order_email|escape}</span>
            </div>
        </div>
    </div>
</form>

{include file='learning_hints.tpl' hintId='hint_order'}
{literal}
<script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
<link rel="stylesheet" type="text/css" href="design/js/autocomplete/styles.css" media="screen" />

<script>
$(function() {
    // Удаление товара
    $(document).on( "click", "#fn_purchase .fn_remove_item", function() {
         $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
         return false;
    });

    // Отобразить список скидок
    $(document).on('click','.fn_discounted_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.order_discounted_block').slideToggle(300);
    });

    $(".fn_labels_show").click(function(){
        $(this).next('.fn_labels_hide').toggleClass("active_labels");
    });
    $(".fn_delete_labels_hide").click(function(){
        $(this).closest('.box_labels_hide').removeClass("active_labels");
    });

    $(".fn_from_date, .fn_to_date ").datepicker({
        dateFormat: 'dd-mm-yy'
    });

    $(document).on("change", ".fn_ajax_labels input", function () {
        elem = $(this);
       var order_id = parseInt($(this).closest(".fn_ajax_labels").data("order_id"));
       var state = "";
       session_id = '{/literal}{$smarty.session.id}{literal}';
       var label_id = parseInt($(this).closest(".fn_ajax_labels").find("input").val());
       if($(this).closest(".fn_ajax_labels").find("input").is(":checked")){
            state = "add";
       } else {
            state = "remove";
       }

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/update_order.php",
            data: {
                order_id : order_id,
                state : state,
                label_id : label_id,
                session_id : session_id
            },
            success: function(data){
                var msg = "";
                if(data){
                    elem.closest(".fn_ajax_label_wrapper").find(".fn_order_labels").html(data.data);
                    toastr.success(msg, "{/literal}{$btr->toastr_success|escape}{literal}");
                } else {
                    toastr.error(msg, "{/literal}{$btr->toastr_error|escape}{literal}");

                }
            }
        });
    });

    // Добавление товара
    var new_purchase = $('#fn_purchase .fn_new_purchase').clone(true);
    $('#fn_purchase .fn_new_purchase').remove().removeAttr('class');

    $("#fn_add_purchase").devbridgeAutocomplete({
    serviceUrl:'{/literal}{url controller='OrderAdmin@addOrderProduct'}{literal}',
    minChars:0,
    orientation:'auto',
    noCache: false,
    onSelect:
        function(suggestion){
            let new_item = new_purchase.clone().appendTo('#fn_purchase'),
                temp_id = Date.now();
            new_item.find('.fn_add_purchase_discount').data('purchase_id', temp_id);
            new_item.find('.fn_default_purchase_discounts').attr('name', `purchases_discounts[${temp_id}]`)
            new_item.removeAttr('id');
            new_item.find('.fn_new_product').html(suggestion.data.name);
            new_item.find('.fn_new_product').attr('href', 'index.php?controller=ProductAdmin&id='+suggestion.data.id);

            // Добавляем варианты нового товара
            var variants_select = new_item.find("select.fn_new_variant");

            for(var i in suggestion.data.variants) {
                variants_select.append("<option {/literal}{get_design_block block="order_new_purchase_variants_option_block"}{literal} " +
                    "value='"+suggestion.data.variants[i].id+"' " +
                    "data-price='"+suggestion.data.variants[i].price+"' " +
                    "data-amount='"+suggestion.data.variants[i].stock+"' " +
                    "data-units='"+suggestion.data.variants[i].units+"'>" +
                    suggestion.data.variants[i].name +
                    "</option>");
            }

            if(suggestion.data.variants.length> 1 || suggestion.data.variants[0].name != '') {
                variants_select.show();
                variants_select.selectpicker();
            } else {
                variants_select.hide();
            }
            variants_select.find('option:first').attr('selected',true);

            variants_select.bind('change', function(){
                change_variant(variants_select);
            });
            change_variant(variants_select);
            variants_select.trigger('change');

            if(suggestion.data.image) {
                new_item.find('.fn_new_image').attr("src", suggestion.data.image);
            } else {
                new_item.find('.fn_new_image').remove();
            }

            {/literal}{get_design_block block="order_new_purchase_js_block"}{literal}

            $("input#fn_add_purchase").val('').focus().blur();
            new_item.show();
        },
        formatResult:
            function(suggestions, currentValue){
                    var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                    var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                    return "<div>" + (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + "</div>" +  "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
                }


  });

  // Изменение цены и макс количества при изменении варианта
    function change_variant(element) {
        var price = element.find('option:selected').data('price');
        var amount = element.find('option:selected').data('amount');
        var units = element.find('option:selected').data('units');
        element.closest('.fn_row').find('input.fn_purchase_price').val(price);
        element.closest('.fn_row').find('.fn_purchase_units').text(units);
        var amount_input = element.closest('.fn_row').find('input.fn_purchase_amount');
        amount_input.val('1');
        amount_input.data('max',amount);
        {/literal}{get_design_block block="order_change_variant_js_block"}{literal}
        return false;
  }

    $(".fn_user_complite").devbridgeAutocomplete({
        serviceUrl:'ajax/search_users.php',
        minChars:0,
        orientation:'auto',
        noCache: false,
        onSelect:function(suggestion){
            $('input[name="user_id"]').val(suggestion.data.id);

            for (let key in suggestion.data) {
                let contactField = $('.fn_contact_info [name="' + key + '"]');
                if (contactField.length > 0 && contactField.val() == '') {
                    contactField.val(suggestion.data[key]);
                }
            }
            
        },
        formatResult: function(suggestions, currentValue){
            var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
            var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
            return "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
        }
    });

    $(document).on("click", ".fn_delete_user", function () {
        $(this).closest(".fn_user_row").hide();
        $('input[name="user_id"]').val(0);
    });


    $("select.fn_purchase_variant").bind("change", function(){
        change_variant($(this));
    });

    $(document).on('click', '.fn_discount_remove', function() {
        $(this).closest('.fn_row').remove();
    });

    $(function(){
       let newOrderDiscount = $('.fn_new_order_discount').clone(true);
        $('.fn_new_order_discount').remove();

        $(document).on('click', '.fn_add_order_discount', function(){
            newOrderDiscount.clone().appendTo($('.fn_order_discounts_block').find('.okay_list_body'));
        });

        $(document).on('click', '.fn_add_purchase_discount', function(){
            let purchaseId = $(this).data('purchase_id'),
                newPurchaseDiscount = newOrderDiscount.clone();
            newPurchaseDiscount.find('input').each(function(){
                $(this).attr('name', $(this).attr('name').replace('order_discounts', `purchases_discounts[${purchaseId}]`));
            });
            newPurchaseDiscount.appendTo($(this).closest('.fn_purchase_discounts_block').find('.okay_list_body'));
            $(this).closest('.fn_row').find('.fn_discounted_toggle').removeClass('tag-default').addClass('tag-danger');
        })
    });

    $(document).on('click', '.fn_discount_change_type', function(){
        let input1 = $(this).closest('.input-group').find('input.fn_discount_type_input.active');
        let input2 = $(this).closest('.input-group').find('input.fn_discount_type_input:not(.active)');
        input1.removeClass('active').attr('disabled', true);
        input2.addClass('active').attr('disabled', false);
        $(this).find('.discount_type_absolute').toggle(0);
        $(this).find('.discount_type_percent').toggle(0);
    });

    $(document).on('click', '.fn_discount_from_last_on', function(){
        $(this).closest('.switch').find('.fn_discount_from_last_off')[0].toggleAttribute('disabled')
    })
});

</script>
{/literal}
