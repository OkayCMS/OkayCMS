<?php
/* Smarty version 3.1.40, created on 2025-11-26 23:57:50
  from '/var/www/html/Okay/Modules/MaxIT/MinOrderAmount/Backend/design/html/settings.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_692777de01cb13_32861620',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f125b66f1dd12dd3a640773aaaa7bfd980d75fe1' => 
    array (
      0 => '/var/www/html/Okay/Modules/MaxIT/MinOrderAmount/Backend/design/html/settings.tpl',
      1 => 1764194090,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 1,
  ),
),false)) {
function content_692777de01cb13_32861620 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__title, ENT_QUOTES, 'UTF-8', true) ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__title, ENT_QUOTES, 'UTF-8', true);?>
</div>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['message_success']->value && empty($_smarty_tpl->tpl_vars['message_error']->value)) {?>
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
                <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__description, ENT_QUOTES, 'UTF-8', true);?>
</p>
            </div>
        </div>
    </div>
</div>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">

    <div class="row" style="margin-left: -15px; margin-right: -15px;">
                <div class="col-lg-6 col-md-12" style="padding-left: 15px; padding-right: 15px;">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__limits, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                                <div class="toggle_body_wrap on fn_card">
                    <?php if ($_smarty_tpl->tpl_vars['deliveries']->value) {?>
                        <style>
                            .min-order-amount-list .okay_list_head,
                            .min-order-amount-list .okay_list_row {
                                display: flex;
                            }
                            .min-order-amount-list .okay_list_boding {
                                display: block;
                                box-sizing: border-box;
                            }
                            .min-order-amount-list .okay_list_boding.okay_list_delivery_name {
                                text-align: left;
                            }
                            .min-order-amount-list .okay_list_heading {
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                box-sizing: border-box;
                                height: auto;
                                padding: 12px;
                                margin: 0;
                                text-align: center;
                                line-height: 1.4;
                                word-wrap: break-word;
                            }
                            .min-order-amount-list .okay_list_heading.okay_list_delivery_name,
                            .min-order-amount-list .okay_list_heading.okay_list_price {
                                text-align: center;
                                justify-content: center;
                                align-items: center;
                                display: flex;
                            }
                            @media (min-width: 768px) {
                                .min-order-amount-list .okay_list_delivery_name {
                                    width: 70%;
                                    flex: 0 0 70%;
                                    max-width: 70%;
                                }
                                .min-order-amount-list .okay_list_price {
                                    width: 30%;
                                    flex: 0 0 30%;
                                    max-width: 30%;
                                    display: flex;
                                    align-items: center;
                                    padding: 8px 12px;
                                    box-sizing: border-box;
                                }
                                .min-order-amount-list .okay_list_boding.okay_list_price {
                                    display: flex;
                                    align-items: center;
                                    padding: 8px 12px;
                                    box-sizing: border-box;
                                }
                                .min-order-amount-list .okay_list_boding.okay_list_price input {
                                    width: 100%;
                                    max-width: 100%;
                                    box-sizing: border-box;
                                }
                            }
                            @media (max-width: 767px) {
                                .min-order-amount-list .okay_list_head,
                                .min-order-amount-list .okay_list_row {
                                    flex-direction: column !important;
                                    display: flex !important;
                                }
                                .min-order-amount-list .okay_list_heading,
                                .min-order-amount-list .okay_list_boding {
                                    width: 100% !important;
                                    flex: 1 1 100% !important;
                                    max-width: 100% !important;
                                    min-width: 100% !important;
                                    padding: 8px 12px;
                                    display: block !important;
                                    box-sizing: border-box !important;
                                }
                                .min-order-amount-list .okay_list_heading.okay_list_price,
                                .min-order-amount-list .okay_list_boding.okay_list_price {
                                    border-top: 1px solid #e0e0e0;
                                    margin-top: 0 !important;
                                }
                                .min-order-amount-list .okay_list_boding.okay_list_price input {
                                    width: 100%;
                                }
                            }
                        </style>
                        <div class="okay_list min-order-amount-list">
                                                        <div class="okay_list_head">
                                <div class="okay_list_heading okay_list_delivery_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__delivery_method, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                <div class="okay_list_heading okay_list_price"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__min_amount, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['main_currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            </div>

                                                        <div class="okay_list_body">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveries']->value, 'delivery');
$_smarty_tpl->tpl_vars['delivery']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['delivery']->value) {
$_smarty_tpl->tpl_vars['delivery']->do_else = false;
?>
                                    <div class="okay_list_body_item">
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding okay_list_delivery_name">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['delivery']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                            </div>
                                            <div class="okay_list_boding okay_list_price">
                                                    <input name="min_order_amount[<?php echo $_smarty_tpl->tpl_vars['delivery']->value->id;?>
]" class="form-control" type="text" value="<?php if ((isset($_smarty_tpl->tpl_vars['min_order_amounts']->value[$_smarty_tpl->tpl_vars['delivery']->value->id]))) {
echo $_smarty_tpl->tpl_vars['min_order_amounts']->value[$_smarty_tpl->tpl_vars['delivery']->value->id];
} else { ?>0<?php }?>" />
                                            </div>
                                        </div>
                                    </div>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="col-lg-12 col-md-12">
                                <div class="alert alert--icon alert--warning">
                                    <div class="alert__content">
                                        <div class="alert__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__no_deliveries, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>

                <div class="col-lg-6 col-md-12" style="padding-left: 15px; padding-right: 15px; margin-bottom: 15px;">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__error_text, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                                <div class="toggle_body_wrap on fn_card">
                    <div class="row">
                        <div class="col-lg-12 col-md-12">
                            <div class="mb-1">
                                <textarea name="maxit__min_order__error" class="okay_textarea" rows="3"><?php if ((isset($_smarty_tpl->tpl_vars['error_text_value']->value))) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['error_text_value']->value, ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->maxit__min_order__error, ENT_QUOTES, 'UTF-8', true);
}?></textarea>
                            </div>
                            <div class="alert alert--icon alert--info mt-1">
                                <div class="alert__content">
                                    <div class="alert__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->maxit__min_order__error_hint, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top: 15px; margin-bottom: 15px;">
        <div class="col-lg-12 col-md-12">
            <button type="submit" class="btn btn_small btn_blue float-md-right">
                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, false);
?>
                <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
            </button>
        </div>
    </div>
</form>
<?php }
}
