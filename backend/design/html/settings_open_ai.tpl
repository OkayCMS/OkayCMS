{$meta_title = $btr->settings_general_sites scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->settings_open_ai_title|escape}</div>
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


    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->settings_open_ai_integration_title|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="alert alert--icon alert--info">
                        <div class="alert__content">
                            <p>
                                {$btr->settings_open_ai_key_help_1|escape} <a class="" target="_blank" rel="nofollow" href="https://platform.openai.com/api-keys">{$btr->settings_open_ai_key_help_2|escape}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 col-md-12">
                            <div class="boxed">
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="heading_label">{$btr->settings_open_ai_api_key|escape}</div>
                                            <div class="mb-1">
                                                <input name="open_ai_api_key" class="form-control" type="text" value="{$settings->open_ai_api_key|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="heading_label">{$btr->settings_open_ai_model|escape}</div>
                                            <div class="mb-1">
                                                <select name="open_ai_model" class="selectpicker form-control">
                                                    {foreach $open_ai_models as $model}
                                                        <option value='{$model['id']}' {if ($settings->open_ai_model == '' && $model['id'] == 'gpt-3.5-turbo') || $settings->open_ai_model == $model['id']}selected{/if}>{$model['id']}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="heading_label">{$btr->settings_open_ai_system_message|escape}</div>
                                            <div class="mb-1">
                                                <input name="ai_system_message" class="form-control" type="text" value="{$settings->ai_system_message|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="boxed">
                                <div class="">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="heading_label">temperature
                                                <i class="fn_tooltips" title="{$btr->tooltip_settings_open_ai_temperature|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </div>
                                            <div class="mb-1">
                                                <input name="open_ai_temperature" class="form-control" type="number" step="0.1" min="0" max="2" required value="{$settings->open_ai_temperature|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="heading_label">presence_penalty
                                                <i class="fn_tooltips" title="{$btr->tooltip_settings_open_ai_presence_penalty|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </div>
                                            <div class="mb-1">
                                                <input name="open_ai_presence_penalty" class="form-control" type="number" step="0.1" min="-2" max="2" required value="{$settings->open_ai_presence_penalty|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="heading_label">frequency_penalty
                                                <i class="fn_tooltips" title="{$btr->tooltip_settings_open_ai_frequency_penalty|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </div>
                                            <div class="mb-1">
                                                <input name="open_ai_frequency_penalty" class="form-control" type="number" step="0.1" min="-2" max="2" required value="{$settings->open_ai_frequency_penalty|escape}" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="heading_label">max_tokens
                                                <i class="fn_tooltips" title="{$btr->tooltip_settings_open_ai_max_tokens|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </div>
                                            <div class="mb-1">
                                                <input name="open_ai_max_tokens" class="form-control" type="number" min="50" max="4096" required value="{$settings->open_ai_max_tokens|escape}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_335px">
                <div class="heading_box">
                    {$btr->settings_open_ai_products_title|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="heading_label">Meta title</div>
                            <div class="mb-1">
                                <input name="ai_product_title_template" class="form-control" type="text" value="{$settings->ai_product_title_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">Meta keywords</div>
                            <div class="mb-1">
                                <input name="ai_product_keywords_template" class="form-control" type="text" value="{$settings->ai_product_keywords_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">Meta description</div>
                            <div class="mb-1">
                                <input name="ai_product_meta_description_template" class="form-control" type="text" value="{$settings->ai_product_meta_description_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->general_short_description|escape}</div>
                            <div class="mb-1">
                                <input name="ai_product_annotation_template" class="form-control" type="text" value="{$settings->ai_product_annotation_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->general_description|escape}</div>
                            <div class="mb-1">
                                <input name="ai_product_description_template" class="form-control" type="text" value="{$settings->ai_product_description_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="mt-2 btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_335px">
                <div class="heading_box">
                    {$btr->settings_open_ai_categories_title|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="heading_label">Meta title</div>
                            <div class="mb-1">
                                <input name="ai_category_title_template" class="form-control" type="text" value="{$settings->ai_category_title_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">Meta keywords</div>
                            <div class="mb-1">
                                <input name="ai_category_keywords_template" class="form-control" type="text" value="{$settings->ai_category_keywords_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">Meta description</div>
                            <div class="mb-1">
                                <input name="ai_category_meta_description_template" class="form-control" type="text" value="{$settings->ai_category_meta_description_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->general_short_description|escape}</div>
                            <div class="mb-1">
                                <input name="ai_category_annotation_template" class="form-control" type="text" value="{$settings->ai_category_annotation_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->general_description|escape}</div>
                            <div class="mb-1">
                                <input name="ai_category_description_template" class="form-control" type="text" value="{$settings->ai_category_description_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="mt-2 btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_335px">
                <div class="heading_box">
                    {$btr->settings_open_ai_brands_title|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="heading_label">Meta title</div>
                            <div class="mb-1">
                                <input name="ai_brand_title_template" class="form-control" type="text" value="{$settings->ai_brand_title_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">Meta keywords</div>
                            <div class="mb-1">
                                <input name="ai_brand_keywords_template" class="form-control" type="text" value="{$settings->ai_brand_keywords_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">Meta description</div>
                            <div class="mb-1">
                                <input name="ai_brand_meta_description_template" class="form-control" type="text" value="{$settings->ai_brand_meta_description_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->general_short_description|escape}</div>
                            <div class="mb-1">
                                <input name="ai_brand_annotation_template" class="form-control" type="text" value="{$settings->ai_brand_annotation_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="heading_label">{$btr->general_description|escape}</div>
                            <div class="mb-1">
                                <input name="ai_brand_description_template" class="form-control" type="text" value="{$settings->ai_brand_description_template|escape}" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="mt-2 btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
