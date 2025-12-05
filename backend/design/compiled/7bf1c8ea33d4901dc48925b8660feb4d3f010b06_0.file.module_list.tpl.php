<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:29:10
  from '/var/www/html/backend/design/html/module_list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c866426360_26407935',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7bf1c8ea33d4901dc48925b8660feb4d3f010b06' => 
    array (
      0 => '/var/www/html/backend/design/html/module_list.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 5,
  ),
),false)) {
function content_6926c866426360_26407935 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['modules']->value, 'module');
$_smarty_tpl->tpl_vars['module']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['module']->value) {
$_smarty_tpl->tpl_vars['module']->do_else = false;
?>
    <div class="fn_row okay_list_body_item fn_sort_item
        <?php if ($_smarty_tpl->tpl_vars['module']->value->params->getDaysToExpire() >= 0) {?> 
            module_access_expire
        <?php } elseif ($_smarty_tpl->tpl_vars['module']->value->params->isAccessExpired()) {?> 
            module_access_expired
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['now_downloaded']->value) {?> 
            fn_now_downloaded
        <?php }?>" 
        <?php if ($_smarty_tpl->tpl_vars['now_downloaded']->value) {?> style="border-left: 5px solid #209db0;"<?php }?>>
        
        <div class="okay_list_row">
            <?php if ($_smarty_tpl->tpl_vars['module']->value->status !== 'Not Installed') {?>
                <input type="hidden" name="positions[<?php echo $_smarty_tpl->tpl_vars['module']->value->id;?>
]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->position, ENT_QUOTES, 'UTF-8', true);?>
">
            <?php }?>

            <div class="okay_list_boding okay_list_drag move_zone">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->status !== 'Not Installed') {
$_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
}?>
            </div>

            <div class="okay_list_boding okay_list_check">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->status !== 'Not Installed') {?>
                    <input class="hidden_check" type="checkbox" id="id_<?php echo $_smarty_tpl->tpl_vars['module']->value->id;?>
" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['module']->value->id;?>
"/>
                    <label class="okay_ckeckbox" for="id_<?php echo $_smarty_tpl->tpl_vars['module']->value->id;?>
"></label>
                <?php }?>
            </div>

            <div class="okay_list_boding okay_list_photo">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->preview) {?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->backend_main_controller) {?>
                        <a href="<?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable1 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable2 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->backend_main_controller, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable3 = ob_get_clean();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>array($_prefixVariable1,$_prefixVariable2,$_prefixVariable3),'id'=>null,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                            <img height="55" width="55" src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->preview, ENT_QUOTES, 'UTF-8', true);?>
"/>
                        </a>
                    <?php } else { ?>
                        <img height="55" width="55" src="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->preview, ENT_QUOTES, 'UTF-8', true);?>
"/>
                    <?php }?>
                <?php } else { ?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->backend_main_controller) {?>
                        <a href="<?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable4 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable5 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->backend_main_controller, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable6 = ob_get_clean();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>array($_prefixVariable4,$_prefixVariable5,$_prefixVariable6),'id'=>null,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'modules_icon'), 0, true);
?>
                        </a>
                    <?php } else { ?>
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'modules_icon'), 0, true);
?>
                    <?php }?>
                <?php }?>
            </div>

            <div class="okay_list_boding okay_list_module_name">
                <div class="text_600 mr-1">
                    <div class="module_official__name">
                        <?php if ($_smarty_tpl->tpl_vars['module']->value->backend_main_controller) {?>
                        <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                            <a href="<?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable7 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable8 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->backend_main_controller, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable9 = ob_get_clean();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>array($_prefixVariable7,$_prefixVariable8,$_prefixVariable9),'id'=>null,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);?>

                            </a>
                        <?php } else { ?>
                            <a href="index.php?controller=ModulesLicenseInfoAdmin">
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);?>

                            </a>
                        <?php }?>
                        <?php } else { ?>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);?>

                        <?php }?>

                        <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isOfficial()) {?>
                            <span data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_tooltip_oficial, ENT_QUOTES, 'UTF-8', true);?>
" class="params_official hint-bottom-middle-t-info-s-small-mobile hint-anim">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.603 3.799A4.49 4.49 0 0 1 12 2.25c1.357 0 2.573.6 3.397 1.549a4.49 4.49 0 0 1 3.498 1.307 4.491 4.491 0 0 1 1.307 3.497A4.49 4.49 0 0 1 21.75 12a4.49 4.49 0 0 1-1.549 3.397 4.491 4.491 0 0 1-1.307 3.497 4.491 4.491 0 0 1-3.497 1.307A4.49 4.49 0 0 1 12 21.75a4.49 4.49 0 0 1-3.397-1.549 4.49 4.49 0 0 1-3.498-1.306 4.491 4.491 0 0 1-1.307-3.498A4.49 4.49 0 0 1 2.25 12c0-1.357.6-2.573 1.549-3.397a4.49 4.49 0 0 1 1.307-3.497 4.49 4.49 0 0 1 3.497-1.307Zm7.007 6.387a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
                                </svg> 
                            </span>
                                             
                        <?php }?>
                    </div>                    
                </div>

                <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {
} else { ?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->enabled == 1) {?>
                    <div class="mt-q">
                        <span class="text_warning font_12 text_600">
                            <?php echo $_smarty_tpl->tpl_vars['btr']->value->module_access_blocked;?>

                        </span>
                    </div>
                <?php }?>
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['module']->value->versionControl->greaterThan($_smarty_tpl->tpl_vars['module']->value->version,$_smarty_tpl->tpl_vars['module']->value->params->getVersion())) {?>
                    <div class="mt-q">
                        <span class="text_attention font_12 text_600">
                            <?php echo $_smarty_tpl->tpl_vars['btr']->value->module_downgrade_warning;?>
 <?php echo $_smarty_tpl->tpl_vars['module']->value->params->getVersion();?>

                        </span>
                    </div>
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['module']->value->params->getDaysToExpire() >= 0) {?>
                    <div class="mt-q">
                        <span class="text_attention font_12 text_600">
                            <?php if ($_smarty_tpl->tpl_vars['module']->value->params->getDaysToExpire() > 0) {?>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_access_expire_days, ENT_QUOTES, 'UTF-8', true);?>

                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getDaysToExpire(), ENT_QUOTES, 'UTF-8', true);?>

                                <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'plural' ][ 0 ], array( $_smarty_tpl->tpl_vars['module']->value->params->getDaysToExpire(),$_smarty_tpl->tpl_vars['btr']->value->module_access_expire_plural_1,$_smarty_tpl->tpl_vars['btr']->value->module_access_expire_plural_5,$_smarty_tpl->tpl_vars['btr']->value->module_access_expire_plural_2 ));?>

                            <?php } else { ?>
                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_access_expire_today, ENT_QUOTES, 'UTF-8', true);?>

                            <?php }?>
                        </span>
                    </div>
                <?php } elseif ($_smarty_tpl->tpl_vars['module']->value->params->isAccessExpired()) {?>
                    <div class="mt-q">
                        <span class="text_warning font_12 text_600">
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_access_expired, ENT_QUOTES, 'UTF-8', true);?>

                        </span>
                    </div>
                <?php }?>

                <div class="mt-q">
                    <div class="fn_switch for_developer_toggle">
                        <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_learn_more, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        <i class="fn_icon_arrow fa fa-angle-down fa-lg m-t-2 rotate_180"></i>
                    </div>
    
                    <div style="display: none;">
                        <div class="mt-q font_12 text_grey text_500">
                            <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_version, ENT_QUOTES, 'UTF-8', true);?>
:</span>
                            <span class="text_dark"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</span>
                        </div>
    
                        <?php if (!empty($_smarty_tpl->tpl_vars['module']->value->params->getOkayVersion()) && $_smarty_tpl->tpl_vars['module']->value->params->getOkayVersion() != $_smarty_tpl->tpl_vars['config']->value->version) {?>
                            <div class="mt-q font_12 text_grey text_500">
                                <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_okay_version, ENT_QUOTES, 'UTF-8', true);?>
:</span>
                                <span class="text_dark"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getOkayVersion(), ENT_QUOTES, 'UTF-8', true);?>
</span>
                            </div>
                        <?php }?>
    
                        <div class="mt-q font_12 text_grey text_500">
                            <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_type, ENT_QUOTES, 'UTF-8', true);?>
:</span>
                            <span class="text_dark"><?php if ($_smarty_tpl->tpl_vars['module']->value->type) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->type, ENT_QUOTES, 'UTF-8', true);
} else {
echo $_smarty_tpl->tpl_vars['btr']->value->not_used_module_type;
}?></span>
                        </div>
    
                        <?php if ($_smarty_tpl->tpl_vars['module']->value->params->getVendorEmail()) {?>
                            <div class="mt-q font_12 text_grey text_500">
                                <span class=""><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_vendor_email, ENT_QUOTES, 'UTF-8', true);?>
:</span>
                                <a class="okay_list_module_name_link" href="mailto:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getVendorEmail(), ENT_QUOTES, 'UTF-8', true);?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getVendorEmail(), ENT_QUOTES, 'UTF-8', true);?>
</a>
                            </div>
                        <?php }?>
    
                        <?php if ($_smarty_tpl->tpl_vars['module']->value->params->getVendorSite()) {?>
                            <div class="mt-q font_12 text_grey text_500">
                                <span class=""><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_vendor_site, ENT_QUOTES, 'UTF-8', true);?>
:</span>
                                <a class="okay_list_module_name_link" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getVendorSite(), ENT_QUOTES, 'UTF-8', true);?>
" target="_blank"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getVendorSite(), ENT_QUOTES, 'UTF-8', true);?>
</a>
                            </div>
                        <?php }?>  

                        <div class="mt-q font_12 text_grey text_500 hidden-lg-up">
                            <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                                <span data-hint="<?php echo $_smarty_tpl->tpl_vars['btr']->value->module_tooltip_licensed;?>
" class="tag tag-licensed hint-bottom-middle-t-info-s-small-mobile hint-anim"><?php echo $_smarty_tpl->tpl_vars['btr']->value->module_status_licensed;?>
</span>
                            <?php } else { ?>
                                <?php if ($_smarty_tpl->tpl_vars['module']->value->enabled == 1) {?>
                                <span data-hint="<?php echo $_smarty_tpl->tpl_vars['btr']->value->module_tooltip_not_licensed;?>
" class="tag tag-not_licensed hint-bottom-middle-t-info-s-small-mobile hint-anim"><?php echo $_smarty_tpl->tpl_vars['btr']->value->module_status_not_licensed;?>
</span>
                            <?php }?>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="okay_list_boding okay_list_module_license_status hidden-md-down">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                    <span data-hint="<?php echo $_smarty_tpl->tpl_vars['btr']->value->module_tooltip_licensed;?>
" class="tag tag-licensed hint-bottom-middle-t-info-s-small-mobile hint-anim"><?php echo $_smarty_tpl->tpl_vars['btr']->value->module_status_licensed;?>
</span>
                <?php } else { ?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->enabled == 1) {?>
                    <span data-hint="<?php echo $_smarty_tpl->tpl_vars['btr']->value->module_tooltip_not_licensed;?>
" class="tag tag-not_licensed hint-bottom-middle-t-info-s-small-mobile hint-anim"><?php echo $_smarty_tpl->tpl_vars['btr']->value->module_status_not_licensed;?>
</span>
                <?php }?>
                <?php }?>
            </div>

            <div class="okay_list_boding okay_list_module_version hidden-md-down">
                <div class="text_700"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</div>

                <?php if ($_smarty_tpl->tpl_vars['module']->value->versionControl->lessThan($_smarty_tpl->tpl_vars['module']->value->version,$_smarty_tpl->tpl_vars['module']->value->params->getVersion())) {?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                        <button type="button" class="fn_update_module btn btn-warning btn--update mt-q hint-top-middle-t-info-s-small-mobile hint-anim" data-hint="<?php echo $_smarty_tpl->tpl_vars['btr']->value->module_need_update;?>
 <?php echo $_smarty_tpl->tpl_vars['module']->value->params->getVersion();?>
"><?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'refresh_icon'), 0, true);
?> <?php echo $_smarty_tpl->tpl_vars['module']->value->params->getVersion();?>
</button>
                    <?php }?>
                <?php }?>

                <?php if (!empty($_smarty_tpl->tpl_vars['module']->value->params->getOkayVersion()) && $_smarty_tpl->tpl_vars['module']->value->params->getOkayVersion() != $_smarty_tpl->tpl_vars['config']->value->version) {?>
                    <div class="font_12 text_grey mt-h">
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_okay_version, ENT_QUOTES, 'UTF-8', true);?>
: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getOkayVersion(), ENT_QUOTES, 'UTF-8', true);?>

                    </div>
                <?php }?>
            </div>
            
            <div class="okay_list_boding okay_list_status">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->status === 'Not Installed') {?>
                        <button class="btn btn_mini btn-info" name="install_module" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->tpl_vars['btr']->value->install_module;?>
</button>
                    <?php } else { ?>
                        <label class="switch switch-default">
                            <input class="switch-input fn_ajax_action <?php if ($_smarty_tpl->tpl_vars['module']->value->enabled) {?>fn_active_class<?php }?>" data-controller="module" data-action="enabled" data-id="<?php echo $_smarty_tpl->tpl_vars['module']->value->id;?>
" name="enabled" value="1" type="checkbox"  <?php if ($_smarty_tpl->tpl_vars['module']->value->enabled) {?>checked=""<?php }?>/>
                            <span class="switch-label"></span>
                            <span class="switch-handle"></span>
                        </label>
                    <?php }?>
                <?php } else { ?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->status === 'Not Installed') {?>
                        <button class="btn btn_mini btn-info" name="install_module" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);?>
/<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);?>
"><?php echo $_smarty_tpl->tpl_vars['btr']->value->install_module;?>
</button>
                    <?php } else { ?>
                    <span class="btn btn_mini" disabled><?php echo $_smarty_tpl->tpl_vars['btr']->value->module_unavailable;?>
</span>
                <?php }?>
                <?php }?>
            </div>

            <div class="okay_list_setting okay_list_products_setting">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->backend_main_controller) {?>
                    <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                        <a data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_action_setting, ENT_QUOTES, 'UTF-8', true);?>
" class="setting_icon setting_icon_setting hint-bottom-middle-t-info-s-small-mobile hint-anim" href="<?php ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->vendor, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable10 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->module_name, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable11 = ob_get_clean();
ob_start();
echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->backend_main_controller, ENT_QUOTES, 'UTF-8', true);
$_prefixVariable12 = ob_get_clean();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>array($_prefixVariable10,$_prefixVariable11,$_prefixVariable12),'id'=>null,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                        </a>
                    <?php }?>

                <?php }?>
                <?php if ($_smarty_tpl->tpl_vars['module']->value->params->isLicensed()) {?>
                <?php if ($_smarty_tpl->tpl_vars['module']->value->status !== 'Not Installed') {?>
                    <a data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->files_list_module, ENT_QUOTES, 'UTF-8', true);?>
" class="setting_icon setting_icon_files hint-bottom-middle-t-info-s-small-mobile hint-anim" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ModuleDesignAdmin','vendor'=>$_smarty_tpl->tpl_vars['module']->value->vendor,'module_name'=>$_smarty_tpl->tpl_vars['module']->value->module_name),$_smarty_tpl ) );?>
">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                        </svg>                          
                    </a>
                <?php }?>
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['module']->value->params->getAddToCartUrl()) {?>
                    <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_access_continue_access, ENT_QUOTES, 'UTF-8', true);?>
" class="fn_continue_access setting_icon setting_icon_extension hint-bottom-middle-t-info-s-small-mobile hint-anim" type="button" data-target="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['module']->value->params->getAddToCartUrl(), ENT_QUOTES, 'UTF-8', true);?>
">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                    </button>
                <?php }?>
            </div>

            <div class="okay_list_boding okay_list_close">
                <?php if ($_smarty_tpl->tpl_vars['module']->value->status !== 'Not Installed') {?>
                    <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_delete, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'trash'), 0, true);
?>
                    </button>
                <?php }?>
            </div>
        </div>
    </div>
<?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php if ($_smarty_tpl->tpl_vars['now_downloaded']->value) {?>
    <?php echo '<script'; ?>
>
        $(document).ready(function () {
            setTimeout(function () {
                $('.fn_now_downloaded').css('background-color', '').removeClass('fn_now_downloaded');
            }, 2000)
        });
    <?php echo '</script'; ?>
>
<?php }
}
}
