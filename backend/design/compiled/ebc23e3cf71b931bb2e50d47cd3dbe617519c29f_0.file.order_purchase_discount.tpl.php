<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:33:18
  from '/var/www/html/backend/design/html/order_purchase_discount.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c95e453fa5_31292133',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ebc23e3cf71b931bb2e50d47cd3dbe617519c29f' => 
    array (
      0 => '/var/www/html/backend/design/html/order_purchase_discount.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 3,
  ),
),false)) {
function content_6926c95e453fa5_31292133 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="order_discounted_block fn_purchase_discounts_block" style="display: none;">
    <input class="fn_default_purchase_discounts" type="hidden" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
]" value="">
    <div class="okay_list_body order_discounted_block__inner sort_extended">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['purchase']->value->discounts, 'discount');
$_smarty_tpl->tpl_vars['discount']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['discount']->value) {
$_smarty_tpl->tpl_vars['discount']->do_else = false;
?>
            <div class="fn_row okay_list_body_item fn_sort_item">
                <div class="okay_list_row">
                    <input type="hidden" name="discount_positions[<?php echo $_smarty_tpl->tpl_vars['discount']->value->id;?>
]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value->position, ENT_QUOTES, 'UTF-8', true);?>
"/>
                    <input type="hidden" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][id][]" value="<?php echo $_smarty_tpl->tpl_vars['discount']->value->id;?>
"/>

                    <div class="okay_list_boding okay_list_drag move_zone">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                    </div>

                    <div class="okay_list_boding okay_list_order_discounted_name">
                        <div class="form_create">
                            <input  name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][name][]" class="form-control input_create text_600" type="text" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_discount_placeholder_name, ENT_QUOTES, 'UTF-8', true);?>
">
                        </div>
                        <div class="form_create">
                            <input name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][description][]" class="form-control input_create text_grey text_400 font_12" type="text" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value->description, ENT_QUOTES, 'UTF-8', true);?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value->description, ENT_QUOTES, 'UTF-8', true);?>
" placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_discount_placeholder_descr, ENT_QUOTES, 'UTF-8', true);?>
">
                        </div>
                    </div>
                    <div class="okay_list_boding okay_list_count hidden-md-down">
                        <div class="activity_of_switch">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch clearfix">
                                    <label class="switch switch-default">
                                        <input class="fn_discount_from_last_off" type="hidden" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][from_last_discount][]" value="0" <?php if ($_smarty_tpl->tpl_vars['discount']->value->fromLastDiscount) {?>disabled<?php }?>>
                                        <input class="fn_discount_from_last_on switch-input" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][from_last_discount][]" value="1" type="checkbox" <?php if ($_smarty_tpl->tpl_vars['discount']->value->fromLastDiscount) {?>checked<?php }?>>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                    <label class="switch_label m-0" >
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="okay_list_boding okay_list_price">
                        <div class="input-group">
                            <input type="text" class="form-control" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][value][]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['discount']->value->value, ENT_QUOTES, 'UTF-8', true);?>
" />
                            <input class="fn_discount_type_input <?php if ($_smarty_tpl->tpl_vars['discount']->value->type == "percent") {?> active <?php }?>" type="hidden" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][type][]" value="percent" <?php if ($_smarty_tpl->tpl_vars['discount']->value->type == "absolute") {?>disabled<?php }?>/>
                            <input class="fn_discount_type_input <?php if ($_smarty_tpl->tpl_vars['discount']->value->type == "absolute") {?> active <?php }?>" type="hidden" name="purchases_discounts[<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
][type][]" value="absolute" <?php if ($_smarty_tpl->tpl_vars['discount']->value->type == "percent") {?>disabled<?php }?> />
                            <span class="fn_discount_change_type discount_change_type input-group-addon p-0">
                            <span class="discount_type_absolute" <?php if ($_smarty_tpl->tpl_vars['discount']->value->type == "percent") {?>style="display:none"<?php }?>>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->code, ENT_QUOTES, 'UTF-8', true);?>

                            </span>
                            <span class="discount_type_percent" <?php if ($_smarty_tpl->tpl_vars['discount']->value->type == "absolute") {?>style="display:none"<?php }?>>
                                %
                            </span>
                        </span>
                        </div>
                    </div>

                    <div class="okay_list_boding okay_list_order_amount_price">
                        <div class="text_dark text_warning text_600">
                            <span class="font_16"><?php echo round($_smarty_tpl->tpl_vars['discount']->value->priceAfterDiscount,2);?>
</span>
                            <span class="font_12"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        </div>
                    </div>

                    <div class="okay_list_boding okay_list_close">
                                                <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->brands_delete_brand, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close hint-bottom-right-t-info-s-small-mobile hint-anim fn_discount_remove">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'trash'), 0, true);
?>
                        </button>
                    </div>
                </div>
            </div>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
    <div class="text-xs-left">
        <button type="button" class="btn btn_mini btn-info btn_openSans fn_add_purchase_discount" data-purchase_id="<?php echo $_smarty_tpl->tpl_vars['purchase']->value->id;?>
">
            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'plus'), 0, true);
?>
            <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_discount_add_discount, ENT_QUOTES, 'UTF-8', true);?>
</span>
        </button>
    </div>
</div>
<?php }
}
