{$meta_title = $btr->okaycms__integration_ic__description_title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okaycms__integration_ic__description_title|escape}
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title mb-q">{$btr->alert_info|escape}</div>
                <div class="text_box">
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_1|escape} (<b>{url_generator route="integration_1c" absolute=1}</b>) {$btr->okaycms__integration_ic__description_part_2|escape}.
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_2_1|escape}</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_3|escape} <b>OkayCMS</b>: <a href="https://okay-cms.com/article/instruktsiya-po-nastrojke-obmena-dannymi-sajta-s-1s-8h-na-primere-konfiguratsii-ut-23" target="_blank">https://okay-cms.com/article/instruktsiya-po-nastrojke-obmena-dannymi-sajta-s-1s-8h-na-primere-konfiguratsii-ut-23</a>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_4|escape}</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_5|escape}</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_6|escape}</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_7|escape} <b>{url_generator route="integration_1c" absolute=1}?mode=init</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_8|escape}
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_9|escape} (<b>{url_generator route="integration_1c" absolute=1}?mode=file&type=catalog&filename=import0_1.xml</b>)
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_10|escape}
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_11|escape} (<b>{url_generator route="integration_1c" absolute=1}?mode=import&type=catalog&filename=import0_1.xml</b>)
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_12|escape}.
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_13|escape} <b>{url_generator route="integration_1c" absolute=1}?mode=import&type=catalog&filename=offers_1.xml</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_14|escape} <b>{url_generator route="integration_1c" absolute=1}?mode=file&type=sale&filename=orders.xml</b>
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_15|escape}
                    </p>
                    <p class="mb-1">
                        {$btr->okaycms__integration_ic__description_part_16|escape} <b>{url_generator route="integration_1c" absolute=1}?mode=query&type=sale</b>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    {*Блок статусов заказов*}
    <div class="col-lg-12 col-md-12">
        <div class="fn_toggle_wrap">
            <div class="toggle_body_wrap on fn_card">
                <form class="fn_form_list" method="post">
                    <input type="hidden" value="status" name="status">
                    <input type="hidden" name="session_id" value="{$smarty.session.id}">
                    <div class="okay_list boxed">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_heading okay_list_order_stg_sts_name_1c">{$btr->general_name|escape}</div>
                            <div class="okay_list_heading okay_list_order_stg_sts_status2">{$btr->order_settings_1c_action|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>
                        <div class="fn_status_list fn_sort_list okay_list_body sortable">
                            {if $orders_statuses}
                                {foreach $orders_statuses as $order_status}
                                    <div class="fn_row okay_list_body_item">
                                        <div class="okay_list_row fn_sort_item">
                                            <input type="hidden" name="id[]" value="{$order_status->id}">

                                            <div class="okay_list_boding okay_list_order_stg_sts_name_1c">
                                                <span>{$order_status->name|escape}</span>

                                                {if $is_mobile == true}
                                                    <div class="hidden-sm-up mt-q">
                                                        <select name="status_1c[{$order_status->id}]" class="selectpicker form-control">
                                                            <option value="not_use" {if $order_status->status_1c == ''}selected=""{/if}>{$btr->order_settings_1c_action|escape}: {$btr->order_settings_1c_not_use|escape}</option>
                                                            <option value="new" {if $order_status->status_1c == 'new'}selected=""{/if}>{$btr->order_settings_1c_action|escape}: {$btr->order_settings_1c_new|escape}</option>
                                                            <option value="accepted" {if $order_status->status_1c == 'accepted'}selected=""{/if}>{$btr->order_settings_1c_action|escape}: {$btr->order_settings_1c_accepted|escape}</option>
                                                            <option value="to_delete" {if $order_status->status_1c == 'to_delete'}selected=""{/if}>{$btr->order_settings_1c_action|escape}: {$btr->order_settings_1c_to_delete|escape}</option>
                                                        </select>
                                                    </div>
                                                {/if}
                                            </div>

                                            {if $is_mobile == false}
                                                <div class="okay_list_boding okay_list_order_stg_sts_status2">
                                                    <select name="status_1c[{$order_status->id}]" class="selectpicker form-control">
                                                        <option value="not_use" {if $order_status->status_1c == ''}selected=""{/if}>{$btr->order_settings_1c_not_use|escape}</option>
                                                        <option value="new" {if $order_status->status_1c == 'new'}selected=""{/if}>{$btr->order_settings_1c_new|escape}</option>
                                                        <option value="accepted" {if $order_status->status_1c == 'accepted'}selected=""{/if}>{$btr->order_settings_1c_accepted|escape}</option>
                                                        <option value="to_delete" {if $order_status->status_1c == 'to_delete'}selected=""{/if}>{$btr->order_settings_1c_to_delete|escape}</option>
                                                    </select>
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                {/foreach}
                            {/if}
                        </div>

                        {*Блок массовых действий*}
                        <div class="okay_list_footer">
                            <div class="okay_list_foot_left"></div>
                            <button type="submit" value="labels" class="btn btn_small btn_blue">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>

                    <div class="row mt-1">
                        <div class="col-lg-12 col-md-12">
                            <div class="boxed fn_toggle_wrap ">
                                <div class="heading_box">
                                    {$btr->okaycms__integration_ic__settings|escape}
                                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                                    </div>
                                </div>
                                {*Параметры элемента*}
                                <div class="toggle_body_wrap on fn_card">
                                    <div class="row">
                                        <div class="fn_step-1 col-lg-4 col-md-6">
                                            <div class="heading_label">{$btr->okaycms__integration_ic__settings_brandOptionName|escape}</div>
                                            <div class="mb-1">
                                                <input name="integration1cBrandOptionName" placeholder="Производитель" class="form-control" type="text" value="{$settings->integration1cBrandOptionName|escape}" />
                                            </div>
                                        </div>
                                        <div class="fn_step-2 col-lg-4 col-md-6">
                                            <div class="heading_label">{$btr->okaycms__integration_ic__settings_guidPriceFrom1C|escape}</div>
                                            <div class="mb-1">
                                                <input name="integration1cGuidPriceFrom1C" class="form-control" type="text" value="{$settings->integration1cGuidPriceFrom1C|escape}" />
                                            </div>
                                        </div>
                                        <div class="fn_step-3 col-lg-4 col-md-6">
                                            <div class="heading_label">{$btr->okaycms__integration_ic__settings_guidComparePriceFrom1C|escape}</div>
                                            <div class="mb-1">
                                                <input name="integration1cGuidComparePriceFrom1C" class="form-control" type="text" value="{$settings->integration1cGuidComparePriceFrom1C|escape}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="permission_block">
                                        <div class="permission_boxes row">
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>{$btr->okaycms__integration_ic__settings_fullUpdate|escape}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cFullUpdate" value='1' type="checkbox" {if $settings->integration1cFullUpdate || !$settings->has('integration1cFullUpdate')}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>{$btr->okaycms__integration_ic__settings_onlyEnabledCurrencies|escape}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cOnlyEnabledCurrencies" value='1' type="checkbox" {if $settings->integration1cOnlyEnabledCurrencies}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>{$btr->okaycms__integration_ic__settings_stockFrom1c|escape}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cStockFrom1c" value='1' type="checkbox" {if $settings->integration1cStockFrom1c || !$settings->has('integration1cStockFrom1c')}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>{$btr->okaycms__integration_ic__settings_importProductsOnly|escape}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cImportProductsOnly" value='1' type="checkbox" {if $settings->integration1cImportProductsOnly}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>{$btr->okaycms__integration_ic__settings_eraseComparePrice|escape}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cEraseComparePrice" value='1' type="checkbox" {if $settings->integration1cEraseComparePrice}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>{$btr->okaycms__integration_ic__settings_eraseComparePriceEqual|escape}</span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cEraseComparePriceEqual" value='1' type="checkbox" {if $settings->integration1cEraseComparePriceEqual}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-xl-12 col-lg-12 col-md-12">
                                                <div class="permission_box permission_box--long">
                                                    <span>
                                                        {$btr->okaycms__integration_1c__settings_exportPurchasesDiscountsSeparate|escape}
                                                        <i class="fn_tooltips" title="{$btr->okaycms__integration_1c__settings_exportPurchasesDiscountsSeparate_tooltip|escape}">
                                                            {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                                        </i>
                                                    </span>
                                                    <label class="switch switch-default">
                                                        <input class="switch-input" name="integration1cExportPurchasesDiscountsSeparate" value='1' type="checkbox" {if $settings->integration1cExportPurchasesDiscountsSeparate}checked=""{/if}/>
                                                        <span class="switch-label"></span>
                                                        <span class="switch-handle"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-lg-12 col-md-12 ">
                                            <button type="submit" class="btn btn_small btn_blue float-md-right" type="submit" name="save" value="1">
                                                {include file='svg_icon.tpl' svgId='checked'}
                                                <span>{$btr->general_apply|escape}</span>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
