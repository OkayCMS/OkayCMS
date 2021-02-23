{$meta_title = $btr->left_setting_router_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
            {$btr->left_setting_router_title|escape}
            <i class="fn_tooltips" title="{$btr->tooltip_title_chpu|escape}">
                {include file='svg_icon.tpl' svgId='icon_tooltips'}
            </i>
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

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->manager_settings|escape}
                </div>
                <div class="permission_block">
                    <div class="permission_boxes">
                        <div class="activity_of_switch activity_of_switch--left">
                            <div class="activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">
                                        {$btr->global_url_label|escape}
                                        <i class="fn_tooltips" title="{$btr->tooltip_settings_router_statuses|escape}">
                                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                        </i>
                                    </label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="global_unique_url" value='1' type="checkbox" id="visible_checkbox" {if $settings->global_unique_url}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="activity_of_switch_item left_indent"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">
                                        {$btr->page_routes_template_slash_end|escape}
                                        <i class="fn_tooltips" title="{$btr->page_routes_template_slash_end_notice|escape}">
                                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                        </i>
                                    </label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="page_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->page_routes_template_slash_end}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            {get_design_block block="settings_router_switth_checkboxes"}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="row">
        {* Группа урлов категорий *}
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap min_height_270px">
                <div class="heading_box">
                    {$btr->category_routing|escape}
                </div>

                <div class="activity_of_switch_item settings_router_switch">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">
                            {$btr->settings_router_url_slash_end|escape}
                            <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </label>
                        <label class="switch switch-default">
                            <input class="switch-input" name="category_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->category_routes_template_slash_end}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_type_radio_wrap">
                        <input id="category_routes_default" class="hidden_check" name="category_routes_template" type="radio" value="default" {if empty($settings->category_routes_template) || $settings->category_routes_template == 'default'} checked="" {/if} />
                        <label for="category_routes_default" class="okay_type_radio">
                            <span>
                                {$rootUrl}/
                                <input name="category_routes_template__default" placeholder="catalog" class="form-control prefix-url-input" type="text" value="{if $settings->category_routes_template__default}{$settings->category_routes_template__default|escape}{else}catalog{/if}" />
                                /category
                            </span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="category_routes_no_prefix" class="hidden_check" name="category_routes_template" type="radio" value="no_prefix" {if $settings->category_routes_template == 'no_prefix'} checked="" {/if} />
                        <label for="category_routes_no_prefix" class="okay_type_radio">
                            <span>{$rootUrl}/category</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="category_routes_prefix_and_path" class="hidden_check" name="category_routes_template" type="radio" value="prefix_and_path" {if $settings->category_routes_template == 'prefix_and_path'} checked="" {/if} />
                        <label for="category_routes_prefix_and_path" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="category_routes_template__prefix_and_path" placeholder="catalog" class="form-control prefix-url-input" type="text" value="{if $settings->category_routes_template__prefix_and_path}{$settings->category_routes_template__prefix_and_path|escape}{else}catalog{/if}" />/category-level-1/.../category</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="category_routes_no_prefix_and_path" class="hidden_check" name="category_routes_template" type="radio" value="no_prefix_and_path" {if $settings->category_routes_template == 'no_prefix_and_path'} checked="" {/if} />
                        <label for="category_routes_no_prefix_and_path" class="okay_type_radio">
                            <span>{$rootUrl}/category-level-1/.../category</span>
                        </label>
                    </div>
                </div>
                {get_design_block block="settings_router_category"}
            </div>
        </div>
        
        {* Группа урлов товаров *}
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_270px">
                <div class="heading_box">
                    {$btr->product_routing|escape}
                </div>
                <div class="activity_of_switch_item settings_router_switch">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">
                            {$btr->settings_router_url_slash_end|escape}
                            <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </label>
                        <label class="switch switch-default">
                            <input class="switch-input" name="product_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->product_routes_template_slash_end}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                     </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_type_radio_wrap">
                        <input id="product_routes_default" class="hidden_check" name="product_routes_template" type="radio" value="default" {if empty($settings->product_routes_template) || $settings->product_routes_template == 'default'} checked="" {/if} />
                        <label for="product_routes_default" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="product_routes_template__default" placeholder="products" class="form-control prefix-url-input" type="text" value="{if $settings->product_routes_template__default}{$settings->product_routes_template__default|escape}{else}products{/if}" />/product-name</span>
                        </label>
                    </div>
                    <div class="okay_type_radio_wrap">
                        <input id="product_routes_prefix_and_all_categories" class="hidden_check" name="product_routes_template" type="radio" value="prefix_and_path" {if $settings->product_routes_template == 'prefix_and_path'} checked="" {/if} />
                        <label for="product_routes_prefix_and_all_categories" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="product_routes_template__prefix_and_path" placeholder="catalog" class="form-control prefix-url-input" type="text" value="{if $settings->product_routes_template__prefix_and_path}{$settings->product_routes_template__prefix_and_path|escape}{else}catalog{/if}" />/category-level-1/.../category/product-name</span>
                        </label>
                    </div>
                    <div class="okay_type_radio_wrap">
                        <input id="product_routes_no_prefix_and_path" class="hidden_check" name="product_routes_template" type="radio" value="no_prefix_and_path" {if $settings->product_routes_template == 'no_prefix_and_path'} checked="" {/if} />
                        <label for="product_routes_no_prefix_and_path" class="okay_type_radio">
                            <span>{$rootUrl}/category-level-1/.../category/product-name</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="product_routes_no_prefix_and_category" class="hidden_check" name="product_routes_template" type="radio" value="no_prefix_and_category" {if $settings->product_routes_template == 'no_prefix_and_category'} checked="" {/if} />
                        <label for="product_routes_no_prefix_and_category" class="okay_type_radio">
                            <span>{$rootUrl}/category/product-name</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="product_routes_no_prefix" class="hidden_check" name="product_routes_template" type="radio" value="no_prefix" {if $settings->product_routes_template == 'no_prefix'} checked="" {/if} />
                        <label for="product_routes_no_prefix" class="okay_type_radio">
                            <span>{$rootUrl}/product-name</span>
                        </label>
                    </div>
                </div>
                {get_design_block block="settings_router_product"}
            </div>
        </div>

        {* Группа урлов брендов *}
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->brand_routing|escape}
                </div>

                <div class="activity_of_switch_item settings_router_switch">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">
                            {$btr->settings_router_url_slash_end|escape}
                            <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </label>
                        <label class="switch switch-default">
                            <input class="switch-input" name="brand_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->brand_routes_template_slash_end}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>

                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_type_radio_wrap">
                        <input id="brand_routes_default" class="hidden_check" name="brand_routes_template" type="radio" value="default" {if empty($settings->brand_routes_template) || $settings->brand_routes_template == 'default'} checked="" {/if} />
                        <label for="brand_routes_default" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="brand_routes_template__default" placeholder="brand" class="form-control prefix-url-input" type="text" value="{if $settings->brand_routes_template__default}{$settings->brand_routes_template__default|escape}{else}brand{/if}" />/brand-name</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="brand_routes_no_prefix" class="hidden_check" name="brand_routes_template" type="radio" value="no_prefix" {if $settings->brand_routes_template == 'no_prefix'} checked="" {/if} />
                        <label for="brand_routes_no_prefix" class="okay_type_radio">
                            <span>{$rootUrl}/brand-name</span>
                        </label>
                    </div>
                </div>
                {get_design_block block="settings_router_brand"}
            </div>
        </div>

        {* Группа урлов для общих разделов *}
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->common_routing|escape}
                </div>

                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_type_radio_wrap">
                        <label for="" class="okay_type_radio_no_width" >
                            <span>{$btr->common_routes_brands}: {$rootUrl}/<input name="all_brands_routes_template__default" placeholder="brands" class="form-control prefix-url-input" type="text" value="{if $settings->all_brands_routes_template__default}{$settings->all_brands_routes_template__default}{else}brands{/if}" /></span>
                        </label>

                        <div class="okay_switch clearfix">
                            <label class="switch_label">
                                {$btr->settings_router_url_slash_end|escape}
                                <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </label>
                            <label class="switch switch-default">
                                <input class="switch-input" name="all_brands_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->all_brands_routes_template_slash_end}checked=""{/if}/>
                                <span class="switch-label"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <label for="" class="okay_type_radio_no_width" >
                            <span>{$btr->common_routes_posts}: {$rootUrl}/<input name="all_blog_routes_template__default" placeholder="all-posts" class="form-control prefix-url-input" type="text" value="{if $settings->all_blog_routes_template__default}{$settings->all_blog_routes_template__default|escape}{else}blog{/if}" /></span>
                        </label>

                        <div class="okay_switch clearfix">
                            <label class="switch_label">
                                {$btr->settings_router_url_slash_end}
                                <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </label>
                            <label class="switch switch-default">
                                <input class="switch-input" name="all_blog_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->all_blog_routes_template_slash_end}checked=""{/if}/>
                                <span class="switch-label"></span>
                                <span class="switch-handle"></span>
                            </label>
                        </div>
                    </div>
                </div>
                {get_design_block block="settings_router_news"}
            </div>
        </div>

        {* Группа урлов категорий блога *}
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap min_height_270px">
                <div class="heading_box">
                    {$btr->category_blog_routing|escape}
                </div>

                <div class="activity_of_switch_item settings_router_switch">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">
                            {$btr->settings_router_url_slash_end|escape}
                            <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </label>
                        <label class="switch switch-default">
                            <input class="switch-input" name="blog_category_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->blog_category_routes_template_slash_end}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_type_radio_wrap">
                        <input id="blog_category_routes_default" class="hidden_check" name="blog_category_routes_template" type="radio" value="default" {if empty($settings->blog_category_routes_template) || $settings->blog_category_routes_template == 'default'} checked="" {/if} />
                        <label for="blog_category_routes_default" class="okay_type_radio">
                            <span>
                                {$rootUrl}/
                                <input name="blog_category_routes_template__default" placeholder="blog" class="form-control prefix-url-input" type="text" value="{if $settings->blog_category_routes_template__default}{$settings->blog_category_routes_template__default|escape}{else}blog{/if}" />
                                /category
                            </span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="blog_category_routes_no_prefix" class="hidden_check" name="blog_category_routes_template" type="radio" value="no_prefix" {if $settings->blog_category_routes_template == 'no_prefix'} checked="" {/if} />
                        <label for="blog_category_routes_no_prefix" class="okay_type_radio">
                            <span>{$rootUrl}/category</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="blog_category_routes_prefix_and_path" class="hidden_check" name="blog_category_routes_template" type="radio" value="prefix_and_path" {if $settings->blog_category_routes_template == 'prefix_and_path'} checked="" {/if} />
                        <label for="blog_category_routes_prefix_and_path" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="blog_category_routes_template__prefix_and_path" placeholder="catalog" class="form-control prefix-url-input" type="text" value="{if $settings->blog_category_routes_template__prefix_and_path}{$settings->blog_category_routes_template__prefix_and_path|escape}{else}blog{/if}" />/category-level-1/.../category</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="blog_category_routes_no_prefix_and_path" class="hidden_check" name="blog_category_routes_template" type="radio" value="no_prefix_and_path" {if $settings->blog_category_routes_template == 'no_prefix_and_path'} checked="" {/if} />
                        <label for="blog_category_routes_no_prefix_and_path" class="okay_type_radio">
                            <span>{$rootUrl}/category-level-1/.../category</span>
                        </label>
                    </div>
                </div>
                {get_design_block block="settings_router_blog_category"}
            </div>
        </div>

        {* Группа урлов записей блога *}
        <div class="col-lg-6 col-md-12">
            <div class="boxed fn_toggle_wrap min_height_270px">
                <div class="heading_box">
                    {$btr->blog_routing|escape}
                </div>
                <div class="activity_of_switch_item settings_router_switch">
                    <div class="okay_switch clearfix">
                        <label class="switch_label">
                            {$btr->settings_router_url_slash_end|escape}
                            <i class="fn_tooltips" title="{$btr->settings_router_url_slash_end_notice|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </label>
                        <label class="switch switch-default">
                            <input class="switch-input" name="post_routes_template_slash_end" value='1' type="checkbox" id="visible_checkbox" {if $settings->post_routes_template_slash_end}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="okay_type_radio_wrap">
                        <input id="post_routes_default" class="hidden_check" name="post_routes_template" type="radio" value="default" {if empty($settings->post_routes_template) || $settings->post_routes_template == 'default'} checked="" {/if} />
                        <label for="post_routes_default" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="post_routes_template__default" placeholder="post" class="form-control prefix-url-input" type="text" value="{if $settings->post_routes_template__default}{$settings->post_routes_template__default|escape}{else}post{/if}" />/post-url</span>
                        </label>
                    </div>
                    <div class="okay_type_radio_wrap">
                        <input id="post_routes_prefix_and_all_categories" class="hidden_check" name="post_routes_template" type="radio" value="prefix_and_path" {if $settings->post_routes_template == 'prefix_and_path'} checked="" {/if} />
                        <label for="post_routes_prefix_and_all_categories" class="okay_type_radio">
                            <span>{$rootUrl}/<input name="post_routes_template__prefix_and_path" placeholder="post" class="form-control prefix-url-input" type="text" value="{if $settings->post_routes_template__prefix_and_path}{$settings->post_routes_template__prefix_and_path|escape}{else}post{/if}" />/category-level-1/.../category/post-url</span>
                        </label>
                    </div>
                    <div class="okay_type_radio_wrap">
                        <input id="post_routes_no_prefix_and_path" class="hidden_check" name="post_routes_template" type="radio" value="no_prefix_and_path" {if $settings->post_routes_template == 'no_prefix_and_path'} checked="" {/if} />
                        <label for="post_routes_no_prefix_and_path" class="okay_type_radio">
                            <span>{$rootUrl}/category-level-1/.../category/post-url</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="post_routes_no_prefix_and_category" class="hidden_check" name="post_routes_template" type="radio" value="no_prefix_and_category" {if $settings->post_routes_template == 'no_prefix_and_category'} checked="" {/if} />
                        <label for="post_routes_no_prefix_and_category" class="okay_type_radio">
                            <span>{$rootUrl}/category/post-url</span>
                        </label>
                    </div>

                    <div class="okay_type_radio_wrap">
                        <input id="post_routes_no_prefix" class="hidden_check" name="post_routes_template" type="radio" value="no_prefix" {if $settings->post_routes_template == 'no_prefix'} checked="" {/if} />
                        <label for="post_routes_no_prefix" class="okay_type_radio">
                            <span>{$rootUrl}/post-url</span>
                        </label>
                    </div>
                </div>
                {get_design_block block="settings_router_post"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="settings_router_custom_block"}}
    {if !empty($block)}
        <div class="fn_toggle_wrap custom_block">
            {$block}
        </div>
    {/if}

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="toggle_body_wrap on fn_card">
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

</form>
