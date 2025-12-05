<?php
/* Smarty version 3.1.40, created on 2025-11-26 19:42:00
  from '/var/www/html/backend/design/html/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_69273be873d978_95477184',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'badc08af5b8c61e74a0609910761505a2ddc3ced' => 
    array (
      0 => '/var/www/html/backend/design/html/index.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 23,
    'file:video_help.tpl' => 1,
    'file:include_languages.tpl' => 1,
  ),
),false)) {
function content_69273be873d978_95477184 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/html/vendor/smarty/smarty/libs/plugins/modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
    <META HTTP-EQUIV="Expires" CONTENT="-1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['meta_title']->value, ENT_QUOTES, 'UTF-8', true);?>
 | OkayCMS v.<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</title>

    
    <?php echo '<script'; ?>
>
        /* Initializing clipboard */
        sclipboard();
        function sclipboard() {
            const links = document.querySelectorAll('.fn_clipboard');
            const cls = {
                copied: 'is-copied',
                hover: 'hint-anim'
            };

            const copyToClipboard = str => {
                const el = document.createElement('input');
                str.dataset.copyString ? el.value = str.dataset.copyString : el.value = str.text;
                el.setAttribute('readonly', '');
                el.style.position = 'absolute';
                el.style.opacity = 0;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            };
            const clickInteraction = e => {
                e.preventDefault();
                copyToClipboard(e.target);
                e.target.classList.add(cls.copied);
                setTimeout(() => e.target.classList.remove(cls.copied), 1000);
                setTimeout(() => e.target.classList.remove(cls.hover), 700);
            };
            Array.from(links).forEach(link => {
                link.addEventListener('click', e => clickInteraction(e));
                link.addEventListener('keypress', e => {
                    if (e.keyCode === 13) clickInteraction(e);
                });
                link.addEventListener('mouseover', e => e.target.classList.add(cls.hover));
                link.addEventListener('mouseleave', e => {
                    if (!e.target.classList.contains(cls.copied)) {
                        e.target.classList.remove(cls.hover);
                    }
                });
            });
        };

        var okay = {};
        okay.router = {};
        
        <?php if ($_smarty_tpl->tpl_vars['front_routes']->value) {?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['front_routes']->value, 'route', false, 'name');
$_smarty_tpl->tpl_vars['route']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['name']->value => $_smarty_tpl->tpl_vars['route']->value) {
$_smarty_tpl->tpl_vars['route']->do_else = false;
?>
                okay.router['<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
'] = '<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>$_smarty_tpl->tpl_vars['name']->value,'absolute'=>1),$_smarty_tpl ) );?>
';
            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php }?>
        
    <?php echo '</script'; ?>
>
    

    <link href="https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,300i,700|Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&subset=cyrillic,cyrillic-ext,latin-ext" rel="stylesheet" type="text/css">

    <?php echo $_smarty_tpl->tpl_vars['ok_head']->value;?>


    <link rel="icon" href="design/images/favicon.png" type="image/x-icon" />

    <?php if (in_array($_GET['controller'],array("OrdersAdmin","PostAdmin","ReportStatsAdmin","CouponsAdmin","CategoryStatsAdmin"))) {?>
        <?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['manager']->value->lang, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable1=ob_get_clean();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['js'][0], array( array('file'=>"jquery/datepicker/jquery.ui.datepicker-".$_prefixVariable1.".js",'admin'=>true),$_smarty_tpl ) );?>

        <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['js'][0], array( array('file'=>"jquery/datepicker/jquery.datepicker.extension.range.min.js",'admin'=>true),$_smarty_tpl ) );?>

    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['settings']->value->gather_enabled) {?>
        
        <!-- Google Tag Manager -->
        <?php echo '<script'; ?>
>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
                    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
                    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
                    })(window,document,'script','dataLayer','GTM-P6T2LJP');
        <?php echo '</script'; ?>
>
        <!-- End Google Tag Manager -->
        
    <?php }?>
</head>
<body class="navbar-fixed <?php if ($_smarty_tpl->tpl_vars['manager']->value->menu_status && $_smarty_tpl->tpl_vars['is_mobile']->value === false && $_smarty_tpl->tpl_vars['is_tablet']->value === false) {?>menu-pin<?php }?>">
    <!-- Google Tag Manager (noscript) -->
    <?php if ($_smarty_tpl->tpl_vars['settings']->value->gather_enabled) {?>
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P6T2LJP" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php }?>
    <!-- End Google Tag Manager (noscript) -->
    <header class="navbar">
        <div class="container-fluid">
            <div id="mobile_menu" class="fn_mobile_menu hidden-xl-up  text_white">
                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'mobile_menu'), 0, false);
?>
            </div>
            <div class="admin_switches">
                <div class="box_adswitch">
                    <a class="btn_admin" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>'main','absolute'=>1),$_smarty_tpl ) );?>
">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_desktop'), 0, true);
?>
                    <span class="hidden-md-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_go_to_site, ENT_QUOTES, 'UTF-8', true);?>
</span>
                    </a>
                </div>
            </div>
            <div class="admin_switches admin_switches_two hidden-sm-down">
                <?php $_smarty_tpl->_subTemplateRender("file:video_help.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
            <div class="admin_switches admin_switches_three">
                <div class="box_adswitch">
                    <?php if (!empty($_smarty_tpl->tpl_vars['has_new_version']->value)) {?>
                        <a class="btn_admin btn_version_old hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_btn_version_old, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['has_new_version']->value['version'], ENT_QUOTES, 'UTF-8', true);?>
" <?php if ($_smarty_tpl->tpl_vars['has_new_version']->value['info_href']) {?>target="_blank" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['has_new_version']->value['info_href'], ENT_QUOTES, 'UTF-8', true);?>
"<?php } else { ?>href="javascript:;"<?php }?>>
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'no_icon'), 0, true);
?>
                            <span class="">Version <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        </a>
                    <?php } else { ?>
                        <div class="btn_admin btn_version_new hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_btn_version_new, ENT_QUOTES, 'UTF-8', true);?>
">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'yes_icon'), 0, true);
?>
                            <span class="">Version <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        </div>
                    <?php }?>
                </div>
            </div>
            <?php if ($_smarty_tpl->tpl_vars['settings']->value->email_for_module) {?>
            <?php } else { ?>
            <div class="admin_switches hidden-md-down">
                <div class="box_adswitch  hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_btn_email_info_hint, ENT_QUOTES, 'UTF-8', true);?>
">
                    <a class="btn_inner"  href="index.php?controller=ModulesAdmin">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'warn_icon'), 0, true);
?>
                        <span class=""><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_btn_email_info, ENT_QUOTES, 'UTF-8', true);?>
</span>
                    </a>
                </div>
            </div>
            <?php }?>
            <div id="mobile_menu_right" class="fn_mobile_menu_right hidden-md-up  text_white float-xs-right">
                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'mobile_menu2'), 0, true);
?>
            </div>
            <div id="quickview" class="fn_quickview">
                <div class="sidebar_header hidden-md-up">
                        <span class="fn_switch_quickview menu_switch">
                            <span class="menu_hamburger"></span>
                        </span>
                    <a href="index.php?controller=<?php echo $_smarty_tpl->tpl_vars['manager_main_controller']->value;?>
" class="logo_box">
                        <img src="design/images/logo_title.png" alt="OkayCMS"/>
                    </a>
                </div>
                <div class="admin_exit hidden-sm-down hint-bottom-right-t-info-s-small-mobile  hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_exit, ENT_QUOTES, 'UTF-8', true);?>
">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
?logout">
                                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'exit'), 0, true);
?>
                    </a>
                </div>
                <div class="admin_name hint-bottom-middle-t-info-s-small-mobile  hint-anim" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['manager']->value->login, ENT_QUOTES, 'UTF-8', true);?>
">
                    <a href="index.php?controller=ManagerAdmin&id=<?php echo $_smarty_tpl->tpl_vars['manager']->value->id;?>
">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'user2_icon'), 0, true);
?>
                        <span class="hidden-md-up"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['manager']->value->login, ENT_QUOTES, 'UTF-8', true);?>
</span>
                    </a>
                </div>
                                <div class="admin_notification">
                    <div class="notification_inner">
                        <span class="notification_title" href="">
                            <span class="quickview_hidden hidden-md-up"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_notifications, ENT_QUOTES, 'UTF-8', true);?>
</span>
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'notify'), 0, true);
?>
                            <?php if ($_smarty_tpl->tpl_vars['all_counter']->value) {?>
                                <span class="counter"><?php echo $_smarty_tpl->tpl_vars['all_counter']->value;?>
</span>
                            <?php }?>
                        </span>
                        <div class="notification_toggle">
                            <?php if ($_smarty_tpl->tpl_vars['new_orders_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=OrdersAdmin" class="l_notif">
                                        <span class="notif_icon boxed_notify">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'left_orders'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_orders, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['new_orders_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['new_comments_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=CommentsAdmin" class="l_notif">
                                        <span class="notif_icon boxed_warning">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'left_comments'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_comments, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['new_comments_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['new_feedbacks_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=FeedbacksAdmin" class="l_notif">
                                        <span class="notif_icon boxed_yellow">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'email'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_feedback, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['new_feedbacks_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['new_callbacks_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=CallbacksAdmin" class="l_notif">
                                        <span class="notif_icon boxed_attention">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'phone'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_callback, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['new_callbacks_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['modules_access_expire_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=ModulesAdmin" class="l_notif">
                                        <span class="notif_icon boxed_notify">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'left_modules'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->left_modules, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['modules_access_expire_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['not_licensed_modules_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=ModulesAdmin" class="l_notif">
                                        <span class="notif_icon boxed_notify">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'left_modules'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->left_modules, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['not_licensed_modules_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php if ($_smarty_tpl->tpl_vars['template_error_counter']->value > 0) {?>
                                <div class="notif_item">
                                    <a href="index.php?controller=ThemeAdmin" class="l_notif">
                                        <span class="notif_icon boxed_notify">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'left_design'), 0, true);
?>
                                        </span>
                                        <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->left_theme_title, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    </a>
                                    <span class="notif_count"><?php echo $_smarty_tpl->tpl_vars['template_error_counter']->value;?>
</span>
                                </div>
                            <?php }?>
                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"notification_counters"),$_smarty_tpl ) );?>

                            <?php if (!$_smarty_tpl->tpl_vars['all_counter']->value) {?>
                                <div class="notif_item">
                                    <span class="notif_title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_no_notification, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
                                <div class="admin_techsupport">
                    <div class="techsupport_inner">
                        <a <?php if ($_smarty_tpl->tpl_vars['support_info']->value->public_key) {?> data-hint="<?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'balance' ][ 0 ], array( $_smarty_tpl->tpl_vars['support_info']->value->balance ));?>
"<?php } else { ?> data-hint="Not active" <?php }?>  class="hint-bottom-middle-t-info-s-small-mobile  hint-anim"  href="index.php?controller=SupportAdmin">
                            <span class="quickview_hidden hidden-lg-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_support, ENT_QUOTES, 'UTF-8', true);?>
</span>
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'techsupport'), 0, true);
?>
                            <?php if ($_smarty_tpl->tpl_vars['support_info']->value->public_key) {?>
                            <span class="counter"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['support_info']->value->new_messages, ENT_QUOTES, 'UTF-8', true);?>
</span>
                            <?php }?>
                        </a>
                        <div class="techsupport_toggle hidden-md-up">
                            <?php if ($_smarty_tpl->tpl_vars['support_info']->value->public_key) {?>
                            <span><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'balance' ][ 0 ], array( $_smarty_tpl->tpl_vars['support_info']->value->balance ));?>
</span>
                            <?php } else { ?>
                            <span>Not active</span>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <div class="admin_languages" >
                    <div class="languages_inner">
                        <span class="languages_title hidden-md-up"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_languages, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        <?php $_smarty_tpl->_subTemplateRender("file:include_languages.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
                    </div>
                </div>
                <div class="admin_exit hidden-md-up">
                    <a href="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
?logout">
                        <span class=""><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_exit, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'exit'), 0, true);
?>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <nav id="admin_catalog" class="fn_left_menu">
        <div id="mob_menu"></div>
        <div class="sidebar_header">
            <a href="javascript:;" id="fix_logo" class="hidden-lg-down"></a>

            <a href="index.php?controller=<?php echo $_smarty_tpl->tpl_vars['manager_main_controller']->value;?>
" class="logo_box">
                <img src="design/images/logo_title.png" alt="OkayCMS"/>
            </a>
            <?php if ($_smarty_tpl->tpl_vars['is_mobile']->value === false && $_smarty_tpl->tpl_vars['is_tablet']->value === false) {?>
                <span class="fn_switch_menu menu_switch fn_ajax_action <?php if ($_smarty_tpl->tpl_vars['manager']->value->menu_status) {?>fn_active_class<?php }?> hint-left-middle-t-white-s-small-mobile  hint-anim" data-controller="managers" data-action="menu_status" data-id="<?php echo $_smarty_tpl->tpl_vars['manager']->value->id;?>
" data-hint="<?php echo $_smarty_tpl->tpl_vars['btr']->value->catalog_fixation;?>
">
                    <span class="menu_hamburger"></span>
                </span>
            <?php } else { ?>
                <span class="fn_switch_menu menu_switch" data-controller="managers" data-action="menu_status" data-id="<?php echo $_smarty_tpl->tpl_vars['manager']->value->id;?>
">
                    <span class="menu_hamburger"></span>
                </span>
            <?php }?>
        </div>
                <div class="sidebar sidebar-menu">
            <div class="scrollbar-inner menu_items">
                <div>
                    <form class="fn_manager_menu">
                        <input type="hidden" name="object" value="managers" />
                        <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
" />
                        <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['manager']->value->id;?>
" />
                        <ul id="fn_sort_menu_section" class="menu_items">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['left_menu']->value, 'items', false, 'section');
$_smarty_tpl->tpl_vars['items']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['section']->value => $_smarty_tpl->tpl_vars['items']->value) {
$_smarty_tpl->tpl_vars['items']->do_else = false;
?>
                                <li class="<?php if ((isset($_smarty_tpl->tpl_vars['items']->value[$_smarty_tpl->tpl_vars['menu_selected']->value]))) {?>open active<?php }?> <?php if (count($_smarty_tpl->tpl_vars['items']->value) > 1) {?> fn_item_sub_switch nav-dropdown<?php }?>">
                                    <?php if (count($_smarty_tpl->tpl_vars['items']->value) == 1) {?>
                                        <input type="hidden" value="<?php echo reset($_smarty_tpl->tpl_vars['items']->value);?>
" name="manager_menu[<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['section']->value, ENT_QUOTES, 'UTF-8', true);?>
][<?php echo key($_smarty_tpl->tpl_vars['items']->value);?>
]" />
                                    <?php }?>

                                    <?php if ($_smarty_tpl->tpl_vars['config']->value->dev_mode) {?>
                                        <div class="fn_backend_menu_section" data-section_name="<?php echo $_smarty_tpl->tpl_vars['section']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['section']->value;?>
</div>
                                    <?php }?>

                                    <a class="fn_learning_<?php echo $_smarty_tpl->tpl_vars['section']->value;?>
 nav-link <?php if (count($_smarty_tpl->tpl_vars['items']->value) > 1) {?>fn_item_switch nav-dropdown-toggle<?php }
if ($_smarty_tpl->tpl_vars['section']->value == 'left_modules' && ($_smarty_tpl->tpl_vars['modules_access_expire_counter']->value > 0 || $_smarty_tpl->tpl_vars['not_licensed_modules_counter']->value > 0)) {?> danger_counter<?php } elseif ($_smarty_tpl->tpl_vars['section']->value == 'left_design' && $_smarty_tpl->tpl_vars['template_error_counter']->value) {?> danger_counter<?php }?>" href="<?php if (count($_smarty_tpl->tpl_vars['items']->value) > 1) {?>javascript:;<?php } else { ?>index.php?controller=<?php echo reset(reset($_smarty_tpl->tpl_vars['items']->value));
}?>">
                                        <span class="<?php echo $_smarty_tpl->tpl_vars['section']->value;?>
 title"><?php ob_start();
echo $_smarty_tpl->tpl_vars['section']->value;
$_prefixVariable2 = ob_get_clean();
echo $_smarty_tpl->tpl_vars['btr']->value->getTranslation($_prefixVariable2);?>
</span>
                                        <span class="icon-thumbnail">
                                            <?php if (!empty($_smarty_tpl->tpl_vars['additional_section_icons']->value[$_smarty_tpl->tpl_vars['section']->value])) {?>
                                                <?php if ($_smarty_tpl->tpl_vars['additional_section_icons']->value[$_smarty_tpl->tpl_vars['section']->value]['type'] === 'file') {?>
                                                    <img src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['additional_section_icons']->value[$_smarty_tpl->tpl_vars['section']->value]['data'];?>
">
                                                <?php } else { ?>
                                                    <?php echo $_smarty_tpl->tpl_vars['additional_section_icons']->value[$_smarty_tpl->tpl_vars['section']->value]['data'];?>

                                                <?php }?>
                                            <?php } else { ?>
                                                <?php ob_start();
$_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>$_smarty_tpl->tpl_vars['section']->value), 0, true);
$_prefixVariable3 = ob_get_clean();
$_smarty_tpl->_assignInScope('svg_icon', $_prefixVariable3);?>
                                                <?php if ($_smarty_tpl->tpl_vars['svg_icon']->value) {?>
                                                    <?php echo $_smarty_tpl->tpl_vars['svg_icon']->value;?>

                                                <?php } else { ?>
                                                    <?php ob_start();
echo $_smarty_tpl->tpl_vars['section']->value;
$_prefixVariable4 = ob_get_clean();
ob_start();
echo $_smarty_tpl->tpl_vars['btr']->value->getTranslation($_prefixVariable4);
$_prefixVariable5 = ob_get_clean();
$_smarty_tpl->_assignInScope('translation', $_prefixVariable5);?>
                                                    <span class="manager_menu_section_icon"><?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'first_letter' ][ 0 ], array( $_smarty_tpl->tpl_vars['translation']->value ));?>
</span>
                                                <?php }?>
                                            <?php }?>
                                        </span>
                                        <?php if (count($_smarty_tpl->tpl_vars['items']->value) > 1) {?>
                                            <span class="arrow"></span>
                                        <?php }?>
                                        <?php if ((isset($_smarty_tpl->tpl_vars['menu_counters']->value[$_smarty_tpl->tpl_vars['section']->value])) && !empty($_smarty_tpl->tpl_vars['menu_counters']->value[$_smarty_tpl->tpl_vars['section']->value])) {?>
                                            <span class="menu_counter">
                                                <?php echo $_smarty_tpl->tpl_vars['menu_counters']->value[$_smarty_tpl->tpl_vars['section']->value];?>

                                            </span>
                                        <?php }?>
                                    </a>
                                    <?php if (count($_smarty_tpl->tpl_vars['items']->value) > 1) {?>
                                        <ul class="fn_submenu_toggle submenu fn_sort_menu_item">
                                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item', false, 'title');
$_smarty_tpl->tpl_vars['item']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['title']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['item']->do_else = false;
?>
                                                <li class="<?php if (in_array($_smarty_tpl->tpl_vars['controller_selected']->value,$_smarty_tpl->tpl_vars['item']->value['controllers_block'])) {?>active<?php }?>">
                                                    <input type="hidden" name="manager_menu[<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['section']->value, ENT_QUOTES, 'UTF-8', true);?>
][<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8', true);?>
]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['item']->value['controller'], ENT_QUOTES, 'UTF-8', true);?>
" />
                                                    <a class="fn_learning_<?php echo $_smarty_tpl->tpl_vars['item']->value['controller'];?>
 nav-link" href="index.php?controller=<?php echo $_smarty_tpl->tpl_vars['item']->value['controller'];
if (!empty($_smarty_tpl->tpl_vars['item']->value['method'])) {?>@<?php echo $_smarty_tpl->tpl_vars['item']->value['method'];
}?>">
                                                        <span class="icon-thumbnail">
                                                            <?php if (((isset($_smarty_tpl->tpl_vars['menu_counters']->value[$_smarty_tpl->tpl_vars['title']->value])) && !empty($_smarty_tpl->tpl_vars['menu_counters']->value[$_smarty_tpl->tpl_vars['title']->value])) || $_smarty_tpl->tpl_vars['config']->value->dev_mode) {?>
                                                                <span class="menu_counter">
                                                                    <?php if ($_smarty_tpl->tpl_vars['config']->value->dev_mode) {?>
                                                                        <div class="fn_backend_menu_section menu_counter_name" data-section_name="<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['title']->value;?>
</div>
                                                                    <?php }?>
                                                                    <?php echo $_smarty_tpl->tpl_vars['menu_counters']->value[$_smarty_tpl->tpl_vars['title']->value];?>

                                                                </span>
                                                            <?php }?>
                                                            <?php ob_start();
echo $_smarty_tpl->tpl_vars['title']->value;
$_prefixVariable6 = ob_get_clean();
echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'first_letter' ][ 0 ], array( $_smarty_tpl->tpl_vars['btr']->value->getTranslation($_prefixVariable6) ));?>

                                                        </span>
                                                        <span class="<?php echo $_smarty_tpl->tpl_vars['title']->value;?>
 title"><?php ob_start();
echo $_smarty_tpl->tpl_vars['title']->value;
$_prefixVariable7 = ob_get_clean();
echo $_smarty_tpl->tpl_vars['btr']->value->getTranslation($_prefixVariable7);?>
</span>
                                                    </a>
                                                </li>
                                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                        </ul>
                                    <?php }?>
                                </li>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
    </nav>
        <div class="page-container">
        <a href='<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>"main",'absolute'=>1),$_smarty_tpl ) );?>
' class='admin_bookmark'></a>

        <div class="main">
            <div class="container-fluid">
                <div class="min_content_fix">
                    <?php if ($_smarty_tpl->tpl_vars['content']->value) {?>
                        <?php echo $_smarty_tpl->tpl_vars['content']->value;?>

                    <?php } else { ?>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 mt-1">
                                <div class="boxed boxed_warning">
                                    <div class="heading_box">
                                        <?php echo $_smarty_tpl->tpl_vars['btr']->value->general_no_permission;?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }?>
                </div>
                <footer id="footer" class="">
                    <div class="col-md-12 font_12 text_white">
                        <a href="https://okay-cms.com">OkayCMS</a> &copy; <?php echo smarty_modifier_date_format(time(),"%Y");?>
 v.<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->version, ENT_QUOTES, 'UTF-8', true);?>
 | <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_logged, ENT_QUOTES, 'UTF-8', true);?>

                        <a href="index.php?controller=ManagerAdmin&id=<?php echo $_smarty_tpl->tpl_vars['manager']->value->id;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['manager']->value->login, ENT_QUOTES, 'UTF-8', true);?>
</a>
                        (<a href="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
?logout"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_exit, ENT_QUOTES, 'UTF-8', true);?>
</a>)
                        <div class="float-md-right">
                            <a href='index.php?controller=LicenseAdmin' class="text_white"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->license_text, ENT_QUOTES, 'UTF-8', true);?>
 </a>
                            ,
                            <?php if ($_smarty_tpl->tpl_vars['support_info']->value->public_key) {?>
                                <a class="text_success" href="index.php?controller=SupportAdmin"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_support_active, ENT_QUOTES, 'UTF-8', true);?>
 (<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['support_info']->value->new_messages, ENT_QUOTES, 'UTF-8', true);?>
)</a>
                            <?php } else { ?>
                                <a href="index.php?controller=SupportAdmin">
                                    <span class="text_warning"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_support_not_active, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                </a>
                            <?php }?>
                        </div>
                    </div>
                </footer>
             </div>
                        <div class="fn_fast_save">
                <div class="fn_fast_action_block fn_action_block">
                    <div class="action"></div>
                    <div class="additional_params"></div>
                </div>
                <button type="submit" class="<?php if ($_GET['controller'] == 'TemplatesAdmin' || $_GET['controller'] == 'StylesAdmin' || $_GET['controller'] == 'ScriptsAdmin') {?>fn_save<?php } else { ?>fast_save_button<?php }?> btn btn_small btn_blue">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </button>

                <?php if ($_GET['controller'] == 'CategoryAdmin' || $_GET['controller'] == 'BlogCategoryAdmin' || $_GET['controller'] == 'BrandAdmin' || $_GET['controller'] == 'FeatureAdmin' || $_GET['controller'] == 'OrderAdmin' || $_GET['controller'] == 'PageAdmin' || $_GET['controller'] == 'PostAdmin' || $_GET['controller'] == 'ProductAdmin') {?><button type="submit" class="fast_save_button_and_quit btn btn_small btn_blue ml-1"><?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?><span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply_and_quit, ENT_QUOTES, 'UTF-8', true);?>
</span></button><?php }?>
            </div>
        </div>

                <div id="fn_action_modal" class="modal fade show" role="document">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="card-header">
                        <div class="heading_modal"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_confirm, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    </div>
                    <div class="modal-body">
                        <button type="submit" class="btn btn_small btn_blue fn_submit_delete mx-h">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                            <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_yes, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        </button>

                        <button type="button" class="btn btn_small btn-danger fn_dismiss_delete mx-h" data-dismiss="modal">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'delete'), 0, true);
?>
                            <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->index_no, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<?php ob_start();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"main_custom_block_before_js"),$_smarty_tpl ) );
$_prefixVariable8 = ob_get_clean();
$_smarty_tpl->_assignInScope('block', $_prefixVariable8);
if (!empty($_smarty_tpl->tpl_vars['block']->value)) {?>
    <div>
        <?php echo $_smarty_tpl->tpl_vars['block']->value;?>

    </div>
<?php }?>

<?php echo $_smarty_tpl->tpl_vars['ok_footer']->value;?>


<?php echo '<script'; ?>
>
    $(function(){

        <?php if ($_smarty_tpl->tpl_vars['config']->value->dev_mode) {?>
            // При нажатии на лейбл под названием секции меню происходит копирование в буфер обмена
            (function copyToBufferMenuSections() {
                $('.fn_backend_menu_section').on('click', function(e) {
                    e.preventDefault();
                    var sectionName = $(this).data('section_name'),
                        code = document.querySelector('.fn_backend_menu_section[data-section_name="' + sectionName + '"]'),
                        range = document.createRange();

                    range.selectNode(code);
                    window.getSelection().addRange(range);

                    sectionHighlightAnimation($(this));

                    var successful = document.execCommand('copy'),
                        msg = "";

                    if (successful) {
                        toastr.success(msg, "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->toastr_success, ENT_QUOTES, 'UTF-8', true);?>
");
                    } else {
                        toastr.error(msg, "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->toastr_error, ENT_QUOTES, 'UTF-8', true);?>
");
                    }

                    window.getSelection().removeAllRanges();
                });
            })();

            $('.fn_design_block_name').parent().addClass('design_block_parent_element');
            $('.fn_design_block_name').on('mouseover', function () {
                $(this).parent().addClass('focus');
            });
            $('.fn_design_block_name').on('mouseout', function () {
                $(this).parent().removeClass('focus');
            });

            function sectionHighlightAnimation($element) {
                $element.css({
                    color: 'white',
                    transition: '0.3s'
                });

                setTimeout(function() {
                    $element.css({
                        color: 'red',
                    });
                }, 300);
            }

        <?php }?>

        /* Initializing the scrollbar */
        if($('.scrollbar-inner').size()>0){
            $('.scrollbar-inner').scrollbar({
                "disableBodyScroll":true
            });
        }

        if($(window).width() < 1199 ){
            if($('.scrollbar-variant').size()>0){
                $('.scrollbar-variant').scrollbar();
            }
        }
        if($('.input_file').size()>0){
            document.querySelector("html").classList.add('fn_input_file');

            var fileInput  = document.querySelector( ".input_file" ),
                button     = document.querySelector( ".input_file_trigger" ),
                the_return = document.querySelector(".input_file_return");

            button.addEventListener( "keydown", function( event ) {
                if ( event.keyCode == 13 || event.keyCode == 32 ) {
                    fileInput.focus();
                }
            });
            button.addEventListener( "click", function( event ) {
                fileInput.focus();
                return false;
            });
            fileInput.addEventListener( "change", function( event ) {
                the_return.innerHTML = this.value;
            });
        }

            if($('form.fn_fast_button').size()>0){
            

            // Связка селектов массовых действий
            $(document).on('change', '.fn_action_block:not(.fn_fast_action_block) select', function(e, trigger) {
                if (!trigger) {
                    var name = $(this).attr('name'),
                        selected = $(this).children(':selected').val();
                    $('.fn_fast_save select[name="' + name + '"]').val(selected).trigger('change', {trigger: true});
                }
            });

            $(document).on('change', '.fn_fast_save select', function(e, trigger) {
                if (!trigger) {
                    var name = $(this).attr('name'),
                        selected = $(this).children(':selected').val();
                    $('form.fn_fast_button select[name="' + name + '"]').val(selected).trigger('change', {trigger: true});
                }
            });
            

            if ($('.fn_action_block').size()>0) {
                var action_block = $('.okay_list_option').clone(true);
                $('.fn_fast_action_block .action').html(action_block);
                if ($('.fn_additional_params').size()) {
                    var additional_params = $('.fn_additional_params').clone(true);
                    $('.fn_fast_action_block .additional_params').html(additional_params);
                }
            }

            $(document).on('keyup change dragover', 'input,textarea,select, .dropdown-toggle, .fn_sort_item, .fn_category_item', function() {
               $('.fn_fast_save').show();
            });
            $(document).on('click', '#fn_add_purchase', function() {
                $('.fn_fast_save').show();
            });

            $(document).on('click', '.fn_fast_save .fast_save_button', function () {
                $('body').find("form.fn_fast_button").trigger('submit');
            });
            <?php if ($_GET['controller'] == 'CategoryAdmin' || $_GET['controller'] == 'BlogCategoryAdmin' || $_GET['controller'] == 'BrandAdmin' || $_GET['controller'] == 'FeatureAdmin' || $_GET['controller'] == 'OrderAdmin' || $_GET['controller'] == 'PageAdmin' || $_GET['controller'] == 'PostAdmin' || $_GET['controller'] == 'ProductAdmin') {?>
            $('.fast_save_button_and_quit').on('click', function () {
                $('body').find("form.fn_fast_button").find("#fast_save_button_and_quit").trigger("click");
            });<?php }?>
        }

        /* Check */
        if($('.fn_check_all').size()>0){
            $(document).on('change','.fn_check_all',function(){
                if($(this).is(":checked")) {
                    $(this).closest("form").find('.hidden_check').each(function () {
                        if(!$(this).is(":checked")) {
                            $(this).trigger("click");
                        }
                    });
                } else {
                    $(this).closest("form").find('.hidden_check').each(function () {
                        if($(this).is(":checked")) {
                            $(this).trigger("click");
                        }
                    })
                }
            });
        }

        $( function(){
            $( ".fn_tooltips" ).tooltip();
        });

        /* Catalog items toggle */
        if($('.fn_item_switch').size()>0){
            $('.fn_item_switch').on('click',function(e){
                var parent = $(this).closest("ul"),
                    li = $(this).closest(".fn_item_sub_switch"),
                    sub = li.find(".fn_submenu_toggle");

                if(li.hasClass("open active")){

                    sub.slideUp(200, function () {
                        li.removeClass("open active")
                    })

                } else {
                    parent.find("li.open").children(".fn_submenu_toggle").slideUp(200),
                    parent.find("li.open").removeClass("open active"),
                    li.children(".arrow").addClass("open active"),

                    sub.slideDown(200, function () {
                        li.addClass("open active")
                    })
                }
            });
        }

        /* Left menu toggle */
        if($('.fn_switch_menu').size()>0){
            $(document).on("click", ".fn_switch_menu", function () {
                $("body").toggleClass("menu-pin");
            });
            $(document).on("click", ".fn_mobile_menu", function () {
                $("body").toggleClass("menu-pin");
                $(".fn_quickview").removeClass("open");
            });
        }

        /* Right menu toggle */
        if($('.fn_switch_quickview').size()>0){
            $(document).on("click", ".fn_mobile_menu_right", function () {
                $(this).next().toggleClass("open");
                $("body").removeClass("menu-pin");
            });
            $(document).on("click", ".fn_switch_quickview", function () {
                $(this).closest(".fn_quickview").toggleClass("open");
            });
        }

        /* Delete images for products */
        if($('.images_list').size()>0){
            $('.fn_delete').on('click',function(){
                if($('.fn_accept_delete').size()>0){
                    $('.fn_accept_delete').val('1');
                    $(this).closest("li").fadeOut(200, function() {
                        $(this).remove();
                    });
                } else {
                    $(this).closest("li").fadeOut(200, function() {
                        $(this).remove();
                    });
                }
                return false;
            });
        }

        <?php if ($_smarty_tpl->tpl_vars['is_mobile']->value === false && $_smarty_tpl->tpl_vars['is_tablet']->value === false) {?>
            
            Sortable.create(document.getElementById("fn_sort_menu_section"), {
                sort: true,  // sorting inside list
                animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                scrollSensitivity: 100, // px, how near the mouse must be to an edge to start scrolling.
                scrollSpeed: 10, // px
                // Changed sorting within list
                onUpdate: function (evt) {
                    save_menu();
                }
            });

            if($(".fn_sort_menu_item").size()>0) {
                $(".fn_sort_menu_item").each(function() {
                    Sortable.create(this, {
                        sort: true,  // sorting inside list
                        animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation.
                        scroll: true,
                        scrollSensitivity: 100, // px, how near the mouse must be to an edge to start scrolling.
                        scrollSpeed: 10, // px
                        // Changed sorting within list
                        onUpdate: function (evt) {
                            save_menu();
                        }
                    });
                });
            }

            function save_menu() {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "ajax/update_object.php",
                    data: $('.fn_manager_menu').serialize()
                });
            }
            
        <?php }?>

        /* Initializing sorting */
        if($(".sortable").size()>0) {
            
            $(".sortable").each(function() {
                Sortable.create(this, {
                    handle: ".move_zone",  // Drag handle selector within list items
                    sort: true,  // sorting inside list
                    animation: 150,  // ms, animation speed moving items when sorting, `0` — without animation
                    ghostClass: "sortable-ghost",  // Class name for the drop placeholder
                    chosenClass: "sortable-chosen",  // Class name for the chosen item
                    dragClass: "sortable-drag",  // Class name for the dragging item
                    scrollSensitivity: 100, // px, how near the mouse must be to an edge to start scrolling.
                    scrollSpeed: 10, // px

                    // Changed sorting within list
                    onUpdate: function (evt) {
                        if ($(".product_images_list").size() > 0) {
                            var itemEl = evt.item;  // dragged HTMLElement
                            if ($(itemEl).closest(".fn_droplist_wrap").data("image") == "product") {
                                $(".product_images_list").find("li.first_image").removeClass("first_image");
                                $(".product_images_list").find("li:nth-child(2)").addClass("first_image");
                            }
                        }
                    }
                });
            });
            
        }

        if($(".sort_extended").size()>0) {

            /*Явно указываем высоту списка, иначе когда скрипт удаляет элемент и ставит на его место заглушку, страница подпрыгивает*/
            $(".fn_sort_list").css('min-height', $(".fn_sort_list").outerHeight());

            $(".sort_extended").sortable({
                items: ".fn_sort_item",
                tolerance: "pointer",
                handle: ".move_zone",
                scrollSensitivity: 50,
                scrollSpeed: 100,
                scroll: true,
                opacity: 0.5,
                containment: "document",
                helper: function(event, ui){
                    if ($('input[type="checkbox"][name*="check"]:checked').size()<1) return ui;
                    var helper = $('<div/>');
                    $('input[type="checkbox"][name*="check"]:checked').each(function() {
                        var item = $(this).closest('.fn_row');
                        helper.height(helper.height()+item.innerHeight());
                        if (item[0]!=ui[0]) {
                            helper.append(item.clone());
                            $(this).closest('.fn_row').remove();
                        } else {
                            helper.append(ui.clone());
                            item.find('input[type="checkbox"][name*="check"]').attr('checked', false);
                        }
                    });
                    return helper;
                },
                start: function(event, ui) {
                    if(ui.helper.children('.fn_row').size()>0)
                        $('.ui-sortable-placeholder').height(ui.helper.height());
                },
                beforeStop:function(event, ui){
                    if(ui.helper.children('.fn_row').size()>0){
                        ui.helper.children('.fn_row').each(function(){
                            $(this).insertBefore(ui.item);
                        });
                        ui.item.remove();
                    }
                },
                update: function (event, ui) {
                    $("#list_form input[name*='check']").attr('checked', false);

                }
            });
        }

        $(".fn_pagination a.droppable").droppable({
            activeClass: "drop_active",
            hoverClass: "drop_hover",
            tolerance: "pointer",
            drop: function(event, ui){
                $(ui.helper).find('input[type="checkbox"][name*="check"]').attr('checked', true);
                $(ui.draggable).closest("form").find('select[name="action"] option[value=move_to_page]').attr("selected", "selected");
                $(ui.draggable).closest("form").find('select[name=target_page] option[value='+$(this).html()+']').attr("selected", "selected");
                $(ui.draggable).closest("form").submit();
                return false;
            }
        });

        /* Call an ajax entity update */
        if($(".fn_ajax_action").size()>0){
            $(document).on("click",".fn_ajax_action",function () {
                ajax_action($(this));
            });
        }

        if($(".fn_parent_image").size()>0 ) {

            $(document).on("click", '.fn_delete_item', function () {
                $(this).closest(".fn_image_block").find(".fn_upload_image").removeClass("hidden");
                $(this).closest(".fn_image_block").find(".fn_accept_delete").val(1);
                $(this).closest(".fn_image_wrapper").remove()
            });

            if(window.File && window.FileReader && window.FileList) {

                $(".fn_upload_image").on('dragover', function (e){
                    e.preventDefault();
                    $(this).css('background', '#bababa');
                });
                $(".fn_upload_image").on('dragleave', function(){
                    $(this).css('background', '#f8f8f8');
                });
                function handleFileSelect(evt){
                    var parent = $(this).closest(".fn_image_block");
                    var parent_image = parent.find(".fn_parent_image");
                    var files = evt.target.files;
                    for (var i = 0, f; f = files[i]; i++) {
                        if (!f.type.match('image.*')) {
                            continue;
                        }
                        var reader = new FileReader();
                        reader.onload = (function(theFile) {
                            return function(e) {
                                var clone_image = parent.find(".fn_new_image").clone(true);
                                clone_image.removeClass("hidden");
                                clone_image.find('[type="hidden"]').prop("disabled", false);
                                clone_image.find("img").attr("src", e.target.result);
                                clone_image.find("img").attr("onerror", '$(this).closest(\"div\").remove()');
                                clone_image.appendTo(parent_image);
                                parent.find(".fn_upload_image").addClass("hidden");
                            };
                        })(f);
                        reader.readAsDataURL(f);
                    }
                    $(".fn_upload_image").removeAttr("style");
                }
                $(document).on('change','.dropzone_image', handleFileSelect);
            }
        }
    });

    $(document).on('click', '.fn_light_remove', function () {
        $(this).closest(".fn_row").remove();
    });
    if($('.fn_remove').size() > 0) {
        // Подтверждение удаления
        /*
        * функция модального окна с подтверждением удаления
        * принимает аргумент $this - по факту сама кнопка удаления
        * функция вызывается прямо в файлах tpl
        * */
        function success_action ($this){
            $(document).on('click','.fn_submit_delete',function(){
                $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
                $this.closest(".fn_row").find('input[type="checkbox"][name*="check"]').prop('checked', true);
                $this.closest(".fn_form_list").find('select[name="action"] option[value=delete]').prop('selected', true);
                $this.closest(".fn_form_list").submit();
            });
            $(document).on('click','.fn_dismiss_delete',function(){
                $('.fn_form_list input[type="checkbox"][name*="check"]').prop('checked', false);
                $this.closest(".fn_form_list").find('select[name="action"] option[value=delete]').removeAttr('selected');
                return false;
            });
        }
    }
    
        if($(".fn_ajax_action,.fn_ajax_block").size()>0) {
            /* Функция аяксового обновления полей
            * state - состояние объекта (включен/выключен)
            * id - id обновляемой сущности
            * controller - типо сущности
            * action - обновляемое поле (поле в БД)
            * класс "fn_ajax_block" у елемента - означает массовое обновление;
            * если нужно:
            * 1) добавить класс "fn_ajax_block" к блоку в котором хотите обновить несколько полей,
            * 2) добавить класс "fn_ajax_element" к елементам, в блоке("fn_ajax_block"), которые хотите обновить
            * .fn_ajax_element: аттрибут "name" - поле БД; val() - значение.
            * */
            function ajax_action($this) {
                var state, controller, session_id, action, id, values = {};
                state = $this.hasClass("fn_active_class") ? 0:1;
                id = parseInt($this.data('id'));
                controller = $this.data("controller");
                action = $this.data("action");
                session_id = '<?php echo $_SESSION['id'];?>
';
                if (!$this.hasClass("fn_ajax_block")) {
                    values = {[action]: state};
                } else {
                    $this.find('.fn_ajax_element').each(function() {
                        var elem = $(this);
                        values[elem.attr('name')] = elem.val();
                    });
                }
                toastr.options = {
                    closeButton: true,
                    newestOnTop: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    preventDuplicates: false,
                    onclick: null
                };
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: "ajax/update_object.php",
                    data: {
                        object : controller,
                        id : id,
                        values: values,
                        session_id : session_id
                    },
                    success: function(data){
                        var msg = "";
                        if(data){
                            toastr.success(msg, "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->toastr_success, ENT_QUOTES, 'UTF-8', true);?>
");
                            if (action == "processed" && controller == "callback") {
                                $this.closest(".fn_row").find(".fn_callbacks_toggle").toggleClass("hidden");
                            } else {
                                $this.toggleClass("fn_active_class");
                                if (action == "approved" || action == "processed") {
                                    $this.closest("div").find(".fn_answer_btn").show();
                                }
                            }
                        } else {
                            toastr.error(msg, "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->toastr_error, ENT_QUOTES, 'UTF-8', true);?>
");
                        }
                    }
                });
                return false;
            }
        }
    

    /*
    * функции генерации мета данных
    * */
    $(document).on('input', 'input[name="meta_title"]', function() { $('#fn_meta_title_counter').text( '('+$('input[name="meta_title"]').val().length+')' ); });
    $(document).on('input', 'textarea[name="meta_description"]', function() { $('#fn_meta_description_counter').text( '('+$('textarea[name="meta_description"]').val().replace(/\n/g, "\r\n").length+')' ); });
    var is_translit_alpha = $(".fn_is_translit_alpha");
    var translit_pairs = [];
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['translit_pairs']->value, 'pair', false, 'i');
$_smarty_tpl->tpl_vars['pair']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['i']->value => $_smarty_tpl->tpl_vars['pair']->value) {
$_smarty_tpl->tpl_vars['pair']->do_else = false;
?>
        translit_pairs[<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
] = {
            from: "<?php echo $_smarty_tpl->tpl_vars['pair']->value['from'];?>
".split("-"),
            to: "<?php echo $_smarty_tpl->tpl_vars['pair']->value['to'];?>
".split("-")
        };
    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    if($('input').is('.fn_meta_field')) {
        $(window).on("load", function() {

            // Автозаполнение мета-тегов
            meta_title_touched = true;
            meta_keywords_touched = true;
            meta_description_touched = true;

            if($('input[name="meta_title"]').val() == generate_meta_title() || $('input[name="meta_title"]').val() == '')
                meta_title_touched = false;
            if($('input[name="meta_keywords"]').val() == generate_meta_keywords() || $('input[name="meta_keywords"]').val() == '')
                meta_keywords_touched = false;
            if($('textarea[name="meta_description"]').val() == generate_meta_description() || $('textarea[name="meta_description"]').val() == '')
                meta_description_touched = false;

            $('input[name="meta_title"]').change(function() { meta_title_touched = true; });
            $('input[name="meta_keywords"]').change(function() { meta_keywords_touched = true; });
            $('textarea[name="meta_description"]').change(function() { meta_description_touched = true; });

            $('#fn_meta_title_counter').text( '('+$('input[name="meta_title"]').val().length+')' );
            $('#fn_meta_description_counter').text( '('+$('textarea[name="meta_description"]').val().replace(/\n/g, "\r\n").length+')' );

            $('input[name="name"]').keyup(function() { set_meta(); });
            $('input[name="meta_title"]').keyup(function() { $('#fn_meta_title_counter').text( '('+$('input[name="meta_title"]').val().length+')' ); });
            $('textarea[name="meta_description"]').keyup(function() { $('#fn_meta_description_counter').text( '('+$('textarea[name="meta_description"]').val().replace(/\n/g, "\r\n").length+')' ); });

            if($(".fn_meta_brand").size()>0) {
                $("select[name=brand_id]").on("change",function () {
                    set_meta();
                })
            }
            if($(".fn_meta_author").size()>0) {
                $("select[name=author_id]").on("change",function () {
                    set_meta();
                })
            }
            if($(".fn_meta_categories").size()>0) {
                $(".fn_meta_categories").on("change",function () {
                    set_meta();
                })
            }
        });

        function set_meta() {
            if(!meta_title_touched)
                $('input[name="meta_title"]').val(generate_meta_title());
            if(!meta_keywords_touched)
                $('input[name="meta_keywords"]').val(generate_meta_keywords());
            if(!meta_description_touched)
                $('textarea[name="meta_description"]').val(generate_meta_description());
            if(!$('#block_translit').is(':checked'))
                $('input[name="url"]').val(generate_url());
        }

        function generate_meta_title() {
            name = $('input[name="name"]').val();
            $('#fn_meta_title_counter').text( '('+name.length+')' );
            return name;
        }

        function generate_meta_keywords() {
            let result = $('input[name="name"]').val();

            if ($(".fn_meta_brand").size() > 0) {
                let brand = $('select[name="brand_id"] option:selected').data('brand_name');
                if (typeof(brand) == 'string' && brand != '')
                    result += ', ' + brand;
            }
            if ($(".fn_meta_author").size() > 0) {
                let author = $('select[name="author_id"] option:selected').data('author_name');
                if (typeof(author) == 'string' && author != '')
                    result += ', ' + author;
            }

            if($(".fn_meta_categories").size()>0) {
                if($(".fn_product_categories_list .fn_category_item").size() == 0) {
                    let c = $(".fn_meta_categories option:selected").data("category_name");
                    if (typeof(c) == 'string' && c != '')
                        result += ', ' + c;
                } else {
                    let cat = $(".fn_product_categories_list .fn_category_item:first");
                    let c = cat.find("input").data("cat_name");
                    if (typeof(c) == 'string' && c != '')
                        result += ', ' + c;
                }

            }
            return result;
        }

        function generate_meta_description() {
            if(typeof(tinyMCE.get("fn_editor")) =='object') {
                description = tinyMCE.get("fn_editor").getContent().replace(/(<([^>]+)>)/ig," ").replace(/\n/g, "\r\n").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
                $('#fn_meta_description_counter').text( '('+description.length+')');
                return description;
            } else {
                return $('.fn_editor_class').val().replace(/(<([^>]+)>)/ig," ").replace(/\n/g, "\r\n").replace(/(\&nbsp;)/ig," ").replace(/^\s+|\s+$/g, '').substr(0, 512);
            }
        }
    }

    function generate_url() {
        url = $('input[name="name"]').val();
        url = translit(url);
        if (is_translit_alpha.size() > 0) {
            url = url.replace(/[^0-9a-z]+/gi, '').toLowerCase();
        } else {
            url = url.replace(/[\s]+/gi, '-');
            url = url.replace(/[^0-9a-z_\-]+/gi, '').toLowerCase();
        }
        return url;
    }

    function translit(str) {
        var str_tm = str;
        for (var j in translit_pairs) {
            var from = translit_pairs[j].from,
                to = translit_pairs[j].to,
                res = '';
            for(var i=0, l=str_tm.length; i<l; i++) {
                var s = str_tm.charAt(i), n = from.indexOf(s);
                if(n >= 0) { res += to[n]; }
                else { res += s; }
            }
            str_tm = res;
        }
        return str_tm;
    }
    /*функции генерации мета данных end*/

    $(window).on('load',function () {

        $("#countries_select").msDropdown({
            roundedBorder:false
        });

        /*
        * Скрипт табов
        * */
        $('.tabs').each(function(i) {
            var cur_nav = $(this).find('.tab_navigation'),
                cur_tabs = $(this).find('.tab_container'),
                cur_tab;

            <?php if ($_GET['active_tab']) {?>
                cur_nav.children().removeClass('selected');
                cur_nav.children('[href="#<?php echo htmlspecialchars($_GET['active_tab'], ENT_QUOTES, 'UTF-8', true);?>
"]').addClass('selected');
            <?php }?>

            if (cur_nav.children('.selected').size() > 0) {
                cur_tab = $(cur_nav.children('.selected').attr("href"));
            } else {
                cur_nav.children().first().addClass('selected');
                cur_tab = cur_tabs.children().first();
            }
            cur_tab.show();
        });

        $('.tab_navigation_link').click(function(e){
            e.preventDefault();
            if($(this).hasClass('selected')){
                return true;
            }
            var cur_nav = $(this).closest('.tabs').find('.tab_navigation'),
                cur_tabs = $(this).closest('.tabs').find('.tab_container'),
                cur_tab = $($(this).attr("href"));
            cur_tabs.children().hide();
            cur_nav.children().removeClass('selected');
            $(this).addClass('selected');

            let newUrl;
            if (window.location.href.indexOf('active_tab') !== -1) {
                newUrl = window.location.href.replace(/([?&]active_tab)=([^#&]*)/g, '$1=' + cur_tab.attr('id'));
            } else {
                newUrl = window.location + '&active_tab=' + cur_tab.attr('id');
            }

            history.pushState(null, null, newUrl);
            cur_tab.fadeIn(200);
        });
        /*Скрипт табов end*/

        /*
        * скрипт сворачивания информационных блоков
        * */
        $(document).on("click", ".fn_toggle_card", function () {
            $(this).closest(".fn_toggle_wrap").find('.fn_icon_arrow').toggleClass('rotate_180');
            $(this).closest(".fn_toggle_wrap").find(".fn_card").slideToggle(500);
        });

        /*
        * скрипт отображения загрузки модуля
        * */
        $(document).on("click", ".fn_switch_add_module", function () {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            }
            else {
                $(this).addClass('active');
            }

            $(".fn_hide_add_module").slideToggle(500);
        return false;
        });

        /*
        * Блокировка автоформирования ссылки
        * */
        $(document).on("click", ".fn_disable_url", function () {
            if($(".fn_url").attr("readonly")){
                $(".fn_url").removeAttr("readonly");
            } else {
                $(".fn_url").attr("readonly",true);
            }
            $(this).find('i').toggleClass("fa-unlock");
            $("#block_translit").trigger("click");
        });
        /*Блокировка автоформирования ссылки end*/

        if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
            $('.selectpicker').selectpicker('mobile');
        }
    });

<?php echo '</script'; ?>
>

<?php ob_start();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"main_custom_block_after_js"),$_smarty_tpl ) );
$_prefixVariable9 = ob_get_clean();
$_smarty_tpl->_assignInScope('block', $_prefixVariable9);
if (!empty($_smarty_tpl->tpl_vars['block']->value)) {?>
    <div>
        <?php echo $_smarty_tpl->tpl_vars['block']->value;?>

    </div>
<?php }?>
</html>
<?php }
}
