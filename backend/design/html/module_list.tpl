{foreach $modules as $module}
    <div class="fn_row okay_list_body_item fn_sort_item {if $now_downloaded} fn_now_downloaded{/if}"{if $now_downloaded} style="background-color: #ebffea; transition: background-color 1s linear;"{/if}>
        <div class="okay_list_row">
            <input type="hidden" name="positions[{$module->id}]" value="{$module->position}">

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
                        <a href="{url controller=[{$module->vendor},{$module->module_name},{$module->backend_main_controller}] id=null return=$smarty.server.REQUEST_URI}">
                            <img src="{$rootUrl}/{$module->preview}"/>
                        </a>
                    {else}
                        <img src="{$rootUrl}/{$module->preview}"/>
                    {/if}
                {else}
                    {if $module->backend_main_controller}
                        <a href="{url controller=[{$module->vendor},{$module->module_name},{$module->backend_main_controller}] id=null return=$smarty.server.REQUEST_URI}">
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
                        <a href="{url controller=[{$module->vendor},{$module->module_name},{$module->backend_main_controller}] id=null return=$smarty.server.REQUEST_URI}">
                            {$module->vendor|escape}/{$module->module_name|escape}
                        </a>
                    {else}
                        {$module->vendor|escape}/{$module->module_name|escape}
                    {/if}
                </div>

                {if $module->params->vendor->email}
                    <div class="mb-h">
                        <span class="text_grey text_bold">{$btr->module_vendor_email|escape}:</span>
                        {$module->params->vendor->email|escape}
                    </div>
                {/if}

                {if $module->params->vendor->site}
                    <div class="mb-h">
                        <span class="text_grey text_bold">{$btr->module_vendor_site|escape}:</span>
                        <a class="okay_list_module_name_link" href="{$module->params->vendor->site|escape}" target="_blank">{$module->params->vendor->site|escape}</a>
                    </div>
                {/if}

                <div class="mb-h hidden-lg-up">
                    <span class="text_grey text_bold">{$btr->module_version|escape}:</span>
                    {$module->version|escape}
                </div>
            </div>

            <div class="okay_list_boding okay_list_module_version hidden-md-down">
                <div class="">{$module->version|escape}</div>

                {if !empty($module->params->version) && $module->params->version != $module->version}
                    {if $module->params->version > $module->version}
                        <button type="button" class="fn_update_module btn btn-outline-warning btn--update mt-h hint-top-middle-t-info-s-small-mobile hint-anim" data-hint="{$btr->module_need_update} {$module->params->version}">{include 'svg_icon.tpl' svgId='refresh_icon'} {$module->params->version}</button>
                    {else}
                        {$btr->module_downgrade_warning} {$module->params->version}
                    {/if}
                {/if}

                {if !empty($module->params->Okay) && $module->params->Okay != $config->version}
                    <div class="font_12 text_grey mt-q">
                        {$btr->module_okay_version|escape}
                        {$module->params->Okay|escape}
                    </div>
                {/if}
            </div>
            <div class="okay_list_boding okay_list_module_type hidden-md-down">{if $module->type}{$module->type}{else}{$btr->not_used_module_type}{/if}</div>

            <div class="okay_list_boding okay_list_status">
                {*visible*}
                {if $module->status === 'Not Installed'}
                    <button class="btn" name="install_module" value="{$module->vendor}/{$module->module_name}">{$btr->install_module}</button>
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
