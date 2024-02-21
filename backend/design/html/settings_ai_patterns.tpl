{* Title *}
{$meta_title=$btr->settings_open_ai_manage_patterns_title scope=global}

<style>
    .mini_textarea_block {
        min-height: 110px;
    }
</style>

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->settings_open_ai_manage_patterns_title|escape}</div>
    </div>

    <div class="heading_box text_green ml-1">
        {$btr->settings_open_ai_manage_patterns_title_info|escape}
    </div>
</div>

<form class="fn_form_list" method="post">
    <input type="hidden" name="session_id" value="{$smarty.session.id}">

    <div class="row pl-1 pr-1">
        {*Блок статусов заказов*}
        <div class="boxed col-lg-12 col-md-12">
            <div class="fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->chatgpt_generate_settings_title|escape}
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_list_pattern">

                        <div class="toggle_body_wrap on fn_card">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="heading_label">{$btr->chatgpt_generate_api_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="chatgpt_generate_api_key"
                                                       class="form-control mb-h" value="{$settings->chatgpt_generate_api_key|escape}" />
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            {*Блок массовых действий*}
                                            <div class="okay_list_footer">
                                                <div class="okay_list_foot_left"></div>
                                                <button type="submit" value="labels" class="btn btn_small btn_blue">
                                                    {include file='svg_icon.tpl' svgId='checked'}
                                                    <span>{$btr->general_apply|escape}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {if !$settings->chatgpt_generate_api_key}
        <div class="row pl-1 pr-1">
            {*Блок статусов заказов*}
            <div class="boxed col-lg-12 col-md-12 pr-1">
                <div class="fn_toggle_wrap">
                    <div class="toggle_body_wrap on fn_card">
                        <div class="okay_list_pattern">
                            <div class="toggle_body_wrap on fn_card">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="font_20 text_warning">{$btr->chatgpt_generate_no_api_key|escape}</div>
                                                <div class="font_18">{$btr->chatgpt_generate_no_api_key2|escape}: <a class="compact_list_product_name" target="_blank" href="https://platform.openai.com/docs/quickstart">Api Chat GPT</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {else}

        <div class="row pl-1 pr-1">
            {*Блок статусов заказов*}
            <div class="boxed col-lg-7 col-md-12 pr-1">
                <div class="fn_toggle_wrap">
                    <div class="heading_box">
                        {$btr->settings_open_ai_patterns_for_product|escape}
                    </div>
                    <input name="settings_open_ai_success" type="hidden" value="1" />
                    <div class="toggle_body_wrap on fn_card">
                        <div class="okay_list_pattern">

                            <div class="toggle_body_wrap on fn_card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_title|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_product_meta_title" type="text"
                                                           class="form-control mb-h" value="{$settings->settings_open_ai_patterns_product_meta_title|escape}" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_keywords|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_product_meta_keywords" type="text"
                                                           class="form-control" value="{$settings->settings_open_ai_patterns_product_meta_keywords|escape}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_description|escape}</div>
                                                <div class="mb-1">
                                                    <textarea name="settings_open_ai_patterns_product_meta_description"
                                                              class="form-control short_textarea mini_textarea_block">{$settings->settings_open_ai_patterns_product_meta_description|escape}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading_label">{$btr->settings_open_ai_patterns_for_annotation|escape}</div>
                                        <div class="mb-1">
                                            <textarea name="settings_open_ai_patterns_product_annotation"
                                                      class="form-control long_textarea">{$settings->settings_open_ai_patterns_product_annotation|escape}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading_label">{$btr->settings_open_ai_patterns_for_description|escape}</div>
                                        <div class="mb-1">
                                            <textarea name="settings_open_ai_patterns_product_description"
                                                      class="form-control long_textarea">{$settings->settings_open_ai_patterns_product_description|escape}</textarea>
                                        </div>
                                    </div>

                                    {$fields_block = {get_design_block block="settings_fields_ai_patterns_product_block"}}
                                    {if !empty($fields_block)}
                                        <div class="custom_fields_block fn_toggle_wrap">
                                            {$fields_block}
                                        </div>
                                    {/if}
                                </div>
                            </div>

                            {*Блок массовых действий*}
                            <div class="okay_list_footer">
                                <div class="okay_list_foot_left"></div>
                                <button type="submit" value="labels" class="btn btn_small btn_blue">
                                    {include file='svg_icon.tpl' svgId='checked'}
                                    <span>{$btr->general_apply|escape}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="alert alert--icon alert--info">
                    <div class="alert__content">
                        <div class="alert__title mb-h">
                            {$btr->alert_info|escape}
                        </div>
                        <div class="text_box">
                            <div class="mb-1">
                                {$btr->seo_filter_patterns_message1|escape}
                                {$btr->seo_filter_patterns_message2|escape} <b style="display: inline;">{ldelim}$product{rdelim}</b> {$btr->seo_filter_patterns_message3|escape}
                            </div>
                            <div class="mb-h"><b>{$btr->seo_filter_patterns_ajax_message4|escape}</b> </div>
                            <div>
                                <ul class="mb-0 pl-1">
                                    {literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$product}</a> - {/literal}{$btr->seo_patterns_ajax_product_name|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$price}</a> - {/literal}{$btr->seo_patterns_ajax_product_price|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$compare_price}</a> - {/literal}{$btr->seo_patterns_ajax_product_compare_price|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$sku}</a> - {/literal}{$btr->seo_patterns_ajax_product_sku|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category}</a> - {/literal}{$btr->seo_patterns_ajax_cat_name|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category_h1}</a> - {/literal}{$btr->seo_patterns_ajax_cat_h1|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand}</a> - {/literal}{$btr->seo_patterns_ajax_brand_name|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand_h1}</a> - {/literal}{$btr->seo_patterns_ajax_brand_h1|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand_url}</a> - {/literal}{$btr->seo_patterns_ajax_brand_route|escape}</li>{literal}
                                    {/literal}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pl-1 pr-1">
            {*Блок статусов заказов*}
            <div class="boxed col-lg-7 col-md-12 pr-1">
                <div class="fn_toggle_wrap">
                    <div class="heading_box">
                        {$btr->settings_open_ai_patterns_for_category|escape}
                    </div>
                    <div class="toggle_body_wrap on fn_card">
                        <div class="okay_list_pattern">

                            <div class="toggle_body_wrap on fn_card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_title|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_category_meta_title" type="text"
                                                           class="form-control mb-h" value="{$settings->settings_open_ai_patterns_category_meta_title|escape}" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_keywords|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_category_meta_keywords" type="text"
                                                           class="form-control" value="{$settings->settings_open_ai_patterns_category_meta_keywords|escape}" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_h1|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_category_meta_h1" type="text"
                                                           class="form-control" value="{$settings->settings_open_ai_patterns_category_meta_h1|escape}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_description|escape}</div>
                                                <div class="mb-1">
                                                    <textarea name="settings_open_ai_patterns_category_meta_description"
                                                              class="form-control short_textarea mini_textarea_block">{$settings->settings_open_ai_patterns_category_meta_description|escape}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading_label">{$btr->settings_open_ai_patterns_for_annotation|escape}</div>
                                        <div class="mb-1">
                                            <textarea name="settings_open_ai_patterns_category_annotation"
                                                      class="form-control long_textarea">{$settings->settings_open_ai_patterns_category_annotation|escape}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading_label">{$btr->settings_open_ai_patterns_for_description|escape}</div>
                                        <div class="mb-1">
                                            <textarea name="settings_open_ai_patterns_category_description"
                                                      class="form-control long_textarea">{$settings->settings_open_ai_patterns_category_description|escape}</textarea>
                                        </div>
                                    </div>

                                    {$fields_block = {get_design_block block="settings_fields_ai_patterns_category_block"}}
                                    {if !empty($fields_block)}
                                        <div class="custom_fields_block fn_toggle_wrap">
                                            {$fields_block}
                                        </div>
                                    {/if}
                                </div>
                            </div>

                            {*Блок массовых действий*}
                            <div class="okay_list_footer">
                                <div class="okay_list_foot_left"></div>
                                <button type="submit" value="labels" class="btn btn_small btn_blue">
                                    {include file='svg_icon.tpl' svgId='checked'}
                                    <span>{$btr->general_apply|escape}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="alert alert--icon alert--info">
                    <div class="alert__content">
                        <div class="alert__title mb-h">
                            {$btr->alert_info|escape}
                        </div>
                        <div class="text_box">
                            <div class="mb-1">
                                {$btr->seo_filter_patterns_message1|escape}
                                {$btr->seo_filter_patterns_message2|escape} <b style="display: inline;">{ldelim}$category{rdelim}</b> {$btr->seo_filter_patterns_message3|escape}
                            </div>
                            <div class="mb-h"><b>{$btr->seo_filter_patterns_ajax_message4|escape}</b> </div>
                            <div>
                                <ul class="mb-0 pl-1">
                                    {literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category}</a> - {/literal}{$btr->seo_patterns_ajax_cat_name|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category_h1}</a> - {/literal}{$btr->seo_patterns_ajax_cat_h1|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$category_url}</a> - {/literal}{$btr->seo_patterns_ajax_category_url|escape}</li>{literal}
                                    {/literal}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row pl-1 pr-1">
            {*Блок статусов заказов*}
            <div class="boxed col-lg-7 col-md-12 pr-1">
                <div class="fn_toggle_wrap">
                    <div class="heading_box">
                        {$btr->settings_open_ai_patterns_for_brand|escape}
                    </div>
                    <div class="toggle_body_wrap on fn_card">
                        <div class="okay_list_pattern">

                            <div class="toggle_body_wrap on fn_card">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_title|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_brand_meta_title" type="text"
                                                           class="form-control mb-h" value="{$settings->settings_open_ai_patterns_brand_meta_title|escape}" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_keywords|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_brand_meta_keywords" type="text"
                                                           class="form-control" value="{$settings->settings_open_ai_patterns_brand_meta_keywords|escape}" />
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_h1|escape}</div>
                                                <div class="mb-1">
                                                    <input name="settings_open_ai_patterns_brand_meta_h1" type="text"
                                                           class="form-control" value="{$settings->settings_open_ai_patterns_brand_meta_h1|escape}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="heading_label">{$btr->settings_open_ai_patterns_for_meta_description|escape}</div>
                                                <div class="mb-1">
                                                    <textarea name="settings_open_ai_patterns_brand_meta_description"
                                                              class="form-control short_textarea mini_textarea_block">{$settings->settings_open_ai_patterns_brand_meta_description|escape}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading_label">{$btr->settings_open_ai_patterns_for_annotation|escape}</div>
                                        <div class="mb-1">
                                            <textarea name="settings_open_ai_patterns_brand_annotation"
                                                      class="form-control long_textarea">{$settings->settings_open_ai_patterns_brand_annotation|escape}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-lg-12 col-md-12">
                                        <div class="heading_label">{$btr->settings_open_ai_patterns_for_description|escape}</div>
                                        <div class="mb-1">
                                            <textarea name="settings_open_ai_patterns_brand_description"
                                                      class="form-control long_textarea">{$settings->settings_open_ai_patterns_brand_description|escape}</textarea>
                                        </div>
                                    </div>

                                    {$fields_block = {get_design_block block="settings_fields_ai_patterns_category_block"}}
                                    {if !empty($fields_block)}
                                        <div class="custom_fields_block fn_toggle_wrap">
                                            {$fields_block}
                                        </div>
                                    {/if}
                                </div>
                            </div>

                            {*Блок массовых действий*}
                            <div class="okay_list_footer">
                                <div class="okay_list_foot_left"></div>
                                <button type="submit" value="labels" class="btn btn_small btn_blue">
                                    {include file='svg_icon.tpl' svgId='checked'}
                                    <span>{$btr->general_apply|escape}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 col-md-12 col-sm-12">
                <div class="alert alert--icon alert--info">
                    <div class="alert__content">
                        <div class="alert__title mb-h">
                            {$btr->alert_info|escape}
                        </div>
                        <div class="text_box">
                            <div class="mb-1">
                                {$btr->seo_filter_patterns_message1|escape}
                                {$btr->seo_filter_patterns_message2|escape} <b style="display: inline;">{ldelim}$brand{rdelim}</b> {$btr->seo_filter_patterns_message3|escape}
                            </div>
                            <div class="mb-h"><b>{$btr->seo_filter_patterns_ajax_message4|escape}</b> </div>
                            <div>
                                <ul class="mb-0 pl-1">
                                    {literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand}</a> - {/literal}{$btr->seo_patterns_ajax_brand_name|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand_h1}</a> - {/literal}{$btr->seo_patterns_ajax_brand_h1|escape}</li>{literal}
                                    <li><a href="" class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">{$brand_url}</a> - {/literal}{$btr->seo_patterns_ajax_brand_route|escape}</li>{literal}
                                    {/literal}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</form>

{$block = {get_design_block block="settings_ai_patterns_custom_block"}}
{if !empty($block)}
    <div class="custom_block fn_toggle_wrap">
        {$block}
    </div>
{/if}

{* On document load *}
{literal}

<script>
    sclipboard();
    {/literal}{get_design_block block="settings_ai_patterns_script_block"}{literal}

</script>
{/literal}
