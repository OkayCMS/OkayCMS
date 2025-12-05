<?php
/* Smarty version 3.1.40, created on 2025-11-26 12:56:41
  from '/var/www/html/Okay/Modules/OkayCMS/GoogleMerchant/Backend/design/html/google_merchant.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926dce96f3c62_81029952',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cbf3d74ad5e5f4ad0dacb922265d3ca78bf784bc' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/GoogleMerchant/Backend/design/html/google_merchant.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 1,
  ),
),false)) {
function content_6926dce96f3c62_81029952 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->smarty->ext->_tplFunction->registerTplFunctions($_smarty_tpl, array (
  'category_select' => 
  array (
    'compiled_filepath' => '/var/www/html/backend/design/compiled/cbf3d74ad5e5f4ad0dacb922265d3ca78bf784bc_0.file.google_merchant.tpl.php',
    'uid' => 'cbf3d74ad5e5f4ad0dacb922265d3ca78bf784bc',
    'call_name' => 'smarty_template_function_category_select_10821650746926dce96c90f5_50110922',
  ),
));
$_smarty_tpl->_assignInScope('meta_title', htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__title, ENT_QUOTES, 'UTF-8', true) ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__title, ENT_QUOTES, 'UTF-8', true);?>

            </div>
        </div>
    </div>
</div>

<?php if ($_smarty_tpl->tpl_vars['message_success']->value) {?>
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
                <?php if ($_GET['return']) {?>
                <a class="alert__button" href="<?php echo $_GET['return'];?>
">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'return'), 0, false);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_back, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </a>
                <?php }?>
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
                        <?php if ($_smarty_tpl->tpl_vars['message_error']->value == 'empty_name') {?>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enter_title, ENT_QUOTES, 'UTF-8', true);?>

                        <?php } else { ?>
                        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['message_error']->value, ENT_QUOTES, 'UTF-8', true);?>

                        <?php }?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php }?>

<form method="post" enctype="multipart/form-data" class="fn_fast_button fn_is_translit_alpha">
    <input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
    <input type="hidden" name="lang_id" value="<?php echo $_smarty_tpl->tpl_vars['lang_id']->value;?>
" />


    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__params, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="toggle_body_wrap on fn_card">
                    <div class="permission_block">
                        <div class="permission_boxes row">
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__upload_without_images, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__google_merchant__upload_without_images" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__upload_without_images) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__upload_non_exists_products_to_google, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__google_merchant__upload_non_exists_products_to_google" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__upload_non_exists_products_to_google) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__use_full_description_to_google, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__google_merchant__use_full_description_to_google" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__use_full_description_to_google) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__no_export_without_price, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__google_merchant__no_export_without_price" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__no_export_without_price) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__adult, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__google_merchant__adult" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__adult) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="permission_box permission_box--long">
                                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__use_variant_name_like_size, ENT_QUOTES, 'UTF-8', true);?>
</span>
                                    <label class="switch switch-default">
                                        <input class="switch-input" name="okaycms__google_merchant__use_variant_name_like_size" value='1' type="checkbox" <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__use_variant_name_like_size) {?>checked=""<?php }?>/>
                                        <span class="switch-label"></span>
                                        <span class="switch-handle"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <div class="heading_label">
                                <strong><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__company;?>
</strong>
                            </div>
                            <div class="mb-1">
                                <input class="form-control" type="text" name="okaycms__google_merchant__company" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__company, ENT_QUOTES, 'UTF-8', true);?>
" />
                            </div>
                        </div>
                        <div class="col-md-6 mb-1">
                            <div class="heading_label">
                                <strong><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__color;?>
</strong> <span>(<?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__color_notify;?>
)</span>
                            </div>
                            <div class="mb-1">
                                <select name="okaycms__google_merchant__color" class="selectpicker form-control">
                                    <option <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__color == 0) {?>selected=""<?php }?> value=""></option>
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['features']->value, 'feature');
$_smarty_tpl->tpl_vars['feature']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['feature']->value) {
$_smarty_tpl->tpl_vars['feature']->do_else = false;
?>
                                        <option <?php if ($_smarty_tpl->tpl_vars['settings']->value->okaycms__google_merchant__color == $_smarty_tpl->tpl_vars['feature']->value->id) {?>selected=""<?php }?> value="<?php echo $_smarty_tpl->tpl_vars['feature']->value->id;?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feature']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 ">
                            <button type="submit" class="btn btn_small btn_blue float-md-right">
                                <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12">
            <div class="boxed fn_toggle_wrap">
                <div class="heading_box">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__edit_and_add_feeds, ENT_QUOTES, 'UTF-8', true);?>

                </div>

                                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-info btn_big mb-1 fn_add_feed" type="submit" name="add_feed" value="1">
                            <span><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__add_feed;?>
</span>
                        </button>
                    </div>
                    <div class="col-md-12">
                        <div class="tabs">
                            <div class="heading_tabs">
                                <div class="tab_navigation">
                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['feeds']->value, 'feed');
$_smarty_tpl->tpl_vars['feed']->iteration = 0;
$_smarty_tpl->tpl_vars['feed']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['feed']->value) {
$_smarty_tpl->tpl_vars['feed']->do_else = false;
$_smarty_tpl->tpl_vars['feed']->iteration++;
$__foreach_feed_1_saved = $_smarty_tpl->tpl_vars['feed'];
?>
                                        <a href="#tab<?php echo $_smarty_tpl->tpl_vars['feed']->iteration;?>
" class="heading_box tab_navigation_link"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feed']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</a>
                                    <?php
$_smarty_tpl->tpl_vars['feed'] = $__foreach_feed_1_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                </div>
                            </div>
                            <div class="tab_container">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['feeds']->value, 'feed');
$_smarty_tpl->tpl_vars['feed']->iteration = 0;
$_smarty_tpl->tpl_vars['feed']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['feed']->value) {
$_smarty_tpl->tpl_vars['feed']->do_else = false;
$_smarty_tpl->tpl_vars['feed']->iteration++;
$__foreach_feed_2_saved = $_smarty_tpl->tpl_vars['feed'];
?>
                                    <div id="tab<?php echo $_smarty_tpl->tpl_vars['feed']->iteration;?>
" class="tab">
                                        <div class="row">
                                            <div class="col-lg-12 col-md-12">
                                                <div class="heading_box">
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__params, ENT_QUOTES, 'UTF-8', true);?>

                                                </div>
                                                                                                <?php if ((isset($_smarty_tpl->tpl_vars['errors']->value['feeds'][$_smarty_tpl->tpl_vars['feed']->value->id]))) {?>
                                                    <div class="row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <div class="alert alert--center alert--icon alert--error">
                                                                <div class="alert__content">
                                                                    <div class="alert__title">
                                                                        <?php if ((isset($_smarty_tpl->tpl_vars['errors']->value['feeds'][$_smarty_tpl->tpl_vars['feed']->value->id]['url']))) {?>
                                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__error_url_exist, ENT_QUOTES, 'UTF-8', true);?>

                                                                        <?php } elseif ((isset($_smarty_tpl->tpl_vars['errors']->value['feeds'][$_smarty_tpl->tpl_vars['feed']->value->id]['url_cyrillic']))) {?>
                                                                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__error_url_cyrillic, ENT_QUOTES, 'UTF-8', true);?>

                                                                        <?php }?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <?php if (count($_smarty_tpl->tpl_vars['feeds']->value) > 1) {?>
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <button class="btn btn-outline-danger btn_big float-md-right" name="remove_feed" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
">
                                                                <span><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__remove_feed;?>
</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php }?>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="heading_label">
                                                                <span>Name</span>
                                                            </div>
                                                            <input class="form-control" type="text" placeholder="Feed name" name="feeds[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
][name]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feed']->value->name, ENT_QUOTES, 'UTF-8', true);?>
">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="activity_of_switch activity_of_switch--left">
                                                            <div class="activity_of_switch_item">
                                                                <div class="okay_switch clearfix">
                                                                    <label class="switch_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enable, ENT_QUOTES, 'UTF-8', true);?>
</label>
                                                                    <label class="switch switch-default">
                                                                        <input type="hidden" name="feeds[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
][enabled]" value="0">
                                                                        <input class="switch-input" name="feeds[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
][enabled]" value="1" type="checkbox" <?php if ($_smarty_tpl->tpl_vars['feed']->value->enabled) {?>checked<?php }?>>
                                                                        <span class="switch-label"></span>
                                                                        <span class="switch-handle"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="heading_label">
                                                                <span>URL</span>
                                                                <i class="fn_tooltips" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__error_url_cyrillic, ENT_QUOTES, 'UTF-8', true);?>
">
                                                                <svg width="20px" height="20px" viewBox="0 0 438.533 438.533" ><path fill="currentColor" d="M409.133,109.203c-19.608-33.592-46.205-60.189-79.798-79.796C295.736,9.801,259.058,0,219.273,0c-39.781,0-76.47,9.801-110.063,29.407c-33.595,19.604-60.192,46.201-79.8,79.796C9.801,142.8,0,179.489,0,219.267c0,39.78,9.804,76.463,29.407,110.062c19.607,33.592,46.204,60.189,79.799,79.798c33.597,19.605,70.283,29.407,110.063,29.407s76.47-9.802,110.065-29.407c33.593-19.602,60.189-46.206,79.795-79.798c19.603-33.596,29.403-70.284,29.403-110.062C438.533,179.485,428.732,142.795,409.133,109.203z M255.82,356.309c0,2.662-0.862,4.853-2.573,6.563c-1.704,1.711-3.895,2.567-6.557,2.567h-54.823c-2.664,0-4.854-0.856-6.567-2.567c-1.714-1.711-2.57-3.901-2.57-6.563v-54.823c0-2.662,0.855-4.853,2.57-6.563c1.713-1.708,3.903-2.563,6.567-2.563h54.823c2.662,0,4.853,0.855,6.557,2.563c1.711,1.711,2.573,3.901,2.573,6.563V356.309z M325.338,187.574c-2.382,7.043-5.044,12.804-7.994,17.275c-2.949,4.473-7.187,9.042-12.709,13.703c-5.51,4.663-9.891,7.996-13.135,9.998c-3.23,1.995-7.898,4.713-13.982,8.135c-6.283,3.613-11.465,8.326-15.555,14.134c-4.093,5.804-6.139,10.513-6.139,14.126c0,2.67-0.862,4.859-2.574,6.571c-1.707,1.711-3.897,2.566-6.56,2.566h-54.82c-2.664,0-4.854-0.855-6.567-2.566c-1.715-1.712-2.568-3.901-2.568-6.571v-10.279c0-12.752,4.993-24.701,14.987-35.832c9.994-11.136,20.986-19.368,32.979-24.698c9.13-4.186,15.604-8.47,19.41-12.847c3.812-4.377,5.715-10.188,5.715-17.417c0-6.283-3.572-11.897-10.711-16.849c-7.139-4.947-15.27-7.421-24.409-7.421c-9.9,0-18.082,2.285-24.555,6.855c-6.283,4.565-14.465,13.322-24.554,26.263c-1.713,2.286-4.093,3.431-7.139,3.431c-2.284,0-4.093-0.57-5.424-1.709L121.35,145.89c-4.377-3.427-5.138-7.422-2.286-11.991c24.366-40.542,59.672-60.813,105.922-60.813c16.563,0,32.744,3.903,48.541,11.708c15.796,7.801,28.979,18.842,39.546,33.119c10.554,14.272,15.845,29.787,15.845,46.537C328.904,172.824,327.71,180.529,325.338,187.574z"/></svg>    </i>
                                                            </div>
                                                            <div class="input-group input-group--dabbl">
                                                                <span class="input-group-addon input-group-addon--left">URL</span>
                                                                <input class="form-control fn_url fn_disabled" type="text" name=feeds[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
][url] value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feed']->value->url, ENT_QUOTES, 'UTF-8', true);?>
" readonly="readonly">
                                                                <span class="input-group-addon fn_disable_url"><i class="fa fa-lock"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="alert alert--icon alert--info">
                                                            <div class="alert__content">
                                                                <div class="alert__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->alert_info, ENT_QUOTES, 'UTF-8', true);?>
</div>
                                                                <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__generation_url, ENT_QUOTES, 'UTF-8', true);?>
 <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>'OkayCMS_GoogleMerchant_feed','url'=>$_smarty_tpl->tpl_vars['feed']->value->url,'absolute'=>1),$_smarty_tpl ) );?>
" target="_blank"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>'OkayCMS_GoogleMerchant_feed','url'=>$_smarty_tpl->tpl_vars['feed']->value->url,'absolute'=>1),$_smarty_tpl ) );?>
</a></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="heading_box">
                                                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__upload_products, ENT_QUOTES, 'UTF-8', true);?>

                                                </div>
                                                <div class="row">
                                                                                                        <div class="col-lg-6 col-md-6">
                                                        <div class="boxed match fn_toggle_wrap">
                                                            <div class="heading_box">
                                                                <?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__categories;?>

                                                                <button class="btn btn_small btn-info" name="add_all_categories" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__select_all;?>
</button>
                                                                <button class="btn btn_small" name="remove_all_categories" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__select_none;?>
</button>
                                                            </div>
                                                            <div class="toggle_body_wrap on fn_card">
                                                                <select style="opacity: 0;" class="selectpicker_categories col-xs-12 px-0" multiple name="related_categories[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
][]" size="10" data-selected-text-format="count" >
                                                                    
                                                                    <?php $_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'category_select', array('categories'=>$_smarty_tpl->tpl_vars['categories']->value), true);?>

                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                                                                        <div class="col-lg-6 col-md-6">
                                                        <div class="boxed match fn_toggle_wrap">
                                                            <div class="heading_box">
                                                                <?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__brands;?>

                                                                <button class="btn btn_small btn-info" name="add_all_brands" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__select_all;?>
</button>
                                                                <button class="btn btn_small" name="remove_all_brands" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
"><?php echo $_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__select_none;?>
</button>
                                                            </div>
                                                            <div class="toggle_body_wrap on fn_card">
                                                                <select style="opacity: 0;" class="selectpicker_brands col-xs-12 px-0" multiple name="related_brands[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
][]" size="10" data-selected-text-format="count" >
                                                                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['brands']->value, 'brand');
$_smarty_tpl->tpl_vars['brand']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['brand']->value) {
$_smarty_tpl->tpl_vars['brand']->do_else = false;
?>
                                                                        <option value='<?php echo $_smarty_tpl->tpl_vars['brand']->value->id;?>
' class="brand_to_xml" <?php if (((isset($_smarty_tpl->tpl_vars['allRelatedBrandsIds']->value[$_smarty_tpl->tpl_vars['feed']->value->id])) && in_array($_smarty_tpl->tpl_vars['brand']->value->id,$_smarty_tpl->tpl_vars['allRelatedBrandsIds']->value[$_smarty_tpl->tpl_vars['feed']->value->id]))) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['brand']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                                                    <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>

                                                                                                        <div class="col-lg-6 col-md-12">
                                                        <div class="boxed fn_toggle_wrap min_height_210px">
                                                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['backend_compact_product_list'][0], array( array('title'=>$_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__products_for_upload,'name'=>"related_products_".((string)$_smarty_tpl->tpl_vars['feed']->value->id),'products'=>$_smarty_tpl->tpl_vars['related_products']->value[$_smarty_tpl->tpl_vars['feed']->value->id],'label'=>$_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__add_products,'placeholder'=>$_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__select_products),$_smarty_tpl ) );?>

                                                        </div>
                                                    </div>

                                                                                                        <div class="col-lg-6 col-md-12">
                                                        <div class="boxed fn_toggle_wrap min_height_210px">
                                                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['backend_compact_product_list'][0], array( array('title'=>$_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__products_not_for_upload,'name'=>"not_related_products_".((string)$_smarty_tpl->tpl_vars['feed']->value->id),'products'=>$_smarty_tpl->tpl_vars['not_related_products']->value[$_smarty_tpl->tpl_vars['feed']->value->id],'label'=>$_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__add_products,'placeholder'=>$_smarty_tpl->tpl_vars['btr']->value->okaycms__google_merchant__select_products),$_smarty_tpl ) );?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php
$_smarty_tpl->tpl_vars['feed'] = $__foreach_feed_2_saved;
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-bottom: 200px;">
        <div class="col-lg-12 col-md-12 ">
            <button type="submit" class="btn btn_small btn_blue float-md-right">
                <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
            </button>
        </div>
    </div>
</form>


    <?php echo '<script'; ?>
>
        $('.selectpicker_categories').selectpicker();
        $('.selectpicker_brands').selectpicker();
    <?php echo '</script'; ?>
>
<?php }
/* smarty_template_function_category_select_10821650746926dce96c90f5_50110922 */
if (!function_exists('smarty_template_function_category_select_10821650746926dce96c90f5_50110922')) {
function smarty_template_function_category_select_10821650746926dce96c90f5_50110922(Smarty_Internal_Template $_smarty_tpl,$params) {
$params = array_merge(array('selected_id'=>$_smarty_tpl->tpl_vars['product_category']->value,'level'=>0), $params);
foreach ($params as $key => $value) {
$_smarty_tpl->tpl_vars[$key] = new Smarty_Variable($value, $_smarty_tpl->isRenderingCache);
}
?>

                                                                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categories']->value, 'category');
$_smarty_tpl->tpl_vars['category']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['category']->value) {
$_smarty_tpl->tpl_vars['category']->do_else = false;
?>
                                                                            <option value='<?php echo $_smarty_tpl->tpl_vars['category']->value->id;?>
' class="category_to_xml" <?php if (((isset($_smarty_tpl->tpl_vars['allRelatedCategoriesIds']->value[$_smarty_tpl->tpl_vars['feed']->value->id])) && in_array($_smarty_tpl->tpl_vars['category']->value->id,$_smarty_tpl->tpl_vars['allRelatedCategoriesIds']->value[$_smarty_tpl->tpl_vars['feed']->value->id]))) {?>selected<?php }?>><?php
$__section_sp_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['level']->value) ? count($_loop) : max(0, (int) $_loop));
$__section_sp_0_total = $__section_sp_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_sp'] = new Smarty_Variable(array());
if ($__section_sp_0_total !== 0) {
for ($__section_sp_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_sp']->value['index'] = 0; $__section_sp_0_iteration <= $__section_sp_0_total; $__section_sp_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_sp']->value['index']++){
?>&nbsp;&nbsp;&nbsp;&nbsp;<?php
}
}
echo htmlspecialchars($_smarty_tpl->tpl_vars['category']->value->name, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                                                            <?php $_smarty_tpl->smarty->ext->_tplFunction->callTemplateFunction($_smarty_tpl, 'category_select', array('categories'=>$_smarty_tpl->tpl_vars['category']->value->subcategories,'selected_id'=>$_smarty_tpl->tpl_vars['selected_id']->value,'level'=>$_smarty_tpl->tpl_vars['level']->value+1), true);?>

                                                                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                                                                    <?php
}}
/*/ smarty_template_function_category_select_10821650746926dce96c90f5_50110922 */
}
