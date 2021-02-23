{* Title *}
{if $banner}
    {$meta_title=$banner->name scope=global}
{else}
    {$meta_title=$btr->banners_images_banners  scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if $banners_images_count}
                   {$btr->banners_images_banners} - {$banners_images_count}
               {elseif $keyword}
                    {$btr->banners_images_banners} - {$banners_images_count}
                {else}
                    {$btr->banners_images_none|escape}
               {/if}
            </div>
            <div class="box_btn_heading">
               <a class="btn btn_small btn-info" href="{url controller=[OkayCMS,Banners,BannersImageAdmin] return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->banners_images_add|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>
{*Блок фильтров*}
<div class="boxed fn_toggle_wrap">
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
                    <div class="col-md-4 col-lg-4 col-sm-12">
                        <div>
                            <select class="selectpicker form-control" onchange="location = this.value;">
                                <option value="{url brand_id=null banner_id=null keyword=null page=null filter=null}" {if !$filter}{/if}>{$btr->banners_images_all|escape}</option>
                                <option value="{url keyword=null brand_id=null banner_id=null page=null filter='visible'}" {if $filter=='visible'}selected{/if}>{$btr->banners_images_enable|escape}</option>
                                <option value="{url keyword=null brand_id=null banner_id=null page=null filter='hidden'}" {if $filter=='hidden'}selected{/if}>{$btr->banners_images_disable|escape}</option>
                            </select>
                        </div>
                    </div>
                    {if $banners}
                        <div class="col-md-4 col-lg-4 col-sm-12">
                            <select class="selectpicker form-control" onchange="location = this.value;">
                                <option value="{url banner_id=null brand_id=null}" {if !$banner->id}selected{/if}>{$btr->general_groups|escape}</option>
                                {foreach $banners as $b}
                                    <option value="{url keyword=null page=null banner_id=$b->id}" {if $banner->id == $b->id}selected{/if}>{$b->name|escape}</option>
                                {/foreach}
                            </select>
                        </div>
                    {/if}
                </div>
            </div>
            </div>
        </div>
    </div>

    {*Главная форма страницы*}
    {if $banners_images}
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                {$block = {get_design_block block="banners_images_custom_block"}}
                {if $block}
                    <div class="custom_block">
                        {$block}
                    </div>
                {/if}
                <form class="fn_form_list" method="post">
                    <div id="main_list" class=" okay_list products_list fn_sort_list">
                        <input type="hidden" name="session_id" value="{$smarty.session.id}" />
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_brands_photo">{$btr->general_image|escape}</div>
                            <div class="okay_list_heading okay_list_bransimages_name">{$btr->general_name|escape}</div>
                            <div class="okay_list_heading okay_list_brands_group">{$btr->general_banner_group|escape}</div>
                            <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
    
                        {*Параметры элемента*}
                        <div class="banners_wrap okay_list_body features_wrap sortable">
                            {foreach $banners_images as $banners_image}
                            <div class="fn_row okay_list_body_item fn_sort_item">
                                <div class="okay_list_row">
                                    <input type="hidden" name="positions[{$banners_image->id}]" value="{$banners_image->position}">
    
                                    <div class="okay_list_boding okay_list_drag move_zone">
                                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                                    </div>
    
                                    <div class="okay_list_boding okay_list_check">
                                        <input class="hidden_check" type="checkbox" id="id_{$banners_image->id}" name="check[]" value="{$banners_image->id}"/>
                                        <label class="okay_ckeckbox" for="id_{$banners_image->id}"></label>
                                    </div>
    
                                    <div class="okay_list_boding okay_list_brands_photo">
                                        {if $banners_image->image}
                                        <a href="{url controller=[OkayCMS,Banners,BannersImageAdmin] id=$banners_image->id return=$smarty.server.REQUEST_URI}">
                                            <img src="{$banners_image->image|resize:200:200:false:$config->resized_banners_images_dir}" width="200px"/>
                                        </a>
                                        {else}
                                        <img height="100" width="100" src="design/images/no_image.png"/>
                                        {/if}
                                    </div>
    
                                    <div class="okay_list_boding okay_list_bransimages_name">
                                        <a class="link" href="{url controller=[OkayCMS,Banners,BannersImageAdmin] id=$banners_image->id return=$smarty.server.REQUEST_URI}">
                                            {$banners_image->name|escape}
                                        </a>
                                        <div class="okay_list_name_brand">
                                            {$banners_image->image}
                                        </div>
                                        {get_design_block block="banners_images_list_name" vars=['banners_image' => $banners_image]}
                                    </div>
    
                                    <div class="okay_list_boding okay_list_brands_group">
                                        {if $banners}
                                        <select class="selectpicker form-control" name=image_banners[{$banners_image->id}]">
                                            {foreach $banners as $b}
                                            <option value="{$b->id}"{if $b->id == $banners_image->banner_id} selected{/if}>{$b->name}</option>
                                            {/foreach}
                                        </select>
                                        {/if}
                                    </div>
    
                                    <div class="okay_list_boding okay_list_status">
                                        {*visible*}
                                        <label class="switch switch-default">
                                            <input class="switch-input fn_ajax_action {if $banners_image->visible}fn_active_class{/if}" data-controller="okay_cms__banners_images" data-action="visible" data-id="{$banners_image->id}" name="visible" value="1" type="checkbox"  {if $banners_image->visible}checked=""{/if}/>
                                            <span class="switch-label"></span>
                                            <span class="switch-handle"></span>
                                        </label>
                                    </div>
                                    <div class="okay_list_boding okay_list_close">
                                        {*delete*}
                                        <button data-hint="{$btr->banners_images_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
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
                                        {if $banners|count>1}
                                        {foreach $banners as $b}
                                        <option value="move_to_banner[{$b->id}]">{$btr->banners_images_move|escape} {$b->name|escape}</option>
                                        {/foreach}
                                        {/if}
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
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->banners_images_none|escape}</div>
        </div>
    {/if}
</div>
