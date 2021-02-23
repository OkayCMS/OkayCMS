{* Title *}
{$meta_title = $btr->pages_site scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->pages_site|escape}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=PageAdmin}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->pages_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_error == 'url_system'}
                        {$btr->pages_delete_error_url|escape}
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
<div class="boxed fn_toggle_wrap">
    {if $pages}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form id="list_form" method="post" class="fn_form_list fn_fast_button">
                    {$block = {get_design_block block="pages_custom_block"}}
                    {if !empty($block)}
                        <div class="row custom_block">
                            {$block}
                        </div>
                    {/if}

                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="pages_wrap okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_page_name">{$btr->pages_name|escape}</div>
                            <div class="okay_list_heading okay_list_pages_group"></div>
                            <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading okay_list_setting okay_list_pages_setting">{$btr->general_activities|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                        {*Параметры элемента*}
                        <div id="sortable" class="okay_list_body sortable">
                            {foreach $pages as $page}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <input type="hidden" name="positions[{$page->id}]" value="{$page->position}">

                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$page->id}" name="check[]" value="{$page->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$page->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_page_name">
                                            <a href="{url controller=PageAdmin id=$page->id return=$smarty.server.REQUEST_URI}">
                                                {$page->name|escape}
                                            </a>

                                            {get_design_block block="pages_list_name"}
                                        </div>

                                        <div class="okay_list_boding okay_list_pages_group">
                                        </div>

                                        <div class="okay_list_boding okay_list_status">
                                            {*visible*}
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $page->visible}fn_active_class{/if}" data-controller="page" data-action="visible" data-id="{$page->id}" name="visible" value="1" type="checkbox"  {if $page->visible}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_setting okay_list_pages_setting">
                                            {*open*}
                                            <a href="{url_generator route="page" url=$page->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='eye'}
                                            </a>

                                            {*copy*}
                                            <button data-hint="{$btr->pages_dublicate|escape}" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='icon_copy'}
                                            </button>

                                            {get_design_block block="pages_icon"}
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
                                        <option value="duplicate">{$btr->general_create_dublicate|escape}</option>
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
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->pages_no|escape}</div>
        </div>
    {/if}
</div>

{literal}
    <script>
        $(function() {
            // Дублировать страницу
            $(document).on("click", ".fn_copy", function () {
                $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
                $(this).closest(".fn_form_list").find('select[name="action"] option[value=duplicate]').attr('selected', true);
                $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
                $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').click();
                $(this).closest(".fn_form_list").submit();
            });
        });
    </script>
{/literal}

