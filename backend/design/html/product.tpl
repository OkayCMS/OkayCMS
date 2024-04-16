{if $product->id}
    {$meta_title = $product->name scope=global}
{else}
    {$meta_title = $btr->product_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$product->id}
                    {$btr->product_add|escape}
                {else}
                    {$product->name|escape}
                {/if}
            </div>
            {if $product->id && !empty($product->url)}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="{url_generator route='product' url=$product->url absolute=1}" >
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
                        {$btr->product_added|escape}
                    {elseif $message_success=='updated'}
                        {$btr->product_updated|escape}
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
                        {$btr->product_exists|escape}
                    {elseif $message_error=='global_url_exists'}
                        {$btr->global_url_exists|escape}
                    {elseif $message_error=='empty_name'}
                        {$btr->general_enter_title|escape}
                    {elseif $message_error == 'empty_url'}
                        {$btr->general_enter_url|escape}
                    {elseif $message_error == 'url_wrong'}
                        {$btr->general_not_underscore|escape}
                    {elseif $message_error == 'empty_categories'}
                        {$btr->product_no_category|escape}
                    {elseif $message_error == 'empty_variants'}
                        {$btr->empty_variants|escape}
                    {elseif $message_error == 'duplicate_variant_names'}
                        {$btr->duplicate_variant_names|escape}
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
<form method="post" id="product" enctype="multipart/form-data" class="clearfix fn_fast_button">
    <input type=hidden name="session_id" value="{$smarty.session.id}">
    <input type="hidden" name="lang_id" value="{$lang_id}" />
    <div class="row">
        <div class="col-xs-12">
            <div class="boxed">
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="fn_step-1">
                            <div class="heading_label heading_label--required">
                                <span>{$btr->general_name|escape}</span>
                                <i class="fn_tooltips" title="{$btr->tooltip_general_name|escape}">
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="name" type="text" value="{$product->name|escape}"/>
                                <input id="product_id" name="id" type="hidden" value="{$product->id|escape}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-8 col-lg-6">
                                <div class="fn_step-2">
                                    <div class="input-group input-group--dabbl">
                                        <span class="input-group-addon input-group-addon--left">URL</span>
                                        <input name="url" class="fn_meta_field form-control fn_url {if $product->id}fn_disabled{/if}" {if $product->id}readonly=""{/if} type="text" value="{$product->url|escape}" />
                                        <input type="checkbox" id="block_translit" class="hidden" value="1" {if $product->id}checked=""{/if}>
                                        <span class="input-group-addon fn_disable_url">
                                            {if $product->id}
                                                <i class="fa fa-lock"></i>
                                            {else}
                                                <i class="fa fa-lock fa-unlock"></i>
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {get_design_block block="product_general"}
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="fn_step-3 activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">
                                        {$btr->general_enable|escape}
                                        <i class="fn_tooltips" title="{$btr->tooltip_general_enable|escape}">
                                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                        </i>
                                    </label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="visible" value='1' type="checkbox" id="visible_checkbox" {if $product->visible}checked=""{/if}/>
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
                        <div class="fn_step-4 activity_of_switch_item">
                            <div class="okay_switch clearfix">
                                <label class="switch_label">
                                    {$btr->general_bestseller|escape}
                                    <i class="fn_tooltips" title="{$btr->tooltip_general_bestseller|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </label>
                                <label class="switch switch-default">
                                    <input class="switch-input" name="featured" value="1" type="checkbox" id="featured_checkbox" {if $product->featured}checked=""{/if}/>
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                        {get_design_block block="product_switch_checkboxes"}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Изображения товара*}
    <div class="row">
        <div class="col-lg-8 col-md-12 pr-0 ">
            <div class="fn_step-5 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->product_images|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_product_images|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>

                <div class="toggle_body_wrap fn_card on ">
                    <ul class="fn_droplist_wrap product_images_list clearfix sortable" data-image="product">
                        <li class="fn_dropzone dropzone_block">
                            <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                            <input type="file" name="" data-name="dropped_images[]" multiple class="dropinput fn_template">
                        </li>
                        {foreach $product_images as $image}
                            <li class="product_image_item {if $image@first}first_image{/if} {if $image@iteration > 4}fn_toggle_hidden hidden{/if} fn_sort_item">
                                <button type="button" class="fn_remove_image remove_image"></button>
                                <i class="move_zone">
                                    {if $image}
                                         <img class="product_icon" src="{$image->filename|resize:300:120}" alt=""/>
                                    {else}
                                        <img class="product_icon" src="design/images/no_image.png" width="40">
                                    {/if}
                                   <input type=hidden name='images_ids[]' value="{$image->id}">
                                </i>
                            </li>
                        {/foreach}
                        <li class="fn_new_image_item product_image_item fn_sort_item hidden">
                            <button type="button" class="fn_remove_image remove_image"></button>
                            <img src="" alt=""/>
                        </li>
                    </ul>
                    {if $product_images|count > 4}
                        <div class="show_more_images fn_show_images">{$btr->product_images_all|escape}</div>
                    {/if}
                </div>
                {get_design_block block="product_images"}
            </div>
        </div>

        {*Параметры элемента*}
        <div class="col-lg-4 col-md-12 ">
            <div class="boxed min_height_230px">
                <div class="fn_step-6">
                    <div class="heading_label">
                        {$btr->general_brand|escape}
                        <i class="fn_tooltips" title="{$btr->tooltip_general_brand|escape}">
                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                        </i>
                    </div>
                    <div class="">
                        <select name="brand_id" class="selectpicker form-control mb-1{if !$brands} hidden{/if} fn_meta_brand" data-live-search="true">
                            <option value="0" {if !$product->brand_id}selected=""{/if} data-brand_name="">{$btr->general_not_set|escape}</option>
                            {foreach $brands as $brand}
                            <option value="{$brand->id}" {if $product->brand_id == $brand->id}selected=""{/if} data-brand_name="{$brand->name|escape}">{$brand->name|escape}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>

                <div class="fn_step-7">
                    <div class="heading_label heading_label--required">
                        <span>{$btr->general_category|escape}</span>
                        <i class="fn_tooltips" title="{$btr->tooltip_general_category|escape}">
                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                        </i>
                    </div>
                    <div id="product_cats" class="clearfix">
                        {assign var ='first_category' value=reset($product_categories)}
                        <select class="selectpicker form-control  mb-1 fn_product_category fn_meta_categories" data-live-search="true">
                            <option value="0" selected="" disabled="" data-category_name="">{$btr->product_select_category}</option>
                            {function name=category_select level=0}
                                {foreach $categories as $category}
                                    <option value="{$category->id}" {if $category->id == $first_category->id}selected{/if} data-category_name="{$category->name|escape}">{section sp $level}- {/section}{$category->name|escape}</option>
                                    {category_select categories=$category->subcategories level=$level+1}
                                {/foreach}
                            {/function}
                            {category_select categories=$categories}
                        </select>
                        <div id="sortable_cat" class="fn_product_categories_list clearfix">
                            {foreach $product_categories as $product_category}
                                <div class="fn_category_item product_category_item {if $product_category@first}first_category{/if}">
                                    <span class="product_cat_name">{$product_category->name|escape}</span>
                                    <label class="fn_delete_product_cat fa fa-times" for="id_{$product_category->id}"></label>
                                    <input id="id_{$product_category->id}" type="checkbox" value="{$product_category->id}" data-cat_name="{$product_category->name|escape}" checked="" name="categories[]">
                                </div>
                            {/foreach}
                        </div>
                        <div class="fn_category_item fn_new_category_item product_category_item">
                            <span class="product_cat_name"></span>
                            <label class="fn_delete_product_cat fa fa-times" for=""></label>
                            <input id="" type="checkbox" value="" name="categories[]" data-cat_name="">
                        </div>
                    </div>
                </div>
                {get_design_block block="product_relations"}
            </div>
        </div>
    </div>

    {*Варианты товара*}
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_step-8 boxed fn_toggle_wrap match_matchHeight_true">
                <div class="heading_box">
                    {$btr->general_options|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_general_options|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>

                <div class="variants_wrapper fn_card">
                    <div class="okay_list variants_list scrollbar-variant">
                        <div class="okay_list_body sortable variants_listadd">
                            {foreach $product_variants as $variant}
                                <div class="okay_list_body_item variants_list_item ">
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding variants_item_drag">
                                            <div class="heading_label"></div>
                                            <div class="move_zone">
                                                {include file='svg_icon.tpl' svgId='drag_vertical'}
                                            </div>
                                        </div>
                                        <div class="okay_list_boding variants_item_sku">
                                            <div class="heading_label">
                                                {$btr->general_sku|escape}
                                                <i class="fn_tooltips" title="{$btr->tooltip_general_sku|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </div>
                                            <input class="variant_input" name="variants[sku][]" type="text" value="{$variant->sku|escape}"/>
                                        </div>
                                        <div class="okay_list_boding variants_item_name">
                                            <div class="heading_label">{$btr->general_option_name|escape}</div>
                                            <input name="variants[id][]" type="hidden" value="{$variant->id|escape}" />
                                            <input class="variant_input" name="variants[name][]" type="text" value="{$variant->name|escape}" />
                                        </div>
                                        <div class="okay_list_boding variants_item_price">
                                            <div class="heading_label">{$btr->general_price|escape}</div>
                                            <input class="variant_input" name="variants[price][]" type="text" value="{$variant->price|escape}"/>
                                        </div>
                                        <div class="okay_list_boding variants_item_discount">
                                            <div class="heading_label">{$btr->general_old_price|escape}</div>
                                            <input class="variant_input" name="variants[compare_price][]" type="text" value="{$variant->compare_price|escape}"/>
                                        </div>
                                        <div class="okay_list_boding variants_item_currency">
                                            <div class="heading_label">{$btr->general_currency|escape}</div>
                                            <select name="variants[currency_id][]" class="selectpicker form-control">
                                                {foreach $currencies as $c}
                                                    <option value="{$c->id}" {if $c->id == $variant->currency_id}selected=""{/if}>{$c->code|escape}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <div class="okay_list_boding variants_item_weight">
                                            <div class="heading_label">
                                                {$btr->general_weight|escape}
                                                <i class="fn_tooltips" title="{$btr->tooltip_general_weight|escape}">
                                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                </i>
                                            </div>
                                            <input class="variant_input" name="variants[weight][]" type="text" value="{$variant->weight|escape}"/>
                                        </div>
                                        <div class="okay_list_boding variants_item_amount">
                                            <div class="heading_label">{$btr->general_qty|escape}</div>
                                            <input class="variant_input" name="variants[stock][]" type="text" value="{if $variant->infinity}∞{else}{$variant->stock|escape}{/if}"/>
                                        </div>
                                        <div class="okay_list_boding variants_item_units">
                                            <div class="heading_label">{$btr->products_variant_units|escape}</div>
                                            <input class="variant_input" name="variants[units][]" type="text" value="{$variant->units|escape}"/>
                                        </div>
                                        {if !$variant@first}
                                        <div class="okay_list_boding okay_list_close remove_variant">
                                            <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_variant hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                {include file='svg_icon.tpl' svgId='delete'}
                                            </button>
                                        </div>
                                        {/if}
                                    </div>

                                    {$block = {get_design_block block="product_variant" vars=['variant' => $variant]}}
                                    {if !empty($block)}
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding variants_item_drag"></div>
                                            {$block}
                                        </div>
                                    {/if}
                                </div>
                            {/foreach}
                            <div class="okay_list_body_item variants_list_item fn_new_row_variant hidden">
                                <div class="okay_list_row ">
                                    <div class="okay_list_boding variants_item_drag">
                                        <div class="heading_label"></div>
                                        <div class="move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>
                                    </div>
                                    <div class="okay_list_boding variants_item_sku">
                                        <div class="heading_label">
                                            {$btr->general_sku|escape}
                                            <i class="fn_tooltips" title="{$btr->tooltip_general_sku|escape}">
                                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                            </i>
                                        </div>
                                        <input class="variant_input" name="variants[sku][]" type="text" value=""/>
                                    </div>
                                    <div class="okay_list_boding variants_item_name">
                                        <div class="heading_label">{$btr->general_option_name|escape}</div>
                                        <input name="variants[id][]" type="hidden" value="" />
                                        <input class="variant_input" name="variants[name][]" type="text" value="" />
                                    </div>
                                    <div class="okay_list_boding variants_item_price">
                                        <div class="heading_label">{$btr->general_price|escape}</div>
                                        <input class="variant_input" name="variants[price][]" type="text" value=""/>
                                    </div>
                                    <div class="okay_list_boding variants_item_discount">
                                        <div class="heading_label">{$btr->general_old_price|escape}</div>
                                        <input class="variant_input" name="variants[compare_price][]" type="text" value=""/>
                                    </div>
                                    <div class="okay_list_boding variants_item_currency">
                                        <div class="heading_label">{$btr->general_currency|escape}</div>
                                        <select name="variants[currency_id][]" class="selectpicker form-control">
                                            {foreach $currencies as $c}
                                                <option value="{$c->id}" >{$c->code|escape}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="okay_list_boding variants_item_weight">
                                        <div class="heading_label">{$btr->general_weight|escape}</div>
                                        <input class="variant_input" name="variants[weight][]" type="text" value=""/>
                                    </div>
                                    <div class="okay_list_boding variants_item_amount">
                                        <div class="heading_label">{$btr->general_qty|escape}</div>
                                        <input class="variant_input" name="variants[stock][]" type="text" value=""/>
                                    </div>
                                    <div class="okay_list_boding variants_item_units">
                                        <div class="heading_label">{$btr->products_variant_units|escape}</div>
                                        <input class="variant_input" name="variants[units][]" type="text" value=""/>
                                    </div>
                                    <div class="okay_list_boding okay_list_close remove_variant">
                                        <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove_variant hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                            {include file='svg_icon.tpl' svgId='delete'}
                                        </button>
                                    </div>
                                </div>

                                {$block = {get_design_block block="product_variant"}}
                                {if !empty($block)}
                                    <div class="okay_list_row">
                                        <div class="okay_list_boding variants_item_drag"></div>
                                        {$block}
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                    <div class="box_btn_heading mt-1">
                        <button type="button" class="btn btn_mini btn-secondary fn_add_variant">
                            {include file='svg_icon.tpl' svgId='plus'}
                            <span>{$btr->product_add_option|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Промо-изображения товара*}
    <div class="row">
        <div class="col-lg-8 col-md-12 pr-0 ">
            <div class="fn_step-9 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->product_promotions|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_product_promotions|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <ul class="fn_droplist_wrap product_images_list clearfix sortable" data-image="special">
                        <li class="fn_dropzone dropzone_block">
                            <i class="fa fa-plus font-5xl" aria-hidden="true"></i>
                            <input type="file" name="" data-name="spec_dropped_images[]" multiple class="dropinput fn_template">
                        </li>
                        {if $special_images|count > 0}
                            {foreach $special_images as $special}
                                <li class="product_image_item {if $special@iteration > 4}fn_toggle_hidden hidden{/if} fn_sort_item {if $product->special == $special->filename}product_special{/if}">
                                    <button type="button" class=" fn_remove_image remove_image"></button>
                                    <i class="move_zone">
                                        <img class="product_icon" title="{$special->name|escape}" src="../{$config->special_images_dir|escape}{$special->filename|escape}" alt="{$special->filename|escape}" />
                                        <span class="fn_change_special change_special" data-origin="{$btr->general_select|escape}" data-result="{$btr->general_unselect|escape}">
                                            {if $product->special == $special->filename}
                                                {$btr->general_unselect|escape}
                                            {else}
                                                {$btr->general_select|escape}
                                            {/if}
                                        </span>
                                        <input type="radio" name="special" value="{$special->filename|escape}" class="hidden" {if $product->special == $special->filename}checked{/if}>
                                    </i>
                                    <input type="hidden" name="spec_images_ids[]" value="{$special->id}" />
                                </li>
                            {/foreach}
                        {/if}
                        <li class="fn_new_spec_image_item product_image_item fn_sort_item hidden">
                            <button type="button" class="fn_remove_image remove_image"></button>
                            <img src="" alt=""/>
                            <i class="move_zone fa fa-arrows font-2xl"></i>
                        </li>
                    </ul>
                    {if $special_images|count > 4}
                        <div class="show_more_images fn_show_images">{$btr->product_images_all|escape}</div>
                    {/if}
                </div>
                {get_design_block block="product_promo_images"}
            </div>
        </div>
        {*Рейтинг*}
        <div class="col-lg-4 col-md-12">
            <div class="fn_step-10 boxed fn_toggle_wrap min_height_230px">
                <div class="heading_box">
                    {$btr->product_rating|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="heading_label">
                        {$btr->product_rating_value|escape}
                        <span class="font-weight-bold fn_show_rating">{$product->rating|escape}</span>
                    </div>
                    <div class="raiting_boxed">
                        <input class="fn_rating_value" type="hidden" value="{$product->rating|escape}" name="rating" />
                        <input class="fn_rating range_input" type="range" min="1" max="5" step="0.1" value="{$product->rating|escape}" />

                        <div class="raiting_range_number">
                            <span class="float-xs-left">1</span>
                            <span class="float-xs-right">5</span>
                        </div>
                    </div>
                    <div class="heading_label">
                        {$btr->product_rating_number|escape}
                        <input type="text" class="form-control" name="votes" value="{$product->votes|escape}">
                    </div>
                </div>
                {get_design_block block="product_rationg"}
            </div>
        </div>
    </div>

    {*Свойства товара*}
    <div class="row">
        <div class="col-lg-6 col-md-12 pr-0 ">
            <div class="fn_step-11 boxed fn_toggle_wrap min_height_210px">
                <div class="heading_box">
                    {$btr->product_features|escape}
                    <i class="fn_tooltips" title="{$btr->tooltip_product_features|escape}">
                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                    </i>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                    {if $lang_id != $main_lang_id}
                        <div class="boxed boxed_attention mt-h mb-0">
                            {$btr->product_features_values_change_notice}
                    </div>
                    {/if}
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="features_wrap fn_features_wrap">
                        {foreach $features as $feature}
                            <div class="fn_feature_block_{$feature->id}">
                                {assign var="feature_id" value=$feature->id}
                                {foreach $features_values.$feature_id as $feature_value}
                                    <div class="feature_row clearfix">
                                        <div class="feature_name{if !$feature_value@first} additional_values{/if}">
                                            {if $feature_value@first}
                                                <span title="{$feature->name|escape}">
                                                    <a href="index.php?controller=FeatureAdmin&id={$feature->id}" target="_blank">
                                                        {$feature->name|escape}
                                                    </a>
                                                </span>
                                            {/if}
                                        </div>
                                        <div class="feature_value">
                                            <input class="feature_input fn_auto_option" data-id="{$feature_id}" type="text" name="features_values_text[{$feature_id}][]" value="{$feature_value->value|escape}"{if $lang_id != $main_lang_id} readonly{/if}/>
                                            <input class="fn_value_id_input" type="hidden" name="features_values[{$feature_id}][]" value="{$feature_value->id}"/>
                                            <button type="button" class="btn btn_mini{if $feature_value@first} btn-secondary fn_add{else} btn-danger fn_remove{/if} fn_feature_multi_values feature_multi_values">
                                                <span class="fn_plus" {if !$feature_value@first}style="display: none;"{/if}>
                                                    {include file='svg_icon.tpl' svgId='plus'}
                                                </span>
                                                <span class="fn_minus" {if $feature_value@first}style="display: none;"{/if}>
                                                    {include file='svg_icon.tpl' svgId='minus'}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                {foreachelse}
                                    <div class="feature_row clearfix">
                                        <div class="feature_name">
                                            <span title="{$feature->name|escape}">
                                                <a href="index.php?controller=FeatureAdmin&id={$feature->id}" target="_blank">
                                                    {$feature->name|escape}
                                                </a>
                                            </span>
                                        </div>
                                        <div class="feature_value">
                                            <input class="feature_input fn_auto_option" data-id="{$feature_id}" type="text" name="features_values_text[{$feature_id}][]" value=""{if $lang_id != $main_lang_id} readonly{/if}/>
                                            <input class="fn_value_id_input" type="hidden" name="features_values[{$feature_id}][]" value=""/>
                                            <button type="button" class="btn btn_mini btn-info fn_add fn_feature_multi_values feature_multi_values">
                                                <span class="fn_plus">
                                                    {include file='svg_icon.tpl' svgId='plus'}
                                                </span>
                                                <span class="fn_minus" style="display: none">
                                                    {include file='svg_icon.tpl' svgId='minus'}
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                        <div class="fn_new_feature">
                            <div class="new_feature_row clearfix">
                                <div class="wrap_inner_new_feature">
                                    <input type="text" class="new_feature new_feature_name" name="new_features_names[]" placeholder="{$btr->product_features_enter|escape}" />
                                    <input type="text" class="new_feature new_feature_value"  name="new_features_values[]" placeholder="{$btr->product_features_value_enter|escape}"/>
                                </div>
                                <span class="fn_delete_feature btn_close delete_feature">
                                    {include file='svg_icon.tpl' svgId='delete'}
                                </span>
                            </div>
                        </div>
                        <div class="fn_new_feature_category">
                            <div class="feature_row clearfix">
                                <div class="feature_name">
                                    <span title="" class="fn_feature_name">
                                        <a href="" target="_blank"></a>
                                    </span>
                                </div>
                                <div class="feature_value">
                                    <input class="feature_input fn_auto_option" data-id="" type="text" name="" value=""{if $lang_id != $main_lang_id} readonly{/if}/>
                                    <input class="fn_value_id_input" type="hidden" name="" value=""/>
                                    <button type="button" class="btn btn_mini btn-info fn_add fn_feature_multi_values feature_multi_values">
                                        <span class="fn_plus">
                                            {include file='svg_icon.tpl' svgId='plus'}
                                        </span>
                                        <span class="fn_minus" style="display: none">
                                            {include file='svg_icon.tpl' svgId='minus'}
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box_btn_heading mt-1">
                        <button type="button" class="btn btn_mini btn-secondary fn_add_feature">
                        {include file='svg_icon.tpl' svgId='plus'}
                        <span>{$btr->product_feature_add|escape}</span>
                        </button>
                    </div>
                </div>
            </div>
            {get_design_block block="product_features"}
        </div>

        {*Связанные товары*}
        <div class="col-lg-6 col-md-12">
            <div class="fn_step-12 boxed fn_toggle_wrap min_height_210px">
                {backend_compact_product_list title=$btr->general_recommended
                name="related_products"
                products=$related_products
                label=$btr->general_recommended_add
                placeholder=$btr->general_add_product
                }
            </div>
            {get_design_block block="product_related_products"}
        </div>
    </div>

    {*Метаданные товара*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-13 boxed match fn_toggle_wrap">
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
                        <div class="">
                            <div class="heading_label heading_label--flex">
                                <div class="heading_label__start">
                                    Meta-title <span id="fn_meta_title_counter"></span>
                                    <i class="fn_tooltips" title="{$btr->tooltip_meta_title|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </div>
                                <button type="button" class="fn_open_ai_generate_meta button_meta__open_ai hint-bottom-right-t-info-s-small-mobile hint-anim" data-hint="{$btr->button_open_ai_tooltip|escape}" data-ai_field="{Okay\Helpers\AiRequests\AiProductRequest::FIELD_META_TITLE}" data-ai_entity="{Okay\Helpers\AiRequests\AiProductRequest::ENTITY_TYPE}">
                                    <svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-2/3 w-2/3" role="img"><text x="-9999" y="-9999">ChatGPT</text><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>
                                    {$btr->button_open_ai_text|escape}
                                </button>
                            </div>
                            <input name="meta_title" class="form-control fn_meta_field mb-h" type="text" value="{$product->meta_title|escape}" />
                        </div>

                        <div class="">
                            <div class="heading_label heading_label--flex">
                                <div class="heading_label__start">
                                    Meta-keywords
                                    <i class="fn_tooltips" title="{$btr->tooltip_meta_keywords|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </div>
                                <button type="button" class="fn_open_ai_generate_meta button_meta__open_ai hint-bottom-right-t-info-s-small-mobile hint-anim" data-hint="{$btr->button_open_ai_tooltip|escape}" data-ai_field="{Okay\Helpers\AiRequests\AiProductRequest::FIELD_META_KEYWORDS}" data-ai_entity="{Okay\Helpers\AiRequests\AiProductRequest::ENTITY_TYPE}">
                                    <svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-2/3 w-2/3" role="img"><text x="-9999" y="-9999">ChatGPT</text><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>
                                    {$btr->button_open_ai_text|escape}
                                </button>
                            </div>
                            <input name="meta_keywords" class="form-control fn_meta_field mb-h" type="text" value="{$product->meta_keywords|escape}" />
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 pl-0">
                        <div class="">
                            <div class="heading_label heading_label--flex">
                                <div class="heading_label__start">
                                    Meta-description <span id="fn_meta_description_counter"></span>
                                    <i class="fn_tooltips" title="{$btr->tooltip_meta_description|escape}">
                                        {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                    </i>
                                </div>
                                <button type="button" class="fn_open_ai_generate_meta button_meta__open_ai hint-bottom-right-t-info-s-small-mobile hint-anim" data-hint="{$btr->button_open_ai_tooltip|escape}" data-ai_field="{Okay\Helpers\AiRequests\AiProductRequest::FIELD_META_DESCRIPTION}" data-ai_entity="{Okay\Helpers\AiRequests\AiProductRequest::ENTITY_TYPE}">
                                    <svg width="41" height="41" viewBox="0 0 41 41" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-2/3 w-2/3" role="img"><text x="-9999" y="-9999">ChatGPT</text><path d="M37.5324 16.8707C37.9808 15.5241 38.1363 14.0974 37.9886 12.6859C37.8409 11.2744 37.3934 9.91076 36.676 8.68622C35.6126 6.83404 33.9882 5.3676 32.0373 4.4985C30.0864 3.62941 27.9098 3.40259 25.8215 3.85078C24.8796 2.7893 23.7219 1.94125 22.4257 1.36341C21.1295 0.785575 19.7249 0.491269 18.3058 0.500197C16.1708 0.495044 14.0893 1.16803 12.3614 2.42214C10.6335 3.67624 9.34853 5.44666 8.6917 7.47815C7.30085 7.76286 5.98686 8.3414 4.8377 9.17505C3.68854 10.0087 2.73073 11.0782 2.02839 12.312C0.956464 14.1591 0.498905 16.2988 0.721698 18.4228C0.944492 20.5467 1.83612 22.5449 3.268 24.1293C2.81966 25.4759 2.66413 26.9026 2.81182 28.3141C2.95951 29.7256 3.40701 31.0892 4.12437 32.3138C5.18791 34.1659 6.8123 35.6322 8.76321 36.5013C10.7141 37.3704 12.8907 37.5973 14.9789 37.1492C15.9208 38.2107 17.0786 39.0587 18.3747 39.6366C19.6709 40.2144 21.0755 40.5087 22.4946 40.4998C24.6307 40.5054 26.7133 39.8321 28.4418 38.5772C30.1704 37.3223 31.4556 35.5506 32.1119 33.5179C33.5027 33.2332 34.8167 32.6547 35.9659 31.821C37.115 30.9874 38.0728 29.9178 38.7752 28.684C39.8458 26.8371 40.3023 24.6979 40.0789 22.5748C39.8556 20.4517 38.9639 18.4544 37.5324 16.8707ZM22.4978 37.8849C20.7443 37.8874 19.0459 37.2733 17.6994 36.1501C17.7601 36.117 17.8666 36.0586 17.936 36.0161L25.9004 31.4156C26.1003 31.3019 26.2663 31.137 26.3813 30.9378C26.4964 30.7386 26.5563 30.5124 26.5549 30.2825V19.0542L29.9213 20.998C29.9389 21.0068 29.9541 21.0198 29.9656 21.0359C29.977 21.052 29.9842 21.0707 29.9867 21.0902V30.3889C29.9842 32.375 29.1946 34.2791 27.7909 35.6841C26.3872 37.0892 24.4838 37.8806 22.4978 37.8849ZM6.39227 31.0064C5.51397 29.4888 5.19742 27.7107 5.49804 25.9832C5.55718 26.0187 5.66048 26.0818 5.73461 26.1244L13.699 30.7248C13.8975 30.8408 14.1233 30.902 14.3532 30.902C14.583 30.902 14.8088 30.8408 15.0073 30.7248L24.731 25.1103V28.9979C24.7321 29.0177 24.7283 29.0376 24.7199 29.0556C24.7115 29.0736 24.6988 29.0893 24.6829 29.1012L16.6317 33.7497C14.9096 34.7416 12.8643 35.0097 10.9447 34.4954C9.02506 33.9811 7.38785 32.7263 6.39227 31.0064ZM4.29707 13.6194C5.17156 12.0998 6.55279 10.9364 8.19885 10.3327C8.19885 10.4013 8.19491 10.5228 8.19491 10.6071V19.808C8.19351 20.0378 8.25334 20.2638 8.36823 20.4629C8.48312 20.6619 8.64893 20.8267 8.84863 20.9404L18.5723 26.5542L15.206 28.4979C15.1894 28.5089 15.1703 28.5155 15.1505 28.5173C15.1307 28.5191 15.1107 28.516 15.0924 28.5082L7.04046 23.8557C5.32135 22.8601 4.06716 21.2235 3.55289 19.3046C3.03862 17.3858 3.30624 15.3413 4.29707 13.6194ZM31.955 20.0556L22.2312 14.4411L25.5976 12.4981C25.6142 12.4872 25.6333 12.4805 25.6531 12.4787C25.6729 12.4769 25.6928 12.4801 25.7111 12.4879L33.7631 17.1364C34.9967 17.849 36.0017 18.8982 36.6606 20.1613C37.3194 21.4244 37.6047 22.849 37.4832 24.2684C37.3617 25.6878 36.8382 27.0432 35.9743 28.1759C35.1103 29.3086 33.9415 30.1717 32.6047 30.6641C32.6047 30.5947 32.6047 30.4733 32.6047 30.3889V21.188C32.6066 20.9586 32.5474 20.7328 32.4332 20.5338C32.319 20.3348 32.154 20.1698 31.955 20.0556ZM35.3055 15.0128C35.2464 14.9765 35.1431 14.9142 35.069 14.8717L27.1045 10.2712C26.906 10.1554 26.6803 10.0943 26.4504 10.0943C26.2206 10.0943 25.9948 10.1554 25.7963 10.2712L16.0726 15.8858V11.9982C16.0715 11.9783 16.0753 11.9585 16.0837 11.9405C16.0921 11.9225 16.1048 11.9068 16.1207 11.8949L24.1719 7.25025C25.4053 6.53903 26.8158 6.19376 28.2383 6.25482C29.6608 6.31589 31.0364 6.78077 32.2044 7.59508C33.3723 8.40939 34.2842 9.53945 34.8334 10.8531C35.3826 12.1667 35.5464 13.6095 35.3055 15.0128ZM14.2424 21.9419L10.8752 19.9981C10.8576 19.9893 10.8423 19.9763 10.8309 19.9602C10.8195 19.9441 10.8122 19.9254 10.8098 19.9058V10.6071C10.8107 9.18295 11.2173 7.78848 11.9819 6.58696C12.7466 5.38544 13.8377 4.42659 15.1275 3.82264C16.4173 3.21869 17.8524 2.99464 19.2649 3.1767C20.6775 3.35876 22.0089 3.93941 23.1034 4.85067C23.0427 4.88379 22.937 4.94215 22.8668 4.98473L14.9024 9.58517C14.7025 9.69878 14.5366 9.86356 14.4215 10.0626C14.3065 10.2616 14.2466 10.4877 14.2479 10.7175L14.2424 21.9419ZM16.071 17.9991L20.4018 15.4978L24.7325 17.9975V22.9985L20.4018 25.4983L16.071 22.9985V17.9991Z" fill="currentColor"></path></svg>
                                    {$btr->button_open_ai_text|escape}
                                </button>
                            </div>
                            <textarea name="meta_description" class="form-control okay_textarea fn_meta_field">{$product->meta_description|escape}</textarea>
                        </div>
                    </div>
                </div>
                {get_design_block block="product_meta_data"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="product_custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}

    {*Описание элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-14 boxed match fn_toggle_wrap tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="heading_box tab_navigation_link">
                            {$btr->general_short_description|escape}

                        </a>
                        <a href="#tab2" class="heading_box tab_navigation_link">
                            {$btr->general_full_description|escape}
                            <i style="right: -8px" class="fn_tooltips" title="{$btr->tooltip_general_full_description|escape}">
                                {include file='svg_icon.tpl' svgId='icon_tooltips'}
                            </i>
                        </a>
                    </div>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <textarea name="annotation" id="annotation" data-ai_entity="{Okay\Helpers\AiRequests\AiProductRequest::ENTITY_TYPE}" class="editor_small">{$product->annotation|escape}</textarea>
                        </div>
                        <div id="tab2" class="tab">
                            <textarea id="fn_editor" name="description" data-ai_entity="{Okay\Helpers\AiRequests\AiProductRequest::ENTITY_TYPE}" class="editor_large fn_editor_class">{$product->description|escape}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12 col-md-12 mt-1">
                        <button id="fast_save_button_and_quit" type="submit" class="fn_step-15 btn btn_small btn_blue float-md-right ml-1" name="apply_and_quit" value="1">
                            {include file='svg_icon.tpl' svgId='checked'}
                            <span>{$btr->general_apply_and_quit|escape}</span>
                        </button>
                        <button type="submit" class="fn_step-15 btn btn_small btn_blue float-md-right">
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
{include file='learning_hints.tpl' hintId='hint_product'}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
{* On document load *}
{literal}
    <script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
    <script src="design/js/chosen/chosen.jquery.js"></script>
    <script src="design/js/open_ai.js"></script>
    <link rel="stylesheet" type="text/css" href="design/js/chosen/chosen.min.css" media="screen" />
<script>
    $(window).on("load", function() {

        $(document).on("click", ".fn_show_images",function () {
           $(this).prev().find($(".fn_toggle_hidden")).toggleClass("hidden");
        });
        // Удаление товара
        $(document).on( "click", ".fn_remove_item", function() {
            $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
            return false;
        });
        $(".chosen").chosen('chosen-select');

        $(document).on("input", ".fn_rating", function () {
            $(".fn_show_rating").html($(this).val());
            $(".fn_rating_value").val($(this).val());
        });

        var image_item_clone = $(".fn_new_image_item").clone(true).removeClass('hidden');
        $(".fn_new_image_item").remove();
        var new_image_tem_clone = $(".fn_new_spec_image_item").clone(true).removeClass('hidden');
        $(".fn_new_spec_image_item").remove();
        // Или перетаскиванием
        if(window.File && window.FileReader && window.FileList) {

            $(".fn_dropzone").on('dragover', function (e){
                e.preventDefault();
                $(this).css('background', '#bababa');
            });
            $(".fn_dropzone").on('dragleave', function(){
                $(this).css('background', '#f8f8f8');
            });

            function handleFileSelect(evt){
                let droplistWrap = $(this).closest(".fn_droplist_wrap");
                droplistWrap.find("input.dropinput").hide();
                let originalDropInput = droplistWrap.find("input.dropinput.fn_template");
                let dropInput = originalDropInput.clone().attr('value', originalDropInput.val());
                dropInput.attr('name', dropInput.data('name')).removeClass('fn_template').show();
                var parent = $(this).closest(".fn_droplist_wrap");
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
                            if(parent.data('image') == "product"){
                                var clone_item = image_item_clone.clone(true);
                            } else if(parent.data('image') == "special") {
                                var clone_item = new_image_tem_clone.clone(true);
                            }
                            clone_item.find("img").attr("onerror",'');
                            clone_item.find("img").attr("src", e.target.result);
                            clone_item.find("input").val(theFile.name);
                            clone_item.appendTo(parent);
                            parent.find(".fn_dropzone").append(dropInput);
                        };
                    })(f);
                    // Read in the image file as a data URL.
                    reader.readAsDataURL(f);
                }
                $(".fn_dropzone").removeAttr("style");
            }
            $(document).on('change', '.dropinput', handleFileSelect);

            $('.dropinput').each(function () {
                let droplistWrap = $(this).closest(".fn_droplist_wrap");
                droplistWrap.find("input.dropinput").hide();
                let dropInput = droplistWrap.find("input.dropinput.fn_template").clone();
                dropInput.attr('name', dropInput.data('name')).removeClass('fn_template').show();
                var parent = $(this).closest(".fn_droplist_wrap");
                parent.find(".fn_dropzone").append(dropInput);
            });
            
        }
        $(document).on("click", ".fn_remove_image", function () {
            $(this).closest("li").remove();
        });
        $(document).on("click", ".fn_change_special", function () {
            if($(this).closest('li').hasClass("product_special")) {
                $(this).closest("ul").find("input[type=radio]").attr("checked", false);
                $(this).closest("li").removeClass("product_special");
                $(this).text($(this).data("origin"));
            } else {
                $(this).closest("ul").find("input[type=radio]").attr("checked", false);
                $(this).closest("li").removeClass("product_special");
                $(this).closest("li").find("input[type=radio]").attr("checked", true).click();
                $(this).closest("ul").find("li").removeClass("product_special");
                $(this).closest("li").addClass("product_special");
                $(this).text($(this).data("result"));
            }

        });
        $(document).on("click",".fn_remove_variant",function () {
            $(this).closest(".variants_list_item ").fadeOut(200);
            $(this).closest(".variants_list_item ").remove();
        });

        // Добавление варианта
        var variant = $(".fn_new_row_variant").clone(false).removeClass('hidden');
        $(".fn_new_row_variant").remove();
        variant.find('.bootstrap-select').replaceWith(function() { return $('select', this); });
        $(document).on("click", ".fn_add_variant", function () {
           var variant_clone = variant.clone(true);
           variant_clone.find("select").selectpicker();
           variant_clone.removeClass("hidden").removeClass("fn_new_row_variant");
           $(".variants_listadd").append(variant_clone);
            return false;
        });

        var clone_cat = $(".fn_new_category_item").clone();
        $(".fn_new_category_item").remove();
        clone_cat.removeClass("fn_new_category_item");
        $(document).on("change", ".fn_product_category select", function () {
            var clone = clone_cat.clone();
            clone.find("label").attr("for","id_"+$(this).find("option:selected").val());
            clone.find("span").html($(this).find("option:selected").data("category_name"));
            clone.find("input").attr("id","id_"+$(this).find("option:selected").val());
            clone.find("input").val($(this).find("option:selected").val());
            clone.find("input").attr("checked",true);
            clone.find("input").attr("data-cat_name",$(this).find("option:selected").data("category_name"));
            $(".fn_product_categories_list").append(clone);
            if ($(".fn_category_item").size() == 1) {
                change_product_category();
            }
        });
        $(document).on("click", ".fn_delete_product_cat", function () {
            var item = $(this).closest(".fn_category_item"),
                is_first = item.hasClass("first_category");
            item.remove();
            if (is_first && $(".fn_category_item").size() > 0) {
                change_product_category();
            }
        });

        var el = document.getElementById('sortable_cat');
        var sortable = Sortable.create(el, {
            handle: ".product_cat_name",  // Drag handle selector within list items
            sort: true,  // sorting inside list
            animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation

            ghostClass: "sortable-ghost",  // Class name for the drop placeholder
            chosenClass: "sortable-chosen",  // Class name for the chosen item
            dragClass: "sortable-drag",  // Class name for the dragging item
            scrollSensitivity: 30, // px, how near the mouse must be to an edge to start scrolling.
            scrollSpeed: 10, // px
            // Changed sorting within list
            onUpdate: function (evt) {
                change_product_category();
            }
        });

        function change_product_category() {
            var wrapper = $(".fn_product_categories_list");
            var first_category = wrapper.find("div.fn_category_item:first input").val();
            wrapper.find("div.first_category").removeClass("first_category");
            wrapper.find("div.fn_category_item:first ").addClass("first_category");
            set_meta();
            show_category_features(first_category);
        }

        var new_feature_category = $(".fn_new_feature_category").clone(true);
        $(".fn_new_feature_category").remove();
        new_feature_category.removeClass("fn_new_feature_category");
        function show_category_features(category_id)
        {
            $("div.fn_features_wrap").empty();
            $.ajax({
                url: "ajax/get_features.php",
                data: {category_id: category_id, product_id: $("#product_id").val()},
                dataType: 'json',
                success: function(data){
                    for(i=0; i<data.length; i++)
                    {
                        feature = data[i];
                        for (var iv=0; iv<feature.values.length; iv++) {
                            let new_line = new_feature_category.clone(true);
                            new_line.addClass('fn_feature_block_'+feature.id);
                            new_line.find(".fn_feature_name").attr('title', feature.name);
                            new_line.find(".fn_feature_name a").text(feature.name).attr('href', "index.php?controller=FeatureAdmin&id="+feature.id);
                            let value = new_line.find(".fn_auto_option"),
                                id_input = new_line.find(".fn_value_id_input");
                            value.data('id', feature.id);
                            value.val(feature.values[iv].value);
                            value.attr('name', "features_values_text["+feature.id+"][]");
                            id_input.attr('name', "features_values["+feature.id+"][]");
                            id_input.val(feature.values[iv].id)
                            {/literal}
                            {if $lang_id == $main_lang_id}
                            {literal}
                                value.devbridgeAutocomplete({
                                    serviceUrl:'ajax/options_autocomplete.php',
                                    minChars:0,
                                    orientation:'auto',
                                    params: {feature_id:feature.id},
                                    noCache: false,
                                    onSelect:function(suggestion){
                                        id_input.val(suggestion.data.id);
                                        $(this).trigger('change');
                                    },
                                    onSearchStart:function(params){
                                        id_input.val("");
                                    }
                                });
                            {/literal}
                            {/if}
                            {literal}
                            if (iv > 0) {
                                new_line.find(".fn_feature_multi_values")
                                    .removeClass("fn_add")
                                    .removeClass("btn-info")
                                    .addClass("fn_remove")
                                    .addClass("btn-danger");
                                new_line.find(".fn_plus").hide();
                                new_line.find(".fn_minus").show();
                                new_line.find(".feature_name").html("").addClass("additional_values");
                            }

                            new_line.appendTo("div.fn_features_wrap");
                        }
                    }
                }
            });
            return false;
        }

        {/literal}
        {if $lang_id == $main_lang_id}
        {literal}
        $(document).on("click",".fn_feature_multi_values.fn_add", function () {
            var feature_id  = $(this).closest(".feature_value").find(".fn_auto_option").data("id"),
                new_value   = new_feature_category.clone(true),
                value_input = new_value.find(".fn_auto_option"),
                id_input    = new_value.find(".fn_value_id_input");
            value_input.data("id", feature_id);
            value_input.val("");
            value_input.attr('name', "features_values_text["+feature_id+"][]");
            id_input.attr("name", "features_values["+feature_id+"][]");

            new_value.find(".feature_name").html("").addClass("additional_values");
            new_value.find(".fn_feature_multi_values")
                .removeClass("fn_add")
                .removeClass("btn-info")
                .addClass("fn_remove")
                .addClass("btn-danger");
            new_value.find(".fn_plus").hide();
            new_value.find(".fn_minus").show();

            value_input.devbridgeAutocomplete({
                serviceUrl:'ajax/options_autocomplete.php',
                minChars:0,
                params: {feature_id:feature_id},
                noCache: false,
                onSelect:function(suggestion){
                    id_input.val(suggestion.data.id);
                    $(this).trigger('change');
                },
                onSearchStart:function(params){
                    id_input.val("");
                }
            });
            new_value.appendTo(".fn_feature_block_"+feature_id).fadeIn('slow');
            return false;
        });

        $(document).on("click",".fn_feature_multi_values.fn_remove",function () {
            $(this).closest(".feature_row").remove();
        });

        // Автодополнение свойств
        $(".fn_auto_option").each(function() {
            var feature_id = $(this).data("id"),
                id_input = $(this).closest(".feature_value").find(".fn_value_id_input");
            $(this).devbridgeAutocomplete({
                serviceUrl:'ajax/options_autocomplete.php',
                minChars:0,
                params: {feature_id:feature_id},
                noCache: false,
                onSelect:function(suggestion){
                    id_input.val(suggestion.data.id);
                    $(this).trigger('change');
                },
                onSearchStart:function(params){
                    id_input.val("");
                }
            });
        });
        {/literal}
        {/if}
        {literal}

        // Добавление нового свойства товара
        var new_feature = $(".fn_new_feature").clone(true);
        $(".fn_new_feature").remove();
        new_feature.removeClass("fn_new_feature");
        $(document).on("click",".fn_add_feature",function () {
            $(new_feature).clone(true).appendTo(".features_wrap").fadeIn('slow');
            return false;
        });
        $(document).on("click",".fn_delete_feature",function () {
           $(this).parent().remove();
        });
        
        // infinity
        $("input[name*=variant][name*=stock]").focus(function() {
            if($(this).val() == '∞')
                $(this).val('');
            return false;
        });

        $("input[name*=variant][name*=stock]").blur(function() {
            if($(this).val() == '')
                $(this).val('∞');
        });
    });

</script>
{/literal}