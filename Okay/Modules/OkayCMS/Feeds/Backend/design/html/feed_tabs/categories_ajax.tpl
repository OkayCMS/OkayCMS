{*Параметры элемента*}
{if $categories}
    {foreach $categories as $category}
        <div class="{if $level == 1}fn_step-1 fn_sort_item {/if}fn_row okay_list_body_item">
            <div class="okay_list_row fn_category_settings">
                <input type="hidden" name="entity_id" value="{$category->id}">

                {if $category->subcategories}
                    <div class="okay_list_heading okay_list_subicon">
                        <a href="javascript:;" class="fn_ajax_toggle" data-toggle="{if $isAllCategories || (!empty($smarty.get.category_id) && in_array($smarty.get.category_id, $category->children))}1{else}0{/if}" data-category_id="{$category->id}" >
                            <i class="fa fa-plus-square{if $isAllCategories || (!empty($smarty.get.category_id) && in_array($smarty.get.category_id, $category->children))} fa-minus-square{/if}"></i>
                        </a>
                    </div>
                {else}
                    <div class="okay_list_heading okay_list_subicon"></div>
                {/if}

                <div class="okay_list_boding okay_list_photo hidden-sm-down">
                    {if $category->image}
                        <a href="{url controller=CategoryAdmin id=$category->id return={url controller=CategoriesAdmin category_id=$category->id}}">
                            <img src="{$category->image|resize:55:55:false:$config->resized_categories_dir}" alt="" />
                        </a>
                    {else}
                        <img height="55" width="55" src="design/images/no_image.png"/>
                    {/if}
                </div>

                <div class="okay_list_boding okay_list_feed_categories_settings_name">
                    <a href="{url controller=CategoryAdmin id=$category->id return={url controller=CategoriesAdmin category_id=$category->id}}">
                        {$category->name|escape}
                    </a>
                </div>

                <div class="okay_list_boding okay_list_feed_categories_settings_settings">
                    {get_design_block block="okay_cms__feeds__feed__categories_settings__settings_custom_block" vars=['category' => $category]}
                </div>
            </div>
            {if $category->subcategories}
                <div class="fn_ajax_categories categories_sub_block sortable {if $level == 1}subcategories_level_1{else}subcategories_level_2{/if}">
                    {if $isAllCategories || (!empty($smarty.get.category_id) && in_array($smarty.get.category_id, $category->children))}
                        {include file="./categories_ajax.tpl" categories=$category->subcategories level=$level+1}
                    {/if}
                </div>
            {/if}
        </div>
    {/foreach}
{/if}
