<?php
/* Smarty version 3.1.40, created on 2025-11-26 12:50:55
  from '/var/www/html/Okay/Modules/OkayCMS/Fondy/Backend/design/html/description.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926db8f96cbd6_24094871',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd43c46746575d10f224e49b1f469cc7e240d7a99' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/Fondy/Backend/design/html/description.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6926db8f96cbd6_24094871 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__fondy__description_title, ENT_QUOTES, 'UTF-8', true) ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__fondy__description_title, ENT_QUOTES, 'UTF-8', true);?>

            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="alert alert--icon alert--info">
            <div class="alert__content">
                <div class="alert__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->alert_info, ENT_QUOTES, 'UTF-8', true);?>
</div>
                <p><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__fondy__description_part_1;?>
</p>
            </div>
        </div>
    </div>
</div><?php }
}
