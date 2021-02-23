{function name=categories_tree3}
    {if $categories}
        <div class="level_{$level} {if $level == 1}categories_nav__menu{else}categories_nav__subcategory{/if}">
            <ul class="fn_category_scroll {if $level == 1}categories_menu {else}subcategory {/if}">
                {foreach $categories as $c}
                    {if $c->visible && ($c->has_products || $settings->show_empty_categories)}
                        {if $c->subcategories && $c->count_children_visible && $level < 3}
                            <li class="categories_menu__item has_child">
                                <a class="d-flex align-items-center categories_menu__link{if $category->id == $c->id} selected{/if}" href="{url_generator route="category" url=$c->url}" data-category="{$c->id}">
                                    {if $c->image}
                                        {if $level == 1 }
                                            {if strtolower(pathinfo($c->image, $smarty.const.PATHINFO_EXTENSION)) == 'svg'} 
                                                <span class="categories_menu__icon">
                                                    {$c->image|read_svg:$config->original_categories_dir}
                                                </span>
                                            {else}
                                                <span class="categories_menu__icon lazy" data-bg="url({$c->image|resize:22:22:false:$config->resized_categories_dir})" ></span>
                                            {/if}
                                        {/if}
                                    {/if}
                                    <span class="categories_menu__name">{$c->name|escape}</span>
                                    {include file='svg.tpl' svgId='arrow_right'}
                                </a>
                                {categories_tree3 categories=$c->subcategories level=$level + 1}
                            </li>
                        {else}
                            <li class="categories_menu__item">
                                <a class="categories_menu__link d-flex align-items-center d-flex align-items-center{if $category->id == $c->id} selected{/if}" href="{url_generator route='category' url=$c->url}" data-category="{$c->id}">
                                    {if $level == 3}
                                        <div class="d-flex align-items-center justify-content-center categories_menu__image">
                                            {if $c->image}
                                                {if strtolower(pathinfo($c->image, $smarty.const.PATHINFO_EXTENSION)) == 'svg'}
                                                    {$c->image|read_svg:$config->original_categories_dir}
                                                {else}
                                                <picture>
                                                    {if $settings->support_webp}
                                                        <source type="image/webp" data-srcset="{$c->image|resize:65:65:false:$config->resized_categories_dir}.webp">
                                                    {/if}
                                                    <source data-srcset="{$c->image|resize:65:65:false:$config->resized_categories_dir}">
                                                    <img class="lazy" data-src="{$c->image|resize:65:65:false:$config->resized_categories_dir}" src="{$rootUrl}/design/{get_theme}/images/xloading.gif" alt="{$c->name|escape}" title="{$c->name|escape}"/>
                                                </picture>
                                                {/if}
                                            {else}
                                                <div class="categories__no_image d-flex align-items-center justify-content-center" title="{$c->name|escape}">
                                                    {include file="svg.tpl" svgId="no_image"}
                                                </div>
                                            {/if}
                                        </div>
                                    {elseif $level == 1}
                                        {if $c->image}
                                            {if strtolower(pathinfo($c->image, $smarty.const.PATHINFO_EXTENSION)) == 'svg'} 
                                                <span class="categories_menu__icon">
                                                    {$c->image|read_svg:$config->original_categories_dir}
                                                </span>
                                            {else}
                                                <span class="categories_menu__icon lazy" data-bg="url({$c->image|resize:22:22:false:$config->resized_categories_dir})" ></span>
                                            {/if}
                                        {/if}
                                    {/if}
                                    <span class="d-flex align-items-center categories_menu__name">{$c->name|escape}</span>
                                </a>
                            </li>
                        {/if}
                    {/if}
                {/foreach}
            </ul>
        </div>
    {/if}
{/function}
{categories_tree3 categories=$categories level=1}
