<?php
/* Smarty version 3.1.40, created on 2025-11-26 12:57:11
  from '/var/www/html/Okay/Modules/OkayCMS/NovaposhtaCost/Backend/design/html/novaposhta_cost.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926dd07ecd103_99570335',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '39fe53ea94dc84889d7742f44ee964c2416a9104' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/NovaposhtaCost/Backend/design/html/novaposhta_cost.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 11,
  ),
),false)) {
function content_6926dd07ecd103_99570335 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', $_smarty_tpl->tpl_vars['btr']->value->settings_np ,false ,32);?>

<style>
    @media (min-width: 1200px) and (max-width: 1400px) {
        .col-xxl-6{
            width: 100%;
        }
    }
</style>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->settings_np, ENT_QUOTES, 'UTF-8', true);?>
</div>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--success">
                <div class="alert__content">
                    <div class="alert__title">
                        <?php if ($_smarty_tpl->tpl_vars['message_success']->value == 'saved') {?>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_settings_saved, ENT_QUOTES, 'UTF-8', true);?>

                        <?php }?>
                    </div>
                </div>
                <?php if ($_GET['return']) {?>
                <a class="alert__button" href="<?php echo $_GET['return'];?>
">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'return'), 0, false);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_back, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </a>
                <?php }?>
            </div>
        </div>
    </div>
<?php }?>

<div class="row d_flex">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->alert_description, ENT_QUOTES, 'UTF-8', true);?>
</div>
                <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->settings_np__description, ENT_QUOTES, 'UTF-8', true);?>
</p>
            </div>
        </div>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['settings']->value->np_api_key_error) {?>
    <div class="row d_flex">
        <div class="col-lg-12 col-md-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_api_key_error, ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                    <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->np_api_key_error, ENT_QUOTES, 'UTF-8', true);?>
</p>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<?php if (!$_smarty_tpl->tpl_vars['uah_currency']->value) {?>
    <div class="row d_flex">
        <div class="col-lg-12 col-md-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_uah_currency_not_exists, ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                    <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_uah_currency_not_exists_text, ENT_QUOTES, 'UTF-8', true);?>
</p>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">

    <div class="row row--xxl">
        <div class="col-lg-6 col-md-12 pr-0">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->settings_np_options, ENT_QUOTES, 'UTF-8', true);?>

                </div>
                                <div class="toggle_body_wrap on fn_card" >
                    <div class="row">
                        <div class="col-xxl-6 col-lg-6 col-md-12">
                            <div class="heading_label">
                                <a href="https://my.novaposhta.ua/settings/index#apikeys" target="_blank"><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_key;?>
</a>
                                <i class="fn_tooltips" title='<?php echo $_smarty_tpl->tpl_vars['btr']->value->tooltip_settings_np_api;?>
'>
                                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_tooltips'), 0, true);
?>
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="text" name="newpost_key" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->newpost_key, ENT_QUOTES, 'UTF-8', true);?>
" class="form-control">
                            </div>
                        </div>
                        <div class="col-xxl-6 col-lg-6 col-md-12">
                            <div class="heading_label heading_label--required">
                                <span><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_city;?>
</span>
                                <i class="fn_tooltips" title='<?php echo $_smarty_tpl->tpl_vars['btr']->value->tooltip_settings_np_city;?>
'>
                                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_tooltips'), 0, true);
?>
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="text" class="fn_newpost_city_name form-control" name="newpost_city_name" value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'newpost_city' ][ 0 ], array( $_smarty_tpl->tpl_vars['settings']->value->newpost_city ));?>
">
                                <input type="hidden" name="newpost_city" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->newpost_city, ENT_QUOTES, 'UTF-8', true);?>
">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="heading_label heading_label--required">
                                <span><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_weight;?>
</span>
                                <i class="fn_tooltips" title='<?php echo $_smarty_tpl->tpl_vars['btr']->value->tooltip_settings_np_weight;?>
'>
                                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_tooltips'), 0, true);
?>
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="number" name="newpost_weight" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->newpost_weight, ENT_QUOTES, 'UTF-8', true);?>
" required min="0.1" max="1000" step="0.1" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-12">
                            <div class="heading_label"><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_volume;?>

                                <i class="fn_tooltips" title='<?php echo $_smarty_tpl->tpl_vars['btr']->value->tooltip_settings_np_volume;?>
'>
                                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_tooltips'), 0, true);
?>
                                </i>
                            </div>
                            <div class="mb-1">
                                <input type="number" name="newpost_volume" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->newpost_volume, ENT_QUOTES, 'UTF-8', true);?>
" min="0.001" max="1000" step="any" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mt-1">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch okay_switch--nowrap clearfix">
                                    <label class="switch switch-default mr-1">
                                        <input class="switch-input" name="newpost_use_volume" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->newpost_use_volume) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label mr-0"><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_include_volume;?>
</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 mt-2">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch okay_switch--nowrap clearfix">
                                    <label class="switch switch-default mr-1">
                                        <input class="switch-input" name="newpost_use_assessed_value" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->newpost_use_assessed_value) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label mr-0"><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_include_assessed;?>
</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 mt-1">
                            <div class="heading_box">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_delivery_types_head, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                            <div class="variants_wrapper fn_card">
                                <div class="okay_list variants_list scrollbar-variant">
                                    <div class="okay_list_body sortable delivery_types_list_add">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveryTypes']->value, 'deliveryType', false, 'key');
$_smarty_tpl->tpl_vars['deliveryType']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['deliveryType']->value) {
$_smarty_tpl->tpl_vars['deliveryType']->do_else = false;
?>
                                            <div class="okay_list_body_item variants_list_item delivery_types_list_item">
                                                <div class="okay_list_row ">
                                                    <div class="okay_list_boding variants_item_drag">
                                                        <div class="heading_label"></div>
                                                        <div class="move_zone">
                                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_option_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                        <input name="delivery_types[id][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['deliveryType']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" />
                                                        <input class="variant_input" name="delivery_types[name][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['deliveryType']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" />
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['warehousesTypesDTO']->value, 'warehouseTypesDTO', false, 'warehouseKey');
$_smarty_tpl->tpl_vars['warehouseTypesDTO']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['warehouseKey']->value => $_smarty_tpl->tpl_vars['warehouseTypesDTO']->value) {
$_smarty_tpl->tpl_vars['warehouseTypesDTO']->do_else = false;
?>
                                                            <div>
                                                                <input id="delivery_type_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['warehouseKey']->value;?>
" type="checkbox" name="delivery_types[warehouses_type_refs][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
][]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef(), ENT_QUOTES, 'UTF-8', true);?>
"<?php if (in_array($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef(),$_smarty_tpl->tpl_vars['deliveryType']->value->warehouses_type_refs)) {?> checked<?php }?>>
                                                                <label for="delivery_type_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['warehouseKey']->value;?>
">
                                                                    <?php if ($_smarty_tpl->tpl_vars['manager']->value->lang == 'ru') {?>
                                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getNameRu(), ENT_QUOTES, 'UTF-8', true);?>

                                                                    <?php } else { ?>
                                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getName(), ENT_QUOTES, 'UTF-8', true);?>

                                                                    <?php }?>
                                                                    <?php if ((isset($_smarty_tpl->tpl_vars['countWarehousesByTypes']->value[$_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef()]))) {?>
                                                                        (<?php echo $_smarty_tpl->tpl_vars['countWarehousesByTypes']->value[$_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef()];?>
)
                                                                    <?php } else { ?>
                                                                        (0)
                                                                    <?php }?>
                                                                </label>
                                                            </div>
                                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                    </div>
                                                    <div class="okay_list_boding okay_list_close remove_variant">
                                                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_delete_delivery_type, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_delivery_type hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                        <div class="okay_list_body_item variants_list_item delivery_types_list_item fn_new_delivery_type hidden">
                                            <div class="okay_list_row ">
                                                <div class="okay_list_boding variants_item_drag">
                                                    <div class="heading_label"></div>
                                                    <div class="move_zone">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding">
                                                    <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_option_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                    <input name="" class="fn_field_id" type="hidden" value="" />
                                                    <input class="variant_input fn_field_name" name="" type="text" value="" />
                                                </div>
                                                <div class="okay_list_boding">
                                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['warehousesTypesDTO']->value, 'warehouseTypesDTO', false, 'key');
$_smarty_tpl->tpl_vars['warehouseTypesDTO']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['warehouseTypesDTO']->value) {
$_smarty_tpl->tpl_vars['warehouseTypesDTO']->do_else = false;
?>
                                                        <div>
                                                            <input type="checkbox" class="fn_type_checkbox" name="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef(), ENT_QUOTES, 'UTF-8', true);?>
" data-key="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
">
                                                            <label class="fn_type_label">
                                                                <?php if ($_smarty_tpl->tpl_vars['manager']->value->lang == 'ru') {?>
                                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getNameRu(), ENT_QUOTES, 'UTF-8', true);?>

                                                                <?php } else { ?>
                                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getName(), ENT_QUOTES, 'UTF-8', true);?>

                                                                <?php }?>
                                                                <?php if ((isset($_smarty_tpl->tpl_vars['countWarehousesByTypes']->value[$_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef()]))) {?>
                                                                    (<?php echo $_smarty_tpl->tpl_vars['countWarehousesByTypes']->value[$_smarty_tpl->tpl_vars['warehouseTypesDTO']->value->getTypeRef()];?>
)
                                                                <?php } else { ?>
                                                                    (0)
                                                                <?php }?>
                                                            </label>
                                                        </div>
                                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                </div>
                                                <div class="okay_list_boding okay_list_close remove_variant">
                                                    <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_delete_delivery_type, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_delivery_type hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box_btn_heading mt-1">
                                    <button type="button" class="btn btn_mini btn-secondary fn_add_delivery_type">
                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'plus'), 0, true);
?>
                                        <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_add_delivery_type, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-md-12 mt-1">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                                <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12">
            <div class="boxed">
                <div class="heading_box">
                    <?php echo $_smarty_tpl->tpl_vars['btr']->value->np_warehouses_data_info;?>

                </div>
                <div class="">

                    <?php if ($_smarty_tpl->tpl_vars['settings']->value->np_last_update_warehouses_date) {?>
                    <div class="text_green text_600">
                        <div class="mb-1">
                            <?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_update_date;?>

                            <strong><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['last_update_date']->value ));?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'time' ][ 0 ], array( $_smarty_tpl->tpl_vars['last_update_date']->value ));?>
</strong>
                        </div>
                    </div>
                    <?php }?>

                    <div class="mt-2 mb-2">
                        <p class="mt-2 mb-2"><?php echo $_smarty_tpl->tpl_vars['btr']->value->settings_np_update_label;?>
</p>
                        <div class="fn_progress_block"></div>
                        <div class="flex_np_update">

                            <div class="flex_np_update__btn">
                                <button type="button" class="btn btn_small btn-warning fn_update_cache"
                                        data-cancel_text="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_cancel_update_cache, ENT_QUOTES, 'UTF-8', true);?>
"
                                        data-resume_text="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_update_cache_now, ENT_QUOTES, 'UTF-8', true);?>
">
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_update_cache_now, ENT_QUOTES, 'UTF-8', true);?>

                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert--icon alert--warning mt-2 mb-0">
                        <div class="alert__content" style="line-height: 1.4">
                            <div class="alert__title"><?php echo $_smarty_tpl->tpl_vars['btr']->value->np_cron_update_cache_title;?>
</div>
                            <?php echo $_smarty_tpl->tpl_vars['btr']->value->np_cron_update_cache_1;?>

                            "<a href=""  class="fn_clipboard hint-bottom-middle-t-info-s-small-mobile" data-hint="Click to copy" data-hint-copied="✔ Copied to clipboard">php <?php echo $_smarty_tpl->tpl_vars['config']->value->root_dir;?>
ok scheduler:run</a>"
                            <?php echo $_smarty_tpl->tpl_vars['btr']->value->np_cron_update_cache_2;?>

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
                    <div class="heading_box"><?php echo $_smarty_tpl->tpl_vars['btr']->value->payment_np_cash_on_delivery_type;?>
</div>
                    <div class="okay_list products_list fn_sort_list">
                                                <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_photo"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_photo, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_brands_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->payment_np_payment_method_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_close"></div>
                            <div class="okay_list_heading okay_list_setting"></div>
                            <div class="okay_list_heading okay_list_status" style="width: 200px;"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->payment_np_cash_on_delivery, ENT_QUOTES, 'UTF-8', true);?>
</div>
                        </div>
                        <div class="okay_list_body sort_extended">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['payment_methods']->value, 'payment_method');
$_smarty_tpl->tpl_vars['payment_method']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['payment_method']->value) {
$_smarty_tpl->tpl_vars['payment_method']->do_else = false;
?>
                                <div class="fn_step-1 fn_row okay_list_body_item fn_sort_item">
                                    <div class="okay_list_row ">
                                        <div class="okay_list_boding okay_list_drag"></div>
                                        <div class="okay_list_boding okay_list_photo">
                                            <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->image) {?>
                                                <img src="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'resize' ][ 0 ], array( $_smarty_tpl->tpl_vars['payment_method']->value->image,55,55,false,$_smarty_tpl->tpl_vars['config']->value->resized_payments_dir ));?>
" alt="" /></a>
                                            <?php } else { ?>
                                                <img height="55" width="55" src="design/images/no_image.png"/>
                                            <?php }?>
                                        </div>
                                        <div class="okay_list_boding okay_list_brands_name">
                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_method']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                        </div>
                                        <div class="okay_list_boding okay_list_close"></div>
                                        <div class="okay_list_setting"></div>

                                        <div class="okay_list_boding okay_list_status" style="width: 200px;">
                                            <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->novaposhta_cost__cash_on_delivery) {?>fn_active_class<?php }?>" data-controller="payment" data-action="novaposhta_cost__cash_on_delivery" data-id="<?php echo $_smarty_tpl->tpl_vars['payment_method']->value->id;?>
" name="novaposhta_cost__cash_on_delivery" value="1" type="checkbox"  <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->novaposhta_cost__cash_on_delivery) {?>checked=""<?php }?>/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/backend/design/js/piecon/piecon.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/backend/design/js/autocomplete/jquery.autocomplete-min.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
>
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
                        $('.fn_update_cache').text('<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_update_cache_finished, ENT_QUOTES, 'UTF-8', true);?>
');
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
    let lastDeliveryTypeIndex = <?php echo count($_smarty_tpl->tpl_vars['deliveryTypes']->value);?>
;
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

<?php echo '</script'; ?>
>

<?php }
}
