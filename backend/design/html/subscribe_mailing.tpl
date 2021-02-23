{* Title *}
{$meta_title=$btr->subscribe_mailing_subscribes scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">
            {if $keyword && $subscribes_count>0}
                {$btr->subscribe_mailing_subscribes|escape} - {$subscribes_count}
            {elseif $subscribes_count>0}
                {$btr->subscribe_mailing_subscribes|escape} - {$subscribes_count}
            {/if}

            {if $subscribes_count>0}
                <div class="fn_start_export export_block export_subscribes hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->subscribe_mailing_export|escape}">
                    {include file='svg_icon.tpl' svgId='export'}
                </div>
            {/if}
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {if $subscribes}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list" method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}"/>

                    <div class="users_wrap okay_list">
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_subscribe_name">{$btr->subscribe_mailing_email|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body sortable">
                            {foreach $subscribes as $subscribe}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_check">
                                        <input class="hidden_check" type="checkbox" id="id_{$subscribe->id}" name="check[]" value="{$subscribe->id}"/>
                                        <label class="okay_ckeckbox" for="id_{$subscribe->id}"></label>
                                    </div>

                                    <div class="okay_list_boding okay_list_subscribe_name">
                                        <a class="link" href="mailto:{$subscribe->email|escape}">
                                            {$subscribe->email|escape}
                                        </a>
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
                        <div class="okay_list_footer fn_action_block">
                            <div class="okay_list_foot_left">
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker form-control">
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
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->subscribe_mailing_mo|escape}</div>
        </div>
    {/if}
</div>



<script src="{$rootUrl}/backend/design/js/piecon/piecon.js"></script>
<script>
    var in_process=false;
    var sort='{$sort|escape}';

    {literal}
    $(function() {

        $(document).on('click','.fn_start_export',function(){
            Piecon.setOptions({fallback: 'force'});
            Piecon.setProgress(0);
            var progress_item = $("#progressbar"); //указываем селектор элемента с анимацией
            progress_item.show();
            do_export('',progress_item);
        });

        function do_export(page,progress) {
            page = typeof(page) != 'undefined' ? page : 1;
            $.ajax({
                url: "ajax/export_subscribes.php",
                data: {page:page, sort:sort},
                dataType: 'json',
                success: function(data){
                    if(data && !data.end)
                    {
                        Piecon.setProgress(Math.round(100*data.page/data.totalpages));
                        progress.attr('value',100*data.page/data.totalpages);
                        do_export(data.page*1+1,progress);
                    }
                    else
                    {
                        Piecon.setProgress(100);
                        progress.attr('value','100');
                        window.location.href = 'files/export_users/subscribes.csv';
                        progress.fadeOut(500);
                    }
                },
                error:function(xhr, status, errorThrown) {
                    alert(errorThrown+'\n'+xhr.responseText);
                }

            });
        }
    });
</script>
{/literal}
