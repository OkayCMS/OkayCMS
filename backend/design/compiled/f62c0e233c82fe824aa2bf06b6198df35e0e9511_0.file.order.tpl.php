<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:33:18
  from '/var/www/html/backend/design/html/order.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c95e43f8a9_59015721',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f62c0e233c82fe824aa2bf06b6198df35e0e9511' => 
    array (
      0 => '/var/www/html/backend/design/html/order.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:tinymce_init.tpl' => 1,
    'file:svg_icon.tpl' => 19,
    'file:labels_ajax.tpl' => 1,
    'file:order_purchase_discount.tpl' => 2,
    'file:order_history.tpl' => 1,
    'file:match_orders.tpl' => 1,
    'file:order_discount.tpl' => 1,
    'file:learning_hints.tpl' => 1,
  ),
),false)) {
function content_6926c95e43f8a9_59015721 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['order']->value->id) {?>
    <?php $_smarty_tpl->_assignInScope('meta_title', ((string)$_smarty_tpl->tpl_vars['btr']->value->general_order_number)." ".((string)$_smarty_tpl->tpl_vars['order']->value->id) ,false ,32);
} else { ?>
    <?php $_smarty_tpl->_assignInScope('meta_title', $_smarty_tpl->tpl_vars['btr']->value->order_new ,false ,32);
}
$_smarty_tpl->_subTemplateRender('file:tinymce_init.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<form method="post" enctype="multipart/form-data" class="fn_fast_button">
    <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
    <input name="id" type="hidden" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>
"/>
    <div class="main_header">
        <div class="main_header__item">
            <div class="fn_step-1 main_header__inner order_toolbar">
                <div class="box_heading heading_page order_toolbar__heading">
                    <?php if ($_smarty_tpl->tpl_vars['order']->value->id) {?>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_order_number, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>

                    <?php } else { ?>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_new, ENT_QUOTES, 'UTF-8', true);?>

                    <?php }?>
                </div>
                                <div class="order_toolbar__status">
                    <select class="selectpicker form-control" name="status_id">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['all_status']->value, 'status_item');
$_smarty_tpl->tpl_vars['status_item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['status_item']->value) {
$_smarty_tpl->tpl_vars['status_item']->do_else = false;
?>
                            <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['status_item']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['order']->value->status_id == $_smarty_tpl->tpl_vars['status_item']->value->id) {?>selected=""<?php }?> <?php if ($_smarty_tpl->tpl_vars['hasVariantNotInStock']->value && !$_smarty_tpl->tpl_vars['order']->value->closed && $_smarty_tpl->tpl_vars['status_item']->value->is_close) {?> disabled<?php }?> >
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['status_item']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                            </option>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    </select>
                </div>
                <?php if ($_smarty_tpl->tpl_vars['order']->value->id && !empty($_smarty_tpl->tpl_vars['order']->value->url)) {?>
                    <a data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_open, ENT_QUOTES, 'UTF-8', true);?>
" class="hint-bottom-middle-t-info-s-small-mobile  hint-anim ml-h site_block_icon order_toolbar__eye" target="_blank"  href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>'order','url'=>$_smarty_tpl->tpl_vars['order']->value->url,'absolute'=>1),$_smarty_tpl ) );?>
" >
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'eye'), 0, false);
?>
                    </a>
                <?php }?>
                <a data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_print, ENT_QUOTES, 'UTF-8', true);?>
" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('view'=>'print','id'=>$_smarty_tpl->tpl_vars['order']->value->id,'return'=>null),$_smarty_tpl ) );?>
" target="_blank" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_print, ENT_QUOTES, 'UTF-8', true);?>
" class="hint-bottom-middle-t-info-s-small-mobile  hint-anim ml-h print_block_icon order_toolbar__print">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'print'), 0, true);
?>
                </a>
                                <?php if ($_smarty_tpl->tpl_vars['payment_method']->value->name === 'RozetkaPay' && $_smarty_tpl->tpl_vars['order']->value->paid) {?>
    <div class="box_btn_heading" style="margin-left: 10px !important;">
        <a class="btn btn_small btn-info" href="/backend/index.php?controller=OkayCMS.RozetkaPay.RefundAdmin@execute?&order=<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
">
            <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->rozetka_pay_refund, ENT_QUOTES, 'UTF-8', true);?>
</span>
        </a>
    </div>
<?php }?>
                <div class="box_btn_heading ml-h hidden-xs-down order_toolbar__markers">
                    <div class="add_order_marker form-control">
                        <span class="fn_ajax_label_wrapper">
                            <span class="fn_labels_show box_labels_show box_btn_heading ml-h">
                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag'), 0, true);
?>
                                <span>
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_select_label, ENT_QUOTES, 'UTF-8', true);?>

                                </span>
                            </span>
                            <div class='fn_labels_hide box_labels_hide'>
                                <span class="heading_label">
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_choose, ENT_QUOTES, 'UTF-8', true);?>

                                    <i class="fn_delete_labels_hide btn_close delete_labels_hide">
                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                                    </i>
                                </span>
                                <ul class="option_labels_box">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['labels']->value, 'l');
$_smarty_tpl->tpl_vars['l']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->do_else = false;
?>
                                        <li class="fn_ajax_labels" data-order_id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>
"  style="background-color: #<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->color, ENT_QUOTES, 'UTF-8', true);?>
">
                                            <input id="l<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>
_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" type="checkbox" class="hidden_check_1" name="order_labels[]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" <?php if (in_array($_smarty_tpl->tpl_vars['l']->value->id,array_keys($_smarty_tpl->tpl_vars['order_labels']->value)) && is_array($_smarty_tpl->tpl_vars['order_labels']->value)) {?>checked=""<?php }?> />
                                            <label for="l<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->id, ENT_QUOTES, 'UTF-8', true);?>
_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" class="label_labels">
                                                <span>
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                                </span>
                                            </label>
                                        </li>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </ul>
                            </div>
                            <div class="fn_order_labels orders_labels box_btn_heading ml-h">
                                <?php $_smarty_tpl->_subTemplateRender("file:labels_ajax.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['neighbors_orders']->value) {?>
            <div class="main_header__item neighbors_orders hidden-md-down">
                <div class="main_header__inner">
                    <?php if ($_smarty_tpl->tpl_vars['neighbors_orders']->value['prev']->id) {?>
                        <span>
                            <a title="<?php echo $_smarty_tpl->tpl_vars['btr']->value->order_prev;?>
" class="prev_order ml-h" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('id'=>$_smarty_tpl->tpl_vars['neighbors_orders']->value['prev']->id),$_smarty_tpl ) );?>
">
                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'prev'), 0, true);
?>
                            </a>
                        </span>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['neighbors_orders']->value['next']) {?>
                        <span>
                            <a title="<?php echo $_smarty_tpl->tpl_vars['btr']->value->order_next;?>
" class="next_order ml-h" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('id'=>$_smarty_tpl->tpl_vars['neighbors_orders']->value['next']->id),$_smarty_tpl ) );?>
">
                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'next'), 0, true);
?>
                            </a>
                        </span>
                    <?php }?>
                </div>
            </div>
        <?php }?>
    </div>
    <?php if ($_smarty_tpl->tpl_vars['hasVariantNotInStock']->value && !$_smarty_tpl->tpl_vars['order']->value->closed) {?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="boxed boxed_warning">
                    <div class="">
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_not_in_stock, ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                </div>
            </div>
        </div>
    <?php }?>
        <?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert--center alert--icon alert--error">
                    <div class="alert__content">
                        <div class="alert__title">
                            <?php if ($_smarty_tpl->tpl_vars['message_error']->value == 'error_closing') {?>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_shortage, ENT_QUOTES, 'UTF-8', true);?>

                        <?php } elseif ($_smarty_tpl->tpl_vars['message_error']->value == 'empty_purchase') {?>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_empty_purchases, ENT_QUOTES, 'UTF-8', true);?>

                        <?php } else { ?>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_error']->value, ENT_QUOTES, 'UTF-8', true);?>

                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                <?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value) {?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="alert alert--center alert--icon alert--success">
                    <div class="alert__content">
                        <div class="alert__title">
                            <?php if ($_smarty_tpl->tpl_vars['message_success']->value == 'updated') {?>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_updated, ENT_QUOTES, 'UTF-8', true);?>

                            <?php } elseif ($_smarty_tpl->tpl_vars['message_success']->value == 'added') {?>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_added, ENT_QUOTES, 'UTF-8', true);?>

                            <?php } else { ?>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_success']->value, ENT_QUOTES, 'UTF-8', true);?>

                            <?php }?>
                        </div>
                    </div>
                    <?php if ($_GET['return']) {?>
                        <a class="alert__button" href="<?php echo $_GET['return'];?>
">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'return'), 0, true);
?>
                            <span>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_back, ENT_QUOTES, 'UTF-8', true);?>

                            </span>
                        </a>
                    <?php }?>
                </div>
            </div>
        </div>
    <?php }?>
    <div class="row">
                <div class="col-xl-8 break_1300_12  pr-0">
            <div class="boxed fn_toggle_wrap min_height_230px fn_step-2 tabs">
                <div class="heading_tabs">
                    <div class="tab_navigation">
                        <a href="#tab1" class="heading_box tab_navigation_link">
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_content, ENT_QUOTES, 'UTF-8', true);?>

                        </a>
                        <?php if ($_smarty_tpl->tpl_vars['order']->value->id) {?>
                            <a href="#tab2" class="heading_box tab_navigation_link">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_history, ENT_QUOTES, 'UTF-8', true);?>

                            </a>
                            <?php if ($_smarty_tpl->tpl_vars['match_orders']->value) {?>
                                <a href="#tab3" class="fn_match_orders_tab_title heading_box tab_navigation_link <?php if ($_smarty_tpl->tpl_vars['match_orders_tab_active']->value) {?>selected<?php }?>">
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_match_orders, ENT_QUOTES, 'UTF-8', true);?>

                                </a>
                            <?php }?>
                        <?php }?>
                    </div>
                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" >
                            <i class="fa fn_icon_arrow fa-angle-down"></i>
                        </a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="tab_container">
                        <div id="tab1" class="tab">
                            <div id="fn_purchase" class="okay_list">
                                                                <div class="okay_list_head">
                                    <div class="okay_list_heading okay_list_photo">
                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_photo, ENT_QUOTES, 'UTF-8', true);?>

                                    </div>
                                    <div class="okay_list_heading okay_list_order_name">
                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_name_option, ENT_QUOTES, 'UTF-8', true);?>

                                    </div>
                                    <div class="okay_list_heading okay_list_price">
                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_price, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>

                                    </div>
                                    <div class="okay_list_heading okay_list_count">
                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_qty, ENT_QUOTES, 'UTF-8', true);?>

                                    </div>
                                    <div class="okay_list_heading okay_list_order_amount_price">
                                        <?php echo $_smarty_tpl->tpl_vars['btr']->value->general_sales_amount;?>

                                    </div>
                                </div>
                                                                <div class="okay_list_body">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['purchases']->value, 'purchase');
$_smarty_tpl->tpl_vars['purchase']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['purchase']->value) {
$_smarty_tpl->tpl_vars['purchase']->do_else = false;
?>
                                        <div class="fn_row okay_list_body_item purchases">
                                            <div class="okay_list_row">
                                                <input type=hidden name=purchases[id][<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->id, ENT_QUOTES, 'UTF-8', true);?>
] value='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->id, ENT_QUOTES, 'UTF-8', true);?>
'>
                                                <div class="okay_list_boding okay_list_photo">
                                                    <?php if ($_smarty_tpl->tpl_vars['purchase']->value->variant) {?>
                                                        <img class=product_icon src="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'resize' ][ 0 ], array( $_smarty_tpl->tpl_vars['purchase']->value->product->image->filename,50,50 ));?>
">
                                                        <?php } else { ?>
                                                        <img width="50" src="design/images/no_image.png"/>
                                                    <?php }?>
                                                </div>
                                                <div class="okay_list_boding okay_list_order_name">
                                                    <div class="boxes_inline">
                                                        <?php if ($_smarty_tpl->tpl_vars['purchase']->value->product) {?>
                                                            <a class="text_600 <?php if (!$_smarty_tpl->tpl_vars['order']->value->closed && $_smarty_tpl->tpl_vars['purchase']->value->variant->stock == 0) {?>hint-bottom-middle-t-info-s-small-mobile  hint-anim text_500 text_warning<?php }?>" <?php if (!$_smarty_tpl->tpl_vars['order']->value->closed && $_smarty_tpl->tpl_vars['purchase']->value->variant->stock == 0) {?>data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->product_out_stock, ENT_QUOTES, 'UTF-8', true);?>
"<?php }?> href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ProductAdmin','id'=>$_smarty_tpl->tpl_vars['purchase']->value->product->id),$_smarty_tpl ) );?>
">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->product_name, ENT_QUOTES, 'UTF-8', true);?>

                                                            </a>
                                                            <?php if ($_smarty_tpl->tpl_vars['purchase']->value->variant_name) {?>
                                                                <div class="mt-q font_12">
                                                                    <span class="text_grey">
                                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_option, ENT_QUOTES, 'UTF-8', true);?>

                                                                    </span>
                                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->variant_name, ENT_QUOTES, 'UTF-8', true);?>

                                                                </div>
                                                            <?php }?>
                                                            <?php if ($_smarty_tpl->tpl_vars['purchase']->value->sku) {?>
                                                                <div class="mt-q font_12">
                                                                    <span class="text_grey">
                                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_sku, ENT_QUOTES, 'UTF-8', true);?>
:
                                                                    </span>
                                                                    <?php echo (($tmp = @$_smarty_tpl->tpl_vars['purchase']->value->sku)===null||$tmp==='' ? "&mdash;" : $tmp);?>

                                                                </div>
                                                            <?php }?>
                                                            <?php } else { ?>
                                                            <div class="text_grey text_600">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->product_name, ENT_QUOTES, 'UTF-8', true);?>

                                                            </div>
                                                            <?php if ($_smarty_tpl->tpl_vars['purchase']->value->variant_name) {?>
                                                                <div class="mt-q font_12">
                                                                    <span class="text_grey">
                                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_option, ENT_QUOTES, 'UTF-8', true);?>

                                                                    </span>
                                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->variant_name, ENT_QUOTES, 'UTF-8', true);?>

                                                                </div>
                                                            <?php }?>
                                                            <?php if ($_smarty_tpl->tpl_vars['purchase']->value->sku) {?>
                                                                <div class="mt-q font_12">
                                                                    <span class="text_grey">
                                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_sku, ENT_QUOTES, 'UTF-8', true);?>
:
                                                                    </span>
                                                                    <?php echo (($tmp = @$_smarty_tpl->tpl_vars['purchase']->value->sku)===null||$tmp==='' ? "&mdash;" : $tmp);?>

                                                                </div>
                                                            <?php }?>
                                                        <?php }?>
                                                        <div class="hidden-lg-up mt-q">
                                                            <span class="text_primary text_600 <?php if ($_smarty_tpl->tpl_vars['purchase']->value->discounts) {?>text_warning<?php }?>">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->price, ENT_QUOTES, 'UTF-8', true);?>

                                                            </span>
                                                            <span class="hidden-md-up text_500">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->amount, ENT_QUOTES, 'UTF-8', true);?>

                                                                <?php if ($_smarty_tpl->tpl_vars['purchase']->value->units) {?>
                                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->units, ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->units, ENT_QUOTES, 'UTF-8', true);?>

                                                                <?php }?>
                                                            </span>
                                                        </div>
                                                        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_purchase_name",'vars'=>array('purchase'=>$_smarty_tpl->tpl_vars['purchase']->value)),$_smarty_tpl ) );?>

                                                    </div>
                                                    <?php if (!$_smarty_tpl->tpl_vars['purchase']->value->variant) {?>
                                                        <input class="form-control " type="hidden" name="purchases[variant_id][<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->id, ENT_QUOTES, 'UTF-8', true);?>
]" value="" />
                                                        <?php } else { ?>
                                                        <div class="boxes_inline mt-h">
                                                            <select name="purchases[variant_id][<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->id, ENT_QUOTES, 'UTF-8', true);?>
]" class="selectpicker form-control <?php if (count($_smarty_tpl->tpl_vars['purchase']->value->product->variants) == 1) {?>hidden<?php }?> fn_purchase_variant">
                                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['purchase']->value->product->variants, 'v');
$_smarty_tpl->tpl_vars['v']->iteration = 0;
$_smarty_tpl->tpl_vars['v']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['v']->value) {
$_smarty_tpl->tpl_vars['v']->do_else = false;
$_smarty_tpl->tpl_vars['v']->iteration++;
$__foreach_v_3_saved = $_smarty_tpl->tpl_vars['v'];
?>
                                                                    <option <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_purchase_variants_option_block",'vars'=>array('v'=>$_smarty_tpl->tpl_vars['v']->value)),$_smarty_tpl ) );?>
 data-price="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value->price, ENT_QUOTES, 'UTF-8', true);?>
" data-units="<?php if ($_smarty_tpl->tpl_vars['v']->value->units) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value->units, ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->units, ENT_QUOTES, 'UTF-8', true);
}?>" data-amount="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value->stock, ENT_QUOTES, 'UTF-8', true);?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['v']->value->id == $_smarty_tpl->tpl_vars['purchase']->value->variant_id) {?>selected<?php }?> >
                                                                        <?php if ($_smarty_tpl->tpl_vars['v']->value->name) {?>
                                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['v']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                                                        <?php } else { ?>
                                                                            #<?php echo $_smarty_tpl->tpl_vars['v']->iteration;?>

                                                                        <?php }?>
                                                                    </option>
                                                                <?php
$_smarty_tpl->tpl_vars['v'] = $__foreach_v_3_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                            </select>
                                                        </div>
                                                    <?php }?>
                                                    <div class="mt-h">
                                                        <span class="tag <?php if ($_smarty_tpl->tpl_vars['purchase']->value->discounts) {?>tag-danger<?php } else { ?>tag-default<?php }?> fn_discounted_toggle">
                                                            <span>
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_discounts, ENT_QUOTES, 'UTF-8', true);?>

                                                            </span>
                                                            <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_price">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control fn_purchase_price" name="purchases[undiscounted_price][<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->id, ENT_QUOTES, 'UTF-8', true);?>
]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->undiscounted_price, ENT_QUOTES, 'UTF-8', true);?>
">
                                                        <span class="input-group-addon">
                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->code, ENT_QUOTES, 'UTF-8', true);?>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_count">
                                                    <div class="input-group">
                                                        <input class="form-control fn_purchase_amount" type="text" name="purchases[amount][<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->id, ENT_QUOTES, 'UTF-8', true);?>
]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->amount, ENT_QUOTES, 'UTF-8', true);?>
"/>
                                                        <span class="input-group-addon p-0 fn_purchase_units">
                                                            <?php if ($_smarty_tpl->tpl_vars['purchase']->value->units) {?>
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->units, ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->units, ENT_QUOTES, 'UTF-8', true);?>

                                                            <?php }?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_order_amount_price">
                                                    <div class="text_dark <?php if ($_smarty_tpl->tpl_vars['purchase']->value->discounts) {?>text_warning text_600<?php }?>">
                                                        <span class="font_16">
                                                            <?php echo ($_smarty_tpl->tpl_vars['purchase']->value->price)*($_smarty_tpl->tpl_vars['purchase']->value->amount);?>

                                                        </span>
                                                        <span class="font_12">
                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>

                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="okay_list_boding okay_list_close">
                                                                                                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_delete_product, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim" >
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'trash'), 0, true);
?>
                                                    </button>
                                                                                                        <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>"product",'url'=>$_smarty_tpl->tpl_vars['purchase']->value->product->url,'absolute'=>1),$_smarty_tpl ) );
if ($_smarty_tpl->tpl_vars['purchase']->value->variant_id) {?>?variant=<?php echo $_smarty_tpl->tpl_vars['purchase']->value->variant_id;
}?>" target="_blank" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_view, ENT_QUOTES, 'UTF-8', true);?>
" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile hint-anim">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_desktop'), 0, true);
?>
                                                    </a>
                                                </div>
                                            </div>
                                            <?php $_smarty_tpl->_subTemplateRender('file:order_purchase_discount.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                                        </div>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </div>
                                <div class="okay_list_body fn_new_purchase" style="display: none">
                                    <div class="fn_row okay_list_body_item " >
                                        <div class="okay_list_row">
                                            <div class="okay_list_boding okay_list_photo">
                                                <input type="hidden" name="purchases[id][]" value="" />
                                                <img class="fn_new_image" src="">
                                            </div>
                                            <div class="okay_list_boding okay_list_order_name">
                                                <div class="boxes_inline">
                                                    <a class="fn_new_product" href=""></a>
                                                    <div class="fn_new_variant_name"></div>
                                                    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_new_purchase_name"),$_smarty_tpl ) );?>

                                                </div>
                                                <div class="boxes_inline">
                                                    <select name="purchases[variant_id][]" class="fn_new_variant"></select>
                                                </div>
                                                <div class="mt-h">
                                                    <span class="tag tag-default fn_discounted_toggle">
                                                        <span>
                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_discounts, ENT_QUOTES, 'UTF-8', true);?>

                                                        </span>
                                                        <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_price">
                                                <div class="input-group">
                                                    <input type="text" class="form-control fn_purchase_price" name=purchases[undiscounted_price][] value="">
                                                    <span class="input-group-addon">
                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->code, ENT_QUOTES, 'UTF-8', true);?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_count">
                                                <div class="input-group">
                                                    <input class="form-control fn_purchase_amount" type="text" name="purchases[amount][]" value="1"/>
                                                    <span class="input-group-addon p-0 fn_purchase_units"></span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_order_amount_price">
                                                <div class="text_dark">
                                                    <span></span>
                                                    <span class=""></span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_close">
                                                                                                <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_delete_product, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove_item hint-bottom-right-t-info-s-small-mobile  hint-anim">
                                                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'trash'), 0, true);
?>
                                                </button>
                                            </div>
                                        </div>
                                        <?php $_smarty_tpl->_subTemplateRender('file:order_purchase_discount.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('purchase'=>null), 0, true);
?>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2 mb-1">
                                <div class="col-lg-6 col-md-12">
                                    <div class="autocomplete_arrow">
                                        <input type="text" name="new_purchase" id="fn_add_purchase" class="form-control" placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_add_product, ENT_QUOTES, 'UTF-8', true);?>
">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <?php if ($_smarty_tpl->tpl_vars['purchases']->value) {?>
                                        <div class="order_prices__total text-xs-right">
                                            <span class="text_grey text_500 font_16 mr-1">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_sum, ENT_QUOTES, 'UTF-8', true);?>

                                            </span>
                                            <span class="text_dark text_600 font_24">
                                                <?php echo $_smarty_tpl->tpl_vars['subtotal']->value;?>

                                            </span>
                                            <span class="text_dark text_400 font_18 ml-q">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>

                                            </span>
                                        </div>
                                    <?php }?>
                                </div>
                            </div>
                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_purchases"),$_smarty_tpl ) );?>

                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['order']->value->id) {?>
                            <div id="tab2" class="tab">
                                <?php $_smarty_tpl->_subTemplateRender('file:order_history.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                                <div class="mt-2">
                                    <textarea name="history_comment" class="editor_small"></textarea>
                                    <div class="row">
                                        <div class="col-lg-12 col-md-12 mt-1">
                                            <button type="submit" class="btn btn_small btn_blue float-sm-right">
                                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                                                <span>
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>

                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ($_smarty_tpl->tpl_vars['match_orders']->value) {?>
                                <div id="tab3" class="tab fn_match_order_container">
                                    <?php $_smarty_tpl->_subTemplateRender('file:match_orders.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                                </div>
                            <?php }?>
                        <?php }?>
                    </div>
                </div>
            </div>
                        <div class="boxed fn_toggle_wrap boxed-discound_flex">
                <div class="heading_box heading_box--discound_flex">
                    <div class="pr-q">
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_discount_title, ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                    <div class="boxed-discound_activity text_400 opensans">
                        <div class="activity_of_switch activity_of_switch--left" style="display: inline-block;">
                            <div class="activity_of_switch_item">
                                <div class="okay_switch clearfix">
                                    <label class="switch_label">
                                        <?php echo $_smarty_tpl->tpl_vars['btr']->value->order_show_order_discounts;?>

                                    </label>
                                    <label class="switch switch-default">
                                        <input class="switch-input" type="checkbox" <?php if ($_smarty_tpl->tpl_vars['discounts']->value) {?>checked<?php }?> onchange="$('.fn_order_discounts_block').toggle(0)">
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <?php $_smarty_tpl->_subTemplateRender('file:order_discount.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                </div>
            </div>
                        <div class="boxed fn_toggle_wrap min_height_230px fn_step-3">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_parameters, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" >
                            <i class="fa fn_icon_arrow fa-angle-down"></i>
                        </a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="">
                        <div class="">
                            <div class="okay_list">
                                <div class="okay_list_body">
                                    <div class="okay_list_body_item">
                                        <div class="okay_list_row  d_flex">
                                            <div class="okay_list_boding okay_list_ordfig_name">
                                                <div class="text_600 text_dark boxes_inline">
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_shipping, ENT_QUOTES, 'UTF-8', true);?>

                                                </div>
                                                <div class="boxes_inline">
                                                    <select name="delivery_id" class="selectpicker form-control">
                                                        <option value="0">
                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_not_selected, ENT_QUOTES, 'UTF-8', true);?>

                                                        </option>
                                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveries']->value, 'd');
$_smarty_tpl->tpl_vars['d']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['d']->value) {
$_smarty_tpl->tpl_vars['d']->do_else = false;
?>
                                                            <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['d']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['d']->value->id == $_smarty_tpl->tpl_vars['delivery']->value->id) {?>selected<?php }?> data-module_id="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['d']->value->module_id, ENT_QUOTES, 'UTF-8', true);?>
">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['d']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                                            </option>
                                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                    </select>
                                                </div>
                                                <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_delivery_info"),$_smarty_tpl ) );?>

                                            </div>
                                            <div class="okay_list_boding okay_list_ordfig_val">
                                                <div class="input-group">
                                                    <input type=text name=delivery_price class="form-control" value='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->delivery_price, ENT_QUOTES, 'UTF-8', true);?>
'>
                                                    <span class="input-group-addon p-0">
                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->code, ENT_QUOTES, 'UTF-8', true);?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="okay_list_boding okay_list_ordfig_price">
                                                <div class="input-group"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="okay_list_body_item">
                                        <div class="okay_list_row  d_flex">
                                            <div class="okay_list_boding okay_list_ordfig_name">
                                                <div class="text_600 text_dark boxes_inline">
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_payment_selected, ENT_QUOTES, 'UTF-8', true);?>

                                                </div>
                                                <div class="boxes_inline">
                                                    <select name="payment_method_id" class="selectpicker form-control">
                                                        <option value="0">
                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_not_selected, ENT_QUOTES, 'UTF-8', true);?>

                                                        </option>
                                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['payment_methods']->value, 'pm');
$_smarty_tpl->tpl_vars['pm']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['pm']->value) {
$_smarty_tpl->tpl_vars['pm']->do_else = false;
?>
                                                            <option value="<?php echo $_smarty_tpl->tpl_vars['pm']->value->id;?>
" <?php if ($_smarty_tpl->tpl_vars['pm']->value->id == $_smarty_tpl->tpl_vars['payment_method']->value->id) {?>selected<?php }?> data-module="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['pm']->value->module, ENT_QUOTES, 'UTF-8', true);?>
">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['pm']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                                            </option>
                                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                    </select>
                                                </div>
                                                <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_payment_info"),$_smarty_tpl ) );?>

                                            </div>
                                            <div class="okay_list_boding okay_list_ordfig_val"></div>
                                            <div class="okay_list_boding okay_list_ordfig_price">
                                                <div class="text_dark">
                                                    <span>
                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->total_price, ENT_QUOTES, 'UTF-8', true);?>

                                                    </span>
                                                    <span class="">
                                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>

                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_common_info"),$_smarty_tpl ) );?>

                            </div>
                            <div class="order_prices mt-1">
                                <div class="order_prices__item my-1">
                                    <div class="">
                                        <div class="okay_switch">
                                            <label class="switch_label boxes_inline">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_paid, ENT_QUOTES, 'UTF-8', true);?>

                                            </label>
                                            <label class="switch switch-default switch-pill switch-primary-outline-alt boxes_inline">
                                                <input class="switch-input" name="paid" value='1' type="checkbox" id="paid" <?php if ($_smarty_tpl->tpl_vars['order']->value->paid) {?>checked<?php }?>/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="order_prices__item my-1">
                                    <div class="">
                                        <?php if ($_smarty_tpl->tpl_vars['payment_method']->value) {?>
                                            <div class="order_prices__total">
                                                <span class="text_grey text_500 font_18 mr-1">
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_to_pay, ENT_QUOTES, 'UTF-8', true);?>

                                                </span>
                                                <span class="text_dark text_600 font_26">
                                                    <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'convert' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->total_price,$_smarty_tpl->tpl_vars['payment_currency']->value->id ));?>

                                                </span>
                                                <span class="text_dark text_400 font_18 ml-q">
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['payment_currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>

                                                </span>
                                            </div>
                                        <?php }?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ob_start();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_custom_block"),$_smarty_tpl ) );
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->_assignInScope('block', $_prefixVariable1);?>
            <?php if (!empty($_smarty_tpl->tpl_vars['block']->value)) {?>
                <div class="boxed fn_toggle_wrap">
                    <?php echo $_smarty_tpl->tpl_vars['block']->value;?>

                </div>
            <?php }?>
        </div>
                        <div class="col-xl-4 break_1300_12">
            <div class="boxed fn_toggle_wrap min_height_230px fn_step-4">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_buyer_information, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" >
                            <i class="fa fn_icon_arrow fa-angle-down"></i>
                        </a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="box_border_buyer fn_contact_info">
                        <?php if ($_smarty_tpl->tpl_vars['order']->value->date) {?>
                            <div class="mb-1">
                                <div class="heading_label boxes_inline">
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_date, ENT_QUOTES, 'UTF-8', true);?>

                                </div>
                                <div class="boxes_inline text_dark text_600">
                                    <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->date ));?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'time' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->date ));?>

                                </div>
                            </div>
                        <?php }?>
                        <div class="mb-1">
                            <div class="heading_label">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_name, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                            <input name="name" class="form-control" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->name, ENT_QUOTES, 'UTF-8', true);?>
" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_last_name, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                            <input name="last_name" class="form-control" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->last_name, ENT_QUOTES, 'UTF-8', true);?>
" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_phone, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                            <input name="phone" class="form-control" type="text" value="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'phone' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->phone ));?>
" />
                        </div>
                        <div class="mb-1">
                            <div class="heading_label">
                                E-mail
                            </div>
                            <input name="email" class="form-control" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->email, ENT_QUOTES, 'UTF-8', true);?>
" />
                        </div>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['deliveryFields']->value, 'field');
$_smarty_tpl->tpl_vars['field']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['field']->value) {
$_smarty_tpl->tpl_vars['field']->do_else = false;
?>
    <div class="mb-1 fn_delivery_field<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['field']->value->deliveries, 'deliveryId');
$_smarty_tpl->tpl_vars['deliveryId']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['deliveryId']->value) {
$_smarty_tpl->tpl_vars['deliveryId']->do_else = false;
?> fn_delivery_field_<?php echo $_smarty_tpl->tpl_vars['deliveryId']->value;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>"
        <?php if (!empty($_smarty_tpl->tpl_vars['field']->value->deliveries) && !in_array($_smarty_tpl->tpl_vars['order']->value->delivery_id,$_smarty_tpl->tpl_vars['field']->value->deliveries)) {?>style="display: none"<?php }?>
    >
        <div class="heading_label">
            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value->name, ENT_QUOTES, 'UTF-8', true);?>

            <?php if ($_smarty_tpl->tpl_vars['field']->value->value) {?>
                <a href="https://www.google.com/maps/search/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value->value, ENT_QUOTES, 'UTF-8', true);?>
?hl=ru" target="_blank">
                    <i class="fa fa-map-marker"></i> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->df_order_on_map, ENT_QUOTES, 'UTF-8', true);?>

                </a>
            <?php }?>
        </div>
        <input name="delivery_fields[<?php echo $_smarty_tpl->tpl_vars['field']->value->id;?>
]"
               class="form-control"
               type="text"
               value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value->value, ENT_QUOTES, 'UTF-8', true);?>
"
               <?php if (!empty($_smarty_tpl->tpl_vars['field']->value->deliveries) && !in_array($_smarty_tpl->tpl_vars['order']->value->delivery_id,$_smarty_tpl->tpl_vars['field']->value->deliveries)) {?>disabled<?php }?>
        />
        <?php if ($_smarty_tpl->tpl_vars['field']->value->value_id) {?>
            <input name="delivery_fields_values_ids[<?php echo $_smarty_tpl->tpl_vars['field']->value->id;?>
]"
                   value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['field']->value->value_id, ENT_QUOTES, 'UTF-8', true);?>
"
                   type="hidden"
                   <?php if (!empty($_smarty_tpl->tpl_vars['field']->value->deliveries) && !in_array($_smarty_tpl->tpl_vars['order']->value->delivery_id,$_smarty_tpl->tpl_vars['field']->value->deliveries)) {?>disabled<?php }?>
            />
        <?php }?>
    </div>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php echo '<script'; ?>
>
    $(document).on('change', 'select[name="delivery_id"]', function (){
        let deliveryId = $(this).children(':selected').val();
        $('.fn_delivery_field').hide().children('input').prop('disabled', true);
        if (deliveryId) {
            $('.fn_delivery_field_' + deliveryId).show().children('input').prop('disabled', false);
        }
    });
<?php echo '</script'; ?>
>
                        <div class="mb-1">
                            <div class="heading_label">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_comment, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                            <textarea name="comment" class="form-control short_textarea"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->comment, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
                        </div>
                        <div class="mb-1">
                            <div class="heading_label boxes_inline">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_ip, ENT_QUOTES, 'UTF-8', true);?>

                                <?php if ($_smarty_tpl->tpl_vars['order']->value->id) {?>
                                    <a href="https://2ip.ua/ru/services/information-service/whois?a=act&ip=<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->ip, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank">
                                        <i class="fa fa-map-marker"></i>
                                        whois
                                    </a>
                                <?php }?>
                            </div>
                            <div class="boxes_inline text_dark text_600">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->ip, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                        </div>
                        <?php if ($_smarty_tpl->tpl_vars['order']->value->referer_channel) {?>
                            <div class="mb-1">
                                <div class="heading_label boxes_inline">
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_referer_channel, ENT_QUOTES, 'UTF-8', true);?>
:
                                </div>
                                <div class="boxes_inline text_dark">
                                    <?php if ($_smarty_tpl->tpl_vars['order']->value->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_EMAIL) {?>
                                        <span class="tag tag-chanel_email" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_email'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                        </span>
                                        <?php } elseif ($_smarty_tpl->tpl_vars['order']->value->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SEARCH) {?>
                                        <span class="tag tag-chanel_search" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_search'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                        </span>
                                        <?php } elseif ($_smarty_tpl->tpl_vars['order']->value->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_SOCIAL) {?>
                                        <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank" class="tag tag-chanel_social" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_social'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                        </a>
                                        <?php } elseif ($_smarty_tpl->tpl_vars['order']->value->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_REFERRAL) {?>
                                        <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank" class="tag tag-chanel_referral" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_referral'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                        </a>
                                        <?php } else { ?>
                                        <span class="tag tag-chanel_unknown" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_unknown'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                        </span>
                                    <?php }?>
                                </div>
                            </div>
                        <?php }?>
                        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_contact"),$_smarty_tpl ) );?>

                    </div>
                    <div class="box_border_buyer">
                        <div class="mb-1">
                            <div style="position:relative;">
                                <?php if (!$_smarty_tpl->tpl_vars['user']->value) {?>
                                    <div class="heading_label">
                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_buyer_not_registred, ENT_QUOTES, 'UTF-8', true);?>

                                    </div>
                                    <div style="position:relative;">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" />
                                        <input type="text" class="fn_user_complite form-control" placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_user_select, ENT_QUOTES, 'UTF-8', true);?>
" />
                                    </div>
                                    <?php } else { ?>
                                    <div class="fn_user_row">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" />
                                        <div class="heading_label boxes_inline">
                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_buyer, ENT_QUOTES, 'UTF-8', true);?>

                                            <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'UserAdmin','id'=>$_smarty_tpl->tpl_vars['user']->value->id),$_smarty_tpl ) );?>
" target=_blank>
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->name, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->last_name, ENT_QUOTES, 'UTF-8', true);?>

                                            </a>
                                        </div>
                                        <a href="javascript:;" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->users_delete, ENT_QUOTES, 'UTF-8', true);?>
" class="btn_close delete_grey fn_delete_user hint-bottom-right-t-info-s-small-mobile  hint-anim boxes_inline" >
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                                        </a>
                                        <?php if ($_smarty_tpl->tpl_vars['user']->value->group_id > 0) {?>
                                            <div class="text_grey">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['user']->value->group->name, ENT_QUOTES, 'UTF-8', true);?>

                                            </div>
                                            <?php } else { ?>
                                            <div class="text_grey">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_not_in_group, ENT_QUOTES, 'UTF-8', true);?>

                                            </div>
                                        <?php }?>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_user_info"),$_smarty_tpl ) );?>

                    </div>
                    <div class="box_border_buyer">
                        <div class="mb-1">
                            <div class="heading_label">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_language, ENT_QUOTES, 'UTF-8', true);?>

                            </div>
                            <select name="entity_lang_id" class="selectpicker form-control">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['languages']->value, 'l');
$_smarty_tpl->tpl_vars['l']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->do_else = false;
?>
                                    <option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->id, ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['l']->value->id == $_smarty_tpl->tpl_vars['order']->value->lang_id) {?>selected=""<?php }?>>
                                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="">
                            <div class="form-group">
                                <div class="heading_label">
                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_note, ENT_QUOTES, 'UTF-8', true);?>

                                </div>
                                <textarea name="note" class="form-control short_textarea"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->note, ENT_QUOTES, 'UTF-8', true);?>
</textarea>
                            </div>
                        </div>
                        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_additional_info"),$_smarty_tpl ) );?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-md-12 mb-3">
            <button id="fast_save_button_and_quit" type="submit" class="btn btn_small btn_blue float-md-right ml-1" name="apply_and_quit" value="1">
                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                <span>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply_and_quit, ENT_QUOTES, 'UTF-8', true);?>

                </span>
            </button>
            <button type="submit" class="btn btn_small btn_blue float-sm-right">
                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                <span>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>

                </span>
            </button>
            <div class="checkbox_email float-sm-right text_dark mr-1 fn_step-5">
                <input id="order_to_email" name="notify_user" type="checkbox" class="hidden_check_1"  value="1" />
                <label for="order_to_email" class="checkbox_label mr-h"></label>
                <label for="order_to_email">
                    <span>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->order_email, ENT_QUOTES, 'UTF-8', true);?>

                    </span>
                </label>
            </div>
        </div>
    </div>
</form>
<?php $_smarty_tpl->_subTemplateRender('file:learning_hints.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('hintId'=>'hint_order'), 0, false);
?>

<?php echo '<script'; ?>
 src="design/js/autocomplete/jquery.autocomplete-min.js"><?php echo '</script'; ?>
>
<link rel="stylesheet" type="text/css" href="design/js/autocomplete/styles.css" media="screen" />
<?php echo '<script'; ?>
>
    $(function() {
    // Удаление товара
    $(document).on( "click", "#fn_purchase .fn_remove_item", function() {
         $(this).closest(".fn_row").fadeOut(200, function() { $(this).remove(); });
         return false;
    });

    // Отобразить список скидок
    $(document).on('click','.fn_discounted_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.order_discounted_block').slideToggle(300);
    });

    $(".fn_labels_show").click(function(){
        $(this).next('.fn_labels_hide').toggleClass("active_labels");
    });
    $(".fn_delete_labels_hide").click(function(){
        $(this).closest('.box_labels_hide').removeClass("active_labels");
    });

    $(".fn_from_date, .fn_to_date ").datepicker({
        dateFormat: 'dd-mm-yy'
    });

    $(document).on("change", ".fn_ajax_labels input", function () {
        elem = $(this);
       var order_id = parseInt($(this).closest(".fn_ajax_labels").data("order_id"));
       var state = "";
       session_id = '<?php echo $_SESSION['id'];?>
';
       var label_id = parseInt($(this).closest(".fn_ajax_labels").find("input").val());
       if($(this).closest(".fn_ajax_labels").find("input").is(":checked")){
            state = "add";
       } else {
            state = "remove";
       }

        $.ajax({
            type: "POST",
            dataType: 'json',
            url: "ajax/update_order.php",
            data: {
                order_id : order_id,
                state : state,
                label_id : label_id,
                session_id : session_id
            },
            success: function(data){
                var msg = "";
                if(data){
                    elem.closest(".fn_ajax_label_wrapper").find(".fn_order_labels").html(data.data);
                    toastr.success(msg, "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->toastr_success, ENT_QUOTES, 'UTF-8', true);?>
");
                } else {
                    toastr.error(msg, "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->toastr_error, ENT_QUOTES, 'UTF-8', true);?>
");

                }
            }
        });
    });

    // Добавление товара
    var new_purchase = $('#fn_purchase .fn_new_purchase').clone(true);
    $('#fn_purchase .fn_new_purchase').remove().removeAttr('class');

    $("#fn_add_purchase").devbridgeAutocomplete({
    serviceUrl:'<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OrderAdmin@addOrderProduct'),$_smarty_tpl ) );?>
',
    minChars:0,
    orientation:'auto',
    noCache: false,
    onSelect:
        function(suggestion){
            let new_item = new_purchase.clone().appendTo('#fn_purchase'),
                temp_id = Date.now();
            new_item.find('.fn_add_purchase_discount').data('purchase_id', temp_id);
            new_item.find('.fn_default_purchase_discounts').attr('name', `purchases_discounts[${temp_id}]`)
            new_item.removeAttr('id');
            new_item.find('.fn_new_product').html(suggestion.data.name);
            new_item.find('.fn_new_product').attr('href', 'index.php?controller=ProductAdmin&id='+suggestion.data.id);

            // Добавляем варианты нового товара
            var variants_select = new_item.find("select.fn_new_variant");

            for(var i in suggestion.data.variants) {
                variants_select.append("<option <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_new_purchase_variants_option_block"),$_smarty_tpl ) );?>
 " +
                    "value='"+suggestion.data.variants[i].id+"' " +
                    "data-price='"+suggestion.data.variants[i].price+"' " +
                    "data-amount='"+suggestion.data.variants[i].stock+"' " +
                    "data-units='"+suggestion.data.variants[i].units+"'>" +
                    suggestion.data.variants[i].name +
                    "</option>");
            }

            if(suggestion.data.variants.length> 1 || suggestion.data.variants[0].name != '') {
                variants_select.show();
                variants_select.selectpicker();
            } else {
                variants_select.hide();
            }
            variants_select.find('option:first').attr('selected',true);

            variants_select.bind('change', function(){
                change_variant(variants_select);
            });
            change_variant(variants_select);
            variants_select.trigger('change');

            if(suggestion.data.image) {
                new_item.find('.fn_new_image').attr("src", suggestion.data.image);
            } else {
                new_item.find('.fn_new_image').remove();
            }

            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_new_purchase_js_block"),$_smarty_tpl ) );?>


            $("input#fn_add_purchase").val('').focus().blur();
            new_item.show();
        },
        formatResult:
            function(suggestions, currentValue){
                    var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
                    var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
                    return "<div>" + (suggestions.data.image?"<img align=absmiddle src='"+suggestions.data.image+"'> ":'') + "</div>" +  "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
                }


  });

  // Изменение цены и макс количества при изменении варианта
    function change_variant(element) {
        var price = element.find('option:selected').data('price');
        var amount = element.find('option:selected').data('amount');
        var units = element.find('option:selected').data('units');
        element.closest('.fn_row').find('input.fn_purchase_price').val(price);
        element.closest('.fn_row').find('.fn_purchase_units').text(units);
        var amount_input = element.closest('.fn_row').find('input.fn_purchase_amount');
        amount_input.val('1');
        amount_input.data('max',amount);
        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"order_change_variant_js_block"),$_smarty_tpl ) );?>

        return false;
  }

    $(".fn_user_complite").devbridgeAutocomplete({
        serviceUrl:'ajax/search_users.php',
        minChars:0,
        orientation:'auto',
        noCache: false,
        onSelect:function(suggestion){
            $('input[name="user_id"]').val(suggestion.data.id);

            for (let key in suggestion.data) {
                let contactField = $('.fn_contact_info [name="' + key + '"]');
                if (contactField.length > 0 && contactField.val() == '') {
                    contactField.val(suggestion.data[key]);
                }
            }
            
        },
        formatResult: function(suggestions, currentValue){
            var reEscape = new RegExp('(\\' + ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'].join('|\\') + ')', 'g');
            var pattern = '(' + currentValue.replace(reEscape, '\\$1') + ')';
            return "<span>" + suggestions.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + "</span>";
        }
    });

    $(document).on("click", ".fn_delete_user", function () {
        $(this).closest(".fn_user_row").hide();
        $('input[name="user_id"]').val(0);
    });


    $("select.fn_purchase_variant").bind("change", function(){
        change_variant($(this));
    });

    $(document).on('click', '.fn_discount_remove', function() {
        $(this).closest('.fn_row').remove();
    });

    $(function(){
       let newOrderDiscount = $('.fn_new_order_discount').clone(true);
        $('.fn_new_order_discount').remove();

        $(document).on('click', '.fn_add_order_discount', function(){
            newOrderDiscount.clone().appendTo($('.fn_order_discounts_block').find('.okay_list_body'));
        });

        $(document).on('click', '.fn_add_purchase_discount', function(){
            let purchaseId = $(this).data('purchase_id'),
                newPurchaseDiscount = newOrderDiscount.clone();
            newPurchaseDiscount.find('input').each(function(){
                $(this).attr('name', $(this).attr('name').replace('order_discounts', `purchases_discounts[${purchaseId}]`));
            });
            newPurchaseDiscount.appendTo($(this).closest('.fn_purchase_discounts_block').find('.okay_list_body'));
            $(this).closest('.fn_row').find('.fn_discounted_toggle').removeClass('tag-default').addClass('tag-danger');
        })
    });

    $(document).on('click', '.fn_discount_change_type', function(){
        let input1 = $(this).closest('.input-group').find('input.fn_discount_type_input.active');
        let input2 = $(this).closest('.input-group').find('input.fn_discount_type_input:not(.active)');
        input1.removeClass('active').attr('disabled', true);
        input2.addClass('active').attr('disabled', false);
        $(this).find('.discount_type_absolute').toggle(0);
        $(this).find('.discount_type_percent').toggle(0);
    });

    $(document).on('click', '.fn_discount_from_last_on', function(){
        $(this).closest('.switch').find('.fn_discount_from_last_off')[0].toggleAttribute('disabled')
    })
});
<?php echo '</script'; ?>
>
<?php }
}
