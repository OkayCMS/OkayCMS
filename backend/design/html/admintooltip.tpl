<link href="backend/design/js/admintooltip/styles/admin.css" type="text/css" rel="stylesheet">

<div class="admTools">
    <a href="javascript:void(0);" class="openTools"></a>
    <p>{$btr->admintooltip_title_1}</p>
    <p class="tool-descr">{$btr->admintooltip_descr}</p>
    <a title="{$btr->admintooltip_go_to_admin}" href="backend/" class="admin_bookmark"></a>
    <p class="tool-title">{$btr->admintooltip_fast_edit}</p>
    <a title="{$btr->admintooltip_enable}" href="javascript:void(0);" class="changeTools"><span></span></a>
</div>

<div class="fn_tooltip tooltip"></div>

<a title="{$btr->admintooltip_go_to_admin}" href="backend/" class="top_admin_bookmark"></a>

<script src="backend/design/js/admintooltip/admintooltip.js"{if $scripts_defer == true} defer{/if}></script>
