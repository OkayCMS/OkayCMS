{$meta_title = $btr->settings_np scope=global}

<style>
    @media (min-width: 1200px) and (max-width: 1400px) {
        .col-xxl-6{
            width: 100%;
        }
    }
</style>

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->settings_np|escape}</div>
    </div>
</div>

{*Вывод успешных сообщений*}
{if $message_success}
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--success">
                <div class="alert__content">
                    <div class="alert__title">
                        {if $message_success == 'saved'}
                        {$btr->general_settings_saved|escape}
                        {/if}
                    </div>
                </div>
                {if $smarty.get.return}
                <a class="alert__button" href="{$smarty.get.return}">
                    {include file='svg_icon.tpl' svgId='return'}
                    <span>{$btr->general_back|escape}</span>
                </a>
                {/if}
            </div>
        </div>
    </div>
{/if}

<div class="row d_flex">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->alert_description|escape}</div>
                <p>{$btr->settings_np__description|escape}</p>
            </div>
        </div>
    </div>
</div>

{if $settings->np_api_key_error}
    <div class="row d_flex">
        <div class="col-lg-12 col-md-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {$btr->np_api_key_error|escape}
                    </div>
                    <p>{$settings->np_api_key_error|escape}</p>
                </div>
            </div>
        </div>
    </div>
{/if}

{if !$uah_currency}
    <div class="row d_flex">
        <div class="col-lg-12 col-md-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        {$btr->np_uah_currency_not_exists|escape}
                    </div>
                    <p>{$btr->np_uah_currency_not_exists_text|escape}</p>
                </div>
            </div>
        </div>
    </div>
{/if}

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    <div class="row row--xxl">
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_np_options|escape}
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card" >
                    <div class="row">
                        <div class="col-xxl-6 col-lg-6 col-md-12">
                            <div class="heading_label">
                                <a href="https://my.novaposhta.ua/settings/index#apikeys" target="_blank">{$btr->settings_np_key}</a>
                                <i class="fn_tooltips" title='{$btr->tooltip_settings_np_api}'>
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="text" name="newpost_key" value="{$settings->newpost_key|escape}" class="form-control">
                            </div>
                        </div>
                        <div class="col-xxl-6 col-lg-6 col-md-12">
                            <div class="heading_label heading_label--required">
                                <span>{$btr->settings_np_city}</span>
                                <i class="fn_tooltips" title='{$btr->tooltip_settings_np_city}'>
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="text" class="fn_newpost_city_name form-control" name="newpost_city_name" value="{$settings->newpost_city|newpost_city}">
                                <input type="hidden" name="newpost_city" value="{$settings->newpost_city|escape}">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="heading_label heading_label--required">
                                <span>{$btr->settings_np_weight}</span>
                                <i class="fn_tooltips" title='{$btr->tooltip_settings_np_weight}'>
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="number" name="newpost_weight" value="{$settings->newpost_weight|escape}" required min="0.1" max="1000" step="0.1" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="heading_label">{$btr->settings_np_volume}
                                <i class="fn_tooltips" title='{$btr->tooltip_settings_np_volume}'>
                                    {include file='svg_icon.tpl' svgId='icon_tooltips'}
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="number" name="newpost_volume" value="{$settings->newpost_volume|escape}" min="0.001" max="1000" step="any" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mt-1">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch okay_switch--nowrap clearfix">
                                    <label class="switch switch-default mr-1">
                                        <input class="switch-input" name="newpost_use_volume" value='1' type="checkbox" {if $settings->newpost_use_volume}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label mr-0">{$btr->settings_np_include_volume}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 mt-2">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch okay_switch--nowrap clearfix">
                                    <label class="switch switch-default mr-1">
                                        <input class="switch-input" name="newpost_use_assessed_value" value='1' type="checkbox" {if $settings->newpost_use_assessed_value}checked=""{/if}/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label mr-0">{$btr->settings_np_include_assessed}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 mt-1">
                            <div class="heading_box">
                                {$btr->np_delivery_types_head|escape}
                            </div>
                            <div class="variants_wrapper fn_card">
                                <div class="okay_list variants_list scrollbar-variant">
                                    <div class="okay_list_body sortable delivery_types_list_add">
                                        {foreach $deliveryTypes as $key => $deliveryType}
                                            <div class="okay_list_body_item variants_list_item delivery_types_list_item">
                                                <div class="okay_list_row ">
                                                    <div class="okay_list_boding variants_item_drag">
                                                        <div class="heading_label"></div>
                                                        <div class="move_zone">
                                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label">{$btr->general_option_name|escape}</div>
                                                        <input name="delivery_types[id][{$key}]" type="hidden" value="{$deliveryType->id|escape}" />
                                                        <input class="variant_input" name="delivery_types[name][{$key}]" type="text" value="{$deliveryType->name|escape}" />
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        {foreach $warehousesTypesDTO as $warehouseKey => $warehouseTypesDTO}
                                                            <div>
                                                                <input id="delivery_type_{$key}_{$warehouseKey}" type="checkbox" name="delivery_types[warehouses_type_refs][{$key}][]" value="{$warehouseTypesDTO->getTypeRef()|escape}"{if in_array($warehouseTypesDTO->getTypeRef(), $deliveryType->warehouses_type_refs)} checked{/if}>
                                                                <label for="delivery_type_{$key}_{$warehouseKey}">
                                                                    {if $manager->lang == 'ru'}
                                                                        {$warehouseTypesDTO->getNameRu()|escape}
                                                                    {else}
                                                                        {$warehouseTypesDTO->getName()|escape}
                                                                    {/if}
                                                                    {if isset($countWarehousesByTypes[$warehouseTypesDTO->getTypeRef()])}
                                                                        ({$countWarehousesByTypes[$warehouseTypesDTO->getTypeRef()]})
                                                                    {else}
                                                                        (0)
                                                                    {/if}
                                                                </label>
                                                            </div>
                                                        {/foreach}
                                                    </div>
                                                    <div class="okay_list_boding okay_list_close remove_variant">
                                                        <button data-hint="{$btr->np_delete_delivery_type|escape}" type="button" class="btn_close fn_remove_delivery_type hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                            {include file='svg_icon.tpl' svgId='delete'}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                        <div class="okay_list_body_item variants_list_item delivery_types_list_item fn_new_delivery_type hidden">
                                            <div class="okay_list_row ">
                                                <div class="okay_list_boding variants_item_drag">
                                                    <div class="heading_label"></div>
                                                    <div class="move_zone">
                                                        {include file='svg_icon.tpl' svgId='drag_vertical'}
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding">
                                                    <div class="heading_label">{$btr->general_option_name|escape}</div>
                                                    <input name="" class="fn_field_id" type="hidden" value="" />
                                                    <input class="variant_input fn_field_name" name="" type="text" value="" />
                                                </div>
                                                <div class="okay_list_boding">
                                                    {foreach $warehousesTypesDTO as $key => $warehouseTypesDTO}
                                                        <div>
                                                            <input type="checkbox" class="fn_type_checkbox" name="" value="{$warehouseTypesDTO->getTypeRef()|escape}" data-key="{$key}">
                                                            <label class="fn_type_label">
                                                                {if $manager->lang == 'ru'}
                                                                    {$warehouseTypesDTO->getNameRu()|escape}
                                                                {else}
                                                                    {$warehouseTypesDTO->getName()|escape}
                                                                {/if}
                                                                {if isset($countWarehousesByTypes[$warehouseTypesDTO->getTypeRef()])}
                                                                    ({$countWarehousesByTypes[$warehouseTypesDTO->getTypeRef()]})
                                                                {else}
                                                                    (0)
                                                                {/if}
                                                            </label>
                                                        </div>
                                                    {/foreach}
                                                </div>
                                                <div class="okay_list_boding okay_list_close remove_variant">
                                                    <button data-hint="{$btr->np_delete_delivery_type|escape}" type="button" class="btn_close fn_remove_delivery_type hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                        {include file='svg_icon.tpl' svgId='delete'}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box_btn_heading mt-1">
                                    <button type="button" class="btn btn_mini btn-secondary fn_add_delivery_type">
                                        {include file='svg_icon.tpl' svgId='plus'}
                                        <span>{$btr->np_add_delivery_type|escape}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 mt-1">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
                                {include file='svg_icon.tpl' svgId='checked'}
                                <span>{$btr->general_apply|escape}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="boxed">
                <div class="heading_box">
                    {$btr->np_warehouses_data_info}
                </div>
                <div class="">

                    {if $settings->np_last_update_warehouses_date}
                    <div class="text_green text_600">
                        <div class="mb-1">
                            {$btr->settings_np_update_date}
                            <strong>{$last_update_date|date} {$last_update_date|time}</strong>
                        </div>
                    </div>
                    {/if}

                    <div class="mt-2 mb-2">
                        <p class="mt-2 mb-2">{$btr->settings_np_update_label}</p>
                        <div class="fn_progress_block"></div>
                        <div class="flex_np_update">

                            <div class="flex_np_update__btn">
                                <button type="button" class="btn btn_small btn-warning fn_update_cache"
                                        data-cancel_text="{$btr->np_cancel_update_cache|escape}"
                                        data-resume_text="{$btr->np_update_cache_now|escape}">
                                    {$btr->np_update_cache_now|escape}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert--icon alert--warning mt-2 mb-0">
                        <div class="alert__content" style="line-height: 1.4">
                            <div class="alert__title">{$btr->np_cron_update_cache_title}</div>
                            {$btr->np_cron_update_cache_1}
                            "<a href=""  class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">php {$config->root_dir}ok scheduler:run</a>"
                            {$btr->np_cron_update_cache_2}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="toggle_body_wrap on fn_card">
                    <div class="heading_box">{$btr->payment_np_cash_on_delivery_type}</div>
                    <div class="okay_list products_list fn_sort_list">
                        {*Шапка таблицы*}
                        <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_photo">{$btr->general_photo|escape}</div>
                            <div class="okay_list_heading okay_list_brands_name">{$btr->payment_np_payment_method_name|escape}</div>
                            <div class="okay_list_heading okay_list_close"></div>
                            <div class="okay_list_heading okay_list_setting"></div>
                            <div class="okay_list_heading okay_list_status" style="width: 200px;">{$btr->payment_np_cash_on_delivery|escape}</div>
                        </div>
                        <div class="okay_list_body sort_extended">
                            {foreach $payment_methods as $payment_method}
                                <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                                    <div class="okay_list_row ">
                                        <div class="okay_list_boding okay_list_drag"></div>
                                        <div class="okay_list_boding okay_list_photo">
                                            {if $payment_method->image}
                                                <img src="{$payment_method->image|resize:55:55:false:$config->resized_payments_dir}" alt="" /></a>
                                            {else}
                                                <img height="55" width="55" src="design/images/no_image.png"/>
                                            {/if}
                                        </div>
                                        <div class="okay_list_boding okay_list_brands_name">
                                            {$payment_method->name|escape}
                                        </div>
                                        <div class="okay_list_boding okay_list_close"></div>
                                        <div class="okay_list_setting"></div>

                                        <div class="okay_list_boding okay_list_status" style="width: 200px;">
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action {if $payment_method->novaposhta_cost__cash_on_delivery}fn_active_class{/if}" data-controller="payment" data-action="novaposhta_cost__cash_on_delivery" data-id="{$payment_method->id}" name="novaposhta_cost__cash_on_delivery" value="1" type="checkbox"  {if $payment_method->novaposhta_cost__cash_on_delivery}checked=""{/if}/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="{$rootUrl}/backend/design/js/piecon/piecon.js"></script>
<script src="{$rootUrl}/backend/design/js/autocomplete/jquery.autocomplete-min.js"></script>
{literal}
<script>
    sclipboard();

    $( ".fn_newpost_city_name" ).devbridgeAutocomplete( {
        serviceUrl: okay.router['OkayCMS_NovaposhtaCost_find_city'],
        minChars: 1,
        maxHeight: 320,
        noCache: true,
        onSelect: function(suggestion) {
            $('[name="newpost_city"]').val(suggestion.data.ref)
        },
        formatResult: function(suggestion, currentValue) {
            var reEscape = new RegExp( '(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join( '|\\' ) + ')', 'g' );
            var pattern = '(' + currentValue.replace( reEscape, '\\$1' ) + ')';
            return "<span>" + suggestion.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + "<\/span>";
        }
    } );

    function doUpdate(page, importItem, isLast, signal)
    {
        if (signal.aborted) {
            importItem.reject('cancel');
            return;
        }
        page = typeof(page) != 'undefined' ? page : 1;
        let data = {
            updatePage: page,
            updateType: importItem.updateType
        };

        for (let paramKey in importItem.updateParams) {
            data[paramKey] = importItem.updateParams[paramKey];
        }

        $.ajax({
            url: "/backend/index.php?controller=OkayCMS.NovaposhtaCost.NovaposhtaCostAdmin@updateData",
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.hasOwnProperty('error')) {
                    importItem.progressBlock.find('.np_import_result')
                        .text(data.error)
                        .addClass('alert--error alert')
                        .removeClass('np_import_result')
                        .css('padding', '5px')
                        .show();
                    importItem.progressItem.hide();
                    importItem.resolve('result');
                } else if (data.hasOwnProperty('pagesNum') && data.pagesNum > 0 && page < data.pagesNum) {
                    importItem.progressItem.attr('value', Math.round(100 * page / (data.pagesNum + 1)));
                    Piecon.setProgress(Math.round(100 * page / (data.pagesNum + 1)));
                    doUpdate(++page, importItem, isLast, signal);
                } else {
                    importItem.progressItem.attr('value', Math.round(100 * page / (data.pagesNum + 1)));
                    Piecon.setProgress(Math.round(100 * page / (data.pagesNum + 1)));
                    finalUpdate(importItem, isLast)
                }
            },
            error: function(xhr, status, errorThrown) {
                alert(errorThrown+'\n'+xhr.responseText);
            }
        });
    }

    function finalUpdate(importItem, isLast)
    {
        let data = {
            removeType: importItem.updateType,
        };
        for (let paramKey in importItem.updateParams) {
            data[paramKey] = importItem.updateParams[paramKey];
        }
        $.ajax({
            url: "/backend/index.php?controller=OkayCMS.NovaposhtaCost.NovaposhtaCostAdmin@finalImport",
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.hasOwnProperty('error')) {
                    importItem.progressBlock.find('.np_import_result')
                        .text(data.error)
                        .addClass('alert--error alert')
                        .removeClass('np_import_result')
                        .css('padding', '5px')
                        .show();
                    importItem.progressItem.hide();
                    importItem.resolve('result');
                } else {
                    Piecon.setProgress(100);
                    importItem.progressItem.attr('value', 100).hide();
                    importItem.progressBlock.find('.np_import_result').fadeIn(500);
                    if (isLast) {
                        $('.fn_update_cache').text('{/literal}{$btr->np_update_cache_finished|escape}{literal}');
                    }
                    importItem.resolve('result');
                }
            },
            error: function(xhr, status, errorThrown) {
                alert(errorThrown+'\n'+xhr.responseText);
            }
        });
    }

    let controller = new AbortController();
    let signal = controller.signal;

    $(document).on('click', '.fn_update_cache', function () {
        let button = $(this);

        if (button.hasClass('running')) {
            button.text(button.data('resume_text')).removeClass('running');
            $('.fn_progress_block').html('');
            controller.abort();
            return;
        } else {
            controller = new AbortController();
            signal = controller.signal;
            button.prop('disabled', true);
        }

        $.ajax({
            url: "/backend/index.php?controller=OkayCMS.NovaposhtaCost.NovaposhtaCostAdmin@getUpdateTypes",
            dataType: 'json',
            success: function(data) {
                if (data.hasOwnProperty('updateTypes')) {
                    button.text(button.data('cancel_text'))
                        .addClass('running')
                        .prop('disabled', false);

                    function initFunction(updateElement, isLast, signal) {
                        if (signal.aborted) {
                            return Promise.reject('cancel');
                        }
                        return new Promise((resolve, reject) => {
                            updateElement.resolve = resolve;
                            updateElement.reject = reject;
                            Piecon.setOptions({fallback: 'force'});
                            Piecon.setProgress(0);
                            doUpdate(1, updateElement, isLast, signal);
                        });
                    }
                    let initPromise = initFunction;

                    for (let key in data.updateTypes) {
                        let updateType = data.updateTypes[key];
                        let updateElement = {
                            progressItem: $('<progress id="progressbar_' + key + '" class="progress progress-xs progress-info"  value="0" max="100">sdfsdfsd</progress>'),
                            progressBlock: $('<div><p class="mb-0">' + updateType['updateName'] + '</p><div class="np_import_result" style="display: none"></div></div>'),
                            updateType: updateType['updateType'],
                            updateParams: updateType['updateParams'],
                            initProgress: function () {
                                this.progressItem.appendTo(this.progressBlock);
                                this.progressBlock.appendTo('.fn_progress_block');
                            }
                        };
                        updateElement.initProgress();
                        let isLast = key == data.updateTypes.length - 1;
                        if (key == 0) {
                            initPromise = initPromise(updateElement, isLast, signal)
                                .catch(e => {
                                    Piecon.reset();
                                });
                        } else {
                            initPromise = initPromise.then(
                                result => initFunction(updateElement, isLast, signal),
                            ).catch(e => {
                                Piecon.reset();
                            });
                        }
                    }
                }
            },
            error: function(xhr, status, errorThrown) {
                alert(errorThrown+'\n'+xhr.responseText);
            }
        });
    });

    $(document).on('click', '.fn_remove_delivery_type', function () {
        $(this).closest('.delivery_types_list_item').fadeOut(200).remove();
    });

    // Додавання типу доставки
    let delivery_type = $('.fn_new_delivery_type').clone(false).removeClass('hidden');
    let lastDeliveryTypeIndex = {/literal}{$deliveryTypes|count}{literal};
    $(".fn_new_delivery_type").remove();
    $(document).on('click', '.fn_add_delivery_type', function () {
        let delivery_type_clone = delivery_type.clone(true);
        delivery_type_clone.removeClass('hidden').removeClass('fn_new_delivery_type');
        delivery_type_clone.find('.fn_field_id').prop('name', 'delivery_types[id][' + lastDeliveryTypeIndex + ']')
        delivery_type_clone.find('.fn_field_name').prop('name', 'delivery_types[name][' + lastDeliveryTypeIndex + ']')
        delivery_type_clone.find('.fn_type_checkbox').each(function () {

            let parent = $(this).parent();
            let key = $(this).data('key');
            $(this).prop(
                'name',
                'delivery_types[warehouses_type_refs][' + lastDeliveryTypeIndex + '][]'
            ).prop(
                'id',
                'delivery_type_' + lastDeliveryTypeIndex + '_' + key
            );
            parent.find('.fn_type_label').prop(
                'for',
                'delivery_type_' + lastDeliveryTypeIndex + '_' + key
            );
        });

        $(".delivery_types_list_add").append(delivery_type_clone);
        lastDeliveryTypeIndex++;
        return false;
    });

</script>
{/literal}
