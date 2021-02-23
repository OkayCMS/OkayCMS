{if $post->id}
    {$meta_title = $post->name scope=global}
{else}
    {$meta_title = $btr->post_new scope=global}
{/if}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {if !$post->id}
                    {$btr->post_add|escape}
                {else}
                    {$post->name|escape}
                {/if}
            </div>
            {if $post->id}
                <div class="box_btn_heading">
                    <a class="btn btn_small btn-info add" target="_blank" href="{url_generator route='post' url=$post->url absolute=1}">
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
                    {if $message_success == 'added'}
                        {$btr->post_added|escape}
                    {elseif $message_success == 'updated'}
                        {$btr->post_updated|escape}
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
                    {if $message_error == 'url_exists'}
                        {$btr->post_exists|escape}
                    {elseif $message_error=='global_url_exists'}
                        {$btr->global_url_exists|escape}
                    {elseif $message_error=='empty_name'}
                        {$btr->general_enter_title|escape}
                    {elseif $message_error == 'empty_url'}
                        {$btr->general_enter_url|escape}
                    {elseif $message_error == 'url_wrong'}
                        {$btr->general_not_underscore|escape}
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
    <input type="hidden" name="session_id" value="{$smarty.session.id}" />
    <input type="hidden" name="lang_id" value="{$lang_id}" />

    <div class="row">
        <div class="col-xs-12 ">
            <div class="boxed match_matchHeight_true">
                {*Название элемента сайта*}
                <div class="row d_flex">
                    <div class="col-lg-10 col-md-9 col-sm-12">
                        <div class="fn_step-1">
                            <div class="heading_label">
                                {$btr->general_name|escape}
                            </div>
                            <div class="form-group">
                                <input class="form-control" name="name" type="text" value="{$post->name|escape}"/>
                                <input name="id" type="hidden" value="{$post->id|escape}"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-lg-6 col-md-10">
                                <div class="fn_step-2">
                                    <div class="input-group input-group--dabbl">
                                        <span class="input-group-addon input-group-addon--left">URL</span>
                                        <input name="url" class="fn_meta_field form-control fn_url {if $post->id}fn_disabled{/if}" {if $post->id}readonly=""{/if} type="text" value="{$post->url|escape}" />
                                        <input type="checkbox" id="block_translit" class="hidden" value="1" {if $post->id}checked=""{/if}>
                                        <span class="input-group-addon fn_disable_url">
                                            {if $post->id}
                                                <i class="fa fa-lock"></i>
                                            {else}
                                                <i class="fa fa-lock fa-unlock"></i>
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {get_design_block block="post_general"}
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-12">
                        <div class="activity_of_switch">
                            <div class="fn_step-3 activity_of_switch_item"> {* row block *}
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">{$btr->general_enable|escape}</label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="visible" value='1' type="checkbox" id="visible_checkbox" {if $post->visible}checked=""{/if}/>
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
                                <label class="switch_label">{$btr->general_show_table_content|escape}</label>
                                <label class="switch switch-default">
                                    <input class="switch-input" name="show_table_content" value='1' type="checkbox" {if $post->show_table_content}checked=""{/if}/>
                                    <span class="switch-label"></span>
                                    <span class="switch-handle"></span>
                                </label>
                            </div>
                        </div>
                        {get_design_block block="post_switch_checkboxes"}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-4 col-md-12 pr-0">
            <div class="fn_step-4 boxed fn_toggle_wrap min_height_250px">
                <div class="heading_box">
                    {$btr->general_image|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <ul class="brand_images_list">
                        <li class="brand_image_item fn_image_block">
                            {if $post->image}
                                <input type="hidden" class="fn_accept_delete" name="delete_image" value="">
                                <div class="fn_parent_image">
                                    <div class="category_image image_wrapper fn_image_wrapper text-xs-center">
                                        <a href="javascript:;" class="fn_delete_item remove_image"></a>
                                        <img src="{$post->image|resize:300:120:false:$config->resized_blog_dir}" alt="" />
                                    </div>
                                </div>
                            {else}
                                <div class="fn_parent_image"></div>
                            {/if}
                            <div class="fn_upload_image dropzone_block_image {if $post->image} hidden{/if}">
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
                {get_design_block block="post_image"}
            </div>
        </div>
        {*Параметры элемента*}
        <div class="col-lg-8 col-md-12">
            <div class="fn_step-5 boxed fn_toggle_wrap min_height_250px">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="{if !$authors}hidden{/if}">
                                    <div class="heading_label">
                                        {$btr->general_author|escape}
                                    </div>
                                    <div class="">
                                        <select name="author_id" class="selectpicker form-control mb-1 fn_meta_author" data-live-search="true">
                                            <option value="0" {if !$post->author_id}selected=""{/if} data-author_name="">{$btr->general_not_set|escape}</option>
                                            {foreach $authors as $author}
                                                <option value="{$author->id}" {if $post->author_id == $author->id}selected=""{/if} data-author_name="{$author->name|escape}">{$author->name|escape}</option>
                                            {/foreach}
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="">
                                    <div class="heading_label heading_label--required">
                                        <span>{$btr->general_category|escape}</span>
                                    </div>
                                    <div id="product_cats" class="clearfix">
                                        {assign var ='first_category' value=reset($post_categories)}
                                        <select class="selectpicker form-control  mb-1 fn_post_category fn_meta_categories" data-live-search="true">
                                            <option value="0" selected="" disabled="" data-category_name="">{$btr->product_select_category}</option>
                                            {function name=category_select level=0}
                                                {foreach $categories as $category}
                                                    <option value="{$category->id}" {if $category->id == $first_category->id}selected{/if} data-category_name="{$category->name|escape}">{section sp $level}- {/section}{$category->name|escape}</option>
                                                    {category_select categories=$category->subcategories level=$level+1}
                                                {/foreach}
                                            {/function}
                                            {category_select categories=$categories}
                                        </select>
                                        <div id="sortable_cat" class="fn_post_categories_list clearfix">
                                            {foreach $post_categories as $post_category}
                                                <div class="fn_category_item product_category_item {if $post_category@first}first_category{/if}">
                                                    <span class="product_cat_name">{$post_category->name|escape}</span>
                                                    <label class="fn_delete_post_cat fa fa-times" for="id_{$post_category->id}"></label>
                                                    <input id="id_{$post_category->id}" type="checkbox" value="{$post_category->id}" data-cat_name="{$post_category->name|escape}" checked="" name="categories[]">
                                                </div>
                                            {/foreach}
                                        </div>
                                        <div class="fn_category_item fn_new_category_item product_category_item">
                                            <span class="product_cat_name"></span>
                                            <label class="fn_delete_post_cat fa fa-times" for=""></label>
                                            <input id="" type="checkbox" value="" name="categories[]" data-cat_name="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="">
                                    <div class="heading_label" >{$btr->general_date|escape}</div>
                                    <div class="mb-1">
                                        <input name="date" class="form-control" type="text" value="{$post->date|date}" />
                                    </div>
                                </div>
                                <div class="">
                                    <div class="heading_label" >{$btr->post_update_date|escape}</div>
                                    <div class="mb-1">
                                        <input name="updated_date" class="form-control" type="text" value="{if !empty($post->updated_date)}{$post->updated_date|date}{/if}" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="">
                                    <div class="heading_label">{$btr->post_read_time|escape}</div>
                                    <div class="">
                                        <input name="read_time" class="form-control" type="text" value="{$post->read_time|intval}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-md-12 pr-0">
            <div class="fn_step-6 boxed fn_toggle_wrap min_height_230px">
                {backend_compact_product_list
                title=$btr->general_recommended
                name='related_products'
                products=$related_products
                label=$btr->general_recommended_add
                placeholder=$btr->general_add_product
                }
                {get_design_block block="post_related_products"}
            </div>
        </div>
        <div class="col-lg-4">
            <div class="boxed min_height_230px">
                <div class="heading_box">
                    {$btr->post_rating|escape}
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="heading_label">
                        {$btr->product_rating_value|escape}
                        <span class="font-weight-bold fn_show_rating">{$post->rating}</span>
                    </div>
                    <div class="raiting_boxed">
                        <input class="fn_rating_value" type="hidden" value="{$post->rating}" name="rating" />
                        <input class="fn_rating range_input" type="range" min="1" max="5" step="0.1" value="{$post->rating}" />

                        <div class="raiting_range_number">
                            <span class="float-xs-left">1</span>
                            <span class="float-xs-right">5</span>
                        </div>
                    </div>
                    <div class="heading_label">
                        {$btr->product_rating_number|escape}
                        <input type="text" class="form-control" name="votes" value="{$post->votes}">
                    </div>
                </div>
                {get_design_block block="post_rationg"}
            </div>
        </div>
    </div>
    {*Параметры элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-7 boxed match fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->general_metatags|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card row">
                    <div class="col-lg-6 col-md-6">
                        <div class="heading_label" >Meta-title <span id="fn_meta_title_counter"></span></div>
                        <input name="meta_title" class="form-control fn_meta_field mb-h" type="text" value="{$post->meta_title|escape}" />
                        <div class="heading_label" >Meta-keywords</div>
                        <input name="meta_keywords" class="form-control fn_meta_field mb-h" type="text" value="{$post->meta_keywords|escape}" />
                    </div>

                    <div class="col-lg-6 col-md-6 pl-0">
                        <div class="heading_label" >Meta-description <span id="fn_meta_description_counter"></span></div>
                        <textarea name="meta_description" class="form-control okay_textarea fn_meta_field">{$post->meta_description|escape}</textarea>
                    </div>
                </div>
                {get_design_block block="post_meta"}
            </div>
        </div>
    </div>

    {$block = {get_design_block block="post_custom_block"}}
    {if !empty($block)}
        <div class="row custom_block">
            {$block}
        </div>
    {/if}
    
    {*Описание элемента*}
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="fn_step-8 boxed match fn_toggle_wrap tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="heading_box tab_navigation_link">{$btr->general_short_description|escape}</a>
                        <a href="#tab2" class="heading_box tab_navigation_link">{$btr->general_full_description|escape}</a>
                    </div>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <textarea name="annotation" id="annotation" class="editor_small">{$post->annotation|escape}</textarea>
                        </div>
                        <div id="tab2" class="tab">
                            <textarea id="fn_editor" name="description" class="editor_large fn_editor_class">{$post->description|escape}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                   <div class="col-lg-12 col-md-12 mt-1">
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

{* Learning script *}
{include file='learning_hints.tpl' hintId='hint_post'}

{* Подключаем Tiny MCE *}
{include file='tinymce_init.tpl'}
{* On document load *}
{literal}
    <script src="design/js/autocomplete/jquery.autocomplete-min.js"></script>
    <script>
        $(document).on("input", ".fn_rating", function () {
            $(".fn_show_rating").html($(this).val());
            $(".fn_rating_value").val($(this).val());
        });
        
        $(window).on("load", function() {
            $('input[name="date"]').datepicker();
            $('input[name="updated_date"]').datepicker();

            var clone_cat = $(".fn_new_category_item").clone();
            $(".fn_new_category_item").remove();
            clone_cat.removeClass("fn_new_category_item");
            $(document).on("change", ".fn_post_category select", function () {
                var clone = clone_cat.clone();
                clone.find("label").attr("for","id_"+$(this).find("option:selected").val());
                clone.find("span").html($(this).find("option:selected").data("category_name"));
                clone.find("input").attr("id","id_"+$(this).find("option:selected").val());
                clone.find("input").val($(this).find("option:selected").val());
                clone.find("input").attr("checked",true);
                clone.find("input").attr("data-cat_name",$(this).find("option:selected").data("category_name"));
                $(".fn_post_categories_list").append(clone);
                if ($(".fn_category_item").size() == 1) {
                    change_post_category();
                }
            });
            $(document).on("click", ".fn_delete_post_cat", function () {
                var item = $(this).closest(".fn_category_item"),
                    is_first = item.hasClass("first_category");
                item.remove();
                if (is_first && $(".fn_category_item").size() > 0) {
                    change_post_category();
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
                    change_post_category();
                }
            });

            function change_post_category() {
                var wrapper = $(".fn_post_categories_list");
                wrapper.find("div.first_category").removeClass("first_category");
                wrapper.find("div.fn_category_item:first ").addClass("first_category");
                set_meta();
            }
        
        });
    </script>
{/literal}
