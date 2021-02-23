{if $menu->id}
    {$meta_title = $menu->name scope=global}
{else}
    {$meta_title = $btr->menu_new_menu scope=global}
{/if}
{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$menu->id}
                    {$btr->menu_new_menu|escape}
                {else}
                    {$menu->name|escape}
                {/if}
            </div>
        </div>
    </div>
</div>

{*Вывод успешных сообщений*}
{if $message_success}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--success">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_success == 'added'}
                        {$btr->menu_added|escape}
                    {elseif $message_success == 'updated'}
                        {$btr->menu_updated|escape}
                    {/if}
                    </div>
                </div>
                {if $smarty.get.return}
                    <a class="alert__button" href="{$smarty.get.return}">
                        {include file='svg_icon.tpl' svgId='return'}
                        <span>{$btr->general_back|escape}</span>
                    </a>
                {/if}
            </div>
        </div>
    </div>
{/if}

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_error == 'group_id_exists'}
                        {$btr->menu_id_exists|escape}
                    {elseif $message_error == 'empty_group_id'}
                        {$btr->menu_enter_id|escape}
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
<form method="post" enctype="multipart/form-data" class="fn_fast_button">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />
    <div class="row">
        <div class="col-xs-12">
            <div class="boxed">
                <div class="row d_flex">
                    {*Название элемента сайта*}
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="heading_label">
                            {$btr->general_name|escape}
                        </div>
                        <div class="form-group">
                            <input class="form-control mb-h" name="name" type="text" value="{$menu->name|escape}"/>
                            <input name="id" type="hidden" value="{$menu->id|escape}"/>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 col-sm-12">
                                <div class="">
                                    <span class="heading_label">{$btr->menu_id_enter|escape}</span>
                                    <input name="group_id" class="form-control" type="text" value="{$menu->group_id|escape}" />
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12">
                                <div class="">
                                    <span class="heading_label">{$btr->menu_var_for_insert|escape}</span>
                                    <input class="form-control" type="text" value="{$menu->var|escape}" readonly />
                                </div>
                            </div>
                        </div>
                        {get_design_block block="menu_general"}
                    </div>
                    {*Видимость элемента*}
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="visible" value='1' type="checkbox" id="visible_checkbox" {if $menu->visible}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        {get_design_block block="menu_switch_checkboxes"}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->menu_items|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_list products_list fn_sort_list fn_row" data-index="0">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_menu_name">
                                <span class="okay_list_menu_item">{$btr->general_name|escape}</span>
                                <span class="okay_list_menu_item quickview_hidden">{$btr->menu_general_url|escape}</span>
                            </div>
                            <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading okay_list_status">{$btr->menu_general_target_blank|escape}</div>
                            <div class="okay_list_heading okay_list_menu_setting">{$btr->menu_general_add|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                            {get_design_block block="menu_items_head_row"}
                        </div>
                        {assign var="index" value=1}
                        {function name=menu_items}
                            <div class="okay_list_body categories_wrap sortable fn_sub_rows submenu_level_{$level}" data-level="{$level}">
                                {foreach $menu_items as $menu_item}
                                    <div class="fn_row okay_list_body_item" data-index="{$index}">
                                        <div class="okay_list_row fn_sort_item">
                                            <input type="hidden" name="menu_items[id][]" value="{$menu_item->id}"/>
                                            <input type="hidden" name="menu_items[index][]" value="{$index}"/>
                                            <input type="hidden" name="menu_items[parent_index][]" value="{$parent_index}"/>
                                            <div class="okay_list_boding okay_list_drag move_zone">
                                                {include file='svg_icon.tpl' svgId='drag_vertical'}
                                            </div>
                                            <div class="okay_list_boding okay_list_menu_name">
                                                <span class="okay_list_menu_item">
                                                    <input class="form-control" type="text" name="menu_items[name][]" value="{$menu_item->name|escape}" placeholder="Введите название"/>
                                                </span>
                                                <span class="okay_list_menu_item">
                                                    <input class="form-control" type="text" name="menu_items[url][]" value="{$menu_item->url|escape}" placeholder="Введите url"/>
                                                </span>
                                            </div>
                                            <div class="okay_list_boding okay_list_status">
                                                {*visible*}
                                                <label class="switch switch-default hint-bottom-middle-t-info-s-small-mobile hint-anim fn_switch_block" data-hint="{$btr->general_enable|escape}">
                                                    <input class="switch-input fn_ajax_action fn_visible {if $menu_item->visible}fn_active_class{/if}" data-controller="menu_item" data-action="visible" data-id="{$menu_item->id}" type="checkbox"  {if $menu_item->visible}checked=""{/if}/>
                                                    <input class="form-control fn_visible_input" type="hidden" name="menu_items[visible][]" value="{$menu_item->visible|intval}"/>
                                                    <span class="switch-label"></span>
                                                    <span class="switch-handle"></span>
                                                </label>
                                            </div>
                                            <div class="okay_list_boding okay_list_status">
                                                {*is_target_blank*}
                                                <label class="switch switch-default hint-bottom-middle-t-info-s-small-mobile hint-anim fn_switch_block" data-hint="{$btr->menu_general_target_blank|escape}">
                                                    <input class="switch-input fn_ajax_action fn_is_target_blank {if $menu_item->is_target_blank}fn_active_class{/if}" data-controller="menu_item" data-action="is_target_blank" data-id="{$menu_item->id}" type="checkbox" {if $menu_item->is_target_blank}checked=""{/if}/>
                                                    <input class="form-control fn_is_target_blank_input" type="hidden" name="menu_items[is_target_blank][]" value="{$menu_item->is_target_blank|intval}"/>
                                                    <span class="switch-label"></span>
                                                    <span class="switch-handle"></span>
                                                </label>
                                            </div>
                                            <div class="okay_list_boding okay_list_menu_setting">
                                                <a href="javascript:;" data-hint="{$btr->menu_item_add|escape}" class="menu_icon_add fn_add_menuitem hint-bottom-middle-t-info-s-small-mobile hint-anim">
                                                    {include file='svg_icon.tpl' svgId='add'}
                                                </a>
                                            </div>
                                            
                                            {get_design_block block="menu_items_item_row" vars=['menu_items' => $menu_items]}
                                            
                                            <div class="okay_list_boding okay_list_close">
                                                {*delete*}
                                                <a href="javascript:;" data-hint="{$btr->general_delete|escape}" class="btn_close fn_remove_menuitem hint-bottom-right-t-info-s-small-mobile hint-anim">
                                                    {include file='svg_icon.tpl' svgId='trash'}
                                                </a>
                                            </div>
                                        </div>
                                        {menu_items menu_items=$menu_item->submenus level=$level+1 parent_index=$index++}
                                    </div>
                                {/foreach}
                            </div>
                        {/function}
                        {menu_items menu_items=$menu_items level=0 parent_index=0}
                        <div class="fn_row fn_new_menuitem okay_list_body_item" data-index="-1">
                            <div class="okay_list_row fn_sort_item">
                                <input type="hidden" name="menu_items[id][]" value="0"/>
                                <input type="hidden" name="menu_items[index][]" value="-1"/>
                                <input type="hidden" name="menu_items[parent_index][]" value="0"/>
                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>
                                <div class="okay_list_boding okay_list_menu_name">
                                    <span class="okay_list_menu_item">
                                        <input class="form-control" type="text" name="menu_items[name][]" value="" placeholder="Введите название"/>
                                    </span>
                                    <span class="okay_list_menu_item">
                                        <input class="form-control" type="text" name="menu_items[url][]" value="" placeholder="Введите url"/>
                                    </span>
                                </div>
                                <div class="okay_list_boding okay_list_status">
                                    <label class="switch switch-default hint-bottom-middle-t-info-s-small-mobile hint-anim fn_switch_block" data-hint="{$btr->general_enable|escape}">
                                        <input class="switch-input fn_visible" type="checkbox"/>
                                        <input class="form-control fn_visible_input" type="hidden" name="menu_items[visible][]" value="0"/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                                <div class="okay_list_boding okay_list_status">
                                    <label class="switch switch-default hint-bottom-middle-t-info-s-small-mobile hint-anim fn_switch_block" data-hint="{$btr->menu_general_target_blank|escape}">
                                        <input class="switch-input fn_is_target_blank" type="checkbox"/>
                                        <input class="form-control fn_is_target_blank_input" type="hidden" name="menu_items[is_target_blank][]" value="0"/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                                <div class="okay_list_boding okay_list_menu_setting">
                                    <a href="javascript:;" data-hint="{$btr->menu_item_add|escape}" class="menu_icon_add fn_add_menuitem hint-bottom-middle-t-info-s-small-mobile hint-anim">
                                        {include file='svg_icon.tpl' svgId='add'}
                                    </a>
                                </div>
                                
                                {get_design_block block="menu_items_item_row"}
                                
                                <div class="okay_list_boding okay_list_close">
                                    {*delete*}
                                    <a href="javascript:;" data-hint="{$btr->general_delete|escape}" class="btn_close fn_remove_menuitem hint-bottom-right-t-info-s-small-mobile hint-anim">
                                        {include file='svg_icon.tpl' svgId='delete'}
                                    </a>
                                </div>
                            </div>
                            <div class="okay_list_body categories_wrap sortable fn_sub_rows" data-level="-1"></div>
                        </div>
                        <div class="box_btn_heading mt-1 mb-1">
                            <button type="button" class="btn btn_small btn-info fn_add_menuitem">
                                {include file='svg_icon.tpl' svgId='plus'}
                                <span>{$btr->menu_item_add|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 mt-1">
                        <button type="submit" class="btn btn_small btn_blue float-md-right">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{literal}
    <script>

        $(document).on('click', '.fn_visible', function() {
            $(this).closest('.fn_switch_block').find('.fn_visible_input').val($(this).is(':checked')?1:0);
        });
        $(document).on('click', '.fn_is_target_blank', function() {
            $(this).closest('.fn_switch_block').find('.fn_is_target_blank_input').val($(this).is(':checked')?1:0);
        });

        var mi_index = {/literal}{$index}{literal};
        $(window).on("load", function() {
            var menuitem = $(".fn_new_menuitem").clone(true);
            $(".fn_new_menuitem").remove();
            menuitem.removeClass("fn_new_menuitem");
            $(document).on("click", ".fn_add_menuitem", function() {
                var elem = $(this),
                    menuitem_clone = menuitem.clone(true),
                    parent = elem.closest(".fn_row"),
                    sub_rows = parent.find(".fn_sub_rows").first(),
                    next_level = sub_rows.data("level")+1;

                menuitem_clone.appendTo(sub_rows);
                menuitem_clone.find(".fn_sub_rows")
                    .data("level", next_level)
                    .addClass("submenu_level_"+next_level);
                Sortable.create(menuitem_clone.find(".sortable").get(0), {
                    handle: ".move_zone",  // Drag handle selector within list items
                    sort: true,  // sorting inside list
                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                    ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                    chosenClass: "sortable-chosen",  // Class name for the chosen item
                    dragClass: "sortable-drag",  // Class name for the dragging item
                    scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
                    scrollSpeed: 10 // px
                });
                menuitem_clone.find("[name='menu_items[index][]']").val(mi_index);
                menuitem_clone.find("[name='menu_items[parent_index][]']").val(parent.data("index"));
                menuitem_clone.data("index", mi_index++);
                return false;
            });

            $(document).on("click", ".fn_remove_menuitem", function () {
                $(this).closest(".fn_row").remove();
                return false;
            });
        });
    </script>
{/literal}
