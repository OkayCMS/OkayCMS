{if $payment_method->id}
    {$meta_title = $payment_method->name scope=global}
{else}
    {$meta_title = $btr->payment_method_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        {if !$payment_method->id}
            <div class="heading_page">{$btr->payment_method_add|escape}</div>
        {else}
            <div class="heading_page">{$payment_method->name|escape}</div>
        {/if}
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
                        {$btr->payment_method_added|escape}
                        {elseif $message_success == 'updated'}
                        {$btr->payment_method_updated|escape}
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
                        {if $message_error=='empty_name'}
                        {$btr->general_enter_title|escape}
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
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="fn_step-1">
                            <div class="heading_label">
                                {$btr->general_name|escape}
                                <i class="fn_tooltips" title="{$btr->tooltip_general_name_payments|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="form-group">
                                <input class="form-control mb-h" name="name" type="text" value="{$payment_method->name|escape}"/>
                                <input name="id" type="hidden" value="{$payment_method->id|escape}"/>
                            </div>
                        </div>
                        {get_design_block block="payment_general"}
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="fn_step-2 activity_of_switch">
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="enabled" value='1' type="checkbox" id="visible_checkbox" {if $payment_method->enabled}checked=""{/if}/>
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
                        <div class="activity_of_switch_item"> {* row block *}
                            <div class="okay_switch clearfix">
                                <label class="switch_label">
                                    {$btr->payment_method_auto_submit|escape}
                                    <i class="fn_tooltips" title="{$btr->payment_method_auto_submit_tooltip|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </label>
                                <label class="switch switch-default">
                                    <input class="switch-input" name="auto_submit" value='1' type="checkbox" {if $payment_method->auto_submit}checked=""{/if}/>
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                        {get_design_block block="payment_switch_checkboxes"}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-4 col-md-12 pr-0">
            <div class="fn_step-3 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->general_image|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <ul class="brand_images_list">
                        <li class="brand_image_item fn_image_block">
                            {if $payment_method->image}
                                <input type="hidden" class="fn_accept_delete" name="delete_image" value="">
                                <div class="fn_parent_image">
                                    <div class="category_image image_wrapper fn_image_wrapper text-xs-center">
                                        <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                        <img src="{$payment_method->image|resize:300:120:false:$config->resized_payments_dir}" alt="" />
                                    </div>
                                </div>
                            {else}
                                <div class="fn_parent_image"></div>
                            {/if}
                            <div class="fn_upload_image dropzone_block_image {if $payment_method->image} hidden{/if}">
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
                {get_design_block block="payment_image"}
            </div>
        </div>
        <div class="col-lg-8 col-md-12">
            <div class="fn_step-4 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->payment_method_settings|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 toggle_body_wrap on fn_card">
                        <div class="row">
                            <div class="col-lg-6 pr-0">
                                <div class="form-group clearfix">
                                    <div class="heading_label" >{$btr->payment_method_type|escape}</div>
                                    <select name="module" class="selectpicker form-control">
                                        <option value='null'>{$btr->payment_method_manual|escape}</option>
                                        {foreach $payment_modules as $payment_module}
                                            <option value="{$payment_module@key|escape}" {if $payment_method->module == $payment_module@key}selected{/if} >{$payment_module->vendor|escape}/{$payment_module->module_name|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group clearfix">
                                    <div class="heading_label" >{$btr->general_currency|escape}</div>
                                    <select name="currency_id" class="selectpicker form-control">
                                        {foreach $currencies as $currency}
                                            <option value="{$currency->id}" {if $currency->id==$payment_method->currency_id}selected{/if}>{$currency->name|escape}</option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-12 col-md-12">
                                {foreach $payment_modules as $payment_module}
                                    <div class="row fn_module_settings" {if $payment_module@key!=$payment_method->module}style="display:none;"{/if} module="{$payment_module@key}">
                                        <div class="col-lg-12 col-md-12 heading_box">{$payment_module->vendor|escape}/{$payment_module->module_name|escape}</div>
                                        {foreach $payment_module->settings as $setting}
                                            {$variable_name = $setting->variable}
                                            {if !empty($setting->options) && $setting->options|@count>1}
                                                <div class="col-lg-6">
                                                    <div class="form-group clearfix">
                                                        <div class="heading_label" >{$setting->name|escape}</div>
                                                        <div class="">
                                                            <select name="payment_settings[{$setting->variable}]" class="selectpicker form-control">
                                                                {foreach $setting->options as $option}
                                                                    <option value="{$option->value}" {if isset($payment_method->payment_settings[$setting->variable]) && $option->value==$payment_method->payment_settings[$setting->variable]}selected{/if}>{$option->name|escape}</option>
                                                                {/foreach}
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            {elseif !empty($setting->options) && $setting->options|@count==1}
                                                {$option = $setting->options|@first}
                                                <div class="col-lg-6">
                                                    <div class="form-group clearfix">
                                                        <div class="boxes_inline">
                                                            <input name="payment_settings[{$setting->variable}]" class="hidden_check" type="checkbox" value="{$option->value|escape}" {if $option->value==$payment_method->payment_settings[$setting->variable]}checked{/if} id="{$setting->variable}" />
                                                            <label class="okay_ckeckbox" for="{$setting->variable}"></label>
                                                        </div>
                                                        <div class="heading_label boxes_inline" for="{$setting->variable}">{$setting->name|escape}</div>
                                                    </div>
                                                </div>
                                            {else}
                                                <div class="col-lg-6">
                                                    <div class="form-group clearfix">
                                                        <div class="heading_label" for="{$setting->variable}">{$setting->name|escape}</div>
                                                        <div class="">
                                                            <input name="payment_settings[{$setting->variable}]" class="form-control" type="text" value="{if isset($payment_method->payment_settings[$setting->variable])}{$payment_method->payment_settings[$setting->variable]|escape}{/if}" id="{$setting->variable}"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            {/if}
                                        {/foreach}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-6 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->payment_method_shipping|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="row wrap_payment_item">
                       {foreach $deliveries as $delivery}
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="payment_item">
                                    <input class="hidden_check" id="id_{$delivery->id}" value="{$delivery->id}" {if in_array($delivery->id, $payment_method->payment_deliveries)}checked{/if} type="checkbox" name="payment_deliveries[]">
                                    <label for="id_{$delivery->id}" class="okay_ckeckbox {if in_array($delivery->id, $payment_method->payment_deliveries)}active_payment{/if}">
                                        <span class="payment_img_wrap">
                                            {if $delivery->image}
                                                <img src="{$delivery->image|resize:50:50:false:$config->resized_deliveries_dir}">
                                            {else}
                                                <img width="50" src="design/images/no_image.png"/>
                                            {/if}
                                        </span>
                                        <span class="payment_name_wrap">{$delivery->name|escape}</span>

                                    </label>
                                </div>
                            </div>
                            {if $delivery@iteration %3 == 0}
                                <div class="col-xs-12 clearfix"></div>
                            {/if}
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {$block = {get_design_block block="payment_custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}
    
    {*Описание элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-7 boxed match fn_toggle_wrap tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="tab_navigation_link">{$btr->payment_method_description|escape}</a>
                    </div>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <textarea name="description" class="editor_small">{$payment_method->description|escape}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-lg-12 col-md-12 mt-1">
                        <button type="submit" class="fn_step-8 btn btn_small btn_blue float-md-right">
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
{include file='learning_hints.tpl' hintId='hint_payment'}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
{* On document load *}
{literal}
    <script>
        $(function() {
            $('div.fn_module_settings').filter(':hidden').find("input, select, textarea").attr("disabled", true);

            $('select[name=module]').on('change',function(){
                $('div.fn_module_settings').hide().find("input, select, textarea").attr("disabled", true);
                $('div.fn_module_settings[module="'+$(this).val()+'"]').show().find("input, select, textarea").attr("disabled", false);
                $('div.fn_module_settings[module="'+$(this).val()+'"]').find('select').selectpicker('refresh');
            });
        });
    </script>
{/literal}
