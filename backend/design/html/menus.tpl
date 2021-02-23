{* Title *}
{$meta_title=$btr->menus_menu scope=global}
{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->menus_menu|escape}
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=MenuAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->menus_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">

    {$block = {get_design_block block="menus_custom_block"}}
    {if $block}
        <div class="custom_block">
            {$block}
        </div>
    {/if}
    
    {if $menus}
        <div class="categories">
            <form class="fn_form_list" method="post">
                <input type="hidden" name="session_id" value="{$smarty.session.id}">
                <div class="okay_list products_list fn_sort_list">
                    {*Шапка таблицы*}
                    <div class="okay_list_head">
                        <div class="okay_list_heading okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_1"></label>
                        </div>
                        <div class="okay_list_heading okay_list_features_name">{$btr->menus_name|escape}</div>
                        <div class="okay_list_heading okay_list_brands_tag">{$btr->menus_var|escape}</div>
                        <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                        {$block = {get_design_block block="menus_icon_title"}}
                        {if $block}
                            <div class="okay_list_setting">
                                {$block}
                            </div>
                        {/if}
                        <div class="okay_list_heading okay_list_close"></div>
                    </div>
                    {*Параметры элемента*}
                    <div class="banners_groups_wrap okay_list_body features_wrap sortable">
                        {foreach $menus as $menu}
                            <div class="fn_row okay_list_body_item fn_sort_item">
                                <div class="okay_list_row">
                                    <input type="hidden" name="positions[{$menu->id}]" value="{$menu->position}">

                                    <div class="okay_list_boding okay_list_drag move_zone">
                                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                                    </div>

                                    <div class="okay_list_boding okay_list_check">
                                        <input class="hidden_check" type="checkbox" id="id_{$menu->id}" name="check[]" value="{$menu->id}"/>
                                        <label class="okay_ckeckbox" for="id_{$menu->id}"></label>
                                    </div>

                                    <div class="okay_list_boding okay_list_features_name">
                                        <a class="link" href="{url controller=MenuAdmin id=$menu->id return=$smarty.server.REQUEST_URI}">
                                            {$menu->name|escape}
                                        </a>
                                        {get_design_block block="menus_list_name" vars=['menu' => $menu]}
                                    </div>

                                    <div class="okay_list_boding okay_list_brands_tag">
                                        <div class="wrap_tags">
                                            {$menu->var|escape}
                                        </div>
                                    </div>

                                    <div class="okay_list_boding okay_list_status">
                                        {*visible*}
                                        <div class="col-lg-4 col-md-3">
                                            <label class="switch switch-default">
                                                <input class="switch-input fn_ajax_action {if $menu->visible}fn_active_class{/if}" data-controller="menu" data-action="visible" data-id="{$menu->id}" name="visible" value="1" type="checkbox"  {if $menu->visible}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                    </div>

                                    {$block = {get_design_block block="menus_icon" vars=['menu' => $menu]}}
                                    {if $block}
                                    <div class="okay_list_setting">
                                        {$block}
                                    </div>
                                    {/if}
                                    
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
                    {*Блок массовых действий*}
                    <div class="okay_list_footer fn_action_block">
                        <div class="okay_list_foot_left">
                            <div class="okay_list_heading okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_2"></label>
                            </div>
                            <div class="okay_list_option">
                                <select name="action" class="selectpicker form-control col-lg-12 col-md-12">
                                    <option value="enable">{$btr->general_do_enable|escape}</option>
                                    <option value="disable">{$btr->general_do_disable|escape}</option>
                                    <option value="delete">{$btr->general_delete|escape}</option>
                                    {get_design_block block="menus_action_list"}
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
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->menus_no_groups|escape}</div>
        </div>
    {/if}
</div>
