{* Title *}
{$meta_title=$btr->general_languages scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->languages_site|escape}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info add" href="{url controller=LanguageAdmin}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->languages_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="alert alert--icon alert--warning">
    <div class="alert__content">
        <div class="alert__title">{$btr->alert_warning|escape}</div>
        <p>{$btr->languages_alert_text1}</p>
        <p>{$btr->languages_alert_text2}</p>
    </div>
</div>

{*Главная форма страницы*}
{if $languages}
    <div class="boxed fn_toggle_wrap">
        <form method="post" class="fn_form_list">
            <input type="hidden" name="session_id" value="{$smarty.session.id}" />
            <div class="okay_list products_list fn_sort_list">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                    <div class="okay_list_heading okay_list_languages_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>
                {*Параметры элемента*}
                <div class="okay_list_body sortable">
                    {foreach $languages as $language}
                        <div class="fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                                <input type="hidden" name="positions[]" value="{$language->id}">

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>
                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$language->id}" name="check[]" value="{$language->id}" />
                                    <label class="okay_ckeckbox" for="id_{$language->id}"></label>
                                </div>
                                <div class="okay_list_boding okay_list_photo">
                                    <a href="{url controller=LanguageAdmin id=$language->id return=$smarty.server.REQUEST_URI}">
                                        {if is_file("{$config->lang_images_dir}{$language->label}.png")}
                                            <img src="{("{$language->label}.png")|resize:55:55:false:$config->lang_resized_dir}" />
                                        {/if}
                                    </a>
                                </div>
                                <div class="okay_list_boding okay_list_languages_name">
                                    <a href="{url controller=LanguageAdmin id=$language->id return=$smarty.server.REQUEST_URI}">
                                        {$language->name|escape} [{$language->label|escape}]
                                    </a>
                                </div>

                                <div class="okay_list_boding okay_list_status">
                                    {*visible*}
                                    <label class="switch switch-default">
                                        <input class="switch-input fn_ajax_action {if $language->enabled}fn_active_class{/if}" data-controller="language" data-action="enabled" data-id="{$language->id}" name="enabled" value="1" type="checkbox"  {if $language->enabled}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                                <div class="okay_list_boding okay_list_close">
                                    {*delete*}
                                    <button data-hint="{$btr->general_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim"  data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                                <option value="enable">{$btr->languages_enable|escape}</option>
                                <option value="disable">{$btr->languages_disable|escape}</option>
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
{else}
    {$btr->languages_no_list|escape}
{/if}
