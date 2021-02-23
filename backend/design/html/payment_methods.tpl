{* Title *}
{$meta_title=$btr->payment_methods_methods scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->payment_methods_methods|escape}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=PaymentMethodAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->payment_methods_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {$block = {get_design_block block="payments_custom_block"}}
    {if $block}
        <div class="custom_block">
            {$block}
        </div>
    {/if}
    {if $payment_methods}
        <form class="fn_form_list" method="post" enctype="multipart/form-data">
            <div class="okay_list products_list fn_sort_list">
                <input type="hidden" name="session_id" value="{$smarty.session.id}">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_delivery_photo">{$btr->general_image|escape}</div>
                    <div class="okay_list_heading okay_list_delivery_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_delivery_condit"></div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>

                {*Параметры элемента*}
                <div class="deliveries_wrap okay_list_body sortable">
                    {foreach $payment_methods as $payment_method}
                        <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                                <input type="hidden" name="positions[{$payment_method->id}]" value="{$payment_method->position}">

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$payment_method->id}" name="check[]" value="{$payment_method->id}"/>
                                    <label class="okay_ckeckbox" for="id_{$payment_method->id}"></label>
                                </div>

                                <div class="okay_list_boding okay_list_delivery_photo">
                                    {if $payment_method->image}
                                        <a href="{url controller=PaymentMethodAdmin id=$payment_method->id return=$smarty.server.REQUEST_URI}">
                                            <img src="{$payment_method->image|escape|resize:55:55:false:$config->resized_payments_dir}"/>
                                        </a>
                                    {else}
                                        <img height="55" width="55" src="design/images/no_image.png"/>
                                    {/if}
                                </div>

                                <div class="okay_list_boding okay_list_delivery_name">
                                    <a href="{url controller=PaymentMethodAdmin id=$payment_method->id return=$smarty.server.REQUEST_URI}">
                                        {$payment_method->name|escape}
                                    </a>
                                    {get_design_block block="payments_list_name" vars=['payment_method' => $payment_method]}
                                </div>
                                <div class="okay_list_boding okay_list_delivery_condit"></div>
                                <div class="okay_list_boding okay_list_status">
                                    {*visible*}
                                    <label class="switch switch-default">
                                        <input class="switch-input fn_ajax_action {if $payment_method->enabled}fn_active_class{/if}" data-controller="payment" data-action="enabled" data-id="{$payment_method->id}" name="visible" value="1" type="checkbox"  {if $payment_method->enabled}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>

                                <div class="okay_list_boding okay_list_close">
                                    {*delete*}
                                    <button data-hint="{$btr->general_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                        {include file='svg_icon.tpl' svgId='trash'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>

                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        <div class="okay_list_boding okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker form-control">
                                <option value="enable">{$btr->general_do_enable|escape}</option>
                                <option value="disable">{$btr->general_do_disable|escape}</option>
                                <option value="delete">{$btr->general_delete|escape}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </form>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->payment_methods_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_payments'}
