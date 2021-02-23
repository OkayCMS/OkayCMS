{if $user->id}
    {$meta_title = $user->name|escape scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->user_user|escape} {$user->name|escape}
            </div>
        </div>
    </div>
    <div class="col-md-12 col-lg-12 col-sm-12 float-xs-right"></div>
</div>

{*Вывод успешных сообщений*}
{if $message_success}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--success">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_success=='updated'}
                        {$btr->user_updated|escape}
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

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_error=='login_exists'}
                        {$btr->user_already_registered|escape}
                    {elseif $message_error=='empty_name'}
                        {$btr->user_name|escape}
                    {elseif $message_error=='empty_email'}
                        {$btr->user_email|escape}
                    {else}
                        {$message_error|escape}
                    {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data" class="clearfix">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />

    <div class="row">
        <div class="col-md-12">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                     {$btr->user_options|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="mb-1">
                                <div class="heading_label">{$btr->index_name|escape}</div>
                                <div class="">
                                    <input class="form-control mb-h" name="name" type="text" value="{$user->name|escape}"/>
                                    <input name="id" type="hidden" value="{$user->id|escape}"/>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="heading_label">{$btr->index_last_name|escape}</div>
                                <div class="">
                                    <input class="form-control mb-h" name="last_name" type="text" value="{$user->last_name|escape}"/>
                                    <input name="id" type="hidden" value="{$user->id|escape}"/>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="heading_label">{$btr->general_phone|escape}</div>
                                <div class="">
                                    <input class="form-control mb-h" name="phone" type="text" value="{$user->phone|phone}"/>
                                    <input name="id" type="hidden" value="{$user->id|escape}"/>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="heading_label">{$btr->general_adress|escape}</div>
                                <div class="">
                                    <input name="address" class="form-control" type="text" value="{$user->address|escape}" />
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="heading_label">{$btr->general_registration_date|escape}</div>
                                <div class="">
                                    <input name="" class="form-control" type="text" disabled value="{$user->created|date}" />
                                </div>
                            </div>
                            {get_design_block block="user_fields_1"}
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-12">
                            <div class="mb-1">
                                <div class="heading_label">{$btr->general_group|escape}</div>
                                <div class="">
                                    <select name="group_id" class="selectpicker form-control">
                                        <option value="0">{$btr->user_not_in_group|escape}</option>
                                        {foreach $groups as $g}
                                            <option value="{$g->id}" {if $user->group_id == $g->id}selected{/if}>{$g->name|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="mb-1">
                                <div class="heading_label">E-mail</div>
                                <div class="">
                                    <input name="email" class="form-control" type="text" value="{$user->email|escape}" />
                                </div>
                            </div>
                             <div class="mb-1">
                                <div class="heading_label">{$btr->user_last_ip|escape}</div>
                                <div class="">
                                    <input name="" class="form-control" type="text" disabled value="{$user->last_ip|escape}" />
                                </div>
                            </div>
                            {get_design_block block="user_fields_2"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {$block = {get_design_block block="user_custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}
    
{*История покупок*}
{if $user->orders}
    <div class="row">
        <div class="col-md-12">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->user_orders|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="">
                        <div class="okay_list products_list">
                            <div class="okay_list_head">
                                <div class="okay_list_heading okay_list_user_number">№ </div>
                                <div class="okay_list_heading okay_list_user_name">{$btr->general_full_name|escape}</div>
                                <div class="okay_list_heading okay_list_user_date">{$btr->general_date|escape}</div>
                                <div class="okay_list_heading okay_list_user_price">{$btr->coupons_order_price|escape}</div>
                            </div>
                            <div class="okay_list_body">
                                {foreach $user->orders as $order}
                                    <div class="fn_row okay_list_body_item">
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding okay_list_user_number">
                                                <a href="{url controller=OrderAdmin id=$order->id return=$smarty.server.REQUEST_URI}">{$btr->general_order_number|escape} {$order->id}</a>
                                            </div>
                                            <div class="okay_list_boding okay_list_user_name">
                                                <span>{$order->name|escape} {$order->last_name|escape}</span>
                                                {if $order->note}
                                                    <div class="note">{$order->note|escape}</div>
                                                {/if}
                                                {if $order->paid}
                                                    <div class="order_paid">
                                                        <span class="tag tag-success">{$btr->general_paid|escape}</span>
                                                    </div>
                                                {/if}
                                                {get_design_block block="user_order_username" vars=['order' => $order]}
                                            </div>
                                            <div class="okay_list_boding okay_list_user_date">
                                                <div>{$order->date|date} | {$order->date|time}</div>
                                            </div>

                                            <div class="okay_list_boding okay_list_user_price">
                                                <div class="input-group">
                                                    <span class="form-control">
                                                        {$order->total_price|escape}
                                                    </span>
                                                    <span class="input-group-addon">
                                                        {$currency->sign|escape}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
    <div class="row">
       <div class="col-lg-12 col-md-12 mb-2">
            <button type="submit" class="btn btn_small btn_blue float-md-right">
                {include file='svg_icon.tpl' svgId='checked'}
                <span>{$btr->general_apply|escape}</span>
            </button>
        </div>
    </div>
</form>
