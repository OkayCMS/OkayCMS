{$meta_title=$btr->import_products scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">
            {$btr->import_products|escape}
            <a class="export_block export_users hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="{$btr->general_example|escape}" href="files/import/example.csv" target="_blank">
                {include file='svg_icon.tpl' svgId='export'}
            </a>
        </div>
    </div>
</div>

<div id="import_error" class="boxed boxed_warning" style="display: none;"></div>

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                    {if $message_error == 'no_permission'}
                        {$btr->general_permissions|escape} {$import_files_dir|escape}
                    {elseif $message_error == 'convert_error'}
                        {$btr->import_utf|escape}
                    {elseif $message_error == 'locale_error'}
                        {$btr->import_locale|escape} {$locale|escape} {$btr->import_not_correctly|escape}
                    {elseif $message_error == 'upload_error'}
                        {$btr->upload_error|escape}
                    {elseif $message_error == 'required_fields'}
                        {$btr->import_required|escape}
                    {elseif $message_error == 'duplicated_columns'}
                        {$btr->import_duplicated|escape}: {implode($duplicated_columns, ", ")}
                    {elseif $message_error == 'duplicated_columns_pairs'}
                        {$btr->import_duplicated_pairs}:<BR>
                        {foreach $duplicated_columns_pairs as $pair}
                            {implode($pair, ", ")}
                            {if !$pair@last}<BR>{/if}
                        {/foreach}
                    {else}
                        {$message_error|escape}
                    {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
{if $message_error != 'no_permission'}
    <form class="form-horizontal hidden-xs-down" method="post" enctype="multipart/form-data">
        <input type=hidden name="session_id" value="{$smarty.session.id}">
        {if $filename || $import}
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="boxed fn_toggle_wrap ">
                        <div class="heading_box boxes_inline">
                            {$btr->import_file|escape} {$file->name|escape} ({($file->size/1024)|round:'2'} {$btr->general_kb|escape})
                        </div>
                        {if $filename}
                            <div class="toggle_body_wrap on fn_card">
                                <div class="boxed boxed_attention">
                                    <div class="text_box mt-0">
                                        <div>{$btr->import_info}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="okay_list">
                                <div class="okay_list_head">
                                    <div class="col-lg-6 col-md-6">{$btr->import_csv_columns}</div>
                                    <div class="col-lg-6 col-md-6">
                                        {$btr->import_fields}
                                        <div>
                                            <a href="javascript:;" class="btn btn_small btn-info fn_skip_all">{$btr->import_skip_all}</a>
                                            <a href="javascript:;" class="btn btn_small btn-info fn_new_all">{$btr->import_new_all}</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    {foreach $source_columns as $column}
                                        <div class="row import_row {if $column@first}mt-1{/if}">
                                            <div class="col-lg-6 col-md-6">

                                                <div class="push-lg-4 col-lg-6 text-xs-left ml-2 col-sm-12">
                                                    {$column->name|escape}{if $column->is_feature || !$column->is_exist} ({$btr->import_feature}){/if}
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12 text-xs-left fn_row">
                                                <input type="hidden" name="csv_fields[{$column->name|escape}]" value="{$column->value|escape}" />
                                                <a href="javascript:;" class="fn_edit_column {if empty($column->value)}text_warning{elseif $column->is_nf_selected}text_green{/if}"
                                                   data-column_name="{$column->name|escape}"
                                                   data-is_exist="{if $column->is_exist}1{else}0{/if}">
                                                    {if in_array($column->value, $columns_names)}
                                                         {$btr->getTranslation('import_field_'|cat:$column->value)} 
                                                    {elseif empty($column->value)}
                                                        {$btr->import_skip}
                                                    {elseif $column->is_nf_selected}
                                                        {$btr->import_new_feature}
                                                    {else}
                                                        {$column->value|escape}
                                                    {/if}
                                                </a>
                                            </div>
                                        </div>
                                    {/foreach}
                                    <select class="selectpicker fn_select" data-live-search="true">
                                        <optgroup label="{$btr->import_additional}">
                                            <option value="" data-label="{$btr->import_skip}">{$btr->import_skip}</option>
                                            <option value="" data-label="{$btr->import_new_feature}" class="fn_new_feature">
                                                {$btr->import_new_feature}
                                            </option>
                                        </optgroup>
                                        <optgroup label="{$btr->import_main_fields}">
                                            {foreach $columns_names as $cname}
                                                <option value="{$cname}" data-label="{$btr->getTranslation('import_field_'|cat:$cname)}">
                                                    {$btr->getTranslation('import_field_'|cat:$cname)}
                                                </option>
                                            {/foreach}
                                        </optgroup>
                                        {$block = {get_design_block block="import_fields_association"}}
                                        {if $block}
                                            <optgroup label="{$btr->import_modules_fields}">
                                                {$block}
                                            </optgroup>
                                        {/if}
                                        <optgroup label="{$btr->import_shop_features}">
                                            {foreach $features as $feature}
                                                <option value="{$feature|escape}" data-label="{$feature|escape}">
                                                    {$feature|escape}
                                                </option>
                                            {/foreach}
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="okay_list_footer">
                                    <div class="col-lg-12 col-md-12 mt-1">
                                        <button type="submit" name="import" value="1" class="btn btn_small btn_blue float-md-right"><i class="fa fa-upload"></i>&nbsp;{$btr->import_do_import}</button>
                                    </div>
                                </div>
                            </div>
                        {elseif $import}
                            <div id='import_result' class="boxes_inline" style="display: none;">
                                <a class="btn btn_small btn-info" href="index.php?controller=ImportLogAdmin" target="_blank">{$btr->import_log|escape}</a>
                            </div>
                            <div>
                                <progress id="progressbar" class="progress progress-xs progress-info mt-1" style="display: none" value="0" max="100"></progress>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        {else}
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="boxed fn_toggle_wrap">
                        <div class="heading_box">
                            {$btr->import_download|escape}
                            <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                                <a class="btn-minimize" href="javascript:;" ><i class="icon-arrow-down"></i></a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="text_warning">
                                    <div class="heading_normal text_warning">
                                        <span class="text_warning">{$btr->import_backup|escape}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="text_primary">
                                    <div class="heading_normal text_primary">
                                        <span class="text_primary">
                                         {$btr->import_maxsize|escape}
                                            {if $config->max_upload_filesize>1024*1024}
                                                {$config->max_upload_filesize/1024/1024|round:'2'} {$btr->general_mb|escape}
                                            {else}
                                                {$config->max_upload_filesize/1024|round:'2'} {$btr->general_kb|escape}
                                            {/if}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-sm-12 my-h">
                                <div class="input_file_container">
                                    <input name="file" class="file_upload input_file" id="my-file" type="file">
                                    <label tabindex="0" for="my-file" class="input_file_trigger">
                                        <svg width="20" height="17" viewBox="0 0 20 17"><path fill="currentcolor" d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path></svg>
                                        <span>Выберите файл...</span>
                                    </label>
                                </div>
                                <p class="input_file_return"></p>
                            </div>
                            <div class="col-lg-7 col-md-6 my-h">
                                <button type="submit" class="btn btn_small btn_blue float-md-right"><i class="fa fa-upload"></i>&nbsp;{$btr->import_to_download|escape}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </form>
{/if}

<script src="{$rootUrl}/backend/design/js/piecon/piecon.js"></script>
<script>
    {literal}
    var new_feature_label = "{/literal}{$btr->import_new_feature}{literal}";
    var skip_label = "{/literal}{$btr->import_skip}{literal}";
    $(function() {
        var select_column = $(".fn_select");
        $(".fn_select").remove();
        $(document).on("click", ".fn_edit_column", function() {
            var edit = $(this),
                parent = edit.closest(".fn_row");
            $(".fn_edit_column").show();
            edit.hide();
            $(".selectpicker").selectpicker("destroy");
            $(".fn_select").remove();
            parent.append(select_column.clone());

            /* настраиваем селект для текущего елемента */
            var select = parent.find("select.fn_select"),
                new_feature = parent.find(".fn_new_feature"),
                input = parent.find("[name*='csv_fields']");
            new_feature.val(edit.data("column_name"));
            new_feature.prop("disabled", edit.data("is_exist") ? true : false);
            select.find("option[value='" + input.val() + "']").prop("selected", true);

            /* дизаблим уже выбранные значения */
            $("[name*='csv_fields']").each(function() {
                if ($(this).val() != "") {
                    select.find("option[value='" + $(this).val() + "']").prop("disabled", true);
                }
            });

            select.selectpicker();
            select.on("hide.bs.select", function() {
                select.closest(".fn_row").find(".fn_edit_column").show();
                $(".selectpicker").selectpicker("destroy");
                $(".fn_select").remove();
            });
            setTimeout(function() {
                select.selectpicker("toggle");
            }, 10);
        });

        $(document).on("change", ".fn_select select", function() {
            var select = $(this),
                parent = select.closest(".fn_row"),
                selected = select.find("option:selected"),
                edit = parent.find(".fn_edit_column");
            edit.text(selected.data("label"));
            edit.removeClass("text_warning").removeClass("text_green");
            if (select.val()=="") {
                edit.addClass("text_warning");
            } else if (selected.hasClass("fn_new_feature")) {
                edit.addClass("text_green");
            }
            parent.find("[name*='csv_fields']").val(select.val());
        });

        $(document).on("click", ".fn_skip_all", function() {
            $('.fn_edit_column').each(function() {
                var edit = $(this),
                    parent = edit.closest(".fn_row");
                edit.text(skip_label);
                edit.removeClass("text_warning").removeClass("text_green");
                edit.addClass("text_warning");
                parent.find("[name*='csv_fields']").val("");
            });
            return false;
        });
        $(document).on("click", ".fn_new_all", function() {
            $('.fn_edit_column').each(function() {
                var edit = $(this),
                    parent = edit.closest(".fn_row");
                if (!edit.data("is_exist")) {
                    edit.text(new_feature_label);
                    edit.removeClass("text_warning").removeClass("text_green");
                    edit.addClass("text_green");
                    parent.find("[name*='csv_fields']").val(edit.data('column_name'));
                }
            });
            return false;
        });
    });
    {/literal}
{if $import}
    {literal}
        // On document load
        $(function(){
            Piecon.setOptions({fallback: 'force'});
            Piecon.setProgress(0);
            var progress_item = $("#progressbar"); //указываем селектор элемента с анимацией
            progress_item.show();
            do_import('',progress_item);
        });

        function do_import(from,progress)
        {
            from = typeof(from) != 'undefined' ? from : 0;
            $.ajax({
                 url: "ajax/import.php",
                    data: {from:from},
                    dataType: 'json',
                    success: function(data){
                        if (data.error) {
                            var error = '';
                            if (data.missing_fields) {
                                error = '<span class="heading_box">В файле импорта отсутствуют необходимые столбцы: </span><b>';
                                for (var i in data.missing_fields) {
                                    error += data.missing_fields[i] + ', ';
                                }
                                error = error.substring(0, error.length-2);
                                error += '</b>';
                            }

                            progress.fadeOut(500);
                            $('#import_error').html(error);
                            $('#import_error').show();
                        } else {
                            Piecon.setProgress(Math.round(100 * data.from / data.totalsize));
                            progress.attr('value',100*data.from/data.totalsize);

                            if (data != false && !data.end) {
                                do_import(data.from,progress);
                            } else {
                                Piecon.setProgress(100);
                                progress.attr('value','100');
                                $("#import_result").show();
                                progress.fadeOut(500);
                            }
                        }
                    },
                    error: function(xhr, status, errorThrown) {
                        alert(errorThrown+'\n'+xhr.responseText);
                    }
            });

        }
    {/literal}
{/if}
</script>
