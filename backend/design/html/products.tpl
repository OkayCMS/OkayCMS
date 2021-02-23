{* Title *}
{if $category}
	{$meta_title=$category->name scope=global}
{else}
	{$meta_title=$btr->general_products scope=global}
{/if}

{*Название страницы*}
<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            {if $products_count}
                <div class="box_heading heading_page">
                    {if $category->name || $brand->name}
                        {$category->name|escape} {$brand->name|escape} - {$products_count}
                    {elseif $keyword}
                         {$btr->general_products|escape} - {$products_count}
                    {else}
                        {$btr->general_products|escape} - {$products_count}
                    {/if}
                </div>
            {else}
                <div class="box_heading heading_page">{$btr->products_no|escape}</div>
            {/if}
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="{url controller=ProductAdmin return=$smarty.server.REQUEST_URI}">
                    {include file='svg_icon.tpl' svgId='plus'}
                    <span>{$btr->products_add|escape}</span>
                </a>
            </div>
            {*{get_design_block block="products_heading"}*}
        </div>
    </div>
    <div class="main_header__item">
        <div class="main_header__inner">
            <form class="search" method="get">
            <input type="hidden" name="controller" value="ProductsAdmin">
            <div class="input-group input-group--search">
                <input name="keyword" class="form-control" placeholder="{$btr->products_search|escape}" type="text" value="{$keyword|escape}" >
                <span class="input-group-btn">
                    <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                </span>
            </div>
            </form>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    {*Блок фильтров*}
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->general_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="fn_step-0 boxed_sorting toggle_body_wrap off fn_card">
                <div class="row">
                    <div class="col-md-3 col-lg-3 col-sm-12">
                        <div>
                            <select id="id_filter" name="products_filter" class="selectpicker form-control" title="{$btr->products_filter|escape}" data-live-search="true" onchange="location = this.value;">
                                <option value="{url brand_id=null category_id=null keyword=null page=null limit=null filter=null}" {if !$filter}selected{/if}>{$btr->general_all_products|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='featured'}" {if $filter == 'featured'}selected{/if}>{$btr->products_bestsellers|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='discounted'}" {if $filter == 'discounted'}selected{/if}>{$btr->products_discount|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='visible'}" {if $filter == 'visible'}selected{/if}>{$btr->products_enable|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='hidden'}" {if $filter == 'hidden'}selected{/if}>{$btr->products_disable|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='instock'}" {if $filter == 'instock'}selected{/if}>{$btr->products_in_stock|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='outofstock'}" {if $filter == 'outofstock'}selected{/if}>{$btr->products_out_of_stock|escape}</option>
                                <option value="{url keyword=null brand_id=null category_id=null page=null limit=null filter='without_images'}" {if $filter == 'without_images'}selected{/if}>{$btr->products_without_photos|escape}</option>
                                {get_design_block block="products_filter_custom_option"}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 col-lg-3 col-sm-12">
                        <select id="id_categories" name="categories_filter" title="{$btr->general_category_filter|escape}" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="location = this.value;">
                            <option value="{url keyword=null brand_id=null page=null limit=null category_id=null}" {if !$category_id}selected{/if}>{$btr->general_all_categories|escape}</option>
                            <option value="{url keyword=null brand_id=null page=null limit=null category_id=-1}" {if $category_id==-1}selected{/if}>{$btr->products_without_category|escape}</option>
                            {function name=category_select level=0}
                                {foreach $categories as $c}
                                    <option value='{url keyword=null brand_id=null page=null limit=null category_id=$c->id}' {if $category_id == $c->id}selected{/if}>
                                        {section sp $level}- {/section}{$c->name|escape}
                                    </option>
                                    {category_select categories=$c->subcategories level=$level+1}
                                {/foreach}
                            {/function}
                            {category_select categories=$categories}
                        </select>
                    </div>
                    <div class="col-md-3 col-lg-3 col-sm-12">
                        <select id="id_brands" name="brands_filter" title="{$btr->general_brand_filter|escape}" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="location = this.value;">
                            <option value="{url keyword=null brand_id=null page=null limit=null}" {if !$brand_id}selected{/if}>{$btr->general_all_brands|escape}</option>
                            <option value="{url keyword=null brand_id=-1 page=null limit=null}" {if $brand_id==-1}selected{/if}>{$btr->products_without_brand}</option>
                            {foreach $brands as $b}
                                <option value="{url keyword=null page=null limit=null brand_id=$b->id}" brand_id="{$b->id}"  {if $brand_id == $b->id}selected{/if}>{$b->name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-12">
                        <div class="pull-right">
                            <select onchange="location = this.value;" class="selectpicker form-control">
                                <option value="{url limit=5}" {if $current_limit == 5}selected{/if}>{$btr->general_show_by|escape} 5</option>
                                <option value="{url limit=10}" {if $current_limit == 10}selected{/if}>{$btr->general_show_by|escape} 10</option>
                                <option value="{url limit=25}" {if $current_limit == 25}selected{/if}>{$btr->general_show_by|escape} 25</option>
                                <option value="{url limit=50}" {if $current_limit == 50}selected{/if}>{$btr->general_show_by|escape} 50</option>
                                <option value="{url limit=100}" {if $current_limit == 100}selected=""{/if}>{$btr->general_show_by|escape} 100</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            {$block = {get_design_block block="products_custom_block"}}
            {if !empty($block)}
                <div class="fn_toggle_wrap custom_block">
                    {$block}
                </div>
            {/if}
        </div>
    </div>

    {if $products}
        <div class="row">
            {*Главная форма страницы*}
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form method="post" class="fn_form_list fn_fast_button">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">

                    <div class="okay_list products_list fn_sort_list">
                        {*Шапка таблицы*}
                        <div class="fn_step_sorting okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_photo">
                                {if $smarty.get.sort == 'main_image_id'}
                                    {$sort = 'main_image_id_desc'}
                                {else}
                                    {$sort = 'main_image_id'}
                                {/if}
                                <a href="{url sort=$sort page=null}" class="{$sort}{if $smarty.get.sort == 'main_image_id' || $smarty.get.sort == 'main_image_id_desc'} active{/if}">
                                    {$btr->general_photo|escape} {include file='svg_icon.tpl' svgId='sorting'}
                                </a>
                            </div>
                            <div class="okay_list_heading okay_list_name">
                                {if $smarty.get.sort == 'name'}
                                    {$sort = 'name_desc'}
                                {else}
                                    {$sort = 'name'}
                                {/if}
                                <a href="{url sort=$sort page=null}" class="{$sort}{if $smarty.get.sort == 'name' || $smarty.get.sort == 'name_desc'} active{/if}">
                                    {$btr->general_name|escape} {include file='svg_icon.tpl' svgId='sorting'}
                                </a>
                            </div>
                            <div class="okay_list_heading okay_list_price">
                                {if $smarty.get.sort == 'price'}
                                    {$sort = 'price_desc'}
                                {else}
                                    {$sort = 'price'}
                                {/if}
                                <a href="{url sort=$sort page=null}" class="{$sort}{if $smarty.get.sort == 'price' || $smarty.get.sort == 'price_desc'} active{/if}">
                                    {$btr->general_price|escape} {include file='svg_icon.tpl' svgId='sorting'}
                                </a>
                            </div>
                            <div class="okay_list_heading okay_list_count">
                                {if $smarty.get.sort == 'stock'}
                                    {$sort = 'stock_desc'}
                                {else}
                                    {$sort = 'stock'}
                                {/if}
                                <a href="{url sort=$sort page=null}" class="{$sort}{if $smarty.get.sort == 'stock' || $smarty.get.sort == 'stock_desc'} active{/if}">
                                    {$btr->general_qty|escape} {include file='svg_icon.tpl' svgId='sorting'}
                                </a>
                            </div>
                            <div class="okay_list_heading okay_list_status">
                                {if $smarty.get.sort == 'visible'}
                                    {$sort = 'visible_desc'}
                                {else}
                                    {$sort = 'visible'}
                                {/if}
                                <a href="{url sort=$sort page=null}" class="{$sort}{if $smarty.get.sort == 'visible' || $smarty.get.sort == 'visible_desc'} active{/if}">
                                    {$btr->general_enable|escape} {include file='svg_icon.tpl' svgId='sorting'}
                                </a>
                            </div>
                            <div class="okay_list_heading okay_list_setting okay_list_products_setting">{$btr->general_activities|escape}</div>
                            <div class="okay_list_heading okay_list_close">
                                {*if !empty($smarty.get.sort)}
                                <a data-hint="Сбросить сортировку" href="{url sort=null}" class="reset_sort hint-left-middle-t-white-s-small-mobile  hint-anim">x</a>
                                {/if*}
                            </div>
                        </div>
                        {*Параметры элемента*}
                        <div class="okay_list_body sort_extended">
                            {foreach $products as $product}
                                <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                                    <div class="okay_list_row">
                                        <input type="hidden" name="positions[{$product->id}]" value="{$product->position}">

                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                        </div>

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_{$product->id}" name="check[]" value="{$product->id}"/>
                                            <label class="okay_ckeckbox" for="id_{$product->id}"></label>
                                        </div>
                                        <div class="okay_list_boding okay_list_photo">
                                            {if $product->image}
                                                <a href="{url controller=ProductAdmin id=$product->id return=$smarty.server.REQUEST_URI}">
                                                    <img src="{$product->image->filename|escape|resize:55:55}"/>
                                                </a>
                                            {else}
                                                <img height="55" width="55" src="design/images/no_image.png"/>
                                            {/if}
                                        </div>
                                        <div class="okay_list_boding okay_list_name">

                                            <a class="text_400 link" href="{url controller=ProductAdmin id=$product->id return=$smarty.server.REQUEST_URI}">
                                                {$product->name|escape}
                                                {if $product->variants[0]->name}
                                                    <span class="text_grey">({$product->variants[0]->name|escape})</span>
                                                {/if}
                                                <div class="hidden-lg-up mt-q">
                                                <span class="text_primary text_500">{$product->variants[0]->price} {if isset($currencies[$product->variants[0]->currency_id])}
                                                          {$currencies[$product->variants[0]->currency_id]->code|escape}
                                                      {/if}</span>
                                                    <span class="text_500">{if $product->variants[0]->infinity}∞{else}{$product->variants[0]->stock}{/if} {if $product->variants[0]->units}{$product->variants[0]->units|escape}{else}{$settings->units|escape}{/if}</span>
                                                </div>
                                                {if $all_brands[$product->brand_id]->name}
                                                <div class="okay_list_name_brand text_400 text_grey">{$btr->general_brand|escape} {$all_brands[$product->brand_id]->name|escape}</div>
                                                {/if}
                                            </a>
                                            {if $product->variants|count > 1}
                                                <div class="fn_variants_toggle variants_toggle">
                                                    <span>{$btr->general_options|escape}</span>
                                                    <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2"></i>
                                                </div>
                                            {/if}

                                            {get_design_block block="products_list_name"}
                                        </div>
                                        <div class="okay_list_boding okay_list_price">
                                            <div class="input-group">
                                                <input class="form-control {if $product->variants[0]->compare_price > 0}text_warning{/if}" type="text" name="price[{$product->variants[0]->id}]" value="{$product->variants[0]->price}">
                                                <span class="input-group-addon">
                                                      {if isset($currencies[$product->variants[0]->currency_id])}
                                                          {$currencies[$product->variants[0]->currency_id]->code|escape}
                                                      {/if}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="okay_list_boding okay_list_count">
                                            <div class="input-group">
                                                <input class="form-control " type="text" name="stock[{$product->variants[0]->id}]" value="{if $product->variants[0]->infinity}∞{else}{$product->variants[0]->stock}{/if}"/>
                                                <span class="input-group-addon  p-0">
                                                     {if $product->variants[0]->units}{$product->variants[0]->units|escape}{else}{$settings->units|escape}{/if}
                                                </span>
                                            </div>
                                        </div>
                                        {*visible*}
                                        <div class="okay_list_boding okay_list_status">
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $product->visible}fn_active_class{/if}" data-controller="product" data-action="visible" data-id="{$product->id}" name="visible" value="1" type="checkbox"  {if $product->visible}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                        <div class=" okay_list_setting okay_list_products_setting">
                                            {*Меню кнопок для планшета*}
                                            <div class="">

                                                {*featured*}
                                                <button data-hint="{$btr->general_bestseller|escape}" type="button" class="setting_icon setting_icon_featured fn_ajax_action {if $product->featured}fn_active_class{/if} hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-controller="product" data-action="featured" data-id="{$product->id}" >
                                                    {include file='svg_icon.tpl' svgId='icon_featured'}
                                                </button>

                                                {*open*}
                                                <a href="{url_generator route='product' url=$product->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                    {include file='svg_icon.tpl' svgId='eye'}
                                                </a>

                                                {*copy*}
                                                <button data-hint="{$btr->products_dublicate|escape}" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                    {include file='svg_icon.tpl' svgId='icon_copy'}
                                                </button>

                                                <span class="okay_list_setting okay_list_products_setting">
                                                    {get_design_block block="products_variant_icon" vars=['variant'=>$product->variants[0]]}
                                                </span>

                                                {get_design_block block="products_icon" vars=['product'=>$product]}
                                            </div>
                                        </div>
                                        <div class="okay_list_boding okay_list_close">
                                            {*delete*}
                                            <button data-hint="{$btr->general_delete_product|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                                {include file='svg_icon.tpl' svgId='trash'}
                                            </button>
                                        </div>
                                    </div>

                                    {if $product->variants|count > 1}
                                    {*Если есть варианты товара*}
                                        <div class="okay_list_variants products_variants_block">
                                        {foreach $product->variants as $variant}
                                            {if $variant@iteration > 1}
                                                <div class="okay_list_row">
                                                    <div class="okay_list_boding okay_list_drag"></div>
                                                    <div class="okay_list_boding okay_list_check"></div>
                                                    <div class="okay_list_boding okay_list_photo"></div>
                                                    <div class="okay_list_boding okay_list_variant_name">
                                                        <span class="text_grey">{$variant->name|escape}</span>
                                                        {get_design_block block="products_variant_list_name"}
                                                    </div>
                                                    <div class="okay_list_boding okay_list_price">
                                                        <div class="input-group">
                                                            <input class="form-control" type="text" name="price[{$variant->id}]" value="{$variant->price}">
                                                            <span class="input-group-addon">
                                                                  {if isset($currencies[$variant->currency_id])}
                                                                      {$currencies[$variant->currency_id]->code}
                                                                  {/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding okay_list_count">
                                                        <div class="input-group">
                                                            <input class="form-control" type="text" name="stock[{$variant->id}]" value="{if $variant->infinity}∞{else}{$variant->stock}{/if}"/>
                                                            <span class="input-group-addon p-0">
                                                                 {if $variant->units}{$variant->units|escape}{else}{$settings->units|escape}{/if}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding okay_list_status"></div>
                                                    <div class="okay_list_setting okay_list_products_setting">
                                                        {get_design_block block="products_variant_icon" vars=['variant'=>$variant]}
                                                    </div>
                                                    <div class="okay_list_boding okay_list_close"></div>
                                                </div>
                                            {else}

                                            {/if}
                                        {/foreach}
                                        </div>
                                    {/if}
                                </div>
                            {/foreach}
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
                                        <option value="enable">{$btr->general_do_enable|escape}</option>
                                        <option value="disable">{$btr->general_do_disable|escape}</option>
                                        <option value="set_featured">{$btr->products_mark_bestseller|escape}</option>
                                        <option value="unset_featured">{$btr->products_unmark_bestseller|escape}</option>
                                        <option value="duplicate">{$btr->general_create_dublicate|escape}</option>
                                        {if $pages_count>1}
                                            <option value="move_to_page">{$btr->products_move_to_page|escape}</option>
                                        {/if}
                                        {if $categories|count>1}
                                            <option value="add_second_category">{$btr->products_add_second_category|escape}</option>
                                            <option value="move_to_category">{$btr->products_move_to_category|escape}</option>
                                        {/if}
                                        {if $all_brands|count>0}
                                            <option value="move_to_brand">{$btr->products_specify_brand|escape}</option>
                                        {/if}
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
                                    <div class="fn_add_second_category col-lg-12 col-md-12 col-sm-12 hidden fn_hide_block">
                                        <select name="target_second_category" class="selectpicker form-control dropup" data-live-search="true" data-size="10">
                                            {function name=second_category_select_btn level=0}
                                                {foreach $categories as $category}
                                                    <option value='{$category->id}'>{section sp $level}&nbsp;&nbsp;&nbsp;&nbsp;{/section}{$category->name|escape}</option>
                                                    {second_category_select_btn categories=$category->subcategories selected_id=$selected_id level=$level+1}
                                                {/foreach}
                                            {/function}
                                            {second_category_select_btn categories=$categories}
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
                    </div>

                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                {include file='pagination.tpl'}
            </div>
        </div>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->products_no|escape}</div>
        </div>
    {/if}
</div>

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_products'}

{literal}
<script>

$(function() {
    $(document).on('click','.fn_variants_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.products_variants_block').slideToggle();
    });


    $(document).on('change','.fn_action_block select.products_action',function(){
        var elem = $(this).find('option:selected').val();
        $('.fn_hide_block').addClass('hidden');
        if($('.fn_'+elem).size()>0){
            $('.fn_'+elem).removeClass('hidden');
        }
    });

    $(document).on('click','.fn_show_icon_menu',function(){
        $(this).toggleClass('show');
    });

    // Дублировать товар
    $(document).on("click", ".fn_copy", function () {
        $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
        $(this).closest(".fn_form_list").find('select[name="action"] option[value=duplicate]').attr('selected', true);
        $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
        $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').click();
        $(this).closest(".fn_form_list").submit();
    });
    // Бесконечность на складе
    $("input[name*=stock]").focus(function() {
        if($(this).val() == '∞')
            $(this).val('');
        return false;
    });
    $("input[name*=stock]").blur(function() {
        if($(this).val() == '')
            $(this).val('∞');
    });
    });
</script>
{/literal}
