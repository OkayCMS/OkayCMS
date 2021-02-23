{$meta_title = $btr->settings_general_sites scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->settings_general_sites|escape}</div>
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
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_335px">
                <div class="heading_box">
                    {$btr->settings_general_options|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->settings_general_sitename|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_settings_general_sitename|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <input name="site_name" class="form-control" type="text" value="{$settings->site_name|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->settings_general_date|escape}</div>
                            <div class="mb-1">
                                <input name="date_format" class="form-control" type="text" value="{$settings->date_format|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->settings_phone_default_region|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_settings_phone_default_region|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <select name="phone_default_region" class="selectpicker form-control" data-live-search="true">
                                    {foreach $phone_regions as $phone_region}
                                        <option value="{$phone_region|escape}" {if $settings->phone_default_region == $phone_region}selected{/if}>{if isset($phone_regions_names[$phone_region])}{$phone_regions_names[$phone_region]|escape} {/if}({$phone_region|escape})</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->settings_phone_default_format|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_settings_phone_default_format|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <select name="phone_default_format" class="selectpicker form-control" data-live-search="false">
                                    <option value="{libphonenumber\PhoneNumberFormat::E164}" {if $settings->phone_default_format == libphonenumber\PhoneNumberFormat::E164}selected{/if}>{$phone_example|phone:libphonenumber\PhoneNumberFormat::E164}</option>
                                    <option value="{libphonenumber\PhoneNumberFormat::INTERNATIONAL}" {if $settings->phone_default_format == libphonenumber\PhoneNumberFormat::INTERNATIONAL}selected{/if}>{$phone_example|phone:libphonenumber\PhoneNumberFormat::INTERNATIONAL}</option>
                                    <option value="{libphonenumber\PhoneNumberFormat::NATIONAL}" {if $settings->phone_default_format == libphonenumber\PhoneNumberFormat::NATIONAL}selected{/if}>{$phone_example|phone:libphonenumber\PhoneNumberFormat::NATIONAL}</option>
                                    <option value="{libphonenumber\PhoneNumberFormat::RFC3966}" {if $settings->phone_default_format == libphonenumber\PhoneNumberFormat::RFC3966}selected{/if}>{$phone_example|phone:libphonenumber\PhoneNumberFormat::RFC3966}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->settings_general_shutdown|escape}</div>
                            <div class="mb-1">
                                <select name="site_work" class="selectpicker form-control">
                                    <option value="on" {if $settings->site_work == "on"}selected{/if}>{$btr->settings_general_turn_on|escape}</option>
                                    <option value="off" {if $settings->site_work == "off"}selected{/if}>{$btr->settings_general_turn_off|escape}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="heading_label">{$btr->settings_general_tech_message|escape}</div>
                            <div class="">
                                <textarea name="site_annotation" class="form-control okay_textarea">{$settings->site_annotation|escape}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_general_general"}
            </div>
        </div>
    </div>

    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->settings_general_capcha|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="alert alert--icon alert--info">
                        <div class="alert__content">
                            <p>
                            {$btr->settings_capcha_help1|escape} <a class="" target="_blank" rel="nofollow" href="https://www.google.com/recaptcha/admin#list">{$btr->settings_capcha_help2|escape}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="heading_label">{$btr->settings_type_capcha|escape}</div>
                            <div class="mb-1">
                                <select name="captcha_type" class="selectpicker form-control">
                                    <option value="default" {if $settings->captcha_type == "default"}selected{/if}>{$btr->captcha_default}</option>
                                    <option value="v3" {if $settings->captcha_type == "v3"}selected{/if}>reCAPTCHA V3</option>
                                    <option value="v2" {if $settings->captcha_type == "v2"}selected{/if}>reCAPTCHA V2</option>
                                    <option value="invisible" {if $settings->captcha_type == "invisible"}selected{/if}>reCAPTCHA Invisible</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="permission_block">
                        <div class="permission_boxes row">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="permission_box">
                                    <span>{$btr->settings_general_comment|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="captcha_comment" value='1' type="checkbox" {if $settings->captcha_comment}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="permission_box">
                                    <span>{$btr->settings_general_cart|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="captcha_cart" value='1' type="checkbox" {if $settings->captcha_cart}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                 </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="permission_box">
                                    <span>{$btr->settings_general_register|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="captcha_register" value='1' type="checkbox" {if $settings->captcha_register}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="permission_box">
                                    <span>{$btr->settings_general_contact_form|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="captcha_feedback" value='1' type="checkbox" {if $settings->captcha_feedback}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="permission_box">
                                    <span>{$btr->settings_general_callback|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="captcha_callback" value='1' type="checkbox" {if $settings->captcha_callback}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {get_design_block block="settings_general_recaptcha_checboxes"}
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="boxed">
                                <div class="heading_box">
                                    reCAPTCHA V2
                                 </div>
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="heading_label">{$btr->recaptcha_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="public_recaptcha" class="form-control" type="text" value="{$settings->public_recaptcha|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="heading_label">{$btr->recaptcha_secret_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="secret_recaptcha" class="form-control" type="text" value="{$settings->secret_recaptcha|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="boxed">
                                <div class="heading_box">
                                    reCAPTCHA invisible
                                </div>
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="heading_label">{$btr->recaptcha_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="public_recaptcha_invisible" class="form-control" type="text" value="{$settings->public_recaptcha_invisible|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="heading_label">{$btr->recaptcha_secret_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="secret_recaptcha_invisible" class="form-control" type="text" value="{$settings->secret_recaptcha_invisible|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="boxed">
                                <div class="heading_box">
                                    reCAPTCHA V3
                                 </div>
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="heading_label">{$btr->recaptcha_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="public_recaptcha_v3" class="form-control" type="text" value="{$settings->public_recaptcha_v3|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="heading_label">{$btr->recaptcha_secret_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="secret_recaptcha_v3" class="form-control" type="text" value="{$settings->secret_recaptcha_v3|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="boxed">
                                <div class="heading_box">
                                    {$btr->recaptcha_v3_scores|escape}
                                </div>
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="heading_label">{$btr->recaptcha_scores_product|escape}</div>
                                            <div class="mb-1">
                                                <input name="recaptcha_scores[product]" class="form-control" type="text" value="{$settings->recaptcha_scores['product']|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="heading_label">{$btr->recaptcha_scores_cart|escape}</div>
                                            <div class="mb-1">
                                                <input name="recaptcha_scores[cart]" class="form-control" type="text" value="{$settings->recaptcha_scores['cart']|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="heading_label">{$btr->recaptcha_scores_other|escape}</div>
                                            <div class="mb-1">
                                                <input name="recaptcha_scores[other]" class="form-control" type="text" value="{$settings->recaptcha_scores['other']|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_general_recaptcha"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="settings_general_custom_block"}}
    {if !empty($block)}
        <div class="custom_block">
            {$block}
        </div>
    {/if}

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->settings_general_gathering_data}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="permission_block">
                        <div class="permission_boxes row">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box ">
                                    <span title="{$btr->settings_general_gather_enabled}">{$btr->settings_general_gather_enabled}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="gather_enabled" value='1' type="checkbox" {if $settings->gather_enabled}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 clearfix"></div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 ">
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
