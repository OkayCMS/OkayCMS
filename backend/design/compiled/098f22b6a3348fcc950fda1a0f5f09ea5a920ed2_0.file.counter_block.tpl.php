<?php
/* Smarty version 3.1.40, created on 2025-11-26 13:14:47
  from '/var/www/html/Okay/Modules/OkayCMS/NovaposhtaCost/Backend/design/html/counter_block.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926e1270756b8_72587098',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '098f22b6a3348fcc950fda1a0f5f09ea5a920ed2' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/NovaposhtaCost/Backend/design/html/counter_block.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 1,
  ),
),false)) {
function content_6926e1270756b8_72587098 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['settings']->value->np_api_key_error || $_smarty_tpl->tpl_vars['uahCurrencyError']->value) {?>
    <div class="notif_item">
        <a href="index.php?controller=OkayCMS.NovaposhtaCost.NovaposhtaCostAdmin" class="l_notif">
            <span class="notif_icon boxed_notify">
                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'left_modules'), 0, false);
?>
            </span>
            <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->left_setting_np_title, ENT_QUOTES, 'UTF-8', true);?>
</span>
        </a>
        <span class="notif_count">1</span>
    </div>
<?php }
}
}
