{* Title *}
{$meta_title=$btr->banners_groups scope=global}
{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->banners_groups|escape}
            </div>
            <div class="box_btn_heading">
                    <a class="btn btn_small btn-info" href="{url controller=[OkayCMS,Banners,BannerAdmin] return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->banners_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {if $banners}
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
                        <div class="okay_list_heading okay_list_features_name">{$btr->banners_group_name|escape}</div>
                        <div class="okay_list_heading okay_list_brands_tag">{$btr->banners_display|escape}</div>
                        <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                        <div class="okay_list_heading okay_list_close"></div>
                    </div>
                    {*Параметры элемента*}
                    <div class="banners_groups_wrap okay_list_body features_wrap sortable">
                    {foreach $banners as $banner}
                        <div class="fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                                <input type="hidden" name="positions[{$banner->id}]" value="{$banner->position}">

                                <div class="okay_list_boding okay_list_drag move_zone">
                                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                                </div>

                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$banner->id}" name="check[]" value="{$banner->id}"/>
                                    <label class="okay_ckeckbox" for="id_{$banner->id}"></label>
                                </div>

                                <div class="okay_list_boding okay_list_features_name">
                                    <a class="link" href="{url controller=[OkayCMS,Banners,BannerAdmin] id=$banner->id return=$smarty.server.REQUEST_URI}">
                                        {$banner->name|escape}
                                    </a>
                                </div>

                                <div class="okay_list_boding okay_list_brands_tag">
                                    <div class="wrap_tags">
                                        {if $banner->show_all_pages}
                                            <span class="tag tag-success">{$btr->general_all_pages|escape}</span>
                                        {/if}
                                        {if !$banner->show_all_pages && $banner->category_show}
                                            <div>
                                                <span class="text_dark text_700 font_12">{$btr->general_categories|escape}</span>
                                                {foreach $banner->category_show as $cat_show}
                                                    <span class="tag tag-info">{$cat_show->name|escape}</span>
                                                {/foreach}
                                            </div>
                                        {/if}
                                        {if !$banner->show_all_pages && $banner->brands_show}
                                            <div>
                                                <span class="text_dark text_700 font_12">{$btr->general_brands|escape}</span>
                                                {foreach $banner->brands_show as $brand_show}
                                                    <span class="tag tag-warning">{$brand_show->name|escape}</span>
                                                {/foreach}
                                            </div>
                                        {/if}
                                        {if !$banner->show_all_pages && $banner->page_show}
                                            <div>
                                                <span class="text_dark text_700 font_12">{$btr->general_pages|escape}</span>
                                                {foreach $banner->page_show as $page_show}
                                                    <span class="tag tag-danger">{$page_show->name|escape}</span>
                                                {/foreach}
                                            </div>
                                        {/if}

                                        {$block = {get_design_block block="banners_custom_block" vars=['banner' => $banner]}}
                                        {if $block}
                                            {$block}
                                        {/if}

                                    </div>
                                </div>

                                <div class="okay_list_boding okay_list_status">
                                    {*visible*}
                                    <div class="col-lg-4 col-md-3">
                                        <label class="switch switch-default">
                                            <input class="switch-input fn_ajax_action {if $banner->visible}fn_active_class{/if}" data-controller="okay_cms__banners" data-action="visible" data-id="{$banner->id}" name="visible" value="1" type="checkbox"  {if $banner->visible}checked=""{/if}/>
                                            <span class="switch-label"></span>
                                            <span class="switch-handle"></span>
                                        </label>
                                    </div>
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
                    {*Блок массовых действий*}
                    <div class="okay_list_footer fn_action_block">
                        <div class="okay_list_foot_left">
                            <div class="okay_list_heading okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_2"></label>
                            </div>
                            <div class="okay_list_option">
                                <select name="action" class="selectpicker form-control">
                                    <option value="enable">{$btr->general_do_enable|escape}</option>
                                    <option value="disable">{$btr->general_do_disable|escape}</option>
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
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->banners_no_groups|escape}</div>
        </div>
    {/if}
</div>

