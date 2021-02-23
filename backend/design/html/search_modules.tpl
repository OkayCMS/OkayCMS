{if $search_modules->modules}
    {foreach $search_modules->modules as $module}
        <div class="fn_row okay_list_body_item fn_sort_item">
            <div class="okay_list_row">
    
                <div class="okay_list_boding okay_list_photo p-1">
                    {if $module->image}
                        <a href="javascript:;">
                            <img src="{$module->image}"/>
                        </a>
                    {else}
                        {include file='svg_icon.tpl' svgId='modules_icon'}
                    {/if}
                </div>
    
                <div class="okay_list_boding okay_list_marketplace_name">
                    <div class="text_600 mb-q mr-1">
                        <a href="javascript:;">{$module->name|escape}</a>
                    </div>

                    <div class="mb-q text_dark hidden-lg-up">
                        <span class="text_grey text_bold">{$btr->s_module_version|escape}:</span>
                        {$btr->not_used_module_type}
                    </div>
                    {if $module->last_version}
                        <div class="text_dark hidden-lg-up">
                            <span class="text_grey text_bold">{$btr->m_module_version|escape}:</span>
                            {$module->last_version->okay_version}
                        </div>
                    {/if}
                </div>

                <div class="okay_list_boding okay_list_market_version_m hidden-md-down">
                    {if $module->last_version}
                        {$module->last_version->module_version}
                    {else}
                        {$btr->not_used_module_type}
                    {/if}
                </div>
    
                <div class="okay_list_boding okay_list_market_version_s hidden-md-down">
                    {if $module->last_version}{$module->last_version->okay_version}{/if}
                </div>
    
                <div class="okay_list_boding okay_list_marketplace_demo hidden-sm-down">
                    {if $module->demoUrl}
                        <a class="btn btn-outline-warning" href="{$module->demoUrl}" target="_blank">{$btr->marketplace_list_demo|escape}</a>
                    {/if}
                </div>
    
                <div class="okay_list_boding okay_list_marketplace_buy hidden-xs-down">
                    {if isset($installed_modules[$module->vendor_name][$module->module_name])}
                        <div class="text_green">
                            {include file='svg_icon.tpl' svgId='check'}
                            {$btr->marketplace_module_installed|escape}
                        </div>
                    {else}
                        {if $module->checkout_url}
                            <form method="get" action="{$module->checkout_url|escape}" target="_blank">
                                <input type="hidden" name="checkout_email" value="{$manager->email|escape}">
                                <button class="btn btn-info mb-q" type="submit">Купить</button>
                            </form>
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
{else}
<div class="fn_row okay_list_body_item fn_sort_item">
    <div class="okay_list_row">
        <div class="p-2 text_600 font_16 text_dark">{$btr->m_modules_not_found|escape}</div>
    </div>
</div>
{/if}
