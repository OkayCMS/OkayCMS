{$meta_title = $btr->settings_general_design scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">
            {$btr->settings_general_design|escape}
            {*<div class="tooltip_box hint-bottom-middle-t-info-s-small-mobile hint-anim hidden-sm-down" data-hint="Описание tooltips">
                {include file='svg_icon.tpl' svgId='info_icon'}
            </div>*}
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
                        {if $message_success == 'saved'}
                        {$btr->general_settings_saved|escape}
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


{*Главная форма страницы*}
<form class="fn_form_list" method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    {*Логотип сайта*}
    <div class="row">
        <div class="col-lg-6 col-md-6">
            <div class="boxed fn_toggle_wrap ">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="main_header pt-0">
                            <div class="main_header__item">
                                <div class="heading_box mb-1">
                                {$btr->settings_theme_site_logo|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_site_logo|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                                </div>
                            </div>
                            <div class="main_header__item">
                                <div class="activity_of_switch mb-1">
                                    <div class="activity_of_switch_item"> {* row block *}
                                        <div class="okay_switch clearfix">
                                            <label class="switch_label">{$btr->settings_theme_multilang_logo|escape}
                                                <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_multilang_logo|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </label>
                                            <label class="switch switch-default">
                                                <input class="switch-input" name="multilang_logo" value='1' type="checkbox" {if $settings->multilang_logo}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                        {get_design_block block="settings_theme_logo_checkboxes"}
                                    </div>
                                </div>
                            </div>
                            <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                                <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                            </div>
                        </div>
                        <div>
                            {$btr->settings_theme_allow_ext|escape}
                            {if $allow_ext}
                                {foreach $allow_ext as $img_ext}
                                    <span class="tag tag-info">{$img_ext|escape}</span>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="boxed fn_image_block site_logo_wrap">
                                {if $settings->site_logo}
                                    <div class="fn_parent_image txt_center">
                                        <div class="image_wrapper fn_image_wrapper fn_new_image text-xs-center">
                                            <a href="javascript:;" class="fn_delete_item delete_image remove_image"></a>
                                            <input type="hidden" name="site_logo" value="{$settings->site_logo|escape}">
                                            <img class="watermark_image" src="{$rootUrl}/{$config->design_images}{$settings->site_logo}?v={$settings->site_logo_version}" alt="" />
                                        </div>
                                    </div>
                                {else}
                                    <div class="fn_parent_image"></div>
                                {/if}

                                <div class="fn_upload_image dropzone_block_image text-xs-center {if $settings->site_logo} hidden{/if}">
                                    <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                    <input class="dropzone_image" name="site_logo" type="file" accept="image/*" />
                                </div>
                                <div class="image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                    <a href="javascript:;" class="fn_delete_item delete_image remove_image"></a>
                                    <input type="hidden" name="site_logo" value="{$settings->site_logo|escape}" disabled="">
                                    <img src="" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_theme_site_logo"}
            </div>
        </div>
        <div class="col-lg-6 col-md-6">
            <div class="boxed fn_toggle_wrap ">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="heading_box row mb-0">
                            <div class="col-lg-12 col-md-12 mb-1">
                                {$btr->settings_theme_site_favicon|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_site_favicon|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                                <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                            </div>
                        </div>
                        <div>
                            {$btr->settings_theme_allow_ext|escape}
                            {if $allow_ext}
                                {foreach $allow_ext as $img_ext}
                                    <span class="tag tag-info">{$img_ext|escape}</span>
                                {/foreach}
                            {/if}
                        </div>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="boxed fn_image_block site_logo_wrap">
                                {if $settings->site_favicon}
                                    <div class="fn_parent_image txt_center">
                                        <div class="image_wrapper fn_image_wrapper fn_new_image text-xs-center">
                                            <a href="javascript:;" class="fn_delete_item delete_image remove_image"></a>
                                            <input type="hidden" name="site_favicon" value="{$settings->site_favicon|escape}">
                                            <img class="watermark_image" src="{$rootUrl}/{$config->design_images}{$settings->site_favicon}?v={$settings->site_favicon_version}" alt="" />
                                        </div>
                                    </div>
                                {else}
                                    <div class="fn_parent_image"></div>
                                {/if}

                                <div class="fn_upload_image dropzone_block_image text-xs-center {if $settings->site_favicon} hidden{/if}">
                                    <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                    <input class="dropzone_image" name="site_favicon" type="file" accept="image/*" />
                                </div>
                                <div class="image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                    <a href="javascript:;" class="fn_delete_item delete_image remove_image"></a>
                                    <input type="hidden" name="site_favicon" value="{$settings->site_favicon|escape}" disabled="">
                                    <img src="" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_theme_favicon"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="settings_theme_custom_block"}}
    {if !empty($block)}
        <div class="row fn_toggle_wrap custom_block">
            {$block}
        </div>
    {/if}

    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_theme_deliveries|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_deliveries|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="mb-1">
                                <textarea name="product_deliveries" class="form-control okay_textarea editor_small">{$settings->product_deliveries}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_theme_deliveries"}
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_theme_payments|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_payments|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="mb-1">
                                <textarea name="product_payments" class="form-control okay_textarea editor_small">{$settings->product_payments}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_theme_payments"}
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_theme_contact|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_contact|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_theme_email|escape}</div>
                            <div class="mb-1">
                                <input name="site_email" class="form-control" type="text" value="{$settings->site_email|escape}" />
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_theme_phones|escape}</div>
                            <div class="mb-1">
                                <input name="site_phones" class="form-control" type="text" value="{$site_phones|escape}" />
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_theme_working_hours|escape}</div>
                            <div class="mb-1">
                                <textarea name="site_working_hours" class="form-control okay_textarea editor_small">{$settings->site_working_hours}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_theme_social|escape}</div>
                            <div class="mb-1">
                                <textarea name="site_social_links" class="form-control okay_textarea">{$site_social_links}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_theme_contacts"}
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_theme_general_settings|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_theme_iframe_map|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_iframe_map|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <textarea name="iframe_map_code" class="form-control okay_textarea">{$settings->iframe_map_code}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_theme_social_share|escape}</div>
                            <div class="mb-1">
                                <div class="">
                                    <select name="social_share_theme" class="fn_social_share_theme selectpicker form-control">
                                        <option value=""{if !$settings->social_share_theme} selected{/if}>default</option>
                                        <option value="flat"{if $settings->social_share_theme == 'flat'} selected{/if}>flat</option>
                                        <option value="classic"{if $settings->social_share_theme == 'classic'} selected{/if}>classic</option>
                                        <option value="minima"{if $settings->social_share_theme == 'minima'} selected{/if}>minima</option>
                                        <option value="plain"{if $settings->social_share_theme == 'plain'} selected{/if}>plain</option>
                                    </select>
                                    <div class="fn_share"></div>

                                    <div style="display: none;">
                                    {foreach $js_socials as $soc}
                                        <input type="checkbox" class="fn_{$soc}" name="sj_shares[]"{if in_array($soc, $settings->sj_shares)} checked{/if} value="{$soc}" />
                                    {/foreach}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 ">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_theme_general"}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->settings_advantages}
                </div>

                <div class="okay_list products_list fn_sort_list">
                    {*Шапка таблицы*}
                    <div class="fn_step_sorting okay_list_head">
                        <div class="okay_list_boding okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                            <label class="okay_ckeckbox" for="check_all_1"></label>
                        </div>
                        <div class="okay_list_heading okay_list_advantage_image">{$btr->advantage_image_title}</div>

                        <div class="okay_list_heading okay_list_advantage_description">{$btr->advantage_description_title}</div>

                        <div class="okay_list_heading okay_list_close hidden-sm-down"></div>
                    </div>

                    <div class="okay_list_body sort_extended fn_advantage_list">
                        {foreach $advantages as $advantage}
                            <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                                <div class="okay_list_row">
                                    <input type="hidden" name="positions[{$advantage->id}]" value="{$advantage->position}">

                                    <div class="okay_list_boding okay_list_drag move_zone">
                                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                                    </div>

                                    <div class="okay_list_boding okay_list_check">
                                        <input class="hidden_check" type="checkbox" id="id_{$advantage->id}" name="check[]" value="{$advantage->id}"/>
                                        <label class="okay_ckeckbox" for="id_{$advantage->id}"></label>
                                    </div>

                                    <div class="okay_list_boding okay_list_advantage_image">
                                        <div class="fn_image_block">

                                            {if $advantage->filename}
                                                <input type="hidden" class="fn_accept_delete" name="advantage_images_to_delete[{$advantage->id}]" value="">
                                                <div class="fn_parent_image">
                                                    <div class="advantage__image image_wrapper fn_image_wrapper text-xs-center">
                                                        <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                                        <img src="{$advantage->filename|resize:120:120:false:$config->resized_advantages_dir}" alt="" />
                                                    </div>
                                                </div>
                                            {else}
                                                <div class="fn_parent_image"></div>
                                            {/if}

                                            <div class="fn_upload_image dropzone_block_image {if $advantage->filename} hidden{/if}">
                                                <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                                <input class="dropzone_image" name="advantages_image[{$advantage->id}]" type="file" />
                                            </div>

                                            <div class="brand_image image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                                <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                                <img style="max-height: 73px;" src="" alt="" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="okay_list_boding okay_list_advantage_description">
                                        <textarea class="advantage_textarea form-control short_textarea" name="advantages_text[{$advantage->id}]">{$advantage->text}</textarea>
                                    </div>

                                    <div class="okay_list_boding okay_list_close hidden-sm-down">
                                        {*delete*}
                                        <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                            {include file='svg_icon.tpl' svgId='trash'}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                        <div class="okay_list_body_item">
                            <button class="fn_add_new_advantage btn btn_small btn-info" type="button">
                                {include file='svg_icon.tpl' svgId='plus'}
                                <span>Добавить преимущество</span>
                            </button>
                        </div>
                    </div>


                    <div id="new_advantage" class="fn_step-1 fn_row okay_list_body_item fn_sort_item hidden">
                        <div class="okay_list_row">
                            <div class="okay_list_boding okay_list_drag "></div>
                            <div class="okay_list_boding okay_list_check"></div>
                            <div class="okay_list_boding okay_list_advantage_image">
                                <div class="fn_image_block">

                                    <div class="fn_parent_image"></div>
                                    <div class="fn_upload_image dropzone_block_image">
                                        <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                                        <input class="dropzone_image" name="new_advantage_images[]" type="file" disabled />
                                    </div>

                                    <div class="advantage__image image_wrapper fn_image_wrapper fn_new_image text-xs-center hidden">
                                        <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                        <img style="max-height: 73px;" src="" alt="" />
                                    </div>
                                </div>
                            </div>

                            <div class="okay_list_boding okay_list_advantage_description">
                                <textarea class="advantage__textarea form-control short_textarea" disabled name="new_advantages[text][]"></textarea>
                            </div>

                            <div class="okay_list_boding okay_list_close hidden-sm-down">
                                {*delete*}
                                <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_new hint-bottom-right-t-info-s-small-mobile  hint-anim"">
                                    {include file='svg_icon.tpl' svgId='trash'}
                                </button>
                            </div>
                        </div>
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
                                <select name="action" class="selectpicker form-control products_action">
                                    <option value="delete">{$btr->general_delete|escape}</option>
                                </select>
                            </div>

                            <div class="fn_additional_params">
                                <div class="fn_move_to_page col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                    <select name="target_page" class="selectpicker form-control dropup">
                                        {section target_page $pages_count}
                                            <option value="{$smarty.section.target_page.index+1}">{$smarty.section.target_page.index+1}</option>
                                        {/section}
                                    </select>
                                </div>
                                <div class="fn_move_to_category col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                    <select name="target_category" class="selectpicker form-control dropup" data-live-search="true" data-size="10">
                                        {function name=category_select_btn level=0}
                                            {foreach $categories as $category}
                                                <option value='{$category->id}'>{section sp $level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
                                                {category_select_btn categories=$category->subcategories selected_id=$selected_id level=$level+1}
                                            {/foreach}
                                        {/function}
                                        {category_select_btn categories=$categories}
                                    </select>
                                </div>
                                <div class="fn_move_to_brand col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                    <select name="target_brand" class="selectpicker form-control dropup" data-live-search="true" data-size="{if $brands|count<10}{$brands|count}{else}10{/if}">
                                        <option value="0">{$btr->general_not_set|escape}</option>
                                        {foreach $all_brands as $b}
                                            <option value="{$b->id}">{$b->name|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn_small btn_blue">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply|escape}</span>
                        </button>
                    </div>

                {get_design_block block="settings_theme_advantages"}
            </div>
        </div>
        </div>
    </div>

    {if !empty($css_variables)}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_theme_color|escape}{if $settings->admin_theme} {$settings->admin_theme|escape}{/if}
                    <i class="fn_tooltips" title="{$btr->tooltip_settings_theme_color|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        {foreach $css_variables as $name => $value}
                            {$translation_name = str_replace('--', '', $name)}
                            {$translation_name = str_replace('-', '_', $translation_name)}
                            {if !empty($btr->getTranslation('settings_theme_'|cat:$translation_name))}
                                <div class="col-md-6 col-xs-12">
                                    <div class="variables_box">
                                        <div class="variables_box__left">
                                            <div class="heading_label">{$btr->getTranslation('settings_theme_'|cat:$translation_name)}</div>
                                        </div>
                                        <div class="variables_box__right">
                                            <div class="">
                                                <span{if !empty($value)} style="background-color: {$value|escape};"{/if} class="fn_color theme_color"></span>
                                                <input name="css_colors[{$name|escape}]" class="form-control" type="hidden" value="{$value|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/if}
                        {/foreach}

                        <div class="col-lg-12 col-md-12 ">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                        
                    </div>
                </div>
                {get_design_block block="settings_theme_css_colors"}
            </div>
        </div>
    </div>
    {/if}
</form>

<link rel="stylesheet" media="screen" type="text/css" href="design/js/colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="design/js/colorpicker/js/colorpicker.js"></script>

<script type="text/javascript" src="{$rootUrl}/js_libraries/js_socials/js/jssocials.min.js"></script>
<link type="text/css" rel="stylesheet" href="{$rootUrl}/js_libraries/js_socials/css/jssocials.css" />
{if $settings->social_share_theme}
    <link type="text/css" class="fn_social_share_style" rel="stylesheet" href="{$rootUrl}/js_libraries/js_socials/css/jssocials-theme-{$settings->social_share_theme|escape}.css" />
{/if}
<script type="text/javascript" src="design/js/tinymce_jq/tinymce.min.js"></script>
{literal}
    <script>

        $(function() {
            $(document).on('click', '.fn_remove_new', function() {
                $(this).closest('.fn_row').remove();
            });

            $('.fn_add_new_advantage').on('click', function() {
                const new_advantage = $('#new_advantage').clone();
                new_advantage.removeAttr('id');
                new_advantage.find('[name]').prop('disabled', false);
                new_advantage.removeClass('hidden');
                $('.fn_advantage_list').append(new_advantage);
            });

            $(document).on("mouseenter click", ".fn_color", function () {
                var elem = $(this);
                elem.ColorPicker({
                    onChange: function (hsb, hex, rgb) {
                        elem.css('backgroundColor', '#' + hex);
                        elem.next().val('#' + hex);
                    },
                    onBeforeShow: function () {
                        $(this).ColorPickerSetColor($(this).next().val());
                    }
                });
            });

        });
        
        {/literal}
        {if $js_custom_socials}
            {foreach $js_custom_socials as $social=>$params}
                jsSocials.shares.{$social|escape} = {$params|json_encode};
            {/foreach}
        {/if}
        {literal}
        
        $(".fn_share").jsSocials({
            showLabel: false,
            showCount: false,
            shares: {/literal}{$js_socials|json_encode}{literal},
            on: {
                click: function(e) {
                    var $share_checkbox = $('.fn_'+this.share);
                    if ($share_checkbox.is(':checked')) {
                        $('.jssocials-share-'+this.share).removeClass('active');
                        $share_checkbox.prop('checked', false);
                    } else {
                        $('.jssocials-share-'+this.share).addClass('active');
                        $share_checkbox.prop('checked', true);
                    }
                    return false;
                }
            }
        });
        {/literal}
        
        {*Отметим выбранные соц. сети как выбранные*}
        {if $settings->sj_shares}
            {foreach $settings->sj_shares as $soc}
                $('.jssocials-share-{$soc}').addClass('active');
            {/foreach}
        {/if}
        {literal}
        
        $(document).on('change', 'select.fn_social_share_theme', function() {
            if ($(this).val() != '') {
                if ($('.fn_social_share_style').length > 0) {
                    $('.fn_social_share_style').prop('href', '{/literal}{$rootUrl}{literal}/js_libraries/js_socials/css/jssocials-theme-' + $(this).val() + '.css')
                } else {
                    $('body').append('<link type="text/css" class="fn_social_share_style" rel="stylesheet" href="{/literal}{$rootUrl}{literal}/js_libraries/js_socials/css/jssocials-theme-' + $(this).val() + '.css" />');
                }
            } else {
                $('.fn_social_share_style').remove();
            }
        });
        
        $(function(){
            tinyMCE.init({
                selector: "textarea.editor_small",
                height: '100',
                plugins: ["code"],
                toolbar_items_size : 'small',
                menubar:'',
                toolbar1: "fontselect fontsizeselect | bold italic underline | alignleft aligncenter alignright alignjustify | forecolor backcolor | code",
                statusbar: true,
                font_formats: "Andale Mono=andale mono,times;"+
                "Arial=arial,helvetica,sans-serif;"+
                "Arial Black=arial black,avant garde;"+
                "Book Antiqua=book antiqua,palatino;"+
                "Comic Sans MS=comic sans ms,sans-serif;"+
                "Courier New=courier new,courier;"+
                "Georgia=georgia,palatino;"+
                "Helvetica=helvetica;"+
                "Impact=impact,chicago;"+
                "Open Sans=Open Sans,sans-serif;"+
                "Symbol=symbol;"+
                "Tahoma=tahoma,arial,helvetica,sans-serif;"+
                "Terminal=terminal,monaco;"+
                "Times New Roman=times new roman,times;"+
                "Trebuchet MS=trebuchet ms,geneva;"+
                "Verdana=verdana,geneva;"+
                "Webdings=webdings;"+
                "Wingdings=wingdings,zapf dingbats",
            });
        });
    </script>
{/literal}
