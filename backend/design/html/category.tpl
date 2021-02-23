{if $category->id}
    {$meta_title = $category->name scope=global}
{else}
    {$meta_title = $btr->category_new  scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$category->id}
                    {$btr->category_add|escape}
                {else}
                    {$category->name|escape}
                {/if}
            </div>
            {if $category->id}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="{url_generator route="category" url=$category->url absolute=1}">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                        <span>{$btr->general_open|escape}</span>
                    </a>
                </div>
            {/if}
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
                        {if $message_success=='added'}
                            {$btr->category_added|escape}
                        {elseif $message_success=='updated'}
                            {$btr->category_updated|escape}
                        {else}
                            {$message_success|escape}
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
                        {if $message_error=='url_exists'}
                            {$btr->category_exists|escape}
                        {elseif $message_error=='global_url_exists'}
                            {$btr->global_url_exists|escape}
                        {elseif $message_error == 'empty_name'}
                            {$btr->general_enter_title|escape}
                        {elseif $message_error == 'empty_url'}
                            {$btr->general_enter_url|escape}
                        {elseif $message_error == 'url_wrong'}
                            {$btr->general_not_underscore|escape}
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
            <div class="boxed match_matchHeight_true">
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="fn_step-1">
                            <div class="heading_label heading_label--required">
                                <span>{$btr->general_name|escape}</span>
                                <i class="fn_tooltips" title="{$btr->tooltip_general_name_category|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="form-group">
                                <input class="fn_step1_name form-control" name="name" type="text" value="{$category->name|escape}"/>
                                <input name="id" type="hidden" value="{$category->id|escape}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-8 col-lg-6">
                                <div class="fn_step-2">
                                    <div class="input-group input-group--dabbl">
                                       <span class="input-group-addon input-group-addon--left">URL</span>
                                        <input name="url" class="fn_meta_field form-control fn_url {if $category->id}fn_disabled{/if}" {if $category->id}readonly=""{/if} type="text" value="{$category->url|escape}" />
                                        <input type="checkbox" id="block_translit" class="hidden" value="1" {if $category->id}checked=""{/if}>
                                        <span class="input-group-addon fn_disable_url">
                                            {if $category->id}
                                                <i class="fa fa-lock"></i>
                                            {else}
                                                <i class="fa fa-lock fa-unlock"></i>
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {get_design_block block="category_heading"}
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="fn_step-3 activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">
                                        {$btr->general_enable|escape}
                                        <i class="fn_tooltips" title="{$btr->tooltip_general_enable_category|escape}">
                                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                        </i>
                                    </label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="visible" value='1' type="checkbox" {if $category->visible}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Дополнительные настройки*}
    {$switch_checkboxes = {get_design_block block="category_switch_checkboxes"}}
    {if !empty($switch_checkboxes)}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap ">
                <div class="heading_box">
                    {$btr->general_additional_settings|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="activity_of_switch activity_of_switch--box_settings">
                        {$switch_checkboxes}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/if}

    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-4 col-md-12 pr-0 hidden-sm-down">
            <div class="fn_step-4 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->general_image|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_general_image_category|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <ul class="category_images_list">
                        <li class="category_image_item fn_image_block">
                            {if $category->image}
                            <input type="hidden" class="fn_accept_delete" name="delete_image" value="">
                                <div class="fn_parent_image">
                                    <div class="category_image image_wrapper fn_image_wrapper text-xs-center">
                                        <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                        <img src="{$category->image|resize:300:120:false:$config->resized_categories_dir}" alt="" />
                                    </div>
                                </div>
                            {else}
                                <div class="fn_parent_image"></div>
                            {/if}
                            <div class="fn_upload_image dropzone_block_image {if $category->image} hidden{/if}">
                                <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                <input class="dropzone_image" name="image" type="file" />
                            </div>
                            <div class="category_image image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                <img src="" alt="" />
                            </div>
                        </li>
                    </ul>
                </div>
                {get_design_block block="category_image"}
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->category_parameters|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_category_parameters|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 toggle_body_wrap on fn_card">
                        <div class="row">
                            <div class="col-lg-6 pr-0">
                                <div class="fn_step-5 form-group clearfix">
                                    <label class="heading_label" >{$btr->category_h1|escape}</label>
                                    <div>
                                        <input name="name_h1" class="form-control" type="text" value="{$category->name_h1|escape}" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="product_categories" class="fn_step-6">
                            <div class="heading_box">
                                {$btr->category_subcategory|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_general_category_category|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <select name="parent_id" class="selectpicker form-control mb-1" data-live-search="true" data-size="10">
                                <option value='0'>{$btr->category_root|escape}</option>
                                {function name=category_select level=0}
                                    {foreach $cats as $cat}
                                        {if $category->id != $cat->id}
                                            <option value='{$cat->id}' {if $category->parent_id == $cat->id}selected{/if}>{section name=sp loop=$level}--{/section}{$cat->name}</option>
                                            {category_select cats=$cat->subcategories level=$level+1}
                                        {/if}
                                    {/foreach}
                                {/function}
                                {category_select cats=$categories}
                            </select>
                        </div>
                    </div>
                </div>
                {get_design_block block="category_parameters"}
            </div>
        </div>
    </div>

    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-7 boxed match fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->general_metatags|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_general_metatags|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card row">
                    <div class="col-lg-6 col-md-6">
                        <div class="heading_label" >Meta-title <span id="fn_meta_title_counter"></span>
                            <i class="fn_tooltips" title="{$btr->tooltip_meta_title|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </div>
                        <input name="meta_title" class="form-control fn_meta_field mb-h" type="text" value="{$category->meta_title|escape}" />
                        <div class="heading_label" >Meta-keywords
                            <i class="fn_tooltips" title="{$btr->tooltip_meta_keywords|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </div>
                        <input name="meta_keywords" class="form-control fn_meta_field mb-h" type="text" value="{$category->meta_keywords|escape}" />
                    </div>
                    <div class="col-lg-6 col-md-6 pl-0">
                        <div class="mb-q" >Meta-description <span id="fn_meta_description_counter"></span>
                            <i class="fn_tooltips" title="{$btr->tooltip_meta_description|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </div>
                        <textarea name="meta_description" class="form-control okay_textarea fn_meta_field">{$category->meta_description|escape}</textarea>
                    </div>
                </div>
                {get_design_block block="category_meta_data"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="category_custom_block"}}
    {if !empty($block)}
        <div class="custom_block">
            {$block}
        </div>
    {/if}

    {*Описание элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-8 boxed match fn_toggle_wrap tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="tab_navigation_link">{$btr->general_short_description|escape}</a>
                        <a href="#tab2" class="fn_step9 tab_navigation_link">{$btr->general_full_description|escape}</a>
                    </div>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card ">
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <textarea name="annotation" id="fn_editor" class="editor_small">{$category->annotation|escape}</textarea>
                        </div>
                        <div id="tab2" class="tab">
                            <textarea name="description" class="editor_large fn_editor_class">{$category->description|escape}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-lg-12 col-md-12 mt-1">
                        <button type="submit" class="fn_step-9 btn btn_small btn_blue float-md-right">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_category'}


{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
{* On document load *}




