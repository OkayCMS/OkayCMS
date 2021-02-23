{if $pages_count>1}
<!-- Листалка страниц -->
<ul class="pagination fn_pagination">
    
    {* Количество выводимых ссылок на страницы *}
    {$visible_pages = 5}

    {* По умолчанию начинаем вывод со страницы 1 *}
    {$page_from = 1}
    
    {* Если выбранная пользователем страница дальше середины "окна" - начинаем вывод уже не с первой *}
    {if $current_page > floor($visible_pages/2)}
        {$page_from = max(1, $current_page-floor($visible_pages/2)-1)}
    {/if}    
    
    {* Если выбранная пользователем страница близка к концу навигации - начинаем с "конца-окно" *}
    {if $current_page > $pages_count-ceil($visible_pages/2)}
        {$page_from = max(1, $pages_count-$visible_pages-1)}
    {/if}
    
    {* До какой страницы выводить - выводим всё окно, но не более ощего количества страниц *}
    {$page_to = min($page_from+$visible_pages, $pages_count-1)}

    {if $current_page>1}
    <li class="page-item">
        <a id="PrevLink" href="{if $current_page == 2}{url page=null}{else}{url page=$current_page-1}{/if}">&lt;</a>
    </li>
    {/if}

    {* Ссылка на 1 страницу отображается всегда *}
    <li class="page-item {if $current_page==1}active{/if}">
        <a class="page-link {if $current_page==1}selected{else}droppable{/if}" href="{url page=null}">1</a>
    </li>
    {* Выводим страницы нашего "окна" *}    
    {section name=pages loop=$page_to start=$page_from}
        {* Номер текущей выводимой страницы *}    
        {$p = $smarty.section.pages.index+1}    
        {* Для крайних страниц "окна" выводим троеточие, если окно не возле границы навигации *}
    <li class="page-item {if $p==$current_page}active{/if}">
        {if ($p == $page_from+1 && $p!=2) || ($p == $page_to && $p != $pages_count-1)}    
        <a class="page-link" href="{url page=$p}">...</a>
        {else}
        <a class="{if $p!=$current_page}droppable{/if}" href="{url page=$p}">{$p}</a>
        {/if}
    </li>
    {/section}

    {* Ссылка на последнююю страницу отображается всегда *}
    <li class="page-item {if $current_page == $pages_count}active{/if}">
        <a class="{if $current_page!=$pages_count}droppable{/if}"  href="{url page=$pages_count}">{$pages_count}</a>
    </li>

    {if $current_page<$pages_count}
    <li class="page-item">
        <a id="NextLink" href="{url page=$current_page+1}">&gt;</a>
    </li>
    {/if}
    <li class="page-item">
        <a href="{url page=all}">{$btr->pagination_show_all|escape}</a>
    </li>
    
</ul>
<!-- Листалка страниц (The End) -->
{/if}
