{* Pagination *}
{if $total_pages_num > 1}
    <ul class="fn_ajax_buttons pagination{if $ajax} fn_is_ajax{/if}">
        {* Number of visible pagination links *}
        {$visible_pages = 5}

        {* The start and end pagination links *}
        {$page_from = 1}
        {if $current_page_num > floor($visible_pages/2)}
            {$page_from = max(1, $current_page_num-floor($visible_pages/2)-1)}
        {/if}
        {if $current_page_num > $total_pages_num-ceil($visible_pages/2)}
            {$page_from = max(1, $total_pages_num-$visible_pages-1)}
        {/if}
        {$page_to = min($page_from+$visible_pages, $total_pages_num-1)}

        {* Link to the previous page *}
        {if $current_page_num > 1}
            <li class="pagination__item">
                <a class="fn_sort_pagination_link pagination__link" href="{if $current_page_num == 2}{furl page=null}{else}{furl page=$current_page_num - 1}{/if}">
                    <span>&laquo; {$lang->pagination_prev}</span>
                </a>
            </li>
        {/if}

        {* Link to the first page *}
        {if $current_page_num == 1}
            <li class="pagination__item active">
                <span class="pagination__link">1</span>
            </li>
        {else}
            <li class="pagination__item">
                <a class="fn_sort_pagination_link pagination__link" href="{furl page=null}">1</a>
            </li>
        {/if}

        {* Pagination links *}
        {section name=pages loop=$page_to start=$page_from}
            {$p = $smarty.section.pages.index+1}
            {if ($p == $page_from+1 && $p!=2) || ($p == $page_to && $p != $total_pages_num-1)}
                <li class="pagination__item">
                    <a class="fn_sort_pagination_link pagination__link" href="{furl page=$p}">...</a>
                </li>
            {elseif $p == $current_page_num}
                <li class="pagination__item{if $p==$current_page_num} active{/if}">
                    <span class="pagination__link">{$p}</span>
                </li>
            {else}
                <li class="pagination__item">
                    <a class="fn_sort_pagination_link pagination__link" href="{furl page=$p}">{$p}</a>
                </li>
            {/if}
        {/section}

        {* Link to the last page *}
        {if $current_page_num == $total_pages_num}
            <li class="pagination__item active">
                <span class="pagination__link">{$total_pages_num}</span>
            </li>
        {else}
            <li class="pagination__item">
                <a class="fn_sort_pagination_link pagination__link" href="{furl page=$total_pages_num}">{$total_pages_num}</a>
            </li>
        {/if}

        {* Display all pages *}
        <li class="pagination__item">
            <a class="fn_sort_pagination_link pagination__link" href="{furl page=all}" data-language="pagination_all">{$lang->pagination_all}</a>
        </li>

        {* Link to the next page *}
        {if $current_page_num < $total_pages_num}
            <li class="pagination__item">
                <a class="fn_sort_pagination_link pagination__link" href="{furl page=$current_page_num+1}">
                    <span>{$lang->pagination_next} &raquo;</span>
                </a>
            </li>
        {/if}
    </ul>
{/if}