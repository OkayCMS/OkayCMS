{* Title *}
{$meta_title=$btr->general_comments scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                {if !$type}
                    {$btr->general_comments} - {$comments_count}
                {elseif $type=='product'}
                    {$btr->general_comments} {$btr->comments_to_products|escape} - {$comments_count}
                {elseif $type=='post'}
                    {$btr->general_comments} {$btr->comments_to_articles|escape} - {$comments_count}
                {/if}
            </div>
        </div>
    </div>

    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="CommentsAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->comments_search|escape}" type="text" value="{$keyword|escape}" >
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn_blue"><i class="fa fa-search"></i> <span class="hidden-md-down"></span></button>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>

{*Блок фильтров*}
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
                            <option value="{url keyword=null page=null type=null status=null}" {if !$type}selected{/if}>{$btr->comments_all|escape}</option>
                            <option value="{url keyword=null page=null type=product status=null}" {if $type == 'product'}selected{/if}>{$btr->comments_to_products|escape}</option>
                            <option value="{url keyword=null page=null type=post status=null}" {if $type == 'post'}selected{/if}>{$btr->comments_to_articles|escape}</option>
                            <option value="{url keyword=null page=null type=null status='approved'}" {if $status == 'approved'}selected{/if}>{$btr->comments_filter_approved|escape}</option>
                            <option value="{url keyword=null page=null type=null status='unapproved'}" {if $status == 'unapproved'}selected{/if}>{$btr->comments_filter_unapproved|escape}</option>
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

    {$block = {get_design_block block="comments_custom_block"}}
    {if !empty($block)}
        <div class="fn_toggle_wrap custom_block">
            {$block}
        </div>
    {/if}

    {if $comments}
        <div class="row">
        {*Главная форма страницы*}
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
                        <div class="okay_list_heading okay_list_comments_name">{$btr->general_comments|escape}</div>
                        <div class="okay_list_heading okay_list_comments_btn"></div>
                        <div class="okay_list_heading okay_list_close"></div>
                    </div>
                    {*Параметры элемента*}
                    <div class="okay_list_body">
                        {function name=comments_tree level=0}
                            {foreach $comments as $comment}
                                <div class="fn_row okay_list_body_item {if $level > 0}admin_note2{/if}">
                                    <div class="okay_list_row">

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$comment->id}" name="check[]" value="{$comment->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$comment->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_comments_name {if $level > 0}admin_note{/if}">
                                            <div class="okay_list_text_inline text_600 mb-h mr-1">
                                                {$comment->name|escape}
                                            </div>
                                            {if $comment->email}
                                                <div class="okay_list_text_inline mb-h">
                                                    <span class="text_grey text_bold">Email: </span>  {$comment->email|escape}
                                                </div>
                                            {/if}
                                            <div class="mb-h">
                                                <span class="text_grey text_bold">{$btr->general_message|escape}</span>
                                                 {$comment->text|escape|nl2br}
                                            </div>
                                            <div class="text_grey">
                                                {$btr->comments_left|escape} <span class="tag tag-default">{$comment->date|date} | {$comment->date|time}</span>
                                                {if $level == 0}
                                                    {$btr->comments_to_the|escape}
                                                    {if $comment->type == "product"}
                                                        {$btr->comments_product|escape}  <a href="{url_generator route="product" url=$comment->product->url absolute=1}" target="_blank">{$comment->product->name|escape}</a>
                                                    {elseif $comment->type == "post"}
                                                        {$btr->comments_article|escape} <a href="{url_generator route="post" url=$comment->post->url absolute=1}" target="_blank">{$comment->post->name|escape}</a>
                                                    {/if}
                                                {/if}
                                            </div>
                                            <div class="hidden-md-up mt-q">
                                                {if !$comment->approved}
                                                    <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action {if $comment->approved}fn_active_class{/if}" data-controller="comment" data-action="approved" data-id="{$comment->id}" onclick="$(this).hide();">
                                                        {$btr->general_approve|escape}
                                                    </button>
                                                {/if}
                                                <div class="answer_wrap {if $level > 0 || !$comment->approved}hidden{/if}">
                                                    <button type="button" data-parent_id="{$comment->id}" data-user_name="{$comment->name|escape}" data-toggle="modal" data-target="#answer_popup" class="btn btn_small btn-outline-info fn_answer">
                                                        {$btr->general_answer|escape}
                                                    </button>
                                                </div>
                                            </div>
                                            {get_design_block block="comments_comment_info" vars=['comment' => $comment]}
                                        </div>

                                        <div class="okay_list_boding okay_list_comments_btn">
                                            {if !$comment->approved}
                                                <button type="button" class="btn btn_small btn-outline-warning fn_ajax_action {if $comment->approved}fn_active_class{/if}" data-controller="comment" data-action="approved" data-id="{$comment->id}" onclick="$(this).hide();">
                                                    {$btr->general_approve|escape}
                                                </button>
                                            {/if}
                                            <div class="answer_wrap fn_answer_btn" {if $level > 0 || !$comment->approved}style="display: none;"{/if}>
                                                <button type="button" data-parent_id="{$comment->id}" data-user_name="{$comment->name|escape}" data-toggle="modal" data-target="#answer_popup" class="btn btn_small btn-outline-info fn_answer">
                                                    {$btr->general_answer|escape}
                                                </button>
                                                {get_design_block block="comments_buttons" vars=['comment' => $comment]}
                                            </div>
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->comments_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                                {include file='svg_icon.tpl' svgId='trash'}
                                            </button>
                                        </div>

                                    </div>
                                    {if isset($children[$comment->id])}
                                        {comments_tree comments=$children[$comment->id] level=$level+1}
                                    {/if}
                                </div>

                            {/foreach}
                        {/function}
                        {comments_tree comments=$comments}
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
                                    <option value="approve">{$btr->general_approve|escape}</option>
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
            <div class="text_grey">{$btr->comments_no|escape}</div>
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
                    <input id="fn_parent_id" type="hidden" name="parent_id" value="" />
                    <div class="form-group">
                        <textarea id="fn_comment_area" class="form-control okay_textarea" placeholder="{$btr->general_enter_answer|escape}" name="text" rows="10" cols="50"></textarea>
                    </div>
                    <button type="submit" name="comment_answer" value="1" class="btn btn_small btn_blue mx-h">
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
        $('#fn_parent_id').val($(this).data('parent_id'));
        $('#fn_comment_area').html($(this).data('user_name')+', ');
    });
});

</script>
{/literal}
