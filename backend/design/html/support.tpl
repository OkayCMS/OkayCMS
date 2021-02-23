{* Title *}
{$meta_title=$btr->support_support scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-10 col-md-9 col-sm-12 col-xs-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if $topics_count}
                    {$btr->support_msg|escape} {$topics_count}
                {else}
                    {$btr->index_support|escape}
                {/if}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=TopicAdmin}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->support_add|escape}</span>
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

{*Главная форма страницы*}
{if $topics}
    <div class="boxed fn_toggle_wrap">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="orders_list okay_list fn_sort_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_support_num">{$btr->support_number|escape}</div>
                            <div class="okay_list_heading okay_list_support_name">{$btr->support_theme|escape}</div>
                            <div class="okay_list_heading okay_list_support_status">{$btr->support_status|escape}</div>
                            <div class="okay_list_heading okay_list_support_time">{$btr->support_time|escape}</div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {foreach $topics as $topic}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding okay_list_support_num">
                                            <div class="text_dark text_600 txt_center mb-q">{$topic->id|escape}</div>
                                            <span class="text_grey font_12">{$topic->created|date} {$topic->created|time}</span>

                                        </div>

                                        <div class="okay_list_boding okay_list_support_name">
                                            <div class="mb-q">
                                                <a href="{url controller=TopicAdmin id=$topic->id page=null return=$smarty.server.REQUEST_URI}">{$topic->header|escape}</a>
                                            </div>
                                            <div class="">
                                                {$btr->support_last_answer|escape}
                                                {if $topic->last_comment}
                                                    <span class="tag tag-default">{$topic->last_comment|date} {$topic->last_comment|time}</span>
                                                {/if}
                                            </div>
                                        </div>

                                        <div class="okay_list_boding okay_list_support_status ">
                                            {if $topic->status == 'received'}
                                                <span class="text_success">{$btr->support_waiting|escape}</span>
                                            {elseif $topic->status == 'sent'}
                                                <span class="text_success">{$btr->support_reseived|escape}</span>
                                            {elseif $topic->status == 'closed'}
                                                <span class="text_grey">{$btr->support_closed|escape}</span>
                                            {else}
                                               <span class="text_grey">unknown</span>
                                            {/if}
                                        </div>
                                        <div class="okay_list_boding okay_list_support_time {if $topic->spent_time < 0}text_success{/if}">
                                            {$topic->spent_time|balance:false}
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{else}

    {*Вывод ошибок*}
    {if $message_error}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert--center alert--icon alert--error">
                    <div class="alert__content">
                        <div class="alert__title">
                            {if $message_error == 'empty_key'}
                                {$btr->support_no_keys|escape}
                            {elseif $message_error == 'domain_not_found'}
                                {$btr->support_no_domain|escape}
                            {elseif $message_error == 'domain_disabled'}
                                {$btr->support_domain_blocked|escape}
                            {elseif $message_error == 'wrong_key'}
                                {$btr->support_wrong_keys|escape}
                            {elseif $message_error == 'domain_has_already_gotten_keys'}
                                {$btr->support_already_receive_keys|escape}
                            {elseif $message_error == 'request_has_already_sent'}
                                {$btr->support_already_sent|escape}
                            {elseif $message_error == 'localhost'}
                                {$btr->support_local|escape}
                            {else}
                                {$message_error|escape}
                            {/if}
                        </div>
                        
                        <div class="text_box mt-1">
                            <form method="post">
                                {if in_array($message_error, array('empty_key', 'domain_not_found'))}
                                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                                    <input class="btn btn_small btn-warning mr-1" type="submit" name="get_new_keys" value="{$btr->support_get_keys|escape}"/>
                                {/if}
                                <input class="btn btn_small btn-info fn_manual_keys_button" type="button" value="{$btr->support_manual_save_keys|escape}"/>
                            </form>
                        </div>
                        
                        <div class="text_box mt-1 fn_manual_keys_block" style="display: none">
                            <form method="post">
                                <input type="hidden" name="session_id" value="{$smarty.session.id}">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <div class="heading_label">Public key</div>
                                        <textarea name="public_key" class="form-control okay_textarea"></textarea>
                                    </div>
                                    <div class="col-lg-6 col-md-6 pl-0">
                                        <div class="heading_label">Private key</div>
                                        <textarea name="private_key" class="form-control okay_textarea"></textarea>
                                    </div>
                                    <div class="col-lg-12 col-md-12 mt-1">
                                        <button type="submit" name="manual_save_keys" value="1" class="fn_step-15 btn btn_small btn_blue float-md-right">
                                            {include file='svg_icon.tpl' svgId='checked'}
                                            <span>{$btr->general_apply|escape}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert">
                    <div class="alert__content">
                        <div class="alert__title">{$btr->support_no|escape}</div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/if}

<script>
    $('.fn_manual_keys_button').on('click', function () {
        $('.fn_manual_keys_block').slideToggle();
    });
</script>
