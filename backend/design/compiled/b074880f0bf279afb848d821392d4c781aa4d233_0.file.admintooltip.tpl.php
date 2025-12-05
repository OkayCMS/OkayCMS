<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:33:18
  from '/var/www/html/backend/design/html/admintooltip.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c95e8752c4_91869368',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b074880f0bf279afb848d821392d4c781aa4d233' => 
    array (
      0 => '/var/www/html/backend/design/html/admintooltip.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6926c95e8752c4_91869368 (Smarty_Internal_Template $_smarty_tpl) {
?><link href="backend/design/js/admintooltip/styles/admin.css" type="text/css" rel="stylesheet">

<div class="admTools">
    <a href="javascript:void(0);" class="openTools"></a>
    <p><?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_title_1;?>
</p>
    <p class="tool-descr"><?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_descr;?>
</p>
    <a title="<?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_go_to_admin;?>
" href="backend/" class="admin_bookmark"></a>
    <p class="tool-title"><?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_fast_edit;?>
</p>
    <a title="<?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_enable;?>
" href="javascript:void(0);" class="changeTools"><span></span></a>
</div>

<div class="fn_tooltip tooltip"></div>

<a title="<?php echo $_smarty_tpl->tpl_vars['btr']->value->admintooltip_go_to_admin;?>
" href="backend/" class="top_admin_bookmark"></a>

<?php echo '<script'; ?>
 src="backend/design/js/admintooltip/admintooltip.js"<?php if ($_smarty_tpl->tpl_vars['scripts_defer']->value == true) {?> defer<?php }?>><?php echo '</script'; ?>
>
<?php }
}
