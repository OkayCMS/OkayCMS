{$meta_title=$btr->export_products scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->export_products|escape}</div>
    </div>
</div>

{*Вывод ошибок*}
{if $message_error}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_error == 'no_permission'}
                        {$btr->general_permissions|escape} {$export_files_dir}
                        {else}
                        {$message_error|escape}
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

{if $message_error != 'no_permission'}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--icon">
                <div class="alert__content">
                    <div class="alert__title">{$btr->alert_description|escape}</div>
                    <p>{$btr->export_message}</p>
                </div>
            </div>
        </div>
    </div>

    <div id="success_export" class="" style="display: none">
        <div class="alert alert--icon alert--success">
            <div class="alert__content">
                <div class="alert__title">{$btr->general_export_successful|escape}</div>
            </div>
        </div>
    </div>

    {*Параметры элемента*}
    <div class="boxed fn_toggle_wrap">
        <div class="row">
            <progress id="progressbar" class="progress progress-info mt-0" style="display: none" value="0" max="100"></progress>
            <div class="col-lg-12 col-md-12 ">
                <div id="fn_start" class="">
                    <div class="row">
                        <div class="col-md-3 col-sm-3 col-lg-3 col-sm-12 mb-h">
                            <div class="option_export_wrap">
                                <div class="heading_label">{$btr->general_export|escape}</div>
                                <select class="selectpicker form-control fn_type_export">
                                   <option value="all_products">{$btr->general_all_products|escape}</option>
                                   <option value="category_products">{$btr->general_from_category|escape}</option>
                                   <option value="brands_products">{$btr->general_from_brand|escape}</option>
                                    {get_design_block block="export_entities_filter"}
                                </select>
                            </div>
                        </div>
                        {if $categories}
                        <div id="category_products"  class="col-md-3 col-sm-3 col-lg-3 col-sm-12 export_options hidden mb-h">
                            <div class="heading_label">{$btr->general_from_category|escape}</div>
                            <select class="selectpicker form-control" data-live-search="true" data-size="10"  name="category_id">
                                {function name=categories_tree}
                                    {foreach $categories as $c}
                                        <option value="{$c->id}">{section name=sp loop=$level}&nbsp;{/section}{$c->name|escape}</option>
                                        {categories_tree categories=$c->subcategories level=$level+1}
                                    {/foreach}
                                {/function}
                                {categories_tree categories=$categories level=0}
                            </select>
                        </div>
                        {/if}
                        {if $brands}
                        <div id="brands_products" class="col-md-3 col-sm-3 col-lg-3 col-sm-12 export_options hidden mb-h">
                            <div class="heading_label">{$btr->general_from_brand|escape}</div>
                            <select class="selectpicker form-control" data-size="10" name="brand_id">
                                {foreach $brands as $b}
                                    <option value="{$b->id}" {if $b@first}selected=""{/if}>{$b->name|escape}</option>
                                {/foreach}
                            </select>
                        </div>
                        {/if}
                        {get_design_block block="export_entity_select_block"}
                        <div class="col-md-3 col-sm-3 col-lg-3 col-sm-12 float-sm-right mt-2">
                            <button id="fn_start" type="submit" class="btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='magic'}
                                <span>{$btr->general_export|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}

<script src="{$rootUrl}/backend/design/js/piecon/piecon.js"></script>
<script>
    {literal}

    var in_process=false;
    var field = '',
        value = '';

    $(function() {
        $(".fn_type_export").on("change",function () {
            elem = $("#"+$(this).val());
            $(".export_options").addClass("hidden");
            elem.removeClass("hidden");

        });

        $('button#fn_start').click(function() {
            if($(".export_options:visible")){
                field = $(".export_options:visible").find('select').attr('name');
                value = $(".export_options:visible").find('select').val();
            }
            Piecon.setOptions({fallback: 'force'});
            Piecon.setProgress(0);
            var progress_item = $("#progressbar"); //указываем селектор элемента с анимацией
            progress_item.show();

            do_export('',progress_item);

        });

        function do_export(page,progress)
        {
            page = typeof(page) != 'undefined' ? page : 1;
            var data = {page: page};
            if (field && value) {
                data[field] = value;
            }
            $.ajax({
                url: "ajax/export.php",
                data: data,
                dataType: 'json',
                success: function(data){

                    if(data && !data.end)
                    {
                        Piecon.setProgress(Math.round(100*data.page/data.totalpages));
                        progress.attr('value',100*data.page/data.totalpages);
                        do_export(data.page*1+1,progress);
                    }
                    else
                    {
                        if(data && data.end)
                        {
                            Piecon.setProgress(100);
                            progress.attr('value','100');
                            window.location.href = 'files/export/export.csv';
                            progress.fadeOut(500);
                            $('#success_export').show();
                        }
                    }
                },
                error:function(xhr, status, errorThrown) {
                    alert(errorThrown+'\n'+xhr.responseText);
                }
            });
        }
    });
    {/literal}
</script>


