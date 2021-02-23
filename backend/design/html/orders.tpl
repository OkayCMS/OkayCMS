{* Title *}
{$meta_title=$btr->general_orders scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                {if $orders_count}
                    {$btr->general_orders|escape}
                    {if !empty($order_user)}
                        {$btr->general_orders_user|escape} {$order_user->name|escape} {$order_user->last_name}
                    {/if}
                    - {$orders_count}
                {else}
                    {$btr->orders_no|escape}
                {/if}
                {if $orders_count>0 && !$keyword}
                    <div class="fn_start_export export_block hint-bottom-middle-t-info-s-small-mobile hint-anim" data-hint="{$btr->orders_export|escape}">
                        {include file='svg_icon.tpl' svgId='export'}
                    </div>
                {/if}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=OrderAdmin}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->orders_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="main_header__item main_header__item--sort_date">
        <form class="box_date_filter fn_date_filter main_header__inner" method="get">
            <input type="hidden" name="controller" value="OrdersAdmin">
            {if $keyword}
                <input type="hidden" name="keyword" value="{$keyword|escape}" >
            {/if}
            
            {if $status_id}
                <input type="hidden" name="status" value="{$status_id|escape}" >
            {/if}
            {if $label_id}
                <input type="hidden" name="label" value="{$label_id|escape}" >
            {/if}
            <ul class="filter_date__list form-control">
                <li class="filter_date__item">
                    <button type="button" class="fn_last_week filter_date__button ">{$btr->orders_date_filter_last_week}</button>
                </li>
                <li class="filter_date__item">
                    <button type="button" class="fn_30_days filter_date__button">{$btr->orders_date_filter_last_30_days}</button>
                </li>
                <li class="filter_date__item">
                    <button type="button" class="fn_7_days filter_date__button">{$btr->orders_date_filter_last_7_days}</button>
                </li>
                <li class="filter_date__item">
                    <button type="button" class="fn_yesterday filter_date__button">{$btr->orders_date_filter_last_yesterday}</button>
                </li>
                <li class="filter_date__item filter_date__item--date hidden-xs-down">
                    <button class="fn_calendar filter_date__button" title="{$btr->orders_date_filter_calendar}" type="button">
                        {include file='svg_icon.tpl' svgId='date'}
                        <span class="hidden-xs-down">{$btr->orders_date_filter_calendar}</span>
                    </button>
                    <button class="btn btn_blue" type="submit">
                        <span class="hidden-sm-up">{include file='svg_icon.tpl' svgId='checked'}</span>
                        <span class="hidden-xs-down">{$btr->general_apply|escape}</span>
                    </button>
                    {*не убирает инпут из дома, просто делаем его невидимым*}
                    <input type="text" class="fn_calendar_pixel" name="" autocomplete="off" >
                </li>
            </ul>
            <input type="hidden" class="fn_from_date" name="from_date" value="{$from_date}" autocomplete="off" >
            <input type="hidden" class="fn_to_date" name="to_date" value="{$to_date}" autocomplete="off" >
        </form>
    </div>
</div>

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_error=='error_closing'}
                            {$btr->orders_in|escape}
                            {foreach $error_orders as $error_order_id}
                                <div>
                                    № {$error_order_id}
                                </div>
                            {/foreach}
                            {$btr->orders_shortage|escape}
                        {else}
                            {$message_error|escape}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{* Visited info *}
<div class="boxed wrap_view_info">
    <div class="view_info_dates">
        <div class="view_info_dates__title">{$btr->orders_date_filter_list_orders|escape}:</div>
        <div class="view_info_dates__text">
            {$btr->orders_date_filter_list_orders_from|escape}
            {if $from_date}
                {$from_date|date}
            {else}
                {$orders_from_date|date}
            {/if}
            {$btr->orders_date_filter_list_orders_to|escape}
            {if $to_date}
                {$to_date|date}
            {else}
                {$orders_to_date|date}
            {/if}
        </div>
        {if $from_date || $to_date}
            <button class="fn_reset_date_filter btn btn-secondary" type="button">{$btr->orders_date_filter_list_orders_reset|escape}</button>
        {/if}
    </div>
    <div class="view_info_visited">
        

        {foreach $all_status as $s}
            {if isset($count_orders_by_statuses[$s->id])}
                {$ordersCount = $count_orders_by_statuses[$s->id]}
                <div class="view_info_visited__item">
                    <div class="view_info_visited__inner">
                        <div class="view_info_visited__left">
                            <a href="{url status=$s->id}" class="view_info_visited__status" style="color: #{$s->color};">{$s->name|escape}</a>
                            <div class="view_info_visited__percent">{round($ordersCount->count / $count_orders_for_statuses * 100, 1)}%</div>
                        </div>
                        <div class="view_info_visited__right">
                            <div class="view_info_visited__count">{$ordersCount->count|escape}</div>
                        </div>
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_step-0 fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->general_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="boxed_sorting toggle_body_wrap off fn_card">
                {*Блок фильтров*}
                <div class="row">
                    {if $all_status}
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <select name="status" class="selectpicker form-control"  onchange="location = this.value;">
                                {foreach $all_status as $order_status}
                                    <option value="{url controller=OrdersAdmin status=$order_status->id keyword=null id=null page=null label=null from_date=null to_date=null}" {if $status_id == $order_status->id}selected=""{/if} >{$order_status->name|escape}</option>
                                {/foreach}
                                <option value="{url controller=OrdersAdmin status=null keyword=null id=null page=null label=null from_date=null to_date=null}" {if !$status_id}selected{/if}>{$btr->general_all_status|escape}</option>
                            </select>
                        </div>
                    {/if}
                    {if $labels}
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <select class="selectpicker form-control" onchange="location = this.value;">
                                {foreach $labels as $l}
                                    <option value="{url label=$l->id}" {if $label_id == $l->id}selected{/if}>{$l->name|escape}</option>
                                {/foreach}
                                <option value="{url label=null}" {if !$label_id} selected{/if}>{$btr->general_all_label|escape}</option>
                            </select>
                        </div>
                    {/if}
                    <div class="col-md-3 col-lg-3 col-sm-12 float-md-right">
                        <form class="search" method="get">
                            <input type="hidden" name="controller" value="OrdersAdmin">
                            {if $from_date}
                                <input type="hidden" name="from_date" value="{$from_date|escape}" >
                            {/if}
                            {if $to_date}
                                <input type="hidden" name="to_date" value="{$to_date|escape}" >
                            {/if}
                            {if $status_id}
                                <input type="hidden" name="status" value="{$status_id|escape}" >
                            {/if}
                            {if $label_id}
                                <input type="hidden" name="label" value="{$label_id|escape}" >
                            {/if}
                            <div class="input-group input-group--search">
                                <input name="keyword" class="form-control" placeholder="{$btr->general_search|escape}" type="text" value="{$keyword|escape}" >
                                <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
            {$block = {get_design_block block="orders_custom_block"}}
            {if !empty($block)}
                <div class="custom_block">
                    {$block}
                </div>
            {/if}
        </div>
    </div>

    {*Главная форма страницы*}
    {if $orders}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list" method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="orders_list okay_list products_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head" style="border-left: 5px solid transparent">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_order_number">{$btr->order_match_id}</div>
                            <div class="okay_list_heading okay_list_orders_name">{$btr->general_full_name|escape}</div>
                            <div class="okay_list_heading okay_list_order_status">{$btr->general_status|escape}</div>
                            <div class="okay_list_heading okay_list_order_product_count">{$btr->general_products|escape}</div>
                            <div class="okay_list_heading okay_list_orders_price">{$btr->general_sales_amount}</div>
                            <div class="okay_list_heading okay_list_order_marker">{$btr->orders_label|escape}</div>
                            <div class="okay_list_heading okay_list_close hidden-sm-down"></div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {foreach $orders as $order}
                            <div class="fn_step-1 fn_row okay_list_body_item " style="border-left: 5px solid #{$order->status_color};">
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_check">
                                        <input class="hidden_check" type="checkbox" id="id_{$order->id}" name="check[]" value="{$order->id}"/>
                                        <label class="okay_ckeckbox" for="id_{$order->id}"></label>
                                    </div>

                                    <div class="okay_list_boding okay_list_order_number">
                                        <a class="text_600 mb-h" href="{url controller=OrderAdmin id=$order->id return=$smarty.server.REQUEST_URI}">{$btr->orders_order|escape} #{$order->id}</a>
                                        {if $order->last_update}
                                            <span class="tag tag-update fn_history_toggle">{$btr->order_history_changed} {$order->last_update->date|date} {$order->last_update->date|time} <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i></span>
                                        {else}
                                            <span class="tag tag-update fn_history_toggle">{$btr->order_history_created} {$order->date|date} {$order->date|time} <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i></span>
                                        {/if}
                                        
                                        {if $order->paid}
                                            <div class="order_paid">
                                                <span class="tag tag-success">{$btr->general_paid|escape}</span>
                                            </div>
                                        {/if}
                                        {if $order->referer_channel}
                                            <div class="order_paid">
                                                {if $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_EMAIL}
                                                    <span class="tag tag-chanel_email" title="{$order->referer_source}">
                                                        {include file='svg_icon.tpl' svgId='tag_email'} {$order->referer_channel}
                                                    </span>
                                                {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SEARCH}
                                                    <span class="tag tag-chanel_search" title="{$order->referer_source}">
                                                        {include file='svg_icon.tpl' svgId='tag_search'} {$order->referer_channel}
                                                    </span>
                                                {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SOCIAL}
                                                    <span class="tag tag-chanel_social" title="{$order->referer_source}">
                                                        {include file='svg_icon.tpl' svgId='tag_social'} {$order->referer_channel}
                                                    </span>
                                                {elseif $order->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_REFERRAL}
                                                    <a href="https://{$order->referer_source|escape}" target="_blank" class="tag tag-chanel_referral" title="{$order->referer_source}">
                                                        {include file='svg_icon.tpl' svgId='tag_referral'} {$order->referer_channel}
                                                    </a>
                                                {else}
                                                    <span class="tag tag-ind_unknown" title="{$order->referer_source}">
                                                        {include file='svg_icon.tpl' svgId='tag_unknown'} {$order->referer_channel}
                                                    </span>
                                                {/if}
                                            </div>
                                        {/if}
                                    </div>

                                    <div class="okay_list_boding okay_list_orders_name">
                                        <a href="{url controller=OrderAdmin id=$order->id return=$smarty.server.REQUEST_URI}" class="text_400 mb-q">{$order->name|escape} {$order->last_name|escape}</a>
                                        <div class="hidden-lg-up mb-h">
                                            <div class="text_600 font_12" style="color: #{$order->status_color};">{$orders_status[$order->status_id]->name|escape}</div>
                                        </div>
                                        <div class="font_12 text_500 text_grey mb-q"><span class="hidden-md-down">{$btr->orders_order_in|escape}</span>
                                        <span class="font_12 text_500 text_grey mb-q">{$order->date|date} | {$order->date|time}</span></div>
                                        {if $order->note}
                                        <div class="tag tag-chanel_search mb-q">{include file='svg_icon.tpl' svgId='warn_icon'} {$order->note|escape}</div>
                                        {/if}

                                        {get_design_block block="orders_list_name" vars=['order' => $order]}
                                    </div>

                                    <div class="okay_list_boding okay_list_order_status">
                                        <div class="text_600 font_14" style="color: #{$order->status_color};">{$orders_status[$order->status_id]->name|escape}</div>
                                    </div>

                                    <div class="okay_list_boding okay_list_order_product_count">
                                        <span>{$order->purchases|count} {$btr->orders_unit|escape}</span>
                                        {if $order->purchases|count > 0}
                                            <span  class="fn_orders_toggle">
                                                <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i>
                                            </span>
                                        {/if}
                                    </div>

                                    <div class="okay_list_boding okay_list_orders_price">
                                        <div class="input-group">
                                            <span class="form-control">
                                                {$order->total_price|escape}
                                            </span>
                                            <span class="input-group-addon">
                                                {$currency->code|escape}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="okay_list_boding okay_list_order_marker">
                                        <span class="fn_ajax_label_wrapper">
                                            <span class="fn_labels_show box_labels_show">{include file='svg_icon.tpl' svgId='tag'} <span>{$btr->general_labels|escape} </span> </span>

                                            <div class='fn_labels_hide box_labels_hide'>
                                                <span class="heading_label">{$btr->orders_choose|escape} <i class="fn_delete_labels_hide btn_close delete_labels_hide">{include file='svg_icon.tpl' svgId='delete'}</i></span>
                                                <ul class="option_labels_box">
                                                    {foreach $labels as $l}
                                                        <li class="fn_ajax_labels" data-order_id="{$order->id}"  style="background-color: #{$l->color|escape}">
                                                            <input id="l{$order->id}_{$l->id}" type="checkbox" class="hidden_check_1"  value="{$l->id}" {if is_array($order->labels_ids) && in_array($l->id,$order->labels_ids)}checked=""{/if} />
                                                            <label   for="l{$order->id}_{$l->id}" class="label_labels"><span>{$l->name|escape}</span></label>
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            </div>
                                            <div class="fn_order_labels orders_labels">
                                                {include file="labels_ajax.tpl"}
                                            </div>
                                        </span>
                                    </div>

                                    <div class="okay_list_boding okay_list_close hidden-sm-down">
                                        {*delete*}
                                        <button data-hint="{$btr->orders_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));" >
                                           {include file='svg_icon.tpl' svgId='trash'}
                                        </button>
                                    </div>
                                </div>
                                {*История заказа*}
                                <div class="okay_list_row order_history_block" style="display: none">
                                    {include 'order_history.tpl' order_history=$orders_history[$order->id]}
                                </div>

                                {*Список товаров из заказа*}
                                {if $order->purchases|count > 0}
                                <div class="okay_list_row orders_purchases_block" style="display: none">
                                    <div class="" >
                                        <div class="purchases_table">
                                            <div class="purchases_head">
                                                <div class="purchases_heading purchases_table_orders_num">№</div>
                                                <div class="purchases_heading purchases_table_orders_sku">{$btr->general_sku|escape}</div>
                                                <div class="purchases_heading purchases_table_orders_name">{$btr->general_name|escape}</div>
                                                <div class="purchases_heading purchases_table_orders_price">{$btr->general_price|escape}</div>
                                                <div class="purchases_heading col-lg-2 purchases_table_orders_unit">{$btr->general_qty|escape}</div>
                                                <div class="purchases_heading purchases_table_orders_total">{$btr->orders_total_price|escape}</div>
                                            </div>
                                            <div class="purchases_body">
                                                {foreach $order->purchases as $purchase}
                                                    <div class="purchases_body_items">
                                                        <div class="purchases_body_item">
                                                            <div class="purchases_bodyng purchases_table_orders_num">{$purchase@iteration}</div>
                                                            <div class="purchases_bodyng purchases_table_orders_sku">{$purchase->sku|default:"&mdash;"}</div>
                                                            <div class="purchases_bodyng purchases_table_orders_name">
                                                                {$purchase->product_name|escape}
                                                                {if $purchase->variant_name}({$purchase->variant_name|escape}){/if}
                                                            </div>
                                                            <div class="purchases_bodyng purchases_table_orders_price">{$purchase->price|convert} {$currency->sign|escape}</div>
                                                            <div class="purchases_bodyng purchases_table_orders_unit"> {$purchase->amount}{if $purchase->units}{$purchase->units|escape}{else}{$settings->units|escape}{/if}</div>
                                                            <div class="purchases_bodyng purchases_table_orders_total"> {($purchase->amount*$purchase->price)|convert} {$currency->sign|escape}</div>

                                                         </div>
                                                    </div>
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {/if}
                            </div>
                            {/foreach}
                        </div>
                        {*Блок массовых действий*}
                        <div class="okay_list_footer">
                            <div class="okay_list_foot_left">
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker form-control fn_change_orders">
                                        <option value="0">{$btr->general_select_action|escape}</option>
                                        <option data-item="status" value="change_status">{$btr->orders_change_status|escape}</option>
                                        <option data-item="label" value="set_label">{$btr->orders_set_label|escape}</option>
                                        <option data-item="label" value="unset_label">{$btr->orders_unset_label|escape}</option>
                                        <option data-item="remove" value="delete">{$btr->orders_permanently_delete|escape}</option>
                                    </select>
                                </div>
                                <div class="okay_list_option fn_show_label" style="display: none">
                                    <select name="change_label_id" class="selectpicker form-control px-0 fn_labels_select" >
                                        <option value="0">{$btr->general_select_label|escape}</option>
                                        {foreach $labels as $change_label}
                                            <option value="{$change_label->id}">{$change_label->name|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                                <div class="okay_list_option fn_show_status" style="display: none;">
                                    <select name="change_status_id" class="selectpicker form-control px-0 fn_labels_select">
                                        <option value="0">{$btr->general_select_status|escape}</option>
                                        {foreach $all_status as $change_status}
                                            <option value="{$change_status->id}">{$change_status->name|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class=" btn btn_small btn_blue">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->orders_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_orders'}

<script src="{$rootUrl}/backend/design/js/piecon/piecon.js"></script>

{* On document load *}
{literal}
<script>

$(function() {

    $(document).on('click','.fn_orders_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.orders_purchases_block').toggle();
    });

    $(document).on('click','.fn_history_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.order_history_block').toggle();
    });

    $(".fn_labels_show").click(function(){
        $(this).next('.fn_labels_hide').toggleClass("active_labels");
    });
    $(".fn_delete_labels_hide").click(function(){
        $(this).closest('.box_labels_hide').removeClass("active_labels");
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest(".fn_ajax_label_wrapper ").find('.active_labels').length) {
            $('.fn_labels_hide').removeClass("active_labels");
        }
        e.stopPropagation();
    });

    function compileDateString(date) {
        let day   = String(date.getDate());
        if (day.length === 1) {
            day = `0${day}`;
        }
        let month = String(Number(date.getMonth()) + 1);
        if (month.length === 1) {
            month = `0${month}`;
        }
        const year  = String(date.getFullYear());
        return `${day}-${month}-${year}`;
    }

    if($(window).width() >= 1199 ){
        $('.fn_last_week').on('click', function() {
            const date   = new Date();
            const dateTo = compileDateString(date);
            $('.fn_to_date').val(dateTo);

            date.setDate(date.getDate() - date.getDay() + 1);
            const dateFrom = compileDateString(date);
            $('.fn_from_date').val(dateFrom);

            $('.fn_date_filter').submit();
        });

        $('.fn_30_days').on('click', function() {
            const date   = new Date();
            const dateTo = compileDateString(date);
            $('.fn_to_date').val(dateTo);

            date.setDate(date.getDate() - 30);
            const dateFrom = compileDateString(date);
            $('.fn_from_date').val(dateFrom);

            $('.fn_date_filter').submit();
        });

        $('.fn_7_days').on('click', function() {
            const date   = new Date();
            const dateTo = compileDateString(date);
            $('.fn_to_date').val(dateTo);

            date.setDate(date.getDate() - 7);
            const dateFrom = compileDateString(date);
            $('.fn_from_date').val(dateFrom);

            $('.fn_date_filter').submit();
        });

        $('.fn_yesterday').on('click', function() {
            const date   = new Date();
            date.setDate(date.getDate() - 1);

            const dateTo = compileDateString(date);
            $('.fn_to_date').val(dateTo);

            const dateFrom = compileDateString(date);
            $('.fn_from_date').val(dateFrom);

            $('.fn_date_filter').submit();
        });

        $('.fn_calendar').on('click', function() {
            $(".fn_calendar_pixel").focus();
        });

        $(".fn_calendar_pixel").datepicker({
            dateFormat: 'dd-mm-yy',
            range_multiple_max: 2,
            range: 'period',
            onSelect: function(_, __, range){
                $('.fn_from_date').val(range.startDateText);
                $('.fn_to_date').val(range.endDateText);
            }
        });

        $('.fn_reset_date_filter').on('click', function() {
            $('.fn_to_date').val('');
            $('.fn_from_date').val('');
            $('.fn_date_filter').submit();
        });
    }


    $(document).on("change", ".fn_change_orders", function () {
       var item = $(this).find("option:selected").data("item");
       if(item == "status") {
           $(".fn_show_label").hide();
           $(".fn_show_status").show();

       } else if (item == "label") {
           $(".fn_show_label").show();
           $(".fn_show_status").hide();
       } else {
           $(".fn_show_label").hide();
           $(".fn_show_status").hide();
       }

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
    {/literal}
    var status = '{$status_id|escape}',
        label='{$label_id|escape}',
        from_date = '{$from_date}',
        to_date = '{$to_date}';
    {literal}
    // On document load
    $(document).on('click','.fn_start_export',function() {
        
        Piecon.setOptions({fallback: 'force'});
        Piecon.setProgress(0);
        var progress_item = $("#progressbar"); //указываем селектор элемента с анимацией
        progress_item.show();
        do_export('',progress_item);
    });

    function do_export(page,progress) {
        page = typeof(page) != 'undefined' ? page : 1;
        label = typeof(label) != 'undefined' ? label : null;
        status = typeof(status) != 'undefined' ? status : null;
        from_date = typeof(from_date) != 'undefined' ? from_date : null;
        to_date = typeof(to_date) != 'undefined' ? to_date : null;
        $.ajax({
            url: "ajax/export_orders.php",
            data: {
                page:page, 
                label:label,
                status:status, 
                from_date:from_date, 
                to_date:to_date
            },
            dataType: 'json',
            success: function(data){
                if(data && !data.end) {
                    Piecon.setProgress(Math.round(100*data.page/data.totalpages));
                    progress.attr('value',100*data.page/data.totalpages);
                    do_export(data.page*1+1,progress);
                }
                else {
                    Piecon.setProgress(100);
                    progress.attr('value','100');
                    window.location.href = 'files/export/export_orders.csv';
                    progress.fadeOut(500);
                }
            },
            error:function(xhr, status, errorThrown) {
                alert(errorThrown+'\n'+xhr.responseText);
            }
        });
    }
});
</script>
{/literal}
