{* Title *}
{$meta_title = $btr->faq_title scope=global}

<link rel="stylesheet" href="{$rootUrl}/Okay/Modules/OkayCMS/FAQ/Backend/design/css/faq.css">

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->faq_title|escape}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller='OkayCMS.FAQ.FAQAdmin' return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->faq_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {if $faqs}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form id="list_form" method="post" class="fn_form_list">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="pages_wrap okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_faq_name">{$btr->pages_name|escape}</div>
                            <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                        {*Параметры элемента*}
                        <div id="sortable" class="okay_list_body sortable">
                            {foreach $faqs as $faq}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <input type="hidden" name="positions[{$faq->id}]" value="{$faq->position}">

                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$faq->id}" name="check[]" value="{$faq->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$faq->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_faq_name">
                                            <a href="{url controller="OkayCMS.FAQ.FAQAdmin" id=$faq->id return=$smarty.server.REQUEST_URI}">
                                                {$faq->question|escape}
                                            </a>
                                        </div>

                                        <div class="okay_list_boding okay_list_status">
                                            {*visible*}
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $faq->visible}fn_active_class{/if}" data-controller="OkayCMS.FAQ.FAQEntity" data-action="visible" data-id="{$faq->id}" name="visible" value="1" type="checkbox"  {if $faq->visible}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->pages_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
            </div>
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->no_faqs|escape}</div>
        </div>
    {/if}
</div>