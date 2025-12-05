<?php
/* Smarty version 3.1.40, created on 2025-11-26 19:41:56
  from '/var/www/html/Okay/Modules/OkayCMS/LiqPay/Backend/design/html/description.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_69273be4b77d14_39601633',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2e5f02c43b3ad2134ac4d67655e5b038c750b624' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/LiqPay/Backend/design/html/description.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69273be4b77d14_39601633 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__liqpay__description_title, ENT_QUOTES, 'UTF-8', true) ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__liqpay__description_title, ENT_QUOTES, 'UTF-8', true);?>

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
                <p><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__liqpay__description_part_1;?>
: <b><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>"OkayCMS_LiqPay_callback",'absolute'=>1),$_smarty_tpl ) );?>
</b> <?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__liqpay__description_part_2;?>
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
/Okay/Modules/OkayCMS/LiqPay/Backend/design/images/liqpay.png">
            </div>
        </div>
    </div>
</div>
<?php }
}
