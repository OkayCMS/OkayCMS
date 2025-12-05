<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:33:11
  from '/var/www/html/backend/design/html/labels_ajax.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c9570c1276_10616814',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '10f7aa054a4da0d5bf302cb8673281a6386d03b4' => 
    array (
      0 => '/var/www/html/backend/design/html/labels_ajax.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6926c9570c1276_10616814 (Smarty_Internal_Template $_smarty_tpl) {
if ($_GET['controller'] == "OrderAdmin") {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order_labels']->value, 'l');
$_smarty_tpl->tpl_vars['l']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->do_else = false;
?>
        <span class="tag font-xs" style="background-color:#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->color, ENT_QUOTES, 'UTF-8', true);?>
;" ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</span>
        <input type="hidden" name="order_labels[]" value="<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
">
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
} else { ?>
    <?php if ($_smarty_tpl->tpl_vars['order']->value->labels) {?>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order']->value->labels, 'l');
$_smarty_tpl->tpl_vars['l']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->do_else = false;
?>
            <span class="tag" style="background-color:#<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->color, ENT_QUOTES, 'UTF-8', true);?>
;" ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</span>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php }
}
}
}
