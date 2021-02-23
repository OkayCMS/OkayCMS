{if $menu_items}
    {function name=menu_items_tree}
    {if $menu_items}
    <ul class="fn_menu_list menu_group__list menu_group__list--{$level} menu_group--{$menu->group_id}">
        {foreach $menu_items as $item}
        {if $item->visible == 1}
        <li class="menu_group__item menu_group__item--{$level} {if $item->submenus && $item->count_children_visible>0}menu_eventer{/if}">
            <a class="menu_group__link" {if $item->url} href="{if preg_match('~^https?://~', {$item->url})}{$item->url}{else}{url_generator route='page' url=$item->url}{/if}"{/if} {if !$item->submenus && $item->is_target_blank}target="_blank"{/if}>
                <span>{$item->name|escape}</span>
            </a>
            {menu_items_tree menu_items=$item->submenus level=$level + 1}
        </li>
        {/if}
        {/foreach}
    </ul>
    {/if}
    {/function}
    {menu_items_tree menu_items=$menu_items level=1}
{/if}
