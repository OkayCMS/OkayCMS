<?php
/* Smarty version 3.1.40, created on 2025-11-26 12:51:09
  from '/var/www/html/Okay/Modules/OkayCMS/DeliveryFields/Backend/design/html/delivery_fields.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926db9dc29803_07945618',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e8a9ed129d90967464b1d162e459297667f01f78' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/DeliveryFields/Backend/design/html/delivery_fields.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 7,
  ),
),false)) {
function content_6926db9dc29803_07945618 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__delivery_fields__module_title, ENT_QUOTES, 'UTF-8', true) ,false ,32);?>

<div class="row">
    <div class="col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__delivery_fields__module_title, ENT_QUOTES, 'UTF-8', true);?>

            </div>
        </div>
    </div>
    <div class="col-md-12 float-xs-right"></div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="alert alert--icon">
            <div class="alert__content">
                <div class="alert__title"><?php echo $_smarty_tpl->tpl_vars['btr']->value->okay_cms__delivery_fields__module_description_title;?>
</div>
                <p><?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__delivery_fields__module_description_content, ENT_QUOTES, 'UTF-8', true));?>
</p>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title"><?php echo $_smarty_tpl->tpl_vars['btr']->value->okay_cms__delivery_fields__module_instruction_title;?>
</div>
                <p><?php echo nl2br(htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__delivery_fields__module_instruction_content, ENT_QUOTES, 'UTF-8', true));?>
</p>
            </div>
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

<form method="post" enctype="multipart/form-data">
    <input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">

    <div class="row row--xxl">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->settings_np_options, ENT_QUOTES, 'UTF-8', true);?>

                </div>
                                <div class="toggle_body_wrap on fn_card" >
                    <div class="row">
                        <div class="col-lg-12 col-md-12 mt-1">
                            <div class="variants_wrapper fn_card">
                                <div class="okay_list variants_list scrollbar-variant">
                                    <div class="okay_list_body sortable delivery_fields_list_add">
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveryFields']->value, 'deliveryField', false, 'key');
$_smarty_tpl->tpl_vars['deliveryField']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['deliveryField']->value) {
$_smarty_tpl->tpl_vars['deliveryField']->do_else = false;
?>
                                            <div class="okay_list_body_item variants_list_item fields_list_item">
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
                                                        <input name="delivery_fields[id][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['deliveryField']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" />
                                                        <input class="variant_input" name="delivery_fields[name][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['deliveryField']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" />
                                                    </div>
                                                    <div class="okay_list_boding df_deliveries_list">
                                                        <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->fd_field_deliveries_list, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveries']->value, 'delivery', false, 'deliveryKey');
$_smarty_tpl->tpl_vars['delivery']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['deliveryKey']->value => $_smarty_tpl->tpl_vars['delivery']->value) {
$_smarty_tpl->tpl_vars['delivery']->do_else = false;
?>
                                                            <span class="df_delivery_item form-control">
                                                            <input type="checkbox" id="delivery_field_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['deliveryKey']->value;?>
"
                                                                   name="delivery_fields[deliveries][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
][]"
                                                                   value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['delivery']->value->id, ENT_QUOTES, 'UTF-8', true);?>
"
                                                                   data-key="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
"
                                                                   <?php if (in_array($_smarty_tpl->tpl_vars['delivery']->value->id,$_smarty_tpl->tpl_vars['deliveryField']->value->deliveries)) {?>checked<?php }?>
                                                            >
                                                            <label for="delivery_field_<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['deliveryKey']->value;?>
">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['delivery']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                                            </label>
                                                        </span>
                                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->fd_field_required, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                        <div class="activity_of_switch">
                                                            <div class="activity_of_switch_item">
                                                                <div class="okay_switch clearfix">
                                                                    <label class="switch switch-default">
                                                                        <input class="switch-input"
                                                                               value='1'
                                                                               name="delivery_fields[required][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]"
                                                                               type="checkbox"
                                                                               <?php if ($_smarty_tpl->tpl_vars['deliveryField']->value->required) {?>checked<?php }?>
                                                                        />
                                                                        <span class="switch-label"></span>
                                                                        <span class="switch-handle"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding">
                                                        <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enable, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                        <div class="activity_of_switch">
                                                            <div class="activity_of_switch_item">
                                                                <div class="okay_switch clearfix">
                                                                    <label class="switch switch-default">
                                                                        <input class="switch-input"
                                                                               value='1'
                                                                               name="delivery_fields[visible][<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
]"
                                                                               type="checkbox"
                                                                               <?php if ($_smarty_tpl->tpl_vars['deliveryField']->value->visible) {?>checked<?php }?>
                                                                        />
                                                                        <span class="switch-label"></span>
                                                                        <span class="switch-handle"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="okay_list_boding okay_list_close remove_variant">
                                                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->df_delete_delivery_field, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_field hint-bottom-right-t-info-s-small-mobile hint-anim">
                                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                        <div class="okay_list_body_item variants_list_item fields_list_item fn_new_field hidden">
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
                                                <div class="okay_list_boding df_deliveries_list">
                                                    <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->fd_field_deliveries_list, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveries']->value, 'delivery', false, 'key');
$_smarty_tpl->tpl_vars['delivery']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['delivery']->value) {
$_smarty_tpl->tpl_vars['delivery']->do_else = false;
?>
                                                        <span class="df_delivery_item form-control">
                                                            <input type="checkbox" class="fn_delivery_checkbox" name="" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['delivery']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" data-key="<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
">
                                                            <label class="fn_delivery_label">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['delivery']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                                            </label>
                                                        </span>
                                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                </div>
                                                <div class="okay_list_boding">
                                                    <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->fd_field_required, ENT_QUOTES, 'UTF-8', true);?>
</div>
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
                                                    <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enable, ENT_QUOTES, 'UTF-8', true);?>
</div>
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
                                                    <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->np_delete_delivery_type, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_field hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="box_btn_heading mt-1">
                                    <button type="button" class="btn btn_mini btn-secondary fn_add_field">
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
    </div>
</form>

<?php echo '<script'; ?>
>
    $(document).on('click', '.fn_remove_field', function () {
        $(this).closest('.fields_list_item').fadeOut(200).remove();
    });

    // Додавання поля
    let field = $('.fn_new_field').clone(false).removeClass('hidden');
    let lastFieldsIndex = <?php echo $_smarty_tpl->tpl_vars['lastDeliveryFieldIndex']->value;?>
;
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

<?php echo '</script'; ?>
>
<?php }
}
