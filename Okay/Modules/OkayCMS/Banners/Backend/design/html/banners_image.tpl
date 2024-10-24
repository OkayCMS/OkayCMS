{if $banners_image->id}
    {$meta_title = $banners_image->name scope=global}
{else}
    {$meta_title = $btr->banners_image_add_banner  scope=global}
{/if}
{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$banners_image->id}
                     {$btr->banners_image_add_banner|escape}
                {else}
                    {$banners_image->name|escape}
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
                        {if $message_success=='added'}
                        {$btr->banners_image_added|escape}
                        {elseif $message_success=='updated'}
                        {$btr->banners_image_updated|escape}
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

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data" class="fn_fast_button">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="boxed">
                <div class="row d_flex">
                    {*Название элемента сайта*}
                    <div class="col-lg-9 col-md-8 col-sm-12">
                        <div class="heading_label heading_label--required">
                            <span>{$btr->general_name|escape}</span>
                            <i class="fn_tooltips" title="{$btr->tooltip_banner_name|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </div>
                        <div class="form-group">
                            <input class="form-control" name="name" type="text" value="{$banners_image->name|escape}"/>
                            <input name="id" type="hidden" value="{$banners_image->id|escape}"/>
                        </div>
                        <div class="row">
                            <div class=" col-lg-6 col-md-10">
                                <div class="heading_label heading_label--required" >
                                    <span>{$btr->general_banner_group|escape}</span>
                                    <i class="fn_tooltips" title="{$btr->tooltip_banner_group|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </div>
                                <select name="banner_id" class="selectpicker form-control mb-1">
                                    {foreach $banners as $banner}
                                    <option value="{$banner->id}" {if $banners_image->banner_id == $banner->id}selected{elseif !$banners_image->id && $banner_id == $banner->id}selected{/if}>{$banner->name|escape}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-lg-6 col-md-10">
                                <div class="heading_label">
                                    <span>{$btr->banners_image_url|escape}</span>
                                    <i class="fn_tooltips" title="{$btr->tooltip_banner_url|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </div>
                                <div class="form-group">
                                    <input name="url" class="form-control" type="text" value="{$banners_image->url|escape}" />
                                </div>
                            </div>
                        </div>
                        {get_design_block block="banner_image_brand_general"}
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="visible" value='1' type="checkbox" id="visible_checkbox" {if $banners_image->visible || !$banners_image->id}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->banner_image_is_lang_banner|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="is_lang_banner" value='1' type="checkbox" {if $banners_image->is_lang_banner || !$banners_image->id}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            {get_design_block block="banner_image_switch_checkboxes"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-lg-4 pr-0">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->general_image|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap fn_card on text-xs-center">
                    <input type="hidden" class="fn_accept_delete" disabled name="delete_image[]" value="desktop">
                    <div class="banner_image banner_image--small text-xs-center">
                        {if $banners_image->image}
                            <a href="javascript:;" class="fn_delete_banner remove_image"></a>
                            <img class="admin_banner_images" src="{$banners_image->image|resize:465:265:false:$config->resized_banners_images_dir}" alt="" />
                        {/if}
                        {get_design_block block="banner_image_image"}
                    </div>
                    <div class="fn_upload_image dropzone_block_image text-xs-center {if $banners_image->image} hidden{/if}">
                        <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                        <input class="dropzone_banner" name="image" type="file" />
                    </div>
                </div>
                {get_design_block block="banner_image_image_block"}
            </div>
        </div>
        <div class="col-md-12 col-lg-4 pr-0">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->banners_image_variant_show|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="banner_type">
                    <div class="banner_type__item">
                        <input id="banner-type_1" class="hidden_check" name="settings[variant_show]" type="radio" {if $banners_image->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DEFAULT}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DEFAULT}" />
                        <label for="banner-type_1" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_1|escape}</span>
                        </label>
                    </div>
                    <div class="banner_type__item">
                        <input id="banner-type_2" class="hidden_check" name="settings[variant_show]" type="radio" {if $banners_image->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DARK}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DARK}" />
                        <label for="banner-type_2" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_2|escape}</span>
                        </label>
                    </div>
                    <div class="banner_type__item">
                        <input id="banner-type_3" class="hidden_check" name="settings[variant_show]" type="radio" {if $banners_image->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_LEFT}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_LEFT}" />
                        <label for="banner-type_3" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_3|escape}</span>
                        </label>
                    </div>
                    <div class="banner_type__item">
                        <input id="banner-type_4" class="hidden_check" name="settings[variant_show]" type="radio" {if $banners_image->settings->getVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_RIGHT}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_RIGHT}" />
                        <label for="banner-type_4" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_4|escape}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->banner_resize_title|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="heading_label">{$btr->banners_image_size_desktop|escape}</div>
                    <div class="banner_group__inputs mt-q">
                        <div class="banner_group__input">
                            <div class="input-group">
                                <input name="settings[desktop_width]" class="form-control" type="text" value="{$banners_image->settings->getDesktopWidth()|escape}" placeholder="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::DEFAULT_DESKTOP_W}" />
                                <span class="input-group-addon">px</span>
                            </div>

                        </div>
                        <div class="banner_group__input">
                            <div class="input-group">
                                <input name="settings[desktop_height]" class="form-control" type="text" value="{$banners_image->settings->getDesktopHeight()|escape}" placeholder="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::DEFAULT_DESKTOP_H}" />
                                <span class="input-group-addon">px</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert--info mb-0">
                    <div class="alert__content">
                        <p>{$btr->banners_image_size_desktop_text|escape}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-lg-4 pr-0">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->general_image|escape} {$btr->banners_image_on_mobile|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap fn_card on text-xs-center">
                    <input type="hidden" class="fn_accept_delete" disabled name="delete_image[]" value="mobile">
                    <div class="banner_image banner_image--small text-xs-center">
                        {if $banners_image->image_mobile}
                            <a href="javascript:;" class="fn_delete_banner remove_image"></a>
                            <img class="admin_banner_images" src="{$banners_image->image_mobile|resize:465:265:false:$config->resized_banners_images_dir}" alt="" />
                        {/if}
                        {get_design_block block="banner_image_image_mobile"}
                    </div>
                    <div class="fn_upload_image dropzone_block_image text-xs-center {if $banners_image->image_mobile} hidden{/if}">
                        <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                        <input class="dropzone_banner" name="image_mobile" type="file" />
                    </div>
                </div>
                {get_design_block block="banner_image_image_mobile_block"}
            </div>
        </div>
        <div class="col-md-12 col-lg-4 pr-0">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->banners_image_variant_show|escape} {$btr->banners_image_on_mobile|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="banner_type">
                    <div class="banner_type__item">
                        <input id="banner_mobile-type_1" class="hidden_check" name="settings[mobile_variant_show]" type="radio" {if $banners_image->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DEFAULT}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DEFAULT}" />
                        <label for="banner_mobile-type_1" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_1|escape}</span>
                        </label>
                    </div>
                    <div class="banner_type__item">
                        <input id="banner_mobile-type_2" class="hidden_check" name="settings[mobile_variant_show]" type="radio" {if $banners_image->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DARK}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_DARK}" />
                        <label for="banner_mobile-type_2" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_2|escape}</span>
                        </label>
                    </div>
                    <div class="banner_type__item">
                        <input id="banner_mobile-type_3" class="hidden_check" name="settings[mobile_variant_show]" type="radio" {if $banners_image->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_LEFT}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_LEFT}" />
                        <label for="banner_mobile-type_3" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_3|escape}</span>
                        </label>
                    </div>
                    <div class="banner_type__item">
                        <input id="banner_mobile-type_4" class="hidden_check" name="settings[mobile_variant_show]" type="radio" {if $banners_image->settings->getMobileVariantShow() == Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_RIGHT}checked{/if} value="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::SHOW_IMAGE_RIGHT}" />
                        <label for="banner_mobile-type_4" class="okay_ckeckbox">
                            <span class="banner_type__img_wrap"></span>
                            <span class="banner_type__name_wrap">{$btr->banners_image_variant_4|escape}</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-lg-4">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->banner_resize_title|escape} {$btr->banners_image_on_mobile|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="mb-1">
                    <div class="heading_label">{$btr->banners_image_size_mobile|escape}</div>
                    <div class="banner_group__inputs mt-q">
                        <div class="banner_group__input">
                            <div class="input-group">
                                <input name="settings[mobile_width]" class="form-control" type="text" value="{$banners_image->settings->getMobileWidth()|escape}" placeholder="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::DEFAULT_MOBILE_W}" />
                                <span class="input-group-addon">px</span>
                            </div>

                        </div>
                        <div class="banner_group__input">
                            <div class="input-group">
                                <input name="settings[mobile_height]" class="form-control" type="text" value="{$banners_image->settings->getMobileHeight()|escape}" placeholder="{Okay\Modules\OkayCMS\Banners\DTO\BannerImageSettingsDTO::DEFAULT_MOBILE_H}" />
                                <span class="input-group-addon">px</span>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="alert alert--info mb-0">
                    <div class="alert__content">
                        <p>{$btr->banners_image_size_mobile_text|escape}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {*Параметры элемента*}
        <div class="col-md-12">
            <div class="boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->banners_image_param|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-6 pr-0">
                            <div class="heading_label">{$btr->banners_image_alt|escape}</div>
                            <div class="mb-1">
                                <input name="alt" class="form-control" type="text" value="{$banners_image->alt|escape}" />
                            </div>
                            <div class="heading_label">{$btr->banners_image_title|escape}</div>
                            <div class="mb-1">
                                <input name="title" class="form-control" type="text" value="{$banners_image->title|escape}" />
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="heading_label">{$btr->banners_image_description|escape}</div>
                            <div class="mb-1">
                                <textarea name="description" class="form-control okay_textarea ">{$banners_image->description|escape}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="banner_image_meta"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="banner_image_custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}

    <div class="boxed">
        <div class="row">
        <div class="col-lg-12 col-md-12">
                <button type="submit" class="btn btn_small btn_blue float-md-right">
                    {include file='svg_icon.tpl' svgId='checked'}
                    <span>{$btr->general_apply|escape}</span>
                </button>
            </div>
        </div>
    </div>
</form>
<script>
    $(document).on("click", ".fn_delete_banner", function () {
        $(this).closest(".banner_image").find("img").remove();
        $(this).closest(".fn_card").find(".fn_upload_image ").removeClass("hidden");
        $(this).closest(".fn_card").find(".fn_accept_delete").attr('disabled', false);
        $(this).remove();
    });

    if (window.File && window.FileReader && window.FileList) {

        $(".fn_upload_image").on('dragover', function (e){
            e.preventDefault();
            $(this).css('background', '#bababa');
        });
        $(".fn_upload_image").on('dragleave', function(){
            $(this).css('background', '#f8f8f8');
        });
        function handleFileSelect(evt) {
            var block = $(this).closest('.fn_card');

            var files = evt.target.files; // FileList object
            // Loop through the FileList and render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {
                // Only process image files.
                if (!f.type.match('image.*')) {
                    continue;
                }
                var reader = new FileReader();
                // Closure to capture the file information.
                reader.onload = (function(theFile) {
                    return function(e) {
                        // Render thumbnail.
                        block.find('div.banner_image').html("<a href='javascript:;' class='fn_delete_banner remove_image'></a><img class='admin_banner_images' onerror='$(this).closest(\"div\").remove();' src='"+e.target.result+"' />");
                        block.find(".fn_upload_image").addClass("hidden");
                    };
                })(f);
                // Read in the image file as a data URL.
                reader.readAsDataURL(f);
            }
            $(".fn_upload_image").removeAttr("style");
        }
        $(document).on('change', '.dropzone_banner', handleFileSelect);
    }
</script>
