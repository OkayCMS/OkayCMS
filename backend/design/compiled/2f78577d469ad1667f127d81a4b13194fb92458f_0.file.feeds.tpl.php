<?php
/* Smarty version 3.1.40, created on 2025-11-26 19:38:25
  from '/var/www/html/Okay/Modules/OkayCMS/Feeds/Backend/design/html/feeds.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_69273b1151fe24_50511533',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2f78577d469ad1667f127d81a4b13194fb92458f' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/Feeds/Backend/design/html/feeds.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 6,
    'file:pagination.tpl' => 1,
  ),
),false)) {
function content_69273b1151fe24_50511533 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', $_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds, ENT_QUOTES, 'UTF-8', true);?>

            </div>
            <div class="box_btn_heading">
                    <a class="btn btn_small btn-info" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OkayCMS.Feeds.FeedAdmin'),$_smarty_tpl ) );?>
">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'plus'), 0, false);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds__add, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
        <div class="row">
        <div class="col-lg-12 col-md-12 ">
            <div class="fn_toggle_wrap">
                <div class="heading_box visible_md">
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_filter, ENT_QUOTES, 'UTF-8', true);?>

                    <div class="toggle_arrow_wrap fn_toggle_card text-primary">
                        <a class="btn-minimize" href="javascript:;" ><i class="fa fn_icon_arrow fa-angle-down"></i></a>
                    </div>
                </div>
                <div class="fn_step-0 boxed_sorting toggle_body_wrap off fn_card">
                    <div class="row">
                        <div class="col-md-9 col-lg-9 col-sm-12">
                            <select name="preset" class="selectpicker form-control" data-live-search="true" data-size="10" onchange="location = this.value;">
                                <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('keyword'=>null,'brand_id'=>null,'page'=>null,'limit'=>null,'preset'=>null),$_smarty_tpl ) );?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds__all_presets, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['presets']->value, 'preset', false, 'preset_name');
$_smarty_tpl->tpl_vars['preset']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['preset_name']->value => $_smarty_tpl->tpl_vars['preset']->value) {
$_smarty_tpl->tpl_vars['preset']->do_else = false;
?>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('keyword'=>null,'page'=>null,'limit'=>null,'preset'=>$_smarty_tpl->tpl_vars['preset_name']->value),$_smarty_tpl ) );?>
" <?php if ($_GET['preset'] == $_smarty_tpl->tpl_vars['preset_name']->value) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['preset_name']->value, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-12">
                            <div class="pull-right">
                                <select onchange="location = this.value;" class="selectpicker form-control">
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('limit'=>5),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['current_limit']->value == 5) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_show_by, ENT_QUOTES, 'UTF-8', true);?>
 5</option>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('limit'=>10),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['current_limit']->value == 10) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_show_by, ENT_QUOTES, 'UTF-8', true);?>
 10</option>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('limit'=>25),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['current_limit']->value == 25) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_show_by, ENT_QUOTES, 'UTF-8', true);?>
 25</option>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('limit'=>50),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['current_limit']->value == 50) {?>selected<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_show_by, ENT_QUOTES, 'UTF-8', true);?>
 50</option>
                                    <option value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('limit'=>100),$_smarty_tpl ) );?>
" <?php if ($_smarty_tpl->tpl_vars['current_limit']->value == 100) {?>selected=""<?php }?>><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_show_by, ENT_QUOTES, 'UTF-8', true);?>
 100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['feeds']->value) {?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form id="list_form" method="post" class="fn_form_list fn_fast_button">
                    <?php ob_start();
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"okay_cms__feeds__feeds__custom_block"),$_smarty_tpl ) );
$_prefixVariable1 = ob_get_clean();
$_smarty_tpl->_assignInScope('block', $_prefixVariable1);?>
                    <?php if (!empty($_smarty_tpl->tpl_vars['block']->value)) {?>
                        <div class="row custom_block">
                            <?php echo $_smarty_tpl->tpl_vars['block']->value;?>

                        </div>
                    <?php }?>

                    <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">

                    <div class="okay_list">
                                                <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_feed_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds__name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_status"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enable, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_setting okay_list_pages_setting"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_activities, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                                                <div id="sortable" class="okay_list_body sortable">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['feeds']->value, 'feed');
$_smarty_tpl->tpl_vars['feed']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['feed']->value) {
$_smarty_tpl->tpl_vars['feed']->do_else = false;
?>
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <input type="hidden" name="positions[<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
]" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->position;?>
">

                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                                        </div>

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
"/>
                                            <label class="okay_ckeckbox" for="id_<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_feed_name">
                                            <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OkayCMS.Feeds.FeedAdmin','id'=>$_smarty_tpl->tpl_vars['feed']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['feed']->value->name, ENT_QUOTES, 'UTF-8', true);?>

                                            </a>

                                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"okay_cms__feeds__feeds__list_name"),$_smarty_tpl ) );?>

                                        </div>

                                        <div class="okay_list_boding okay_list_status">
                                                                                        <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action <?php if ($_smarty_tpl->tpl_vars['feed']->value->enabled) {?>fn_active_class<?php }?>" data-controller="okay_cms__feed" data-action="enabled" data-id="<?php echo $_smarty_tpl->tpl_vars['feed']->value->id;?>
" name="enabled" value="1" type="checkbox"  <?php if ($_smarty_tpl->tpl_vars['feed']->value->enabled) {?>checked=""<?php }?>/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_setting okay_list_pages_setting">
                                                                                        <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url_generator'][0], array( array('route'=>"OkayCMS.Feeds.Feed",'url'=>$_smarty_tpl->tpl_vars['feed']->value->url,'absolute'=>1),$_smarty_tpl ) );?>
" target="_blank" data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_view, ENT_QUOTES, 'UTF-8', true);?>
" class="setting_icon setting_icon_open hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'eye'), 0, true);
?>
                                            </a>

                                                                                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds__duplicate, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="setting_icon setting_icon_copy fn_copy hint-bottom-middle-t-info-s-small-mobile  hint-anim">
                                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'icon_copy'), 0, true);
?>
                                            </button>

                                            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['get_design_block'][0], array( array('block'=>"okay_cms__feeds__feeds__icons"),$_smarty_tpl ) );?>

                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                                                                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds__delete, ENT_QUOTES, 'UTF-8', true);?>
" type="button" class="btn_close fn_remove hint-bottom-right-t-info-s-small-mobile  hint-anim" data-toggle="modal" data-target="#fn_action_modal" onclick="success_action($(this));">
                                                <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'trash'), 0, true);
?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                        </div>

                                                <div class="okay_list_footer fn_action_block">
                            <div class="okay_list_foot_left">
                                <div class="okay_list_boding okay_list_drag"></div>
                                <div class="okay_list_heading okay_list_check">
                                    <input class="hidden_check fn_check_all" type="checkbox" id="check_all_2" name="" value=""/>
                                    <label class="okay_ckeckbox" for="check_all_2"></label>
                                </div>
                                <div class="okay_list_option">
                                    <select name="action" class="selectpicker form-control">
                                        <option value="enable"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_do_enable, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option value="disable"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_do_disable, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option value="delete"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_delete, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                        <option value="duplicate"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_create_dublicate, ENT_QUOTES, 'UTF-8', true);?>
</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn_small btn_blue">
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
            <div class="text_grey"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->okay_cms__feeds__feeds__no, ENT_QUOTES, 'UTF-8', true);?>
</div>
        </div>
    <?php }?>
</div>


    <?php echo '<script'; ?>
>
        $(function() {
            // Дублировать страницу
            $(document).on("click", ".fn_copy", function () {
                $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
                $(this).closest(".fn_form_list").find('select[name="action"] option[value=duplicate]').attr('selected', true);
                $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
                $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').click();
                $(this).closest(".fn_form_list").submit();
            });
        });
    <?php echo '</script'; ?>
>


<style>
    .okay_list .okay_list_feed_name {
        width: calc(100% - 350px);
        position: relative;
        text-align: left;
    }

    @media only screen and (max-width : 991px) {
        .okay_list .okay_list_feed_name {
            width: calc(100% - 435px);
        }
    }

    @media only screen and (max-width : 767px) {
        .okay_list .okay_list_feed_name {
            width: calc(100% - 200px);
        }
    }

    @media only screen and (max-width : 575px) {
        .okay_list .okay_list_feed_name {
            width: calc(100% - 0px);
        }
    }
</style><?php }
}
