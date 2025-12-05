<?php
/* Smarty version 3.1.40, created on 2025-11-26 13:14:46
  from '/var/www/html/Okay/Modules/OkayCMS/RozetkaPay/Backend/design/html/description.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926e126ee8f39_74355271',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '84ef2182a24a1e2fec0f25548f6b84c4d4b91d13' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/RozetkaPay/Backend/design/html/description.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_6926e126ee8f39_74355271 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__rozetkapay__description_title, ENT_QUOTES, 'UTF-8', true) ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__rozetkapay__description_title, ENT_QUOTES, 'UTF-8', true);?>

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
                <p><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__rozetkapay__description_part_1;?>
: <b><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>'RozetkaPay_callback','absolute'=>1),$_smarty_tpl ) );?>
</b> <?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__rozetkapay__description_part_2;?>
</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="boxed">
            <div>
                <img src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/Okay/Modules/OkayCMS/RozetkaPay/Backend/design/images/rozetka-pay.svg">
            </div>
        </div>
    </div>
</div><?php }
}
