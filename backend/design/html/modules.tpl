{* Title *}
{$meta_title=$btr->modules_list_title scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->modules_list_title|escape}
            </div>
            <div class="box_btn_heading hidden-xs-down">
                <a class="btn btn_small btn-info fn_switch_add_module" href="javascript:;">
                    {include file='svg_icon.tpl' svgId='plus'}
                    {include file='svg_icon.tpl' svgId='minus'}
                    <span>{$btr->modules_add_new_module|escape}</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="fn_hide_add_module hidden-xs-down" style="display: none;">
    <div class="boxed">
        <div class="">
            <div class="heading_box">
                {$btr->modules_download_heading|escape}
            </div>
            <form method="post" id="fn_download_module" action="{url controller='ModulesAdmin@downloadModule'}">
                <div class="alert alert--center alert--icon alert--error fn_error_block hidden">
                    <div class="alert__content">
                        <div class="alert__title fn_error_text"></div>
                    </div>
                </div>
                <input type="hidden" name="session_id" value="{$smarty.session.id}">
        
                <div class="alert  alert--icon alert--info">
                    <div class="alert__content">
                        <div class="alert__title">{$btr->alert_info|escape}</div>
                        <p>
                            {strip}
                            {$btr->modules_url_description|escape}
                            <a href="index.php?controller=ModulesAdmin@marketplace">{$btr->modules_url_description_marketplace}</a>
                            {$btr->modules_url_description_2|escape}
                            {/strip}
                        </p>
                        <p>{$btr->modules_url_example|escape} {$config->marketplace_url|escape}my_module/&#60;uniqueHash&#62;</p>
                    </div>
                </div>
                
                <div class="modules_add_new_modules">
                    <div class="input-group input-group--dabbl input-group--reset">
                        <span class="input-group-addon input-group-addon--left">URL</span>
                        <input class="form-control" type="text" name="access_url">
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

{*Главная форма страницы*}
<div class="fn_toggle_wrap">
    {if $modules}
        <form class="fn_form_list" method="post">
            <div class="okay_list products_list bg_white mb-1 fn_sort_list">
                <input type="hidden" name="session_id" value="{$smarty.session.id}">
                {*Шапка таблицы*}
                <div class="okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                    <div class="okay_list_heading okay_list_module_name">{$btr->general_name|escape}</div>
                    <div class="okay_list_heading okay_list_module_version hidden-md-down">{$btr->module_version|escape}</div>
                    <div class="okay_list_heading okay_list_module_type hidden-md-down">{$btr->module_type|escape}</div>
                    <div class="okay_list_heading okay_list_status">{$btr->general_enable|escape}</div>
                    <div class="okay_list_heading okay_list_setting okay_list_products_setting">{$btr->modules_files|escape}</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>

                {*Параметры элемента*}
                <div class="deliveries_wrap okay_list_body sortable fn_modules_list">
                    {include 'module_list.tpl'}
                </div>

                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        <div class="okay_list_boding okay_list_drag"></div>
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker form-control">
                                <option value="enable">{$btr->general_do_enable|escape}</option>
                                <option value="disable">{$btr->general_do_disable|escape}</option>
                                <option value="delete">{$btr->general_delete|escape}</option>
                                <option value="update">{$btr->module_update|escape}</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        {include file='svg_icon.tpl' svgId='checked'}
                        <span>{$btr->general_apply|escape}</span>
                    </button>
                </div>
            </div>
        </form>
    {else}
        <div class="heading_box mt-1">
            <div class="text_grey">{$btr->no_modules|escape}</div>
        </div>
    {/if}
</div>

<script>
    $(document).on("click", ".fn_update_module", function () {
        $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
        $(this).closest(".fn_form_list").find('select[name="action"] option[value=update]').attr('selected', true);
        $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
        $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').click();
        $(this).closest(".fn_form_list").submit();
    });
    
    $(document).on("submit", "#fn_download_module", function (e) {
        e.preventDefault();
        let form = $(this),
            urlInput = form.find('[name="access_url"]'),
            errorBlock = form.find('.fn_error_block');
        urlInput.removeClass('error');
        errorBlock.addClass('hidden');
        if (!/{str_replace('/', '\/', $config->marketplace_url)}my_module\/\w+$/.test(urlInput.val())) {
            urlInput.addClass('error');
        }
        
        $.ajax({
            url: '{url controller='ModulesAdmin@downloadModule'}',
            dataType: "json",
            type: "POST",
            data: form.serialize(),
            success: function (data) {
                if (data.hasOwnProperty('installed_version')) {
                    $('.fn_error_text').text('{$btr->modules_already_installed|escape} ' + data.installed_version);
                    errorBlock.removeClass('hidden');
                }
                if (data.hasOwnProperty('error')) {
                    $('.fn_error_text').text(data.error);
                    errorBlock.removeClass('hidden');
                }
                if (data.hasOwnProperty('success') && data.success === true) {
                    $('.fn_modules_list').prepend(data.modules);
                }
                console.log(data);
            }
        });
    });
</script>
