{* Title *}
{$meta_title=$btr->left_setting_indexing_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->left_setting_indexing_title|escape}</div>
    </div>
</div>

<form class="fn_form_list" method="post">
    <input type="hidden" name="session_id" value="{$smarty.session.id}">
    <div class="row">
        {*Блок статусов заказов*}
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_indexing_table_title|escape} <span class="text_green">&lt;link rel="canonical"&gt;</span> 
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_list">
                        <div class="fn_status_list fn_sort_list okay_list_body sortable">
                            <div class="fn_row okay_list_body_item">
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_pagination}</div>
                                            <select name="canonical_catalog_pagination" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{CANONICAL_FIRST_PAGE}" {if $settings->canonical_catalog_pagination == CANONICAL_FIRST_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_pagination_val_1|escape}</option>
                                                <option value="{CANONICAL_CURRENT_PAGE}" {if $settings->canonical_catalog_pagination == CANONICAL_CURRENT_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_pagination_val_2|escape}</option>
                                                <option value="{CANONICAL_PAGE_ALL}" {if $settings->canonical_catalog_pagination == CANONICAL_PAGE_ALL}selected=""{/if}>{$btr->settings_indexing_catalog_pagination_val_3|escape}</option>
                                                <option value="{CANONICAL_ABSENT}" {if $settings->canonical_catalog_pagination == CANONICAL_ABSENT}selected=""{/if}>{$btr->settings_indexing_catalog_pagination_val_4|escape}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_page_all}</div>
                                            <select name="canonical_catalog_page_all" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{CANONICAL_FIRST_PAGE}" {if $settings->canonical_catalog_page_all == CANONICAL_FIRST_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_page_all_val_1|escape}</option>
                                                <option value="{CANONICAL_CURRENT_PAGE}" {if $settings->canonical_catalog_page_all == CANONICAL_CURRENT_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_page_all_val_2|escape}</option>
                                                <option value="{CANONICAL_ABSENT}" {if $settings->canonical_catalog_page_all == CANONICAL_ABSENT}selected=""{/if}>{$btr->settings_indexing_catalog_page_all_val_3|escape}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_category_brand}</div>
                                            <select name="canonical_category_brand" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{CANONICAL_WITHOUT_FILTER}" {if $settings->canonical_category_brand == CANONICAL_WITHOUT_FILTER}selected=""{/if}>{$btr->settings_indexing_category_brand_val_1|escape}</option>
                                                <option value="{CANONICAL_WITH_FILTER}" {if $settings->canonical_category_brand == CANONICAL_WITH_FILTER}selected=""{/if}>{$btr->settings_indexing_category_brand_val_2|escape}</option>
                                                <option value="{CANONICAL_ABSENT}" {if $settings->canonical_category_brand == CANONICAL_ABSENT}selected=""{/if}>{$btr->settings_indexing_category_brand_val_3|escape}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_category_features}</div>
                                            <select name="canonical_category_features" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{CANONICAL_WITHOUT_FILTER}" {if $settings->canonical_category_features == CANONICAL_WITHOUT_FILTER}selected=""{/if}>{$btr->settings_indexing_category_features_val_1|escape}</option>
                                                <option value="{CANONICAL_WITH_FILTER}" {if $settings->canonical_category_features == CANONICAL_WITH_FILTER}selected=""{/if}>{$btr->settings_indexing_category_features_val_2|escape}</option>
                                                <option value="{CANONICAL_ABSENT}" {if $settings->canonical_category_features == CANONICAL_ABSENT}selected=""{/if}>{$btr->settings_indexing_category_features_val_3|escape}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_other_filter}</div>
                                            <select name="canonical_catalog_other_filter" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{CANONICAL_WITHOUT_FILTER}" {if $settings->canonical_catalog_other_filter == CANONICAL_WITHOUT_FILTER}selected=""{/if}>{$btr->settings_indexing_catalog_other_filter_val_1|escape}</option>
                                                <option value="{CANONICAL_WITH_FILTER}" {if $settings->canonical_catalog_other_filter == CANONICAL_WITH_FILTER}selected=""{/if}>{$btr->settings_indexing_catalog_other_filter_val_2|escape}</option>
                                                <option value="{CANONICAL_ABSENT}" {if $settings->canonical_catalog_other_filter == CANONICAL_ABSENT}selected=""{/if}>{$btr->settings_indexing_catalog_other_filter_val_3|escape}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_filter_pagination}</div>
                                            <select name="canonical_catalog_filter_pagination" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{CANONICAL_WITHOUT_FILTER_FIRST_PAGE}" {if $settings->canonical_catalog_filter_pagination == CANONICAL_WITHOUT_FILTER_FIRST_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_filter_pagination_val_1|escape}</option>
                                                <option value="{CANONICAL_FIRST_PAGE}" {if $settings->canonical_catalog_filter_pagination == CANONICAL_FIRST_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_filter_pagination_val_2|escape}</option>
                                                <option value="{CANONICAL_CURRENT_PAGE}" {if $settings->canonical_catalog_filter_pagination == CANONICAL_CURRENT_PAGE}selected=""{/if}>{$btr->settings_indexing_catalog_filter_pagination_val_3|escape}</option>
                                                <option value="{CANONICAL_ABSENT}" {if $settings->canonical_catalog_filter_pagination == CANONICAL_ABSENT}selected=""{/if}>{$btr->settings_indexing_catalog_filter_pagination_val_4|escape}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
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
    
        {*Блок меток заказов*}
        <div class="col-lg-6 col-md-12 hidden-md-down">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_indexing_table_title|escape} <span class="text_green">&lt;meta name="robots"&gt;</span> 
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_list">
                        <div class="okay_list_body sortable">
                            <div class="fn_row okay_list_body_item">
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_pagination}</div>
                                            <select name="robots_catalog_pagination" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{ROBOTS_INDEX_FOLLOW}" {if $settings->robots_catalog_pagination == ROBOTS_INDEX_FOLLOW}selected=""{/if}>index,follow</option>
                                                <option value="{ROBOTS_NOINDEX_FOLLOW}" {if $settings->robots_catalog_pagination == ROBOTS_NOINDEX_FOLLOW}selected=""{/if}>noindex,follow</option>
                                                <option value="{ROBOTS_NOINDEX_NOFOLLOW}" {if $settings->robots_catalog_pagination == ROBOTS_NOINDEX_NOFOLLOW}selected=""{/if}>noindex,nofollow</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_page_all}</div>
                                            <select name="robots_catalog_page_all" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{ROBOTS_INDEX_FOLLOW}" {if $settings->robots_catalog_page_all == ROBOTS_INDEX_FOLLOW}selected=""{/if}>index,follow</option>
                                                <option value="{ROBOTS_NOINDEX_FOLLOW}" {if $settings->robots_catalog_page_all == ROBOTS_NOINDEX_FOLLOW}selected=""{/if}>noindex,follow</option>
                                                <option value="{ROBOTS_NOINDEX_NOFOLLOW}" {if $settings->robots_catalog_page_all == ROBOTS_NOINDEX_NOFOLLOW}selected=""{/if}>noindex,nofollow</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_category_brand}</div>
                                            <select name="robots_category_brand" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{ROBOTS_INDEX_FOLLOW}" {if $settings->robots_category_brand == ROBOTS_INDEX_FOLLOW}selected=""{/if}>index,follow</option>
                                                <option value="{ROBOTS_NOINDEX_FOLLOW}" {if $settings->robots_category_brand == ROBOTS_NOINDEX_FOLLOW}selected=""{/if}>noindex,follow</option>
                                                <option value="{ROBOTS_NOINDEX_NOFOLLOW}" {if $settings->robots_category_brand == ROBOTS_NOINDEX_NOFOLLOW}selected=""{/if}>noindex,nofollow</option>
                                            </select>
                                        </div>
                                    </div>
                                 </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_category_features}</div>
                                            <select name="robots_category_features" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{ROBOTS_INDEX_FOLLOW}" {if $settings->robots_category_features == ROBOTS_INDEX_FOLLOW}selected=""{/if}>index,follow</option>
                                                <option value="{ROBOTS_NOINDEX_FOLLOW}" {if $settings->robots_category_features == ROBOTS_NOINDEX_FOLLOW}selected=""{/if}>noindex,follow</option>
                                                <option value="{ROBOTS_NOINDEX_NOFOLLOW}" {if $settings->robots_category_features == ROBOTS_NOINDEX_NOFOLLOW}selected=""{/if}>noindex,nofollow</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_other_filter}</div>
                                            <select name="robots_catalog_other_filter" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{ROBOTS_INDEX_FOLLOW}" {if $settings->robots_catalog_other_filter == ROBOTS_INDEX_FOLLOW}selected=""{/if}>index,follow</option>
                                                <option value="{ROBOTS_NOINDEX_FOLLOW}" {if $settings->robots_catalog_other_filter == ROBOTS_NOINDEX_FOLLOW}selected=""{/if}>noindex,follow</option>
                                                <option value="{ROBOTS_NOINDEX_NOFOLLOW}" {if $settings->robots_catalog_other_filter == ROBOTS_NOINDEX_NOFOLLOW}selected=""{/if}>noindex,nofollow</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_settings_indexing_name">
                                        <div class="mt-h">
                                            <div class="heading_label">{$btr->settings_indexing_catalog_filter_pagination}</div>
                                            <select name="robots_catalog_filter_pagination" class="selectpicker form-control col-xs-12 px-0">
                                                <option value="{ROBOTS_INDEX_FOLLOW}" {if $settings->robots_catalog_filter_pagination == ROBOTS_INDEX_FOLLOW}selected=""{/if}>index,follow</option>
                                                <option value="{ROBOTS_NOINDEX_FOLLOW}" {if $settings->robots_catalog_filter_pagination == ROBOTS_NOINDEX_FOLLOW}selected=""{/if}>noindex,follow</option>
                                                <option value="{ROBOTS_NOINDEX_NOFOLLOW}" {if $settings->robots_catalog_filter_pagination == ROBOTS_NOINDEX_NOFOLLOW}selected=""{/if}>noindex,nofollow</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {*Блок массовых действий*}
                        <div class="okay_list_footer ">
                            <div class="okay_list_foot_left">
                                <div class="okay_list_heading okay_list_check hidden">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_4" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_4"></label>
                                </div>
                            </div>
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

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap ">
                <div class="heading_box">
                    {$btr->settings_chpu_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-6 col-md-6">
                            <div class="heading_label">{$btr->max_filter_brands|escape}</div>
                            <div class="mb-1">
                                <input name="max_brands_filter_depth" class="form-control" type="text" value="{$settings->max_brands_filter_depth|escape}" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="heading_label">{$btr->max_filter_filter|escape}</div>
                            <div class="mb-1">
                                <input name="max_other_filter_depth" class="form-control" type="text" value="{$settings->max_other_filter_depth|escape}" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="heading_label">{$btr->max_filter_features|escape}</div>
                            <div class="mb-1">
                                <input name="max_features_filter_depth" class="form-control" type="text" value="{$settings->max_features_filter_depth|escape}" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="heading_label">{$btr->max_filter_features_values|escape}</div>
                            <div class="mb-1">
                                <input name="max_features_values_filter_depth" class="form-control" type="text" value="{$settings->max_features_values_filter_depth|escape}" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6">
                            <div class="heading_label">{$btr->max_filter_depth|escape}</div>
                            <div class="mb-1">
                                <input name="max_filter_depth" class="form-control" type="text" value="{$settings->max_filter_depth|escape}" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 ">
                            <div class="heading_label">&nbsp;</div>
                            <button type="submit" class="btn btn_small btn_blue float-md-right fn_update_category" data-template_type="{if $category->id}category{else}default{/if}" data-category_id="{$category->id}">
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

{$block = {get_design_block block="settings_custom_block"}}
{if !empty($block)}
    <div class="custom_block fn_toggle_wrap">
        {$block}
    </div>
{/if}

{* On document load *}
{literal}
<link rel="stylesheet" media="screen" type="text/css" href="design/js/colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="design/js/colorpicker/js/colorpicker.js"></script>
<script>
    $(function() {
        var new_label = $(".fn_new_label").clone(true);
        $(".fn_new_label").remove();

        var new_status = $(".fn_new_status").clone(true);
        $(".fn_new_status").remove();

        $(document).on("click", ".fn_add_Label", function () {
           clone_label = new_label.clone(true);
           clone_label_classes = clone_label.addClass("fn_ancor_label");
           $(".fn_labels_list").append(clone_label);

           setTimeout(function () {
            setChanges2();
            }, 100);

            function setChanges2() {
                $(clone_label_classes).each(function () {
                    $('html, body').animate({
                        scrollTop: clone_label_classes.offset().top - 70
                    }, 2000);
                });
            }
        });

        $(document).on("click", ".fn_add_status", function () {
            clone_status = new_status.clone(true);
            clone_status_classes = clone_status.addClass("fn_ancor_status");
            clone_status.find("select").selectpicker();
            $(".fn_status_list").append(clone_status);

            setTimeout(function () {
            setChanges();
            }, 100);

            function setChanges() {
                $(clone_status_classes).each(function () {
                    $('html, body').animate({
                        scrollTop: clone_status_classes.offset().top - 70
                    }, 2000);
                });
            }
        });

        $(document).on("mouseenter click", ".fn_color", function () {
            var elem = $(this);
            elem.ColorPicker({
                onChange: function (hsb, hex, rgb) {
                    elem.css('backgroundColor', '#' + hex);
                    elem.prev().val(hex);
                }
            });
        });

    });
</script>
{/literal}
