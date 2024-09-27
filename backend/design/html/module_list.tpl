{foreach $modules as $module}
    <div class="fn_row okay_list_body_item fn_sort_item
        {if $module->params->getDaysToExpire() >= 0} 
            module_access_expire
        {elseif $module->params->isAccessExpired()} 
            module_access_expired
        {/if}

        {if $now_downloaded} 
            fn_now_downloaded
        {/if}" 
        {if $now_downloaded} style="border-left: 5px solid #209db0;"{/if}>
        
        <div class="okay_list_row">
            {if $module->status !== 'Not Installed'}
                <input type="hidden" name="positions[{$module->id}]" value="{$module->position|escape}">
            {/if}

            <div class="okay_list_boding okay_list_drag move_zone">
                {if $module->status !== 'Not Installed'}{include file='svg_icon.tpl' svgId='drag_vertical'}{/if}
            </div>

            <div class="okay_list_boding okay_list_check">
                {if $module->status !== 'Not Installed'}
                    <input class="hidden_check" type="checkbox" id="id_{$module->id}" name="check[]" value="{$module->id}"/>
                    <label class="okay_ckeckbox" for="id_{$module->id}"></label>
                {/if}
            </div>

            <div class="okay_list_boding okay_list_photo">
                {if $module->preview}
                    {if $module->backend_main_controller}
                        <a href="{url controller=[{$module->vendor|escape},{$module->module_name|escape},{$module->backend_main_controller|escape}] id=null return=$smarty.server.REQUEST_URI}">
                            <img height="55" width="55" src="{$rootUrl}/{$module->preview|escape}"/>
                        </a>
                    {else}
                        <img height="55" width="55" src="{$rootUrl}/{$module->preview|escape}"/>
                    {/if}
                {else}
                    {if $module->backend_main_controller}
                        <a href="{url controller=[{$module->vendor|escape},{$module->module_name|escape},{$module->backend_main_controller|escape}] id=null return=$smarty.server.REQUEST_URI}">
                            {include file='svg_icon.tpl' svgId='modules_icon'}
                        </a>
                    {else}
                        {include file='svg_icon.tpl' svgId='modules_icon'}
                    {/if}
                {/if}
            </div>

            <div class="okay_list_boding okay_list_module_name">
                <div class="text_600 mr-1">
                    <div class="module_official__name">
                        {if $module->backend_main_controller}
                        {if $module->params->isLicensed()}
                            <a href="{url controller=[{$module->vendor|escape},{$module->module_name|escape},{$module->backend_main_controller|escape}] id=null return=$smarty.server.REQUEST_URI}">
                                {$module->vendor|escape}/{$module->module_name|escape}
                            </a>
                        {else}
                            <a href="index.php?controller=ModulesLicenseInfoAdmin">
                                {$module->vendor|escape}/{$module->module_name|escape}
                            </a>
                        {/if}
                        {else}
                            {$module->vendor|escape}/{$module->module_name|escape}
                        {/if}

                        {if $module->params->isOfficial()}
                            <span data-hint="{$btr->module_tooltip_oficial|escape}" class="params_official hint-bottom-middle-t-info-s-small-mobile hint-anim">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                </svg> 
                            </span>
                        {*{else}   
                            <span data-hint="{$btr->module_tooltip_not_oficial|escape}" class="params_not_official hint-bottom-middle-t-info-s-small-mobile hint-anim">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6">
                                    <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                                </svg>                           
                            </span>*}                     
                        {/if}
                    </div>                    
                </div>

                {if $module->params->isLicensed()}{else}
                    {if $module->enabled == 1}
                    <div class="mt-q">
                        <span class="text_warning font_12 text_600">
                            {$btr->module_access_blocked}
                        </span>
                    </div>
                {/if}
                {/if}

                {if $module->versionControl->greaterThan($module->version, $module->params->getVersion())}
                    <div class="mt-q">
                        <span class="text_attention font_12 text_600">
                            {$btr->module_downgrade_warning} {$module->params->getVersion()}
                        </span>
                    </div>
                {/if}

                {if $module->params->getDaysToExpire() >= 0}
                    <div class="mt-q">
                        <span class="text_attention font_12 text_600">
                            {if $module->params->getDaysToExpire() > 0}
                                {$btr->module_access_expire_days|escape}
                                {$module->params->getDaysToExpire()|escape}
                                {$module->params->getDaysToExpire()|plural:$btr->module_access_expire_plural_1:$btr->module_access_expire_plural_5:$btr->module_access_expire_plural_2}
                            {else}
                                {$btr->module_access_expire_today|escape}
                            {/if}
                        </span>
                    </div>
                {elseif $module->params->isAccessExpired()}
                    <div class="mt-q">
                        <span class="text_warning font_12 text_600">
                            {$btr->module_access_expired|escape}
                        </span>
                    </div>
                {/if}

                <div class="mt-q">
                    <div class="fn_switch for_developer_toggle">
                        <span>{$btr->module_learn_more|escape}</span>
                        <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 rotate_180"></i>
                    </div>
    
                    <div style="display: none;">
                        <div class="mt-q font_12 text_grey text_500">
                            <span>{$btr->module_version|escape}:</span>
                            <span class="text_dark">{$module->version|escape}</span>
                        </div>
    
                        {if !empty($module->params->getOkayVersion()) && $module->params->getOkayVersion() != $config->version}
                            <div class="mt-q font_12 text_grey text_500">
                                <span>{$btr->module_okay_version|escape}:</span>
                                <span class="text_dark">{$module->params->getOkayVersion()|escape}</span>
                            </div>
                        {/if}
    
                        <div class="mt-q font_12 text_grey text_500">
                            <span>{$btr->module_type|escape}:</span>
                            <span class="text_dark">{if $module->type}{$module->type|escape}{else}{$btr->not_used_module_type}{/if}</span>
                        </div>
    
                        {if $module->params->getVendorEmail()}
                            <div class="mt-q font_12 text_grey text_500">
                                <span class="">{$btr->module_vendor_email|escape}:</span>
                                <a class="okay_list_module_name_link" href="mailto:{$module->params->getVendorEmail()|escape}">{$module->params->getVendorEmail()|escape}</a>
                            </div>
                        {/if}
    
                        {if $module->params->getVendorSite()}
                            <div class="mt-q font_12 text_grey text_500">
                                <span class="">{$btr->module_vendor_site|escape}:</span>
                                <a class="okay_list_module_name_link" href="{$module->params->getVendorSite()|escape}" target="_blank">{$module->params->getVendorSite()|escape}</a>
                            </div>
                        {/if}  

                        <div class="mt-q font_12 text_grey text_500 hidden-lg-up">
                            {if $module->params->isLicensed()}
                                <span data-hint="{$btr->module_tooltip_licensed}" class="tag tag-licensed hint-bottom-middle-t-info-s-small-mobile hint-anim">{$btr->module_status_licensed}</span>
                            {else}
                                {if $module->enabled == 1}
                                <span data-hint="{$btr->module_tooltip_not_licensed}" class="tag tag-not_licensed hint-bottom-middle-t-info-s-small-mobile hint-anim">{$btr->module_status_not_licensed}</span>
                            {/if}
                            {/if}
                        </div>
                    </div>
                </div>
            </div>

            <div class="okay_list_boding okay_list_module_license_status hidden-md-down">
                {if $module->params->isLicensed()}
                    <span data-hint="{$btr->module_tooltip_licensed}" class="tag tag-licensed hint-bottom-middle-t-info-s-small-mobile hint-anim">{$btr->module_status_licensed}</span>
                {else}
                    {if $module->enabled == 1}
                    <span data-hint="{$btr->module_tooltip_not_licensed}" class="tag tag-not_licensed hint-bottom-middle-t-info-s-small-mobile hint-anim">{$btr->module_status_not_licensed}</span>
                {/if}
                {/if}
            </div>

            <div class="okay_list_boding okay_list_module_version hidden-md-down">
                <div class="text_700">{$module->version|escape}</div>

                {if $module->versionControl->lessThan($module->version, $module->params->getVersion())}
                    {if $module->params->isLicensed()}
                        <button type="button" class="fn_update_module btn btn-warning btn--update mt-q hint-top-middle-t-info-s-small-mobile hint-anim" data-hint="{$btr->module_need_update} {$module->params->getVersion()}">{include 'svg_icon.tpl' svgId='refresh_icon'} {$module->params->getVersion()}</button>
                    {/if}
                {/if}

                {if !empty($module->params->getOkayVersion()) && $module->params->getOkayVersion() != $config->version}
                    <div class="font_12 text_grey mt-h">
                        {$btr->module_okay_version|escape}: {$module->params->getOkayVersion()|escape}
                    </div>
                {/if}
            </div>
            
            <div class="okay_list_boding okay_list_status">
                {if $module->params->isLicensed()}
                    {if $module->status === 'Not Installed'}
                        <button class="btn btn_mini btn-info" name="install_module" value="{$module->vendor|escape}/{$module->module_name|escape}">{$btr->install_module}</button>
                    {else}
                        <label class="switch switch-default">
                            <input class="switch-input fn_ajax_action {if $module->enabled}fn_active_class{/if}" data-controller="module" data-action="enabled" data-id="{$module->id}" name="enabled" value="1" type="checkbox"  {if $module->enabled}checked=""{/if}/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    {/if}
                {else}
                    {if $module->status === 'Not Installed'}
                        <button class="btn btn_mini btn-info" name="install_module" value="{$module->vendor|escape}/{$module->module_name|escape}">{$btr->install_module}</button>
                    {else}
                    <span class="btn btn_mini" disabled>{$btr->module_unavailable}</span>
                {/if}
                {/if}
            </div>

            <div class="okay_list_setting okay_list_products_setting">
                {if $module->backend_main_controller}
                    {if $module->params->isLicensed()}
                        <a data-hint="{$btr->module_action_setting|escape}" class="setting_icon setting_icon_setting hint-bottom-middle-t-info-s-small-mobile hint-anim" href="{url controller=[{$module->vendor|escape},{$module->module_name|escape},{$module->backend_main_controller|escape}] id=null return=$smarty.server.REQUEST_URI}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                    {/if}

                {/if}
                {if $module->params->isLicensed()}
                {if $module->status !== 'Not Installed'}
                    <a data-hint="{$btr->files_list_module|escape}" class="setting_icon setting_icon_files hint-bottom-middle-t-info-s-small-mobile hint-anim" href="{url controller='ModuleDesignAdmin' vendor=$module->vendor module_name=$module->module_name}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                        </svg>                          
                    </a>
                {/if}
                {/if}

                {if $module->params->getAddToCartUrl()}
                    <button data-hint="{$btr->module_access_continue_access|escape}" class="fn_continue_access setting_icon setting_icon_extension hint-bottom-middle-t-info-s-small-mobile hint-anim" type="button" data-target="{$module->params->getAddToCartUrl()|escape}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </button>
                {/if}
            </div>

            <div class="okay_list_boding okay_list_close">
                {if $module->status !== 'Not Installed'}
                    <button data-hint="{$btr->general_delete|escape}" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                        {include file='svg_icon.tpl' svgId='trash'}
                    </button>
                {/if}
            </div>
        </div>
    </div>
{/foreach}

{if $now_downloaded}
    <script>
        $(document).ready(function () {
            setTimeout(function () {
                $('.fn_now_downloaded').css('background-color', '').removeClass('fn_now_downloaded');
            }, 2000)
        });
    </script>
{/if}
