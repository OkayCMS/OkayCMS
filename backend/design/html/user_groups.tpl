{* Title *}
{$meta_title=$btr->user_groups_groups scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->user_groups_groups|escape} - {$groups|count}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="index.php?controller=UserGroupAdmin&return={$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->user_groups_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {$block = {get_design_block block="groups_custom_block"}}
    {if $block}
        <div class="custom_block">
            {$block}
        </div>
    {/if}
    
    {if $groups}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list" method="post">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="groups_wrap okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_usergroups_name">{$btr->user_groups_name|escape}</div>
                            <div class="okay_list_heading okay_list_usergroups_sale">{$btr->user_groups_discount|escape}</div>
                            <div class="okay_list_heading okay_list_usergroups_counts">{$btr->user_groups_number|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body">
                            {foreach $groups as $group}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row ">
                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$group->id}" name="check[]" value="{$group->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$group->id}"></label>
                                        </div>
                                        <div class="okay_list_boding okay_list_usergroups_name">
                                            <a href="{url controller=UserGroupAdmin id=$group->id}">
                                                {$group->name|escape}
                                            </a>
                                            {get_design_block block="groups_list_name" vars=['group' => $group]}
                                        </div>
                                        <div class="okay_list_boding okay_list_usergroups_sale">
                                            <span class="tag tag-danger">{$btr->general_discount|escape} {$group->discount} %</span>
                                            {get_design_block block="groups_list_discount" vars=['group' => $group]}
                                        </div>
                                        <div class="okay_list_boding okay_list_usergroups_counts">
                                            <span>
                                                <a href="{url controller=UsersAdmin group_id=$group->id}">{$group->cnt_users}</a>
                                            </span>
                                            {get_design_block block="groups_list_users_count" vars=['group' => $group]}
                                        </div>
                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->user_groups_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
            <div class="text_grey">{$btr->user_groups_no|escape}</div>
        </div>
    {/if}
</div>
