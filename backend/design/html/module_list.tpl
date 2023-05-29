{foreach $modules as $module}
    <div class="fn_row okay_list_body_item fn_sort_item{if $module->params->getDaysToExpire() >= 0} module_access_expire{elseif $module->params->isAccessExpired()} module_access_expired{/if}{if $now_downloaded} fn_now_downloaded{/if}"{if $now_downloaded} style="background-color: #ebffea; transition: background-color 1s linear;"{/if}>
        <div class="okay_list_row">
            <input type="hidden" name="positions[{$module->id}]" value="{$module->position|escape}">

            <div class="okay_list_boding okay_list_drag move_zone">
                {if $module->status !== 'Not Installed'}{include file='svg_icon.tpl' svgId='drag_vertical'}{/if}
            </div>

            <div class="okay_list_boding okay_list_check">
                <input class="hidden_check" type="checkbox" id="id_{$module->id}" name="check[]" value="{$module->id}"/>
                <label class="okay_ckeckbox" for="id_{$module->id}"></label>
            </div>

            <div class="okay_list_boding okay_list_photo">
                {if $module->preview}
                    {if $module->backend_main_controller}
                        <a href="{url controller=[{$module->vendor|escape},{$module->module_name|escape},{$module->backend_main_controller|escape}] id=null return=$smarty.server.REQUEST_URI}">
                            <img src="{$rootUrl}/{$module->preview|escape}"/>
                        </a>
                    {else}
                        <img src="{$rootUrl}/{$module->preview|escape}"/>
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
                <div class="text_600 mb-h mr-1">
                    {if $module->backend_main_controller}
                        <a href="{url controller=[{$module->vendor|escape},{$module->module_name|escape},{$module->backend_main_controller|escape}] id=null return=$smarty.server.REQUEST_URI}">
                            {$module->vendor|escape}/{$module->module_name|escape}
                        </a>
                    {else}
                        {$module->vendor|escape}/{$module->module_name|escape}
                    {/if}
                </div>

                {if $module->params->getVendorEmail()}
                    <div class="mb-h">
                        <span class="text_grey text_bold">{$btr->module_vendor_email|escape}:</span>
                        {$module->params->getVendorEmail()|escape}
                    </div>
                {/if}

                {if $module->params->getVendorSite()}
                    <div class="mb-h">
                        <span class="text_grey text_bold">{$btr->module_vendor_site|escape}:</span>
                        <a class="okay_list_module_name_link" href="{$module->params->getVendorSite()|escape}" target="_blank">{$module->params->getVendorSite()|escape}</a>
                    </div>
                {/if}

                {if $module->params->getDaysToExpire() >= 0}
                    <div class="mb-h">
                        <span class="text_warning text_bold">
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
                    <div class="mb-h">
                        <span class="text_warning text_bold">
                            {$btr->module_access_expired|escape}
                        </span>
                    </div>
                {/if}

                <div class="mb-h hidden-lg-up">
                    <span class="text_grey text_bold">{$btr->module_version|escape}:</span>
                    {$module->version|escape}
                </div>
            </div>

            <div class="okay_list_boding okay_list_module_expire hidden-md-down">
                {if $module->params->getAddToCartUrl()}
                    <button class="fn_continue_access btn btn-warning btn--update mt-h" type="button" data-target="{$module->params->getAddToCartUrl()|escape}">{$btr->module_access_continue_access|escape}</button>
                {/if}
            </div>
            <div class="okay_list_boding okay_list_module_version hidden-md-down">
                <div class="">{$module->version|escape}</div>

                {if !empty($module->params->getVersion()) && $module->params->getVersion() != $module->version}
                    {if $module->params->getVersion() > $module->version}
                        <button type="button" class="fn_update_module btn btn-outline-warning btn--update mt-h hint-top-middle-t-info-s-small-mobile hint-anim" data-hint="{$btr->module_need_update} {$module->params->version}">{include 'svg_icon.tpl' svgId='refresh_icon'} {$module->params->version}</button>
                    {else}
                        {$btr->module_downgrade_warning} {$module->params->getVersion()}
                    {/if}
                {/if}

                {if !empty($module->params->getOkayVersion()) && $module->params->getOkayVersion() != $config->version}
                    <div class="font_12 text_grey mt-q">
                        {$btr->module_okay_version|escape}
                        {$module->params->getOkayVersion()|escape}
                    </div>
                {/if}
            </div>
            <div class="okay_list_boding okay_list_module_type hidden-md-down">{if $module->type}{$module->type|escape}{else}{$btr->not_used_module_type}{/if}</div>

            <div class="okay_list_boding okay_list_status">
                {*visible*}
                {if $module->status === 'Not Installed'}
                    <button class="btn" name="install_module" value="{$module->vendor|escape}/{$module->module_name|escape}">{$btr->install_module}</button>
                {else}
                    <label class="switch switch-default">
                        <input class="switch-input fn_ajax_action {if $module->enabled}fn_active_class{/if}" data-controller="module" data-action="enabled" data-id="{$module->id}" name="enabled" value="1" type="checkbox"  {if $module->enabled}checked=""{/if}/>
                        <span class="switch-label"></span>
                        <span class="switch-handle"></span>
                    </label>
                {/if}
            </div>

            <div class="okay_list_setting okay_list_products_setting">
                {if $module->status !== 'Not Installed'}
                    <a data-hint="{$btr->files_list_module|escape}" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim" href="{url controller='ModuleDesignAdmin' vendor=$module->vendor module_name=$module->module_name}">
                        {include file='svg_icon.tpl' svgId='icon_copy'}
                    </a>
                {/if}
            </div>

            <div class="okay_list_boding okay_list_close">
                {*delete*}
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
