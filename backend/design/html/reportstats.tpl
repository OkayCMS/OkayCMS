{$meta_title = $btr->reportstats_orders scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->reportstats_orders|escape}
                <i class="fn_tooltips" title="{$btr->tooltip_reportstats_orders|escape}">
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
            {* Форма для фильрации *}
            <form class="date_filter row" method="get">
                <input type="hidden" name="controller" value="ReportStatsAdmin">
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
                                    <div class="col-md-11 col-lg-11 col-xl-7 col-sm-12 ">
                                        {*Блок фильтров*}
                                       <div class="date">
                                           <input type="hidden" name="date_filter" value="">
        
                                           <div class="col-md-5 col-lg-5 pr-0 pl-0">
                                               <div class="input-group mobile_input-group input-group--date">
                                                   <span class="input-group-addon-date">{$btr->general_from|escape}</span>
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
                                                       <input type="date" class="fn_to_date form-control" name="date_to" value="{$date_to}" autocomplete="off" >
                                                       {else}
                                                       <input type="text" class="fn_to_date form-control" name="date_to" value="{$date_to}" autocomplete="off" >
                                                   {/if}
                                               </div>
                                           </div>
                                           <div class="col-md-2 col-lg-2 pr-0 mobile_text_right">
                                               <button class="btn btn_blue" type="submit">{$btr->general_apply|escape}</button>
                                           </div>
                                           
                                       </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-lg-3 col-sm-12">
                                <select id="id_categories" name="category_id" title="{$btr->general_category_filter|escape}" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="this.form.submit()">
                                    <option value="{url keyword=null brand_id=null page=null limit=null category_id=null}" {if !$smarty.get.category_id}selected{/if}>{$btr->general_all_categories|escape}</option>
                                    {function name=category_select level=0}
                                        {foreach $categories as $c}
                                            <option value='{$c->id}' {if $smarty.get.category_id == $c->id}selected{/if}>
                                                {section sp $level}-{/section}{$c->name|escape}
                                            </option>
                                            {category_select categories=$c->subcategories level=$level+1}
                                        {/foreach}
                                    {/function}
                                    {category_select categories=$categories}
                                </select>
                            </div>
                            <div class="col-md-3 col-lg-3 col-sm-12">
                                <select class="selectpicker form-control" name="status" data-live-search="true" data-size="10" onchange="this.form.submit()">
                                    <option {if !$smarty.get.status}selected{/if} value="{url status=null}">{$btr->reportstats_all_statuses|escape}</option>
                                    {foreach $all_status as $status_item}
                                        <option {if $status_item->id == $smarty.get.status}selected{/if} value="{$status_item->id}">{$status_item->name|escape}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm 12">
                                <select onchange="$('.fn_from_date').val('');$('.fn_to_date').val('');this.form.submit()" name="date_filter" class="selectpicker form-control">
                                    <option {if !$date_filter}selected{/if} value="">{$btr->reportstats_all_orders|escape}</option>
                                    <option {if $date_filter == today}selected{/if} value="today" >{$btr->reportstats_today|escape}</option>
                                    <option {if $date_filter == this_week}selected{/if} value="this_week">{$btr->reportstats_this_week|escape}</option>
                                    <option {if $date_filter == this_month}selected{/if} value="this_month" >{$btr->reportstats_this_month|escape}</option>
                                    <option {if $date_filter == this_year}selected{/if} value="this_year" >{$btr->reportstats_this_year|escape}</option>
                                    <option {if $date_filter == yesterday}selected{/if}  value="yesterday">{$btr->reportstats_yesterday|escape}</option>
                                    <option {if $date_filter == last_week}selected{/if} value="last_week" >{$btr->reportstats_last_week|escape}</option>
                                    <option {if $date_filter == last_month}selected{/if} value="last_month" >{$btr->reportstats_last_month|escape}</option>
                                    <option {if $date_filter == last_year}selected{/if} value="last_year" >{$btr->reportstats_last_year|escape}</option>
                                    <option {if $date_filter == last_24hour}selected{/if} value="last_24hour" >{$btr->reportstats_last_24|escape}</option>
                                    <option {if $date_filter == last_7day}selected{/if} value="last_7day" >{$btr->reportstats_last_7_days|escape}</option>
                                    <option {if $date_filter == last_30day}selected{/if} value="last_30day" >{$btr->reportstats_last_30_days|escape}</option>
                                </select>
                            </div>
        
                            <div class="col-md-3 col-lg-3 col-sm-12 mobile_text_right">
                                <button id="fn_start" type="submit" class="btn btn_small btn_blue float-md-right">
                                    {include file='svg_icon.tpl' svgId='magic'}
                                    <span>{$btr->general_export|escape}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <form method="post" class="fn_form_list">
        <input type="hidden" name="session_id" value="{$smarty.session.id}" />
        {assign 'total_summ' 0}
        {assign 'total_amount' 0}
        <div class="okay_list products_list fn_sort_list">
            {*Шапка таблицы*}
            <div class="okay_list_head">
                <div class="okay_list_heading okay_list_reportstats_categories">{$btr->general_category|escape}</div>
                <div class="okay_list_heading okay_list_reportstats_products">{$btr->general_name|escape}</div>
                <div class="okay_list_heading okay_list_reportstats_total">{$btr->general_sales_amount|escape}</div>
                <div class="okay_list_heading okay_list_reportstats_setting">{$btr->general_amt|escape}</div>
            </div>

            {*Параметры элемента*}
            <div class="okay_list_body">
                {foreach $report_stat_purchases as $purchase}
                    {assign var='total_summ'  value=$total_summ+$purchase->sum_price}
                    {assign var='total_amount' value=$total_amount+$purchase->amount}
                    <div class="okay_list_body_item">
                        <div class="okay_list_row ">
                            <div class="okay_list_boding okay_list_reportstats_categories">
                                {foreach $purchase->category->path as $c}
                                    {$c->name}/
                                {/foreach}
                            </div>
                            <div class="okay_list_boding okay_list_reportstats_products">
                                <a title="{$purchase->product_name|escape}" href="{url controller=ProductAdmin id=$purchase->product_id return=$smarty.server.REQUEST_URI}">{$purchase->product_name}</a> {$purchase->variant_name}
                                <div class="hidden-md-up mt-q">
                                    <span class="text_dark text_600">
                                        <span class="hidden-xs-down">Сумма продаж: </span>
                                        <span class="text_primary">
                                            {$purchase->sum_price} {$currency->sign|escape}
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="okay_list_boding okay_list_reportstats_total">
                                {$purchase->sum_price} {$currency->sign|escape}
                            </div>

                            <div class="okay_list_reportstats_setting">
                                {$purchase->amount} {if $purchase->units}{$purchase->units|escape}{else}{$settings->units}{/if}
                            </div>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-lg-12 col-md-12">
                <div class="text_dark text_500 text-xs-right mr-1 mt-h">
                    <div class="h5">{$btr->general_total|escape} {$total_summ|string_format:'%.2f'} {$currency->sign|escape}  <span class="text_grey">({$total_amount}  {$btr->reportstats_units})</span></div>
                </div>
            </div>
        </div>
    </form>
    <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
        {include file='pagination.tpl'}
    </div>
</div>

{literal}
<script>
    $(function() {
        $('input[name="date_from"]').datepicker();
        $('input[name="date_to"]').datepicker();

        $('button#fn_start').click(function() {
            $.ajax({
                url: "{/literal}{url controller='ReportStatsAdmin@export'}{literal}",
                dataType: 'json',
                success: function () {

                    window.location.href = 'files/export/export_stat_products.csv';
                },
                error: function (xhr, status, errorThrown) {
                    alert(errorThrown + '\n' + xhr.responseText);
                }
            });
        });
    });
</script>
{/literal}
