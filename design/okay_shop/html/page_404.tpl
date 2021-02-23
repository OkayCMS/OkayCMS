{* The template of page 404 *}

{* The page heading *}
{*<h1 class="h1"><span data-page="{$page->id}">{if $page->name_h1|escape}{$page->name_h1|escape}{else}{$page->name|escape}{/if}</span></h1>*}

{* The page content *}
<div class="block">
    <div class="container">
        <div class="not_found">
            <div class="not_found__image">
                {include file="svg.tpl" svgId="404_icon"}
            </div>
            <div class="not_found__description">
                {$description}
            </div>
            <div class="not_found__menu">
                {$menu_404}
            </div>
        </div>
    </div>
</div>