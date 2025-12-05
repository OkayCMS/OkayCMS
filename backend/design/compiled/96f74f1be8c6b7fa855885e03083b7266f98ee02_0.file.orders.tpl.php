<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:33:11
  from '/var/www/html/backend/design/html/orders.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c9570b2797_33232052',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '96f74f1be8c6b7fa855885e03083b7266f98ee02' => 
    array (
      0 => '/var/www/html/backend/design/html/orders.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 14,
    'file:labels_ajax.tpl' => 1,
    'file:order_history.tpl' => 1,
    'file:pagination.tpl' => 1,
    'file:learning_hints.tpl' => 1,
  ),
),false)) {
function content_6926c9570b2797_33232052 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', $_smarty_tpl->tpl_vars['btr']->value->general_orders ,false ,32);?>

<div class="main_header">
    <div class="main_header__item">
        <div class="main_header__inner">
            <div class="box_heading heading_page">
                <?php if ($_smarty_tpl->tpl_vars['orders_count']->value) {?>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_orders, ENT_QUOTES, 'UTF-8', true);?>

                    <?php if (!empty($_smarty_tpl->tpl_vars['order_user']->value)) {?>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_orders_user, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order_user']->value->name, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order_user']->value->last_name, ENT_QUOTES, 'UTF-8', true);?>

                    <?php }?>
                    - <?php echo $_smarty_tpl->tpl_vars['orders_count']->value;?>

                <?php } else { ?>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_no, ENT_QUOTES, 'UTF-8', true);?>

                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['orders_count']->value > 0 && !$_smarty_tpl->tpl_vars['keyword']->value) {?>
                    <div class="fn_start_export export_block hint-bottom-middle-t-info-s-small-mobile hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_export, ENT_QUOTES, 'UTF-8', true);?>
">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'export'), 0, false);
?>
                    </div>
                <?php }?>
            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OrderAdmin'),$_smarty_tpl ) );?>
">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'plus'), 0, true);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_add, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </a>
            </div>
        </div>
    </div>
    <div class="main_header__item main_header__item--sort_date">
        <form class="box_date_filter fn_date_filter main_header__inner" method="get">
            <input type="hidden" name="controller" value="OrdersAdmin">
            <?php if ($_smarty_tpl->tpl_vars['keyword']->value) {?>
                <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
            <?php }?>
            
            <?php if ($_smarty_tpl->tpl_vars['status_id']->value) {?>
                <input type="hidden" name="status" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['status_id']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['label_id']->value) {?>
                <input type="hidden" name="label" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['label_id']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
            <?php }?>
            <ul class="filter_date__list form-control">
                <li class="filter_date__item">
                    <button type="button" class="fn_last_week filter_date__button "><?php echo $_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_last_week;?>
</button>
                </li>
                <li class="filter_date__item">
                    <button type="button" class="fn_30_days filter_date__button"><?php echo $_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_last_30_days;?>
</button>
                </li>
                <li class="filter_date__item">
                    <button type="button" class="fn_7_days filter_date__button"><?php echo $_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_last_7_days;?>
</button>
                </li>
                <li class="filter_date__item">
                    <button type="button" class="fn_yesterday filter_date__button"><?php echo $_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_last_yesterday;?>
</button>
                </li>
                <li class="filter_date__item filter_date__item--date hidden-xs-down">
                    <button class="fn_calendar filter_date__button" title="<?php echo $_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_calendar;?>
" type="button">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'date'), 0, true);
?>
                        <span class="hidden-xs-down"><?php echo $_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_calendar;?>
</span>
                    </button>
                    <button class="btn btn_blue" type="submit">
                        <span class="hidden-sm-up"><?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?></span>
                        <span class="hidden-xs-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                    </button>
                                        <input type="text" class="fn_calendar_pixel" name="" autocomplete="off" >
                </li>
            </ul>
            <input type="hidden" class="fn_from_date" name="from_date" value="<?php echo $_smarty_tpl->tpl_vars['from_date']->value;?>
" autocomplete="off" >
            <input type="hidden" class="fn_to_date" name="to_date" value="<?php echo $_smarty_tpl->tpl_vars['to_date']->value;?>
" autocomplete="off" >
        </form>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['message_error']->value) {?>
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="alert alert--center alert--icon alert--error">
                <div class="alert__content">
                    <div class="alert__title">
                        <?php if ($_smarty_tpl->tpl_vars['message_error']->value == 'error_closing') {?>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_in, ENT_QUOTES, 'UTF-8', true);?>

                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['error_orders']->value, 'error_order_id');
$_smarty_tpl->tpl_vars['error_order_id']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['error_order_id']->value) {
$_smarty_tpl->tpl_vars['error_order_id']->do_else = false;
?>
                                <div>
                                    № <?php echo $_smarty_tpl->tpl_vars['error_order_id']->value;?>

                                </div>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_shortage, ENT_QUOTES, 'UTF-8', true);?>

                        <?php } else { ?>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_error']->value, ENT_QUOTES, 'UTF-8', true);?>

                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<div class="boxed wrap_view_info">
    <div class="view_info_dates">
        <div class="view_info_dates__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_list_orders, ENT_QUOTES, 'UTF-8', true);?>
:</div>
        <div class="view_info_dates__text">
            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_list_orders_from, ENT_QUOTES, 'UTF-8', true);?>

            <?php if ($_smarty_tpl->tpl_vars['from_date']->value) {?>
                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['from_date']->value ));?>

            <?php } else { ?>
                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['orders_from_date']->value ));?>

            <?php }?>
            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_list_orders_to, ENT_QUOTES, 'UTF-8', true);?>

            <?php if ($_smarty_tpl->tpl_vars['to_date']->value) {?>
                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['to_date']->value ));?>

            <?php } else { ?>
                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['orders_to_date']->value ));?>

            <?php }?>
        </div>
        <?php if ($_smarty_tpl->tpl_vars['from_date']->value || $_smarty_tpl->tpl_vars['to_date']->value) {?>
            <button class="fn_reset_date_filter btn btn-secondary" type="button"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_date_filter_list_orders_reset, ENT_QUOTES, 'UTF-8', true);?>
</button>
        <?php }?>
    </div>
    <div class="view_info_visited">
        

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['all_status']->value, 's');
$_smarty_tpl->tpl_vars['s']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['s']->value) {
$_smarty_tpl->tpl_vars['s']->do_else = false;
?>
            <?php if ((isset($_smarty_tpl->tpl_vars['count_orders_by_statuses']->value[$_smarty_tpl->tpl_vars['s']->value->id]))) {?>
                <?php $_smarty_tpl->_assignInScope('ordersCount', $_smarty_tpl->tpl_vars['count_orders_by_statuses']->value[$_smarty_tpl->tpl_vars['s']->value->id]);?>
                <div class="view_info_visited__item">
                    <div class="view_info_visited__inner">
                        <div class="view_info_visited__left">
                            <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('status'=>$_smarty_tpl->tpl_vars['s']->value->id),$_smarty_tpl ) );?>
" class="view_info_visited__status" style="color: #<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['s']->value->color, ENT_QUOTES, 'UTF-8', true);?>
;"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['s']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a>
                            <div class="view_info_visited__percent"><?php echo round($_smarty_tpl->tpl_vars['ordersCount']->value->count/$_smarty_tpl->tpl_vars['count_orders_for_statuses']->value*100,1);?>
%</div>
                        </div>
                        <div class="view_info_visited__right">
                            <div class="view_info_visited__count"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['ordersCount']->value->count, ENT_QUOTES, 'UTF-8', true);?>
</div>
                        </div>
                    </div>
                </div>
            <?php }?>
        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_step-0 fn_toggle_wrap">
                <div class="heading_box visible_md">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_filter, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="boxed_sorting toggle_body_wrap off fn_card">
                                <div class="row">
                    <?php if ($_smarty_tpl->tpl_vars['all_status']->value) {?>
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <select name="status" class="selectpicker form-control"  onchange="location = this.value;">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['all_status']->value, 'order_status');
$_smarty_tpl->tpl_vars['order_status']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['order_status']->value) {
$_smarty_tpl->tpl_vars['order_status']->do_else = false;
?>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OrdersAdmin','status'=>$_smarty_tpl->tpl_vars['order_status']->value->id,'keyword'=>null,'id'=>null,'page'=>null,'label'=>null,'from_date'=>null,'to_date'=>null),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['status_id']->value == $_smarty_tpl->tpl_vars['order_status']->value->id) {?>selected=""<?php }?> ><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order_status']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OrdersAdmin','status'=>null,'keyword'=>null,'id'=>null,'page'=>null,'label'=>null,'from_date'=>null,'to_date'=>null),$_smarty_tpl ) );?>
" <?php if (!$_smarty_tpl->tpl_vars['status_id']->value) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_all_status, ENT_QUOTES, 'UTF-8', true);?>
</option>
                            </select>
                        </div>
                    <?php }?>
                    <?php if ($_smarty_tpl->tpl_vars['labels']->value) {?>
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <select class="selectpicker form-control" onchange="location = this.value;">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['labels']->value, 'l');
$_smarty_tpl->tpl_vars['l']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->do_else = false;
?>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('label'=>$_smarty_tpl->tpl_vars['l']->value->id),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['label_id']->value == $_smarty_tpl->tpl_vars['l']->value->id) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('label'=>null),$_smarty_tpl ) );?>
" <?php if (!$_smarty_tpl->tpl_vars['label_id']->value) {?> selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_all_label, ENT_QUOTES, 'UTF-8', true);?>
</option>
                            </select>
                        </div>
                    <?php }?>
                    <div class="col-md-3 col-lg-3 col-sm-12 float-md-right">
                        <form class="search" method="get">
                            <input type="hidden" name="controller" value="OrdersAdmin">
                            <?php if ($_smarty_tpl->tpl_vars['from_date']->value) {?>
                                <input type="hidden" name="from_date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['from_date']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['to_date']->value) {?>
                                <input type="hidden" name="to_date" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['to_date']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['status_id']->value) {?>
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['status_id']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['label_id']->value) {?>
                                <input type="hidden" name="label" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['label_id']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
                            <?php }?>
                            <div class="input-group input-group--search">
                                <input name="keyword" class="form-control" placeholder="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_search, ENT_QUOTES, 'UTF-8', true);?>
" type="text" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword']->value, ENT_QUOTES, 'UTF-8', true);?>
" >
                                <button type="submit" class="btn"><i class="fa fa-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
            <?php ob_start();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"orders_custom_block"),$_smarty_tpl ) );
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->_assignInScope('block', $_prefixVariable1);?>
            <?php if (!empty($_smarty_tpl->tpl_vars['block']->value)) {?>
                <div class="custom_block">
                    <?php echo $_smarty_tpl->tpl_vars['block']->value;?>

                </div>
            <?php }?>
        </div>
    </div>

        <?php if ($_smarty_tpl->tpl_vars['orders']->value) {?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form class="fn_form_list" method="post">
                    <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">

                    <div class="orders_list okay_list products_list">
                                                <div class="okay_list_head" style="border-left: 5px solid transparent">
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_order_number"><?php echo $_smarty_tpl->tpl_vars['btr']->value->order_match_id;?>
</div>
                            <div class="okay_list_heading okay_list_orders_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_full_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_order_status"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_status, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_order_product_count"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_products, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_orders_price"><?php echo $_smarty_tpl->tpl_vars['btr']->value->general_sales_amount;?>
</div>
                            <div class="okay_list_heading okay_list_order_marker"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_label, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_close hidden-sm-down"></div>
                        </div>
                                                <div class="okay_list_body">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['orders']->value, 'order');
$_smarty_tpl->tpl_vars['order']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['order']->value) {
$_smarty_tpl->tpl_vars['order']->do_else = false;
?>
                            <div class="fn_step-1 fn_row okay_list_body_item " style="border-left: 5px solid #<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->status_color, ENT_QUOTES, 'UTF-8', true);?>
;">
                                <div class="okay_list_row">
                                    <div class="okay_list_boding okay_list_check">
                                        <input class="hidden_check" type="checkbox" id="id_<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
"/>
                                        <label class="okay_ckeckbox" for="id_<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
"></label>
                                    </div>

                                    <div class="okay_list_boding okay_list_order_number">
                                        <a class="text_600 mb-h" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OrderAdmin','id'=>$_smarty_tpl->tpl_vars['order']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
"><span class="hidden-sm-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_order, ENT_QUOTES, 'UTF-8', true);?>
</span> #<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
</a>
                                        <?php if ($_smarty_tpl->tpl_vars['order']->value->last_update) {?>
                                            <span class="tag tag-update fn_history_toggle"><?php echo $_smarty_tpl->tpl_vars['btr']->value->order_history_changed;?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->last_update->date ));?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'time' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->last_update->date ));?>
 <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i></span>
                                        <?php } else { ?>
                                            <span class="tag tag-update fn_history_toggle"><?php echo $_smarty_tpl->tpl_vars['btr']->value->order_history_created;?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->date ));?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'time' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->date ));?>
 <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i></span>
                                        <?php }?>
                                        
                                        <?php if ($_smarty_tpl->tpl_vars['order']->value->paid) {?>
                                            <div class="order_paid">
                                                <span class="tag tag-success"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_paid, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                            </div>
                                        <?php }?>
                                        <?php if ($_smarty_tpl->tpl_vars['order']->value->referer_channel) {?>
                                            <div class="order_paid">
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
                                                    <span class="tag tag-chanel_social" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_social'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                                    </span>
                                                <?php } elseif ($_smarty_tpl->tpl_vars['order']->value->referer_channel == Okay\Core\UserReferer\UserReferer::CHANNEL_REFERRAL) {?>
                                                    <a href="https://<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank" class="tag tag-chanel_referral" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_referral'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                                    </a>
                                                <?php } else { ?>
                                                    <span class="tag tag-ind_unknown" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_source, ENT_QUOTES, 'UTF-8', true);?>
">
                                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag_unknown'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->referer_channel, ENT_QUOTES, 'UTF-8', true);?>

                                                    </span>
                                                <?php }?>
                                            </div>
                                        <?php }?>
                                    </div>

                                    <div class="okay_list_boding okay_list_orders_name">
                                        <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OrderAdmin','id'=>$_smarty_tpl->tpl_vars['order']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
" class="text_400 mb-q"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->name, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->last_name, ENT_QUOTES, 'UTF-8', true);?>
</a>
                                        <div class="hidden-lg-up mb-h">
                                            <div class="text_600 font_12" style="color: #<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->status_color, ENT_QUOTES, 'UTF-8', true);?>
;"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['orders_status']->value[$_smarty_tpl->tpl_vars['order']->value->status_id]->name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                        </div>
                                        <div class="font_12 text_500 text_grey mb-q"><span class="hidden-md-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_order_in, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                        <span class="font_12 text_500 text_grey mb-q"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'date' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->date ));?>
 | <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'time' ][ 0 ], array( $_smarty_tpl->tpl_vars['order']->value->date ));?>
</span></div>
                                        <?php if ($_smarty_tpl->tpl_vars['order']->value->note) {?>
                                        <div class="tag tag-chanel_search mb-q"><?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'warn_icon'), 0, true);
?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->note, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                        <?php }?>

                                        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"orders_list_name",'vars'=>array('order'=>$_smarty_tpl->tpl_vars['order']->value)),$_smarty_tpl ) );?>

                                    </div>

                                    <div class="okay_list_boding okay_list_order_status">
                                        <div class="text_600 font_14" style="color: #<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->status_color, ENT_QUOTES, 'UTF-8', true);?>
;"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['orders_status']->value[$_smarty_tpl->tpl_vars['order']->value->status_id]->name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                    </div>

                                    <div class="okay_list_boding okay_list_order_product_count">
                                        <span><?php echo count($_smarty_tpl->tpl_vars['order']->value->purchases);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_unit, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                        <?php if (count($_smarty_tpl->tpl_vars['order']->value->purchases) > 0) {?>
                                            <span  class="fn_orders_toggle">
                                                <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 "></i>
                                            </span>
                                        <?php }?>
                                    </div>

                                    <div class="okay_list_boding okay_list_orders_price">
                                        <div class="input-group">
                                            <span class="form-control">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['order']->value->total_price, ENT_QUOTES, 'UTF-8', true);?>

                                            </span>
                                            <span class="input-group-addon">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->code, ENT_QUOTES, 'UTF-8', true);?>

                                            </span>
                                        </div>
                                    </div>

                                    <div class="okay_list_boding okay_list_order_marker">
                                        <span class="fn_ajax_label_wrapper">
                                            <span class="fn_labels_show box_labels_show"><?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'tag'), 0, true);
?> <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_labels, ENT_QUOTES, 'UTF-8', true);?>
 </span> </span>

                                            <div class='fn_labels_hide box_labels_hide'>
                                                <span class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_choose, ENT_QUOTES, 'UTF-8', true);?>
 <i class="fn_delete_labels_hide btn_close delete_labels_hide"><?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?></i></span>
                                                <ul class="option_labels_box">
                                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['labels']->value, 'l');
$_smarty_tpl->tpl_vars['l']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['l']->value) {
$_smarty_tpl->tpl_vars['l']->do_else = false;
?>
                                                        <li class="fn_ajax_labels" data-order_id="<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
"  style="background-color: #<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->color, ENT_QUOTES, 'UTF-8', true);?>
">
                                                            <input id="l<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
_<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
" type="checkbox" class="hidden_check_1"  value="<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
" <?php if (is_array($_smarty_tpl->tpl_vars['order']->value->labels_ids) && in_array($_smarty_tpl->tpl_vars['l']->value->id,$_smarty_tpl->tpl_vars['order']->value->labels_ids)) {?>checked=""<?php }?> />
                                                            <label   for="l<?php echo $_smarty_tpl->tpl_vars['order']->value->id;?>
_<?php echo $_smarty_tpl->tpl_vars['l']->value->id;?>
" class="label_labels"><span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['l']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</span></label>
                                                        </li>
                                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                </ul>
                                            </div>
                                            <div class="fn_order_labels orders_labels">
                                                <?php $_smarty_tpl->_subTemplateRender("file:labels_ajax.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, true);
?>
                                            </div>
                                        </span>
                                    </div>

                                    <div class="okay_list_boding okay_list_close hidden-sm-down">
                                                                                <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_delete, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));" >
                                           <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'trash'), 0, true);
?>
                                        </button>
                                    </div>
                                </div>
                                                                <div class="okay_list_row order_history_block" style="display: none">
                                    <?php $_smarty_tpl->_subTemplateRender('file:order_history.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('order_history'=>$_smarty_tpl->tpl_vars['orders_history']->value[$_smarty_tpl->tpl_vars['order']->value->id]), 0, true);
?>
                                </div>

                                                                <?php if (count($_smarty_tpl->tpl_vars['order']->value->purchases) > 0) {?>
                                <div class="okay_list_row orders_purchases_block" style="display: none">
                                    <div class="" >
                                        <div class="purchases_table">
                                            <div class="purchases_head">
                                                <div class="purchases_heading purchases_table_orders_num">№</div>
                                                <div class="purchases_heading purchases_table_orders_sku"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_sku, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                <div class="purchases_heading purchases_table_orders_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                <div class="purchases_heading purchases_table_orders_price"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_price, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                <div class="purchases_heading col-lg-2 purchases_table_orders_unit"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_qty, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                <div class="purchases_heading purchases_table_orders_total"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_total_price, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                            </div>
                                            <div class="purchases_body">
                                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['order']->value->purchases, 'purchase');
$_smarty_tpl->tpl_vars['purchase']->iteration = 0;
$_smarty_tpl->tpl_vars['purchase']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['purchase']->value) {
$_smarty_tpl->tpl_vars['purchase']->do_else = false;
$_smarty_tpl->tpl_vars['purchase']->iteration++;
$__foreach_purchase_6_saved = $_smarty_tpl->tpl_vars['purchase'];
?>
                                                    <div class="purchases_body_items">
                                                        <div class="purchases_body_item">
                                                            <div class="purchases_bodyng purchases_table_orders_num"><?php echo $_smarty_tpl->tpl_vars['purchase']->iteration;?>
</div>
                                                            <div class="purchases_bodyng purchases_table_orders_sku"><?php echo (($tmp = @$_smarty_tpl->tpl_vars['purchase']->value->sku)===null||$tmp==='' ? "&mdash;" : $tmp);?>
</div>
                                                            <div class="purchases_bodyng purchases_table_orders_name">
                                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->product_name, ENT_QUOTES, 'UTF-8', true);?>

                                                                <?php if ($_smarty_tpl->tpl_vars['purchase']->value->variant_name) {?>(<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->variant_name, ENT_QUOTES, 'UTF-8', true);?>
)<?php }?>
                                                            </div>
                                                            <div class="purchases_bodyng purchases_table_orders_price"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'convert' ][ 0 ], array( $_smarty_tpl->tpl_vars['purchase']->value->price ));?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                            <div class="purchases_bodyng purchases_table_orders_unit"> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->amount, ENT_QUOTES, 'UTF-8', true);
if ($_smarty_tpl->tpl_vars['purchase']->value->units) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['purchase']->value->units, ENT_QUOTES, 'UTF-8', true);
} else {
echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->units, ENT_QUOTES, 'UTF-8', true);
}?></div>
                                                            <div class="purchases_bodyng purchases_table_orders_total"> <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'convert' ][ 0 ], array( ($_smarty_tpl->tpl_vars['purchase']->value->amount*$_smarty_tpl->tpl_vars['purchase']->value->price) ));?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['currency']->value->sign, ENT_QUOTES, 'UTF-8', true);?>
</div>

                                                         </div>
                                                    </div>
                                                <?php
$_smarty_tpl->tpl_vars['purchase'] = $__foreach_purchase_6_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </div>
                                                <div class="okay_list_footer">
                            <div class="okay_list_foot_left">
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker form-control fn_change_orders">
                                        <option value="0"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_select_action, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option data-item="status" value="change_status"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_change_status, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option data-item="label" value="set_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_set_label, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option data-item="label" value="unset_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_unset_label, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option data-item="remove" value="delete"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_permanently_delete, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                    </select>
                                </div>
                                <div class="okay_list_option fn_show_label" style="display: none">
                                    <select name="change_label_id" class="selectpicker form-control px-0 fn_labels_select" >
                                        <option value="0"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_select_label, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['labels']->value, 'change_label');
$_smarty_tpl->tpl_vars['change_label']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['change_label']->value) {
$_smarty_tpl->tpl_vars['change_label']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['change_label']->value->id;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['change_label']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                                <div class="okay_list_option fn_show_status" style="display: none;">
                                    <select name="change_status_id" class="selectpicker form-control px-0 fn_labels_select">
                                        <option value="0"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_select_status, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['all_status']->value, 'change_status');
$_smarty_tpl->tpl_vars['change_status']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['change_status']->value) {
$_smarty_tpl->tpl_vars['change_status']->do_else = false;
?>
                                            <option value="<?php echo $_smarty_tpl->tpl_vars['change_status']->value->id;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['change_status']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class=" btn btn_small btn_blue">
                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                                <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                <?php $_smarty_tpl->_subTemplateRender('file:pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
        </div>
    <?php } else { ?>
        <div class="heading_box mt-1">
            <div class="text_grey"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->orders_no, ENT_QUOTES, 'UTF-8', true);?>
</div>
        </div>
    <?php }?>
</div>

<?php $_smarty_tpl->_subTemplateRender('file:learning_hints.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('hintId'=>'hint_orders'), 0, false);
?>

<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/backend/design/js/piecon/piecon.js"><?php echo '</script'; ?>
>


<?php echo '<script'; ?>
>

$(function() {

    $(document).on('click','.fn_orders_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.orders_purchases_block').toggle();
    });

    $(document).on('click','.fn_history_toggle',function(){
        $(this).find('.fn_icon_arrow').toggleClass('rotate_180');
        $(this).parents('.fn_row').find('.order_history_block').toggle();
    });

    $(".fn_labels_show").click(function(){
        $(this).next('.fn_labels_hide').toggleClass("active_labels");
    });
    $(".fn_delete_labels_hide").click(function(){
        $(this).closest('.box_labels_hide').removeClass("active_labels");
    });
    $(document).on('click', function(e) {
        if (!$(e.target).closest(".fn_ajax_label_wrapper ").find('.active_labels').length) {
            $('.fn_labels_hide').removeClass("active_labels");
        }
        e.stopPropagation();
    });

    function compileDateString(date) {
        let day   = String(date.getDate());
        if (day.length === 1) {
            day = `0${day}`;
        }
        let month = String(Number(date.getMonth()) + 1);
        if (month.length === 1) {
            month = `0${month}`;
        }
        const year  = String(date.getFullYear());
        return `${day}-${month}-${year}`;
    }

    $('.fn_last_week').on('click', function() {
        const date   = new Date();
        const day = date.getDay();
        const dateTo = compileDateString(date);
        $('.fn_to_date').val(dateTo);

        date.setDate(date.getDate() - (day ? day : 7) + 1);
        const dateFrom = compileDateString(date);
        $('.fn_from_date').val(dateFrom);

        $('.fn_date_filter').submit();
    });

    $('.fn_30_days').on('click', function() {
        const date   = new Date();
        const dateTo = compileDateString(date);
        $('.fn_to_date').val(dateTo);

        date.setDate(date.getDate() + 1);
        date.setMonth(date.getMonth() - 1);
        const dateFrom = compileDateString(date);
        $('.fn_from_date').val(dateFrom);

        $('.fn_date_filter').submit();
    });

    $('.fn_7_days').on('click', function() {
        const date   = new Date();
        const dateTo = compileDateString(date);
        $('.fn_to_date').val(dateTo);

        date.setDate(date.getDate() - 6);
        const dateFrom = compileDateString(date);
        $('.fn_from_date').val(dateFrom);

        $('.fn_date_filter').submit();
    });

    $('.fn_yesterday').on('click', function() {
        const date   = new Date();
        date.setDate(date.getDate() - 1);

        const dateTo = compileDateString(date);
        $('.fn_to_date').val(dateTo);

        const dateFrom = compileDateString(date);
        $('.fn_from_date').val(dateFrom);

        $('.fn_date_filter').submit();
    });

    $('.fn_calendar').on('click', function() {
        $(".fn_calendar_pixel").focus();
    });

    $(".fn_calendar_pixel").datepicker({
        dateFormat: 'dd-mm-yy',
        range_multiple_max: 2,
        range: 'period',
        onSelect: function(_, __, range){
            $('.fn_from_date').val(range.startDateText);
            $('.fn_to_date').val(range.endDateText);
        }
    });

    $('.fn_reset_date_filter').on('click', function() {
        $('.fn_to_date').val('');
        $('.fn_from_date').val('');
        $('.fn_date_filter').submit();
    });


    $(document).on("change", ".fn_change_orders", function () {
       var item = $(this).find("option:selected").data("item");
       if(item == "status") {
           $(".fn_show_label").hide();
           $(".fn_show_status").show();

       } else if (item == "label") {
           $(".fn_show_label").show();
           $(".fn_show_status").hide();
       } else {
           $(".fn_show_label").hide();
           $(".fn_show_status").hide();
       }

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
    
    var status = '<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['status_id']->value, ENT_QUOTES, 'UTF-8', true);?>
',
        label='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['label_id']->value, ENT_QUOTES, 'UTF-8', true);?>
',
        from_date = '<?php echo $_smarty_tpl->tpl_vars['from_date']->value;?>
',
        to_date = '<?php echo $_smarty_tpl->tpl_vars['to_date']->value;?>
';
    
    // On document load
    $(document).on('click','.fn_start_export',function() {
        
        Piecon.setOptions({fallback: 'force'});
        Piecon.setProgress(0);
        var progress_item = $("#progressbar"); //указываем селектор элемента с анимацией
        progress_item.show();
        do_export('',progress_item);
    });

    function do_export(page,progress) {
        page = typeof(page) != 'undefined' ? page : 1;
        label = typeof(label) != 'undefined' ? label : null;
        status = typeof(status) != 'undefined' ? status : null;
        from_date = typeof(from_date) != 'undefined' ? from_date : null;
        to_date = typeof(to_date) != 'undefined' ? to_date : null;
        $.ajax({
            url: "ajax/export_orders.php",
            data: {
                page:page, 
                label:label,
                status:status, 
                from_date:from_date, 
                to_date:to_date
            },
            dataType: 'json',
            success: function(data){
                if(data && !data.end) {
                    Piecon.setProgress(Math.round(100*data.page/data.totalpages));
                    progress.attr('value',100*data.page/data.totalpages);
                    do_export(data.page*1+1,progress);
                }
                else {
                    Piecon.setProgress(100);
                    progress.attr('value','100');
                    window.location.href = 'files/export/export_orders.csv';
                    progress.fadeOut(500);
                }
            },
            error:function(xhr, status, errorThrown) {
                alert(errorThrown+'\n'+xhr.responseText);
            }
        });
    }
});
<?php echo '</script'; ?>
>

<?php }
}
