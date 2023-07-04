{$meta_title = $btr->okay_cms__delivery_fields__module_title|escape scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                {$btr->okay_cms__delivery_fields__module_title|escape}
            </div>
        </div>
    </div>
    <div class="col-md-12 float-xs-right"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title">{$btr->okay_cms__delivery_fields__module_description_title}</div>
                <p>{$btr->okay_cms__delivery_fields__module_description_content|escape|nl2br}</p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title">{$btr->okay_cms__delivery_fields__module_instruction_title}</div>
                <p>{$btr->okay_cms__delivery_fields__module_instruction_content|escape|nl2br}</p>
            </div>
        </div>
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

{*Главная форма страницы*}
<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="{$smarty.session.id}">

    <div class="row row--xxl">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    {$btr->settings_np_options|escape}
                </div>
                {*Параметры элемента*}
                <div class="toggle_body_wrap on fn_card" >
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mt-1">
                            <div class="variants_wrapper fn_card">
                                <div class="okay_list variants_list scrollbar-variant">
                                    <div class="okay_list_body sortable delivery_fields_list_add">
                                        {foreach $deliveryFields as $key => $deliveryField}
                                            <div class="okay_list_body_item variants_list_item fields_list_item">
                                                <div class="okay_list_row ">
                                                    <div class="okay_list_boding variants_item_drag">
                                                        <div class="heading_label"></div>
                                                        <div class="move_zone">
                                                            {include file='svg_icon.tpl' svgId='drag_vertical'}
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label">{$btr->general_option_name|escape}</div>
                                                        <input name="delivery_fields[id][{$key}]" type="hidden" value="{$deliveryField->id|escape}" />
                                                        <input class="variant_input" name="delivery_fields[name][{$key}]" type="text" value="{$deliveryField->name|escape}" />
                                                    </div>
                                                    <div class="okay_list_boding df_deliveries_list">
                                                        <div class="heading_label">{$btr->fd_field_deliveries_list|escape}</div>
                                                        {foreach $deliveries as $deliveryKey => $delivery}
                                                            <span class="df_delivery_item form-control">
                                                            <input type="checkbox" id="delivery_field_{$key}_{$deliveryKey}"
                                                                   name="delivery_fields[deliveries][{$key}][]"
                                                                   value="{$delivery->id|escape}"
                                                                   data-key="{$key}"
                                                                   {if in_array($delivery->id, $deliveryField->deliveries)}checked{/if}
                                                            >
                                                            <label for="delivery_field_{$key}_{$deliveryKey}">
                                                                {$delivery->name|escape}
                                                            </label>
                                                        </span>
                                                        {/foreach}
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label">{$btr->fd_field_required|escape}</div>
                                                        <div class="activity_of_switch">
                                                            <div class="activity_of_switch_item">
                                                                <div class="okay_switch clearfix">
                                                                    <label class="switch switch-default">
                                                                        <input class="switch-input"
                                                                               value='1'
                                                                               name="delivery_fields[required][{$key}]"
                                                                               type="checkbox"
                                                                               {if $deliveryField->required}checked{/if}
                                                                        />
                                                                        <span class="switch-label"></span>
                                                                        <span class="switch-handle"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label">{$btr->general_enable|escape}</div>
                                                        <div class="activity_of_switch">
                                                            <div class="activity_of_switch_item">
                                                                <div class="okay_switch clearfix">
                                                                    <label class="switch switch-default">
                                                                        <input class="switch-input"
                                                                               value='1'
                                                                               name="delivery_fields[visible][{$key}]"
                                                                               type="checkbox"
                                                                               {if $deliveryField->visible}checked{/if}
                                                                        />
                                                                        <span class="switch-label"></span>
                                                                        <span class="switch-handle"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding okay_list_close remove_variant">
                                                        <button data-hint="{$btr->df_delete_delivery_field|escape}" type="button" class="btn_close fn_remove_field hint-bottom-right-t-info-s-small-mobile hint-anim">
                                                            {include file='svg_icon.tpl' svgId='delete'}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        {/foreach}
                                        <div class="okay_list_body_item variants_list_item fields_list_item fn_new_field hidden">
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
                                                <div class="okay_list_boding df_deliveries_list">
                                                    <div class="heading_label">{$btr->fd_field_deliveries_list|escape}</div>
                                                    {foreach $deliveries as $key => $delivery}
                                                        <span class="df_delivery_item form-control">
                                                            <input type="checkbox" class="fn_delivery_checkbox" name="" value="{$delivery->id|escape}" data-key="{$key}">
                                                            <label class="fn_delivery_label">
                                                                {$delivery->name|escape}
                                                            </label>
                                                        </span>
                                                    {/foreach}
                                                </div>
                                                <div class="okay_list_boding">
                                                    <div class="heading_label">{$btr->fd_field_required|escape}</div>
                                                    <div class="activity_of_switch">
                                                        <div class="activity_of_switch_item">
                                                            <div class="okay_switch clearfix">
                                                                <label class="switch switch-default">
                                                                    <input class="switch-input fn_field_required" value='1' type="checkbox"/>
                                                                    <span class="switch-label"></span>
                                                                    <span class="switch-handle"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding">
                                                    <div class="heading_label">{$btr->general_enable|escape}</div>
                                                    <div class="activity_of_switch">
                                                        <div class="activity_of_switch_item">
                                                            <div class="okay_switch clearfix">
                                                                <label class="switch switch-default">
                                                                    <input class="switch-input fn_field_visible" value='1' type="checkbox"/>
                                                                    <span class="switch-label"></span>
                                                                    <span class="switch-handle"></span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_close remove_variant">
                                                    <button data-hint="{$btr->np_delete_delivery_type|escape}" type="button" class="btn_close fn_remove_field hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                        {include file='svg_icon.tpl' svgId='delete'}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box_btn_heading mt-1">
                                    <button type="button" class="btn btn_mini btn-secondary fn_add_field">
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
    </div>
</form>
{literal}
<script>
    $(document).on('click', '.fn_remove_field', function () {
        $(this).closest('.fields_list_item').fadeOut(200).remove();
    });

    // Додавання поля
    let field = $('.fn_new_field').clone(false).removeClass('hidden');
    let lastFieldsIndex = {/literal}{$lastDeliveryFieldIndex}{literal};
    $(".fn_new_field").remove();
    $(document).on('click', '.fn_add_field', function () {
        let fieldClone = field.clone(true);
        fieldClone.removeClass('hidden').removeClass('fn_new_field');
        fieldClone.find('.fn_field_id').prop('name', 'delivery_fields[id][' + lastFieldsIndex + ']')
        fieldClone.find('.fn_field_name').prop('name', 'delivery_fields[name][' + lastFieldsIndex + ']')
        fieldClone.find('.fn_field_required').prop('name', 'delivery_fields[required][' + lastFieldsIndex + ']')
        fieldClone.find('.fn_field_visible').prop('name', 'delivery_fields[visible][' + lastFieldsIndex + ']')
        fieldClone.find('.fn_delivery_checkbox').each(function () {

            let parent = $(this).parent();
            let key = $(this).data('key');
            $(this).prop(
                'name',
                'delivery_fields[deliveries][' + lastFieldsIndex + '][]'
            ).prop(
                'id',
                'field_' + lastFieldsIndex + '_' + key
            );
            parent.find('.fn_delivery_label').prop(
                'for',
                'field_' + lastFieldsIndex + '_' + key
            );
        });

        $(".delivery_fields_list_add").append(fieldClone);
        lastFieldsIndex++;
        return false;
    });

</script>
{/literal}