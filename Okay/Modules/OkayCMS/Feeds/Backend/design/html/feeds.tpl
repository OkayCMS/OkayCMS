{* Title *}
{$meta_title = $btr->okay_cms__feeds__feeds scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okay_cms__feeds__feeds|escape}
            </div>
            <div class="box_btn_heading">
                    <a class="btn btn_small btn-info" href="{url controller='OkayCMS.Feeds.FeedAdmin'}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->okay_cms__feeds__feeds__add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {*Блок фильтров*}
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->general_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="fn_step-0 boxed_sorting toggle_body_wrap off fn_card">
                    <div class="row">
                        <div class="col-md-9 col-lg-9 col-sm-12">
                            <select name="preset" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="location = this.value;">
                                <option value="{url keyword=null brand_id=null page=null limit=null preset=null}">{$btr->okay_cms__feeds__feeds__all_presets|escape}</option>
                                {foreach $presets as $preset_name => $preset}
                                    <option value="{url keyword=null page=null limit=null preset=$preset_name}" {if $smarty.get.preset == $preset_name}selected{/if}>{$preset_name|escape}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12">
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

    {if $feeds}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form id="list_form" method="post" class="fn_form_list fn_fast_button">
                    {$block = {get_design_block block="okay_cms__feeds__feeds__custom_block"}}
                    {if !empty($block)}
                        <div class="row custom_block">
                            {$block}
                        </div>
                    {/if}

                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="okay_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_feed_name">{$btr->okay_cms__feeds__feeds__name|escape}</div>
                            <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading okay_list_setting okay_list_pages_setting">{$btr->general_activities|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                        {*Параметры элемента*}
                        <div id="sortable" class="okay_list_body sortable">
                            {foreach $feeds as $feed}
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <input type="hidden" name="positions[{$feed->id}]" value="{$feed->position}">

                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$feed->id}" name="check[]" value="{$feed->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$feed->id}"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_feed_name">
                                            <a href="{url controller='OkayCMS.Feeds.FeedAdmin' id=$feed->id return=$smarty.server.REQUEST_URI}">
                                                {$feed->name|escape}
                                            </a>

                                            {get_design_block block="okay_cms__feeds__feeds__list_name"}
                                        </div>

                                        <div class="okay_list_boding okay_list_status">
                                            {*visible*}
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $feed->enabled}fn_active_class{/if}" data-controller="okay_cms__feed" data-action="enabled" data-id="{$feed->id}" name="enabled" value="1" type="checkbox"  {if $feed->enabled}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_setting okay_list_pages_setting">
                                            {*open*}
                                            <a href="{url_generator route="OkayCMS.Feeds.Feed" url=$feed->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='eye'}
                                            </a>

                                            {*copy*}
                                            <button data-hint="{$btr->okay_cms__feeds__feeds__duplicate|escape}" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='icon_copy'}
                                            </button>

                                            {get_design_block block="okay_cms__feeds__feeds__icons"}
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->okay_cms__feeds__feeds__delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->okay_cms__feeds__feeds__no|escape}</div>
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

<style>
    .okay_list .okay_list_feed_name {
        width: calc(100% - 350px);
        position: relative;
        text-align: left;
    }

    @media only screen and (max-width : 991px) {
        .okay_list .okay_list_feed_name {
            width: calc(100% - 435px);
        }
    }

    @media only screen and (max-width : 767px) {
        .okay_list .okay_list_feed_name {
            width: calc(100% - 200px);
        }
    }

    @media only screen and (max-width : 575px) {
        .okay_list .okay_list_feed_name {
            width: calc(100% - 0px);
        }
    }
</style>