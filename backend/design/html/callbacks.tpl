{* Title *}
{$meta_title=$btr->callbacks_order scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
               {$btr->callbacks_requests|escape} - {$callbacks_count}
            </div>
        </div>
    </div>

    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="CallbacksAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->callbacks_search|escape}" type="text" value="{$keyword|escape}" >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn_blue"><i class="fa fa-search"></i> <span class="hidden-md-down"></span></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->general_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="boxed_sorting toggle_body_wrap off fn_card">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <select class="selectpicker form-control" onchange="location = this.value;">
                            <option value="{url keyword=null page=null status=null}" {if $status == null}selected{/if}>{$btr->callbacks_all|escape}</option>
                            <option value="{url keyword=null page=null status='processed'}" {if $status == 'processed'}selected{/if}>{$btr->general_filter_processed|escape}</option>
                            <option value="{url keyword=null page=null status='unprocessed'}" {if $status == 'unprocessed'}selected{/if}>{$btr->general_filter_unprocessed|escape}</option>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-3 col-sm-12 pull-right">
                        <div class="pull-right">
                            <select onchange="location = this.value;" class="selectpicker form-control">
                                <option value="{url limit=5}" {if $current_limit == 5}selected{/if}>{$btr->general_show_by|escape} 5</option>
                                <option value="{url limit=10}" {if $current_limit == 10}selected{/if}>{$btr->general_show_by|escape} 10</option>
                                <option value="{url limit=25}" {if $current_limit == 25}selected{/if}>{$btr->general_show_by|escape} 25</option>
                                <option value="{url limit=50}" {if $current_limit == 50}selected{/if}>{$btr->general_show_by|escape} 50</option>
                                <option value="{url limit=100}" {if $current_limit == 100}selected=""{/if}>{$btr->general_show_by|escape} 100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {$block = {get_design_block block="callbacks_custom_block"}}
        {if $block}
            <div class="custom_block">
                {$block}
            </div>
        {/if}

    </div>
    {if $callbacks}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list" method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                    <div class="post_wrap okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_comments_name">{$btr->callbacks_requests|escape}</div>
                            <div class="okay_list_heading okay_list_comments_btn"></div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {foreach $callbacks as $callback}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$callback->id}" name="check[]" value="{$callback->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$callback->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_comments_name">
                                            <div class="okay_list_text_inline text_600 mb-h mr-1" style="display: block">
                                                {$callback->name|escape}
                                            </div>
                                            <div class="okay_list_text_inline mb-h">
                                                <span class="text_grey text_bold">{$btr->general_phone|escape} </span>{$callback->phone|phone}
                                            </div>

                                            {get_design_block block="callbacks_admin_extended" vars=['callback'=>$callback]}

                                            <div class="mb-h">
                                                <span class="text_grey text_bold">{$btr->general_message|escape} </span>
                                                {$callback->message|escape|nl2br}
                                            </div>
                                            <div class="text_grey mb-h">
                                                {$btr->general_request_sent|escape} <span class="tag tag-default">{$callback->date|date} | {$callback->date|time}</span>
                                                {$btr->general_from_page|escape} <a href="{$callback->url|escape}" target="_blank">{$callback->url|escape}</a>
                                            </div>
                                            <div class="hidden-md-up mt-q">
                                                <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action fn_callbacks_toggle {if $callback->processed}hidden{/if}" data-controller="callback" data-action="processed" data-id="{$callback->id}">
                                                    {$btr->general_process|escape}
                                                </button>
                                                <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action fn_callbacks_toggle fn_active_class {if !$callback->processed}hidden{/if}" data-controller="callback" data-action="processed" data-id="{$callback->id}">
                                                    {$btr->general_unprocess|escape}
                                                </button>
                                             </div>
                                            <div class="mb-q fn_ajax_block admin_note" data-id="{$callback->id}" data-controller="callback">
                                                <span class="text_dark text_bold">{$btr->callbacks_admin_notes|escape}</span>
                                                <span class="fn_an_text">{$callback->admin_notes|escape|nl2br}</span>
                                                <div>
                                                    <a href="javascript:;" class="fn_an_edit">{$btr->callbacks_edit|escape}</a>
                                                </div>
                                                <div class="fn_an_edit_block hidden">
                                                    <textarea class="fn_ajax_element form-control okay_textarea okay_textarea--small mt-h mb-h" name="admin_notes">{$callback->admin_notes|escape|nl2br}</textarea>
                                                    <p><a href="javascript:;" class="fn_an_save">{$btr->general_apply|escape}</a></p>
                                                </div>
                                            </div>

                                            {get_design_block block="callbacks_item" vars=['callback' => $callback]}
                                        </div>

                                        <div class="okay_list_boding okay_list_comments_btn">
                                            <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action fn_callbacks_toggle {if $callback->processed}hidden{/if}" data-controller="callback" data-action="processed" data-id="{$callback->id}">
                                                {$btr->general_process|escape}
                                            </button>
                                            <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action fn_callbacks_toggle fn_active_class {if !$callback->processed}hidden{/if}" data-controller="callback" data-action="processed" data-id="{$callback->id}">
                                                {$btr->general_unprocess|escape}
                                            </button>

                                            {get_design_block block="callbacks_buttons" vars=['callback' => $callback]}
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->general_delete_request|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker form-control">
                                        <option value="processed">{$btr->general_process|escape}</option>
                                        <option value="unprocessed">{$btr->general_unprocess|escape}</option>
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
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->general_no_request|escape}</div>
        </div>
    {/if}
</div>

{literal}
    <script>
        $(function() {
            $(document).on('click', '.fn_an_edit', function() {
                var block = $(this).closest('.fn_ajax_block');
                block.find('.fn_an_edit_block').removeClass("hidden");
                $(this).addClass("hidden");
            });
            $(document).on('click', '.fn_an_save', function() {
                var block = $(this).closest('.fn_ajax_block');
                block.find('.fn_an_text').text(block.find('[name="admin_notes"]').val());
                ajax_action(block);

                block.find('.fn_an_edit_block').addClass("hidden");
                block.find('.fn_an_edit').removeClass("hidden");
            });
        });
    </script>
{/literal}