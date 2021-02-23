{* Title *}
{$meta_title=$btr->authors_authors scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->authors_authors|escape} - {$authors_count}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=AuthorAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->authors_add_author|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {if $authors}
        <div class="row">
            <div class="col-lg-12 col-md-12 ">
                <div class="fn_toggle_wrap">
                    <div class="heading_box visible_md">
                        {$btr->general_filter|escape}
                        <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                            <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                        </div>
                    </div>
                    <div class="boxed_sorting toggle_body_wrap off fn_card">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm 12">
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

        {$block = {get_design_block block="authors_custom_block"}}
        {if $block}
            <div class="custom_block">
                {$block}
            </div>
        {/if}

        <form method="post" class="fn_form_list fn_fast_button">
            <input type="hidden" name="session_id" value="{$smarty.session.id}" />

            <div class="okay_list products_list fn_sort_list">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                    <div class="okay_list_heading okay_list_authors_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    {$block = {get_design_block block="authors_icon_title"}}
                    {if $block}
                        <div class="okay_list_setting">
                            {$block}
                        </div>
                    {/if}
                    <div class="okay_list_heading okay_list_setting">{$btr->general_activities|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>

                {*Параметры элемента*}
                <div class="okay_list_body sort_extended">
                    {foreach $authors as $author}
                        <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row ">
                                <input type="hidden" name="positions[{$author->id}]" value="{$author->position}" />

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$author->id}" name="check[]" value="{$author->id}" />
                                    <label class="okay_ckeckbox" for="id_{$author->id}"></label>
                                </div>

                                <div class="okay_list_boding okay_list_photo">
                                    {if $author->image}
                                        <a href="{url controller=AuthorAdmin id=$author->id return=$smarty.server.REQUEST_URI}">
                                            <img src="{$author->image|resize:55:55:false:$config->resized_authors_dir}" alt="" /></a>
                                    {else}
                                        <img height="55" width="55" src="design/images/no_image.png"/>
                                    {/if}
                                </div>

                                <div class="okay_list_boding okay_list_authors_name">
                                    <a href="{url controller=AuthorAdmin id=$author->id return=$smarty.server.REQUEST_URI}">
                                        {$author->name|escape}
                                    </a>

                                    {get_design_block block="authors_list_name" vars=['author' => $author]}
                                </div>

                                <div class="okay_list_boding okay_list_status">
                                    {*visible*}
                                     <label class="switch switch-default ">
                                        <input class="switch-input fn_ajax_action {if $author->visible}fn_active_class{/if}" data-controller="authors" data-action="visible" data-id="{$author->id}" name="visible" value="1" type="checkbox"  {if $author->visible}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>

                                <div class="okay_list_setting">
                                    <a href="{url_generator route="author" url=$author->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='eye'}
                                    </a>

                                    {*copy*}
                                    <button data-hint="{$btr->authors_duplicate|escape}" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='icon_copy'}
                                    </button>

                                    {get_design_block block="authors_icon" vars=['author' => $author]}
                                </div>

                                <div class="okay_list_boding okay_list_close">
                                    {*delete*}
                                    <button data-hint="{$btr->authors_delete_author|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                            <select name="action" class="selectpicker form-control dropup authors_action" data-size="5">
                                <option value="delete">{$btr->general_delete|escape}</option>
                                <option value="enable">{$btr->general_do_enable|escape}</option>
                                <option value="disable">{$btr->general_do_disable|escape}</option>
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
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->authors_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{*{include file='learning_hints.tpl' hintId='hint_authors'}*} {*todo сделать обучение*}

{literal}
    <script>
        $(function() {
            $(document).on('change', '.fn_action_block select.authors_action', function () {
                var elem = $(this).find('option:selected').val();
                $('.fn_hide_block').addClass('hidden');
                if ($('.fn_' + elem).size() > 0) {
                    $('.fn_' + elem).removeClass('hidden');
                }
            });

            // Дублировать бренд
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
