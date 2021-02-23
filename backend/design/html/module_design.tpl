{* Title *}
{$meta_title=$module->module_name scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->module_design_title} ({$module->vendor}/{$module->module_name})
            </div>
        </div>
    </div>
</div>

<div class="alert alert--icon">
    <div class="alert__content">
        <div class="alert__title mb-q">{$btr->alert_description|escape}</div>
        <p>{$btr->theme_coverage_module_design}</p>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    {if $files}
        <form class="fn_form_list" method="post">
            <div class="okay_list products_list fn_sort_list">
                <input type="hidden" name="session_id" value="{$smarty.session.id}">

                {*Параметры элемента*}
                <div class="deliveries_wrap okay_list_body sortable">
                    {foreach $files as $file}
                        <div class="fn_row okay_list_body_item fn_sort_item">
                            <div class="okay_list_row">
                                {*<div class="okay_list_boding okay_list_drag move_zone"></div>*}
                                <div class="okay_list_boding okay_list_check">
                                    <input class="hidden_check" type="checkbox" id="id_{$file->directory}{$file->filename}" name="check[]" value="{$file->directory}{$file->filename}"/>
                                    <label class="okay_ckeckbox" for="id_{$file->directory}{$file->filename}"></label>
                                </div>
                                <div class="okay_list_boding okay_list_module_design_name">
                                    {$file->directory|escape}{$file->filename|escape}
                                </div>
                                <div class="okay_list_boding okay_list_module_design_status">
                                    {if !$file->cloned_to_theme}
                                        <button class="btn btn_small btn-outline-warning" name="clone_single_file" value="{$file->directory|escape}{$file->filename|escape}">{$btr->module_design_copy_to_actual_theme_for_edit}</button>
                                    {else}
                                        <div class="btn btn_small btn-block btn-outline-info disabled">
                                            {include file='svg_icon.tpl' svgId='checked'}
                                            <span>{$btr->module_design_already_redefined_in_actual_theme}</span>
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>

                {*Блок массовых действий*}
                <div class="okay_list_footer fn_action_block">
                    <div class="okay_list_foot_left">
                        {*<div class="okay_list_boding okay_list_drag"></div>*}
                        <div class="okay_list_heading okay_list_check">
                            <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                            <label class="okay_ckeckbox" for="check_all_2"></label>
                        </div>
                        <div class="okay_list_option">
                            <select name="action" class="selectpicker form-control">
                                <option value="clone_to_theme">{$btr->module_design_copy_to_actual_theme_for_edit}</option>
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
            <div class="text_grey">{$btr->module_design_files_not_exists}</div>
        </div>
    {/if}
</div>
