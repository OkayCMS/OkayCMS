<?php
/* Smarty version 3.1.40, created on 2025-11-26 19:41:23
  from '/var/www/html/backend/design/html/include_languages.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_69273bc3231633_77839322',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '86e107cd825b3a89e67d5ea4e938cbace3bd65e7' => 
    array (
      0 => '/var/www/html/backend/design/html/include_languages.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69273bc3231633_77839322 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['languages']->value) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['languages']->value, 'lang');
$_smarty_tpl->tpl_vars['lang']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['lang']->value) {
$_smarty_tpl->tpl_vars['lang']->do_else = false;
?>
        <a class="flag flag_<?php echo $_smarty_tpl->tpl_vars['lang']->value->id;?>
 <?php if ($_smarty_tpl->tpl_vars['lang']->value->id == $_smarty_tpl->tpl_vars['lang_id']->value) {?> focus<?php }?> hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('lang_id'=>$_smarty_tpl->tpl_vars['lang']->value->id),$_smarty_tpl ) );?>
" data-label="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value->label, ENT_QUOTES, 'UTF-8', true);?>
">
            <?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->lang_images_dir, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable10=ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value->label, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable11=ob_get_clean();
if (is_file($_prefixVariable10.$_prefixVariable11.".png")) {?>
                <img src="<?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['lang']->value->label, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable12=ob_get_clean();
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'resize' ][ 0 ], array( ($_prefixVariable12.".png"),32,32,false,$_smarty_tpl->tpl_vars['config']->value->lang_resized_dir ));?>
" width="32px;" height="32px;">
            <?php }?>
        </a>
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
}
