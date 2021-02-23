{*Параметры элемента*}
{if $categories}
    {foreach $categories as $category}
        <div class="{if $level == 1}fn_step-1 fn_sort_item {/if}fn_row okay_list_body_item">
            <div class="okay_list_row">
                <input type="hidden" name="positions[{$category->id}]" value="{$category->position}" />

                {if $category->subcategories}
                    <div class="okay_list_heading okay_list_subicon">
                        <a href="javascript:;" class="fn_ajax_toggle" data-toggle="{if $isAllCategories || (!empty($smarty.get.category_id) && in_array($smarty.get.category_id, $category->children))}1{else}0{/if}" data-category_id="{$category->id}" >
                            <i class="fa fa-plus-square{if $isAllCategories || (!empty($smarty.get.category_id) && in_array($smarty.get.category_id, $category->children))} fa-minus-square{/if}"></i>
                        </a>
                    </div>
                {else}
                    <div class="okay_list_heading okay_list_subicon"></div>
                {/if}

                <div class="okay_list_boding okay_list_drag move_zone">
                    {include file='svg_icon.tpl' svgId='drag_vertical'}
                </div>

                <div class="okay_list_boding okay_list_check">
                    <input class="hidden_check" type="checkbox" id="id_{$category->id}" name="check[]" value="{$category->id}" />
                    <label class="okay_ckeckbox" for="id_{$category->id}"></label>
                </div>

                <div class="okay_list_boding okay_list_photo hidden-sm-down">
                    {if $category->image}
                        <a href="{url controller=BlogCategoryAdmin id=$category->id return={url controller=BlogCategoriesAdmin category_id=$category->id}}">
                            <img src="{$category->image|resize:55:55:false:$config->resized_blog_categories_dir}" alt="" />
                        </a>
                    {else}
                        <img height="55" width="55" src="design/images/no_image.png"/>
                    {/if}
                </div>

                <div class="okay_list_boding okay_list_categories_name">
                    <a href="{url controller=BlogCategoryAdmin id=$category->id return={url controller=BlogCategoriesAdmin category_id=$category->id}}">
                        {$category->name|escape}
                    </a>
                    {get_design_block block="blog_categories_list_name" vars=['category' => $category]}
                </div>


                <div class="okay_list_boding okay_list_status">
                    {*visible*}
                    <div>
                        <label class="switch switch-default">
                            <input class="switch-input fn_ajax_action {if $category->visible}fn_active_class{/if}" data-controller="blog_category" data-action="visible" data-id="{$category->id}" name="visible" value="1" type="checkbox"  {if $category->visible}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    </div>
                </div>

                <div class="okay_list_setting">
                    {*open*}
                    <a href="{url_generator route="blog_category" url=$category->url absolute=1}" target="_blank" data-hint="{$btr->general_view|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                        {include file='svg_icon.tpl' svgId='icon_desktop'}
                    </a>
                    {*copy*}
                    <button data-hint="{$btr->categories_dublicate|escape}" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                        {include file='svg_icon.tpl' svgId='icon_copy'}
                    </button>

                    {get_design_block block="blog_categories_actions" vars=['category' => $category]}
                </div>
                <div class="okay_list_boding okay_list_close">
                    {*delete*}
                    <button data-hint="{$btr->categories_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                        {include file='svg_icon.tpl' svgId='trash'}
                    </button>
                </div>
            </div>
            {if $category->subcategories}
                <div class="fn_ajax_categories categories_sub_block sortable {if $level == 1}subcategories_level_1{else}subcategories_level_2{/if}">
                    {if $isAllCategories || (!empty($smarty.get.category_id) && in_array($smarty.get.category_id, $category->children))}
                        {include file="blog_categories_ajax.tpl" categories=$category->subcategories level=$level+1}
                    {/if}
                </div>
            {/if}
        </div>
    {/foreach}
{/if}
