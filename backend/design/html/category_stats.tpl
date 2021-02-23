
{$meta_title=$btr->category_stats_sales scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->category_stats_sales|escape} {$category->name|escape} {$brand->name|escape}
                <i class="fn_tooltips" title="{$btr->tooltip_category_stats_sales|escape}">
                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                </i>
            </div>
        </div>
    </div>
</div>

{*Главная форма страницы*}
<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_toggle_wrap">
                <div class="heading_box visible_md">
                    {$btr->general_filter|escape}
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="boxed_sorting toggle_body_wrap off fn_card">
                <div class="row">
                    <div class="col-xs-12 mb-1">
                        <div class="row">
                            <div class="col-md-11 col-lg-11 col-xl-7 col-sm-12">
                                <div class="date">
                                    {*Блок фильтров*}
                                    <form class="date_filter row" method="get">
                                        <input type="hidden" name="controller" value="CategoryStatsAdmin" />
                                        <div class="col-md-5 col-lg-5 pr-0 pl-0">
                                            <div class="input-group mobile_input-group input-group--date">
                                                <span class=" input-group-addon-date">{$btr->general_from|escape}</span>
                                                {if $is_mobile || $is_tablet}
                                                    <input type="date" class="fn_from_date form-control" name="date_from" value="{$date_from}" autocomplete="off">
                                                    {else}
                                                    <input type="text" class="fn_from_date form-control" name="date_from" value="{$date_from}" autocomplete="off">
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-lg-5 pr-0 pl-0">
                                            <div class="input-group mobile_input-group input-group--date">
                                                <span class=" input-group-addon-date">{$btr->general_to|escape}</span>
                                                {if $is_mobile || $is_tablet}
                                                    <input type="date" class="fn_to_date form-control" name="date_to" value="{$date_to}" autocomplete="off">
                                                    {else}
                                                    <input type="text" class="fn_to_date form-control" name="date_to" value="{$date_to}" autocomplete="off">
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="col-md-2 pr-0 mobile_text_right">
                                            <button class="btn btn_blue" type="submit">{$btr->general_apply|escape}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-lg-4 col-sm-12">
                        <select id="id_categories" name="categories_filter" title="{$btr->general_category_filter|escape}" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="location = this.value;">
                            <option value="{url brand=null category=null}" {if !$category}selected{/if}>{$btr->general_all_categories|escape}</option>
                            {function name=category_select level=0}
                                {foreach $categories as $c}
                                    <option value='{url brand=null category=$c->id}' {if $smarty.get.category == $c->id}selected{/if}>
                                        {section sp $level}-{/section}{$c->name|escape}
                                    </option>
                                    {category_select categories=$c->subcategories level=$level+1}
                                {/foreach}
                            {/function}
                            {category_select categories=$categories}
                        </select>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm 12">
                        <select onchange="location = this.value;" class="selectpicker form-control">
                            <option value="{url brand=null}" {if !$brand}selected{/if}>{$btr->general_all_brands|escape}</option>
                            {foreach $brands as $b}
                                <option value="{url brand=$b->id}" {if $brand->id == $b->id}selected{/if}>{$b->name|escape}</option>
                            {/foreach}
                        </select>
                    </div>

                    <div class="col-md-4 col-lg-4 col-sm-12 mobile_text_right">
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

    {$block = {get_design_block block="category_stats_custom_block"}}
    {if !empty($block)}
        <div class="boxed fn_toggle_wrap">
            {$block}
        </div>
    {/if}

    <form method="post" class="fn_form_list">
        <input type="hidden" name="session_id" value="{$smarty.session.id}" />
        <div class="okay_list products_list fn_sort_list">
            {*Шапка таблицы*}
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_categorystats_categories">{$btr->general_category|escape}</div>
                <div class="okay_list_heading okay_list_categorystats_total">{$btr->general_sales_amount|escape}</div>
                <div class="okay_list_heading okay_list_categorystats_setting">{$btr->general_amount|escape}</div>
            </div>
            {*Параметры элемента*}
            <div class="okay_list_body">
                {function name=categories_list_tree level=0}
                    {foreach $categories as $category}
                        {if $categories}
                            <div class="okay_list_body_item">
                                <div class="okay_list_row ">
                                    <div class="okay_list_boding okay_list_categorystats_categories">
                                        {$category->name|escape}
                                        <div class="hidden-md-up mt-q">
                                            <span class="text_dark text_600">
                                                <span class="hidden-xs-down">{$btr->general_sales_amount|escape} </span>
                                                <span class="{if $category->price}text_primary {else}text_dark {/if}">
                                                    {$category->price} {$currency->sign}
                                                </span>
                                            </span>
                                        </div>
                                        {get_design_block block="category_stats_list_name"}
                                    </div>
                                    <div class="okay_list_boding okay_list_categorystats_total">
                                        {if $category->price}<span class="text_dark">{$category->price} {$currency->sign}</span>{else}{$category->price} {$currency->sign}{/if}
                                    </div>
                                    <div class="okay_list_boding okay_list_categorystats_setting">
                                        {if $category->amount}<span class="text_dark">{$category->amount} {$btr->reportstats_units}</span>{else}{$category->amount} {$btr->reportstats_units}{/if}
                                    </div>
                                </div>
                            </div>
                        {/if}
                        {categories_list_tree categories=$category->subcategories level=$level+1}
                    {/foreach}
                {/function}
                {categories_list_tree categories=$categories_list}
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-lg-12 col-md-12">
                <div class="text_dark text_500 text-xs-right mr-1 mt-h">
                    <div class="h5">{$btr->general_total|escape} {$total_price} {$currency->sign|escape} <span class="text_grey">({$total_amount} {$btr->reportstats_units|escape})</span></div>
                </div>
            </div>
        </div>
    </form>
    <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
        {include file='pagination.tpl'}
    </div>
</div>
{* On document load *}
<script>
    {if $category}
    var category = {$category->id};
    {/if}
    {if $brand}
    var brand = {$brand->id};
    {/if}
    {if $date_from}
    var date_from = '{$date_from}';
    {/if}
    {if $date_to}
    var date_to = '{$date_to}';
    {/if}
</script>
{literal}
    <script type="text/javascript">
        $(function() {
            $('input[name="date_from"]').datepicker();
            $('input[name="date_to"]').datepicker();
            $('button#fn_start').click(function() {
                do_export();
            });
            function do_export(page) {
                page = typeof(page) != 'undefined' ? page : 1;
                category = typeof(category) != 'undefined' ? category : 0;
                brand = typeof(brand) != 'undefined' ? brand : 0;
                date_from = typeof(date_from) != 'undefined' ? date_from : 0;
                date_to = typeof(date_to) != 'undefined' ? date_to : 0;
                $.ajax({
                    url: "ajax/export_stat.php",
                    data: {
                        page: page,
                        category: category,
                        brand: brand,
                        date_from: date_from,
                        date_to: date_to
                    },
                    dataType: 'json',
                    success: function () {
                        window.location.href = 'files/export/export_stat.csv';
                    },
                    error: function (xhr, status, errorThrown) {
                        alert(errorThrown + '\n' + xhr.responseText);
                    }
                });

            }
        });
    </script>
{/literal}
