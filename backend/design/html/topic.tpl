{* Title *}
{if $topic->id}
    {$meta_title = $topic->header|escape scope=global}
{else}
    {$meta_title = $btr->topic_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-10 col-md-9 col-sm-12 col-xs-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if $topic->id}
                {$btr->topic_number} {$topic->id|escape} ({$topic->spent_time|balance}) - {$comments_count}
                {else}
                {$btr->topic_new|escape}
                {/if}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=SupportAdmin id=null}">
                    {include file='svg_icon.tpl' svgId='return'}
                    <span>{$btr->general_back|escape}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-md-3 col-sm-12 col-xs-12">
        <div class="wrap_heading wrap_head_mob float-sm-right">
            <a class="btn btn_blue btn_small" target="_blank" href="https://okay-cms.com/support">
                {include file='svg_icon.tpl' svgId='sertificat'}
                <span class="ml-q">{$btr->support_condition|escape}</span>
            </a>
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
                        {if $message_error == 'domain_not_found'}
                        {$btr->support_no_domain|escape}
                        {elseif $message_error == 'domain_disabled'}
                        {$btr->support_domain_blocked|escape}
                        {elseif $message_error == 'wrong_key'}
                        {$btr->support_wrong_keys|escape}
                        {elseif $message_error == 'topic_not_found'}
                        {$btr->topic_no_theme|escape}
                        {elseif $message_error == 'topic_closed'}
                        {$btr->topic_closed|escape}
                        {elseif $message_error == 'localhost'}
                        {$btr->support_local|escape}
                        {elseif $message_error == 'empty_comment'}
                        {$btr->support_empty_comment|escape}
                        {elseif $message_error == 'empty_name'}
                        {$btr->support_empty_name|escape}
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
<form class="fn_form_list" method="post">
    <input type="hidden" name="session_id" value="{$smarty.session.id}">

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap ">
                <div class="heading_box">
                    {$btr->support_heading_accesses|escape}
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="heading_label">
                        {$btr->support_attention_accesses|escape}
                        </div>
                        <div class="mb-1">
                            <textarea class="form-control okay_textarea" name="accesses">{$accesses}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="boxed">
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm 12">
                            {include file='pagination.tpl'}
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            {if $comments}
                            <div class="okay_list">
                                {*Шапка таблицы*}
                                <div class="okay_list_head">
                                    <div class="okay_list_heading okay_list_topic_name">{$btr->general_name|escape}</div>
                                    <div class="okay_list_heading okay_list_topic_message">{$btr->topic_message|escape}</div>
                                    <div class="okay_list_heading okay_list_topic_time">{$btr->topic_spent_time|escape}</div>
                                </div>
                                {*Параметры элемента*}
                                <div class="okay_list_body">
                                    {foreach $comments as $comment}
                                    <div class="fn_row okay_list_body_item">
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding okay_list_topic_name">
                                                <div class="text_dark text_600 mb-q mr-1 {if $comment->is_support}text-primary{/if}">
                                                    {if $comment->is_support}Support: {/if}
                                                    {$comment->manager|escape}
                                                </div>
                                                {$btr->support_last_answer|escape}
                                                {if $topic->last_comment}
                                                <span class="tag tag-default">{$comment->created|date} {$comment->created|time}</span>
                                                {/if}
                                            </div>

                                            <div class="okay_list_boding okay_list_topic_message">
                                                {$comment->text}
                                            </div>

                                            <div class="okay_list_boding okay_list_topic_time {if $comment->spent_time < 0}text-success{/if}">
                                                {$comment->spent_time|balance}
                                            </div>
                                        </div>
                                    </div>
                                    {/foreach}
                                </div>
                            </div>
                            {else}
                            <div class="heading_box mt-1">
                                <div class="text_grey">Нет сообщений</div>
                            </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {if $topic->status!='closed'}
    <div class="row">
        <div class="col-md-12">
            <div class="boxed match fn_toggle_wrap tabs">
                <div class="heading_box">
                    Написать сообщение
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card row">
                    <div class="col-md-12">
                        {if $topic->id}
                            <div class="my-q mb-1">
                                <span class="text_700 text_primary font_16">
                                    {$topic->header|escape}{if $topic->status=='closed'} (Closed){/if}
                                </span>
                                <span class="text_400 text_grey font_14">
                                    ({$topic->created|date} {$topic->created|time})
                                </span>
                            </div>
                        {else}
                            <div class="">
                                <div class="heading_label">{$btr->topic_new_theme|escape}</div>
                                <div class="mb-1">
                                    <input name="header" class="name form-control" value="{$topic_header|escape}" type="text">
                                </div>
                            </div>
                        {/if}
                        <input name="id" type="hidden" value="{$topic->id|escape}"/>
                    </div>
                    <div class="col-md-12">
                        <div class="heading_label">{$btr->topic_message|escape}</div>
                        <textarea name="comment_text" id="fn_editor" class="form-control okay_textarea editor_small">{$topic_message|nl2br}</textarea>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 fn_action_block mt-1">
                        {if $topic->id}
                        <button type="submit" class="btn btn_small btn-danger" name="close_topic" value="1">
                            <span>{$btr->topic_close|escape}</span>
                        </button>
                        {/if}
                        <button type="submit" class="btn btn_small btn_blue float-md-right" name="new_message" value="1">
                            <span>{$btr->topic_send|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}
</form>
{if $subfolder !='/'}
    <script type="text/javascript" src="/{$subfolder}backend/design/js/tinymce_jq/tinymce.min.js"></script>
{else}
    <script type="text/javascript" src="/design/js/tinymce_jq/tinymce.min.js"></script>
{/if}
<script>
    $(function(){
        tinyMCE.init({literal}{{/literal}
            selector: "textarea.editor_small",
            height: '300',
            plugins: [
                "advlist autolink lists link image preview anchor responsivefilemanager",
                "hr visualchars autosave noneditable searchreplace wordcount visualblocks",
                "code fullscreen save charmap nonbreaking",
                "insertdatetime media table paste imagetools"
            ],
            toolbar_items_size : 'small',
            menubar:'edit view format table',
            toolbar1: "fontselect formatselect fontsizeselect | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist | forecolor backcolor | table | link unlink | fullscreen visualblocks visualchars",
            statusbar: true,
            font_formats: "Andale Mono=andale mono,times;"+
            "Arial=arial,helvetica,sans-serif;"+
            "Arial Black=arial black,avant garde;"+
            "Book Antiqua=book antiqua,palatino;"+
            "Comic Sans MS=comic sans ms,sans-serif;"+
            "Courier New=courier new,courier;"+
            "Georgia=georgia,palatino;"+
            "Helvetica=helvetica;"+
            "Impact=impact,chicago;"+
            "Symbol=symbol;"+
            "Tahoma=tahoma,arial,helvetica,sans-serif;"+
            "Terminal=terminal,monaco;"+
            "Times New Roman=times new roman,times;"+
            "Trebuchet MS=trebuchet ms,geneva;"+
            "Verdana=verdana,geneva;"+
            "Webdings=webdings;"+
            "Wingdings=wingdings,zapf dingbats",


            save_enablewhendirty: true,
            save_title: "save",
            theme_advanced_buttons3_add : "save",
            save_onsavecallback: function() {literal}{{/literal}
                $("[type='submit']").trigger("click");
                {literal}}{/literal},

            language : "{$manager->lang}",
            /* Замена тега P на BR при разбивке на абзацы
             force_br_newlines : true,
             force_p_newlines : false,
             forced_root_block : '',
             */
            {literal}}{/literal});
    });
</script>
