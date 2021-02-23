{* Title *}
{$meta_title=$btr->brands_brands scope=global}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                {$btr->brands_brands|escape} - {$brands_count}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=BrandAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->brands_add_brand|escape}</span>
                </a>
            </div>
        </div>
    </div>
    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
                <input type="hidden" name="controller" value="BrandsAdmin">
                <div class="input-group input-group--search">
                    <input name="keyword" class="form-control" placeholder="{$btr->brands_search|escape}" type="text" value="{$keyword|escape}" >
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
    {if $brands}
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

        {$block = {get_design_block block="brands_custom_block"}}
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
                    <div class="okay_list_heading okay_list_brands_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    {$block = {get_design_block block="brands_icon_title"}}
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
                    {foreach $brands as $brand}
                        <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row ">
                                <input type="hidden" name="positions[{$brand->id}]" value="{$brand->position}" />

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$brand->id}" name="check[]" value="{$brand->id}" />
                                    <label class="okay_ckeckbox" for="id_{$brand->id}"></label>
                                </div>

                                <div class="okay_list_boding okay_list_photo">
                                    {if $brand->image}
                                        <a href="{url controller=BrandAdmin id=$brand->id return=$smarty.server.REQUEST_URI}">
                                            <img src="{$brand->image|resize:55:55:false:$config->resized_brands_dir}" alt="" /></a>
                                    {else}
                                        <img height="55" width="55" src="design/images/no_image.png"/>
                                    {/if}
                                </div>

                                <div class="okay_list_boding okay_list_brands_name">
                                    <a href="{url controller=BrandAdmin id=$brand->id return=$smarty.server.REQUEST_URI}">
                                        {$brand->name|escape}
                                    </a>

                                    {get_design_block block="brands_list_name" vars=['brand' => $brand]}
                                </div>

                                <div class="okay_list_boding okay_list_status">
                                    {*visible*}
                                     <label class="switch switch-default ">
                                        <input class="switch-input fn_ajax_action {if $brand->visible}fn_active_class{/if}" data-controller="brands" data-action="visible" data-id="{$brand->id}" name="visible" value="1" type="checkbox"  {if $brand->visible}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>

                                <div class="okay_list_setting">
                                    <a href="{url_generator route="brand" url=$brand->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='eye'}
                                    </a>

                                    {*copy*}
                                    <button data-hint="{$btr->brands_dublicate|escape}" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                        {include file='svg_icon.tpl' svgId='icon_copy'}
                                    </button>

                                    {get_design_block block="brands_icon" vars=['brand' => $brand]}
                                </div>

                                <div class="okay_list_boding okay_list_close">
                                    {*delete*}
                                    <button data-hint="{$btr->brands_delete_brand|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                            <select name="action" class="selectpicker form-control dropup brands_action" data-size="5">
                                <option value="delete">{$btr->general_delete|escape}</option>
                                <option value="enable">{$btr->general_do_enable|escape}</option>
                                <option value="disable">{$btr->general_do_disable|escape}</option>
                                <option value="duplicate">{$btr->general_create_dublicate|escape}</option>
                                {if $pages_count>1}
                                    <option value="move_to_page">{$btr->products_move_to_page|escape}</option>
                                {/if}
                            </select>
                        </div>
                        <div class="fn_additional_params">
                            <div class="fn_move_to_page col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                <select name="target_page" class="selectpicker form-control dropup" data-size="5">
                                    {section target_page $pages_count}
                                        <option value="{$smarty.section.target_page.index+1}">{$smarty.section.target_page.index+1}</option>
                                    {/section}
                                </select>
                            </div>
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
            <div class="text_grey">{$btr->brands_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_brands'}

{literal}
    <script>
        $(function() {
            $(document).on('change', '.fn_action_block select.brands_action', function () {
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
