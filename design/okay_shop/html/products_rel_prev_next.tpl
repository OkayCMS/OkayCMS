{if $total_pages_num > 1}
    {if $current_page_num == $total_pages_num}
        {if $current_page_num == 2}
            <link rel="prev" href="{furl page=null}"/>
        {else}
            <link rel="prev" href="{furl page=$current_page_num-1}"/>
        {/if}
    {elseif $current_page_num == 1}
        <link rel="next" href="{furl page=2}"/>
    {else}
        {if $current_page_num == 2}
            <link rel="prev" href="{furl page=null}"/>
        {else}
            <link rel="prev" href="{furl page=$current_page_num-1}"/>
        {/if}
        <link rel="next" href="{furl page=$current_page_num+1}"/>
    {/if}
{/if}