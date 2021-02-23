{* Title *}
{$meta_title=$btr->general_feedback scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                {if $feedbacks_count > 0}
                    {$btr->general_feedback|escape} - {$feedbacks_count}
                {else}
                    {$btr->general_no_request|escape}
                {/if}
            </div>
        </div>
    </div>

    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="FeedbacksAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->feedbacks_search|escape}" type="text" value="{$keyword|escape}" >
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
                            <option value="{url keyword=null page=null status=null}" {if $status == null}selected{/if}>{$btr->feedbacks_all|escape}</option>
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
        </div>
    </div>
    {if $feedbacks}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post" class="fn_form_list">
                    {$block = {get_design_block block="feedbacks_custom_block"}}
                    {if !empty($block)}
                        <div class="row custom_block">
                            {$block}
                        </div>
                    {/if}


                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                    <div class="post_wrap okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_comments_name">{$btr->general_messages|escape}</div>
                            <div class="okay_list_heading okay_list_comments_btn"></div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {function name=comments_tree level=0}
                                {foreach $feedbacks as $feedback}
                                    <div class="fn_row okay_list_body_item {if $level > 0}admin_note2{/if}">
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding okay_list_check">
                                                <input class="hidden_check" type="checkbox" id="id_{$feedback->id}" name="check[]" value="{$feedback->id}"/>
                                                <label class="okay_ckeckbox" for="id_{$feedback->id}"></label>
                                            </div>

                                            <div class="okay_list_boding okay_list_comments_name {if $level > 0}admin_note{/if}">
                                                <div class="okay_list_text_inline text_600 mb-h mr-1" style="display: block">
                                                    {$feedback->name|escape}
                                                </div>
                                                <div class="okay_list_text_inline mb-h">
                                                    <span class="text_grey text_bold">Email: </span> {$feedback->email|escape}
                                                </div>
                                                <div class=" mb-q">
                                                    <span class="text_grey text_bold">{$btr->general_message|escape} </span>
                                                     {$feedback->message|escape|nl2br}
                                                </div>
                                                <div class="text_grey">
                                                    {$btr->general_request_sent|escape}  <span class="tag tag-default">{$feedback->date|date} | {$feedback->date|time}</span>
                                                </div>
                                                
                                                <div class="hidden-md-up mt-q">
                                                    {if !$feedback->processed}
                                                    <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action {if $feedback->processed}fn_active_class{/if}" data-controller="feedback" data-action="processed" data-id="{$feedback->id}" onclick="$(this).hide();">
                                                        {$btr->general_process|escape}
                                                    </button>
                                                    {/if}
                                                    <div class="answer_wrap {if $level > 0 || !$feedback->processed}hidden{/if}">
                                                        <button type="button" data-parent_id="{$feedback->id}" data-user_name="{$feedback->name|escape}" data-toggle="modal" data-target="#answer_popup" class="btn btn_small btn-outline-info fn_answer">
                                                            {$btr->general_answer|escape}
                                                        </button>
                                                    </div>
                                                 </div>

                                                {get_design_block block="feedbacks_item" vars=['feedback' => $feedback]}
                                                
                                            </div>

                                            <div class="okay_list_boding okay_list_comments_btn">
                                                {if !$feedback->processed}
                                                    <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action {if $feedback->processed}fn_active_class{/if}" data-controller="feedback" data-action="processed" data-id="{$feedback->id}" onclick="$(this).hide();">
                                                        {$btr->general_process|escape}
                                                    </button>
                                                {/if}
                                                <div class="answer_wrap fn_answer_btn" {if $level > 0 || !$feedback->processed}style="display: none;"{/if}>
                                                    <button type="button" data-feedback_id="{$feedback->id}" data-user_name="{$feedback->name|escape}" data-toggle="modal" data-target="#answer_popup" class="btn btn_small btn-outline-info fn_answer">
                                                        {$btr->general_answer|escape}
                                                    </button>
                                                </div>

                                                {get_design_block block="feedbacks_buttons" vars=['feedback' => $feedback]}
                                            </div>
                                            <div class="okay_list_boding okay_list_close">
                                                {*delete*}
                                                <button data-hint="{$btr->general_delete_request|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                                    {include file='svg_icon.tpl' svgId='trash'}
                                                </button>
                                            </div>
                                        </div>
                                        {if isset($admin_answer[$feedback->id])}
                                            {comments_tree feedbacks=$admin_answer[$feedback->id] level=$level+1}
                                        {/if}
                                    </div>
                                {/foreach}
                            {/function}
                            {comments_tree feedbacks=$feedbacks}
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
                                        <option value="approve">{$btr->general_process|escape}</option>
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
            <div class="text_grey">{$btr->feedbacks_no|escape}</div>
        </div>
    {/if}
</div>

{*Форма ответа на сообщение*}
<div id="answer_popup" class="modal fade show" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="card-header">
                <div class="heading_modal">{$btr->general_answer|escape}</div>
            </div>
            <div class="modal-body">
                <form class="form-horizontal " method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                    <input class="fn_feedback_id" type="hidden" name="feedback_id" value="" />
                    <div class="form-group">
                        <textarea class="fn_comment_area form-control okay_textarea" placeholder="{$btr->general_enter_answer|escape}" name="text" rows="10" cols="50"></textarea>
                    </div>
                    <button type="submit" name="feedback_answer" value="1" class="btn btn_small btn_blue mx-h">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_answer|escape}</span>
                   </button>
                    <button type="button" class="btn btn_small btn-danger mx-h" data-dismiss="modal">
                        {include file='svg_icon.tpl' svgId='delete'}
                        <span>{$btr->general_cancel|escape}</span>
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
{literal}
<script>
$(function() {
    $('.fn_answer').on('click',function(){
        $('.fn_feedback_id').val($(this).data('feedback_id'));
        $('.fn_comment_area').html($(this).data('user_name')+', ');
    });

});
</script>
{/literal}
