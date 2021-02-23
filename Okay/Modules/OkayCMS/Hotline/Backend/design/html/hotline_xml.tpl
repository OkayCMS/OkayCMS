{$meta_title = $btr->okaycms__hotline__title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okaycms__hotline__title|escape}
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
<form method="post" enctype="multipart/form-data" class="fn_fast_button fn_is_translit_alpha">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->okaycms__hotline__params|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="permission_block">
                        <div class="permission_boxes row">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span>{$btr->okaycms__hotline__upload_without_images|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__hotline__upload_without_images" value='1' type="checkbox" {if $settings->okaycms__hotline__upload_without_images}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span class="permission_box__label">{$btr->okaycms__hotline__upload_non_exists_products_to_hotline|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__hotline__upload_only_available_to_hotline" value='1' type="checkbox" {if $settings->okaycms__hotline__upload_only_available_to_hotline}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span class="permission_box__label">{$btr->okaycms__hotline__store|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__hotline__store" value='1' type="checkbox" {if $settings->okaycms__hotline__store}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span class="permission_box__label">{$btr->okaycms__hotline__use_full_description_to_hotline|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__hotline__use_full_description_to_hotline" value='1' type="checkbox" {if $settings->okaycms__hotline__use_full_description_to_hotline}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span class="permission_box__label">{$btr->okaycms__hotline__no_export_without_price|escape}</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__hotline__no_export_without_price" value='1' type="checkbox" {if $settings->okaycms__hotline__no_export_without_price}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <div class="heading_label">
                                <strong>{$btr->okaycms__hotline__company}</strong>
                            </div>
                            <div class="mb-1">
                                <input class="form-control" type="text" name="okaycms__hotline__company" value="{$settings->okaycms__hotline__company}" />
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="heading_label">
                                <strong>{$btr->okaycms__hotline__guarantee_manufacturer}</strong>
                            </div>
                            <div class="mb-1">
                                <select name="okaycms__hotline__guarantee_manufacturer" class="selectpicker">
                                    <option {if $settings->okaycms__hotline__guarantee_manufacturer == 0}selected=""{/if} value=""></option>
                                    {foreach $features as $feature}
                                        <option {if $settings->okaycms__hotline__guarantee_manufacturer == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="heading_label">
                                {$btr->okaycms__hotline__guarantee_shop}
                            </div>
                            <div class="mb-1">
                                <select name="okaycms__hotline__guarantee_shop" class="selectpicker">
                                    <option {if $settings->okaycms__hotline__guarantee_shop == 0}selected=""{/if} value=""></option>
                                    {foreach $features as $feature}
                                        <option {if $settings->okaycms__hotline__guarantee_shop == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="heading_label">
                                <strong>{$btr->okaycms__hotline__country_of_origin}</strong>
                            </div>
                            <div class="mb-1">
                                <select name="okaycms__hotline__country_of_origin" class="selectpicker">
                                    <option {if $settings->okaycms__hotline__country_of_origin == 0}selected=""{/if} value=""></option>
                                    {foreach $features as $feature}
                                        <option {if $settings->okaycms__hotline__country_of_origin == $feature->id}selected=""{/if} value="{$feature->id}">{$feature->name}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 ">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
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
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->okaycms__hotline__edit_and_add_feeds|escape}
                </div>

                {*Параметры элемента*}
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-info btn_big mb-1 fn_add_feed" type="submit" name="add_feed" value="1">
                            <span>{$btr->okaycms__hotline__add_feed}</span>
                        </button>
                    </div>
                    <div class="col-md-12">
                        <div class="tabs">
                            <div class="heading_tabs">
                                <div class="tab_navigation">
                                    {foreach $feeds as $feed}
                                        <a href="#tab{$feed@iteration}" class="heading_box tab_navigation_link">{$feed->name|escape}</a>
                                    {/foreach}
                                </div>
                            </div>
                            <div class="tab_container">
                                {foreach $feeds as $feed}
                                    <div id="tab{$feed@iteration}" class="tab">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12">
                                                <div class="heading_box">
                                                    {$btr->okaycms__hotline__params|escape}
                                                </div>
                                                {*Вывод ошибок*}
                                                {if isset($errors['feeds'][$feed->id])}
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="alert alert--center alert--icon alert--error">
                                                                <div class="alert__content">
                                                                    <div class="alert__title">
                                                                        {if isset($errors['feeds'][$feed->id]['url'])}
                                                                            {$btr->okaycms__hotline__error_url_exist|escape}
                                                                        {elseif isset($errors['feeds'][$feed->id]['url_cyrillic'])}
                                                                            {$btr->okaycms__hotline__error_url_cyrillic|escape}
                                                                        {/if}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                {/if}
                                                {if $feeds|count > 1}
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <button class="btn btn-outline-danger btn_big float-md-right" name="remove_feed" value="{$feed->id}">
                                                                <span>{$btr->okaycms__hotline__remove_feed}</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                {/if}
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="heading_label">
                                                                <span>Name</span>
                                                            </div>
                                                            <input class="form-control" type="text" placeholder="Feed name" name="feeds[{$feed->id}][name]" value="{$feed->name}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="activity_of_switch activity_of_switch--left">
                                                            <div class="activity_of_switch_item">
                                                                <div class="okay_switch clearfix">
                                                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                                                    <label class="switch switch-default">
                                                                        <input type="hidden" name="feeds[{$feed->id}][enabled]" value="0">
                                                                        <input class="switch-input" name="feeds[{$feed->id}][enabled]" value="1" type="checkbox" {if $feed->enabled}checked{/if}>
                                                                        <span class="switch-label"></span>
                                                                        <span class="switch-handle"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="heading_label">
                                                                <span>URL</span>
                                                                <i class="fn_tooltips" title="{$btr->okaycms__hotline__error_url_cyrillic|escape}">
                                                                <svg width="20px" height="20px" viewBox="0 0 438.533 438.533" ><path fill="currentColor" d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062C438.533,179.485,428.732,142.795,409.133,109.203z M255.82,356.309c0,2.662-0.862,4.853-2.573,6.563c-1.704,1.711-3.895,2.567-6.557,2.567h-54.823c-2.664,0-4.854-0.856-6.567-2.567c-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662,0.855-4.853,2.57-6.563c1.713-1.708,3.903-2.563,6.567-2.563h54.823c2.662,0,4.853,0.855,6.557,2.563c1.711,1.711,2.573,3.901,2.573,6.563V356.309z M325.338,187.574c-2.382,7.043-5.044,12.804-7.994,17.275c-2.949,4.473-7.187,9.042-12.709,13.703c-5.51,4.663-9.891,7.996-13.135,9.998c-3.23,1.995-7.898,4.713-13.982,8.135c-6.283,3.613-11.465,8.326-15.555,14.134c-4.093,5.804-6.139,10.513-6.139,14.126c0,2.67-0.862,4.859-2.574,6.571c-1.707,1.711-3.897,2.566-6.56,2.566h-54.82c-2.664,0-4.854-0.855-6.567-2.566c-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752,4.993-24.701,14.987-35.832c9.994-11.136,20.986-19.368,32.979-24.698c9.13-4.186,15.604-8.47,19.41-12.847c3.812-4.377,5.715-10.188,5.715-17.417c0-6.283-3.572-11.897-10.711-16.849c-7.139-4.947-15.27-7.421-24.409-7.421c-9.9,0-18.082,2.285-24.555,6.855c-6.283,4.565-14.465,13.322-24.554,26.263c-1.713,2.286-4.093,3.431-7.139,3.431c-2.284,0-4.093-0.57-5.424-1.709L121.35,145.89c-4.377-3.427-5.138-7.422-2.286-11.991c24.366-40.542,59.672-60.813,105.922-60.813c16.563,0,32.744,3.903,48.541,11.708c15.796,7.801,28.979,18.842,39.546,33.119c10.554,14.272,15.845,29.787,15.845,46.537C328.904,172.824,327.71,180.529,325.338,187.574z"/></svg>    </i>
                                                            </div>
                                                            <div class="input-group input-group--dabbl">
                                                                <span class="input-group-addon input-group-addon--left">URL</span>
                                                                <input class="form-control fn_url fn_disabled" type="text" name=feeds[{$feed->id}][url] value="{$feed->url}" readonly="readonly">
                                                                <span class="input-group-addon fn_disable_url"><i class="fa fa-lock"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="alert alert--icon alert--info">
                                                            <div class="alert__content">
                                                                <div class="alert__title">{$btr->alert_info|escape}</div>
                                                                <p>{$btr->okaycms__hotline__generation_url|escape} <a href="{url_generator route='OkayCMS_Hotline_Feed' url=$feed->url absolute=1}" target="_blank">{url_generator route='OkayCMS_Hotline_Feed' url=$feed->url absolute=1}</a></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="heading_box">
                                                    {$btr->okaycms__hotline__upload_products|escape}
                                                </div>
                                                <div class="row">
                                                    {* Категории для выгрузки *}
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="boxed match fn_toggle_wrap">
                                                            <div class="heading_box">
                                                                {$btr->okaycms__hotline__categories}
                                                                <button class="btn btn_small btn-info" name="add_all_categories" value="{$feed->id}">{$btr->okaycms__hotline__select_all}</button>
                                                                <button class="btn btn_small" name="remove_all_categories" value="{$feed->id}">{$btr->okaycms__hotline__select_none}</button>
                                                            </div>
                                                            <div class="toggle_body_wrap on fn_card">
                                                                <select style="opacity: 0;" class="selectpicker_categories col-xs-12 px-0" multiple name="related_categories[{$feed->id}][]" size="10" data-selected-text-format="count" >
                                                                    {function name=category_select selected_id=$product_category level=0}
                                                                        {foreach $categories as $category}
                                                                            <option value='{$category->id}' class="category_to_xml" {if (isset($allRelatedCategoriesIds[$feed->id]) && in_array($category->id, $allRelatedCategoriesIds[$feed->id]))}selected{/if}>{section name=sp loop=$level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name}</option>
                                                                            {category_select categories=$category->subcategories selected_id=$selected_id  level=$level+1}
                                                                        {/foreach}
                                                                    {/function}
                                                                    {category_select categories=$categories}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {* Бренды для выгрузки *}
                                                    <div class="col-lg-6 col-md-6">
                                                        <div class="boxed match fn_toggle_wrap">
                                                            <div class="heading_box">
                                                                {$btr->okaycms__hotline__brands}
                                                                <button class="btn btn_small btn-info" name="add_all_brands" value="{$feed->id}">{$btr->okaycms__hotline__select_all}</button>
                                                                <button class="btn btn_small" name="remove_all_brands" value="{$feed->id}">{$btr->okaycms__hotline__select_none}</button>
                                                            </div>
                                                            <div class="toggle_body_wrap on fn_card">
                                                                <select style="opacity: 0;" class="selectpicker_brands col-xs-12 px-0" multiple name="related_brands[{$feed->id}][]" size="10" data-selected-text-format="count" >
                                                                    {foreach $brands as $brand}
                                                                        <option value='{$brand->id}' class="brand_to_xml" {if (isset($allRelatedBrandsIds[$feed->id]) && in_array($brand->id, $allRelatedBrandsIds[$feed->id]))}selected{/if}>{$brand->name|escape}</option>
                                                                    {/foreach}
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {* Товары для выгрузки *}
                                                    <div class="col-lg-6 col-md-12">
                                                        <div class="boxed fn_toggle_wrap min_height_210px">
                                                            {backend_compact_product_list
                                                            title=$btr->okaycms__hotline__products_for_upload
                                                            name="related_products_{$feed->id}"
                                                            products=$related_products[$feed->id]
                                                            label=$btr->okaycms__hotline__add_products
                                                            placeholder=$btr->okaycms__hotline__select_products
                                                            }
                                                        </div>
                                                    </div>

                                                    {* Товары не для выгрузки *}
                                                    <div class="col-lg-6 col-md-12">
                                                        <div class="boxed fn_toggle_wrap min_height_210px">
                                                            {backend_compact_product_list
                                                            title=$btr->okaycms__hotline__products_not_for_upload
                                                            name="not_related_products_{$feed->id}"
                                                            products=$not_related_products[$feed->id]
                                                            label=$btr->okaycms__hotline__add_products
                                                            placeholder=$btr->okaycms__hotline__select_products
                                                            }
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 200px;">
        <div class="col-lg-12 col-md-12 ">
            <button type="submit" class="btn btn_small btn_blue float-md-right">
                <span>{$btr->general_apply|escape}</span>
            </button>
        </div>
    </div>
</form>

<style>

    .permission_box__label {
        width: auto !important;
    }

</style>

{literal}
    <script>
        $('.selectpicker_categories').selectpicker();
        $('.selectpicker_brands').selectpicker();
    </script>
{/literal}