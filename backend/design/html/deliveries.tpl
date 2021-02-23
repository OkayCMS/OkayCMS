{* Title *}
{$meta_title=$btr->general_shipping scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-7 col-md-7">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->general_shipping|escape}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=DeliveryAdmin}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->deliveries_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {$block = {get_design_block block="deliveries_custom_block"}}
    {if $block}
        <div class="custom_block">
            {$block}
        </div>
    {/if}
    {if $deliveries}
        <form class="fn_form_list" method="post">
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
                    <div class="okay_list_heading okay_list_delivery_condit">{$btr->general_conditions|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>

                {*Параметры элемента*}
                <div class="deliveries_wrap okay_list_body sortable">
                    {foreach $deliveries as $delivery}
                        <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                               <input type="hidden" name="positions[{$delivery->id}]" value="{$delivery->position}">

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$delivery->id}" name="check[]" value="{$delivery->id}"/>
                                    <label class="okay_ckeckbox" for="id_{$delivery->id}"></label>
                                </div>

                                <div class="okay_list_boding okay_list_delivery_photo">
                                    {if $delivery->image}
                                        <a href="{url controller=DeliveryAdmin id=$delivery->id return=$smarty.server.REQUEST_URI}">
                                            <img src="{$delivery->image|escape|resize:75:75:false:$config->resized_deliveries_dir}"/>
                                        </a>
                                    {else}
                                        <img width="75" src="design/images/no_image.png"/>
                                    {/if}
                                </div>

                                <div class="okay_list_boding okay_list_delivery_name">
                                    <a href="{url controller=DeliveryAdmin id=$delivery->id return=$smarty.server.REQUEST_URI}">
                                        {$delivery->name|escape}
                                    </a>
                                    {get_design_block block="deliveries_list_name" vars=['delivery' => $delivery]}
                                    <div class="hidden-lg-up mt-q">
                                        {if $delivery->separate_payment}
                                            <div><span class="tag tag-primary">{$btr->general_paid_separately|escape}</span></div>
                                        {/if}
                                        {if $delivery->price > 0}
                                            <div><span class="tag tag-warning">{$btr->general_price|escape} {$delivery->price} {$currency->sign|escape}</span></div>
                                        {else}
                                            <div><span class="tag tag-info">{$btr->deliveries_free|escape}</span></div>
                                        {/if}
                                        {if $delivery->free_from > 0}
                                            <div><span class="tag tag-success">{$btr->deliveries_free_from|escape} {$delivery->free_from} {$currency->sign|escape}</span></div>
                                        {/if}
                                        {get_design_block block="deliveries_list_additional_blok" vars=['delivery' => $delivery]}
                                    </div>
                                </div>

                                <div class="okay_list_boding okay_list_delivery_condit">
                                    {if $delivery->separate_payment}
                                        <div><span class="tag tag-primary">{$btr->general_paid_separately|escape}</span></div>
                                    {/if}
                                    {if $delivery->price > 0}
                                        <div><span class="tag tag-warning">{$btr->general_price|escape} {$delivery->price} {$currency->sign|escape}</span></div>
                                    {else}
                                        <div><span class="tag tag-info">{$btr->deliveries_free|escape}</span></div>
                                    {/if}
                                    {if $delivery->free_from > 0}
                                        <div><span class="tag tag-success">{$btr->deliveries_free_from|escape} {$delivery->free_from} {$currency->sign|escape}</span></div>
                                    {/if}
                                    {get_design_block block="deliveries_list_additional_blok" vars=['delivery' => $delivery]}
                                </div>

                                <div class="okay_list_boding okay_list_status">
                                    {*visible*}
                                    <label class="switch switch-default">
                                        <input class="switch-input fn_ajax_action {if $delivery->enabled}fn_active_class{/if}" data-controller="delivery" data-action="enabled" data-id="{$delivery->id}" name="enabled" value="1" type="checkbox"  {if $delivery->enabled}checked=""{/if}/>
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
            <div class="text_grey">{$btr->deliveries_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_deliveries'}
