{$meta_title = $btr->settings_system_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->settings_system_title|escape}</div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="boxed fn_toggle_wrap">
            <div class="heading_box">
                {$btr->settings_general_options|escape}
                <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                    <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                </div>
            </div>
            {*Параметры элемента*}
            <div class="toggle_body_wrap on fn_card">
               <div class="row">
                   {if $php_version}
                       <div class="col-lg-4 col-md-4 col-sm-12">
                           <div class="banner_card">
                               <div class="system_header">
                                   <span class="font-weight-bold">PHP Version</span>
                               </div>
                               <div class="banner_card_block">
                                   <div class="system_information">
                                       Version: {$php_version|escape}
                                   </div>
                               </div>
                           </div>
                       </div>
                   {/if}
                   {if $server_ip}
                   <div class="col-lg-4 col-md-4 col-sm-12">
                       <div class="banner_card">
                           <div class="system_header">
                               <span class="font-weight-bold">{$btr->system_server_ip}</span>
                           </div>
                           <div class="banner_card_block">
                               <div class="system_information">
                                   IP: {$server_ip|escape}
                               </div>
                           </div>
                       </div>
                   </div>
                   {/if}
                   {if $sql_info}
                       <div class="col-lg-4 col-md-4 col-sm-12">
                           <div class="banner_card">
                               <div class="system_header">
                                   <span class="font-weight-bold">SQL</span>
                               </div>
                               <div class="banner_card_block">
                                   <div class="system_information">
                                       {foreach $sql_info as $sql_param => $sql_ver}
                                           <div>
                                               <span>{$sql_param|escape}: </span>
                                               <span>{$sql_ver|escape}</span>
                                           </div>
                                       {/foreach}
                                   </div>
                               </div>
                           </div>
                       </div>
                   {/if}

                   {if $all_extensions}
                   <div class="col-lg-12 col-md-12 col-sm-12">
                       <div class="banner_card">
                           <div class="system_header">
                               <span class="font-weight-bold">Server extensions</span>
                           </div>
                           <div class="banner_card_block">
                               <div class="system_information clearfix">
                                   {foreach $all_extensions as $ext_val}
                                   <div class="col-xl-3 col-lg-4 col-md-6">
                                       <div>
                                           <span>{$ext_val|escape}</span>
                                       </div>
                                   </div>
                                   {/foreach}
                               </div>
                           </div>
                       </div>
                   </div>
                   {/if}

                   {if $ini_params}
                       <div class="col-lg-4 col-md-4 col-sm-12">
                           <div class="banner_card">
                               <div class="system_header">
                                   <span class="font-weight-bold">INI params</span>
                               </div>
                               <div class="banner_card_block">
                                   <div class="system_information">
                                       {foreach $ini_params as $param_name => $param_value}
                                           <div>
                                               <span>{$param_name|escape}: </span>
                                               <span>{$param_value|escape}</span>
                                           </div>
                                       {/foreach}
                                   </div>
                               </div>
                           </div>
                       </div>
                   {/if}


                   <div class="col-lg-12 col-md-12 col-sm-12">
                       <div class="alert alert--icon alert--info">
                           <div class="alert__content">
                               <div class="alert__title mb-h">
                                   {$btr->alert_info|escape}
                               </div>
                               <div class="text_box">
                                   <div class="mb-1">
                                       {$btr->system_message_1|escape}
                                   </div>
                                   <div class="mb-h"><b>{$btr->system_message_2|escape}</b> </div>
                                   <div>
                                       <ul class="mb-0 pl-1">
                                           <li>display_errors - {$btr->system_display_errors|escape}</li>
                                           <li>memory_limit - {$btr->system_memory_limit|escape}</li>
                                           <li>post_max_size - {$btr->system_post_max_size|escape}</li>
                                           <li>max_input_time - {$btr->system_max_input_time|escape}</li>
                                           <li>max_file_uploads - {$btr->system_max_file_uploads|escape}</li>
                                           <li>max_execution_time - {$btr->system_max_execution_time|escape}</li>
                                           <li>upload_max_filesize - {$btr->system_upload_max_filesize|escape}</li>
                                           <li>max_input_vars - {$btr->system_max_input_vars|escape}</li>
                                       </ul>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>
            </div>
        </div>
    </div>
</div>