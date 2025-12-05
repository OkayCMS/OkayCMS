<?php
/* Smarty version 3.1.40, created on 2025-11-26 19:41:23
  from '/var/www/html/Okay/Modules/OkayCMS/FAQ/Backend/design/html/faqs.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_69273bc31c5a10_45695174',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4a8580fdcc00966fd6b2b1d9d0798c48bce97686' => 
    array (
      0 => '/var/www/html/Okay/Modules/OkayCMS/FAQ/Backend/design/html/faqs.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 4,
    'file:pagination.tpl' => 1,
  ),
),false)) {
function content_69273bc31c5a10_45695174 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', $_smarty_tpl->tpl_vars['btr']->value->faq_title ,false ,32);?>

<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['rootUrl']->value;?>
/Okay/Modules/OkayCMS/FAQ/Backend/design/css/faq.css">

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->faq_title, ENT_QUOTES, 'UTF-8', true);?>

            </div>
            <div class="box_btn_heading">
                <a class="btn btn_small btn-info" href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'OkayCMS.FAQ.FAQAdmin','return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'plus'), 0, false);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->faq_add, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="boxed fn_toggle_wrap">
    <?php if ($_smarty_tpl->tpl_vars['faqs']->value) {?>
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <form id="list_form" method="post" class="fn_form_list">
                    <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">

                    <div class="pages_wrap okay_list">
                                                <div class="okay_list_head">
                            <div class="okay_list_boding okay_list_drag"></div>
                            <div class="okay_list_heading okay_list_check">
                                <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value=""/>
                                <label class="okay_ckeckbox" for="check_all_1"></label>
                            </div>
                            <div class="okay_list_heading okay_list_faq_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->pages_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_status"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enable, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            <div class="okay_list_heading okay_list_close"></div>
                        </div>

                                                <div id="sortable" class="okay_list_body sortable">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['faqs']->value, 'faq');
$_smarty_tpl->tpl_vars['faq']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['faq']->value) {
$_smarty_tpl->tpl_vars['faq']->do_else = false;
?>
                                <div class="fn_row okay_list_body_item">
                                    <div class="okay_list_row">
                                        <input type="hidden" name="positions[<?php echo $_smarty_tpl->tpl_vars['faq']->value->id;?>
]" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['faq']->value->position, ENT_QUOTES, 'UTF-8', true);?>
">

                                        <div class="okay_list_boding okay_list_drag move_zone">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'drag_vertical'), 0, true);
?>
                                        </div>

                                        <div class="okay_list_boding okay_list_check">
                                            <input class="hidden_check" type="checkbox" id="id_<?php echo $_smarty_tpl->tpl_vars['faq']->value->id;?>
" name="check[]" value="<?php echo $_smarty_tpl->tpl_vars['faq']->value->id;?>
"/>
                                            <label class="okay_ckeckbox" for="id_<?php echo $_smarty_tpl->tpl_vars['faq']->value->id;?>
"></label>
                                        </div>

                                        <div class="okay_list_boding okay_list_faq_name">
                                            <a href="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>"OkayCMS.FAQ.FAQAdmin",'id'=>$_smarty_tpl->tpl_vars['faq']->value->id,'return'=>$_SERVER['REQUEST_URI']),$_smarty_tpl ) );?>
">
                                                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['faq']->value->question, ENT_QUOTES, 'UTF-8', true);?>

                                            </a>
                                        </div>

                                        <div class="okay_list_boding okay_list_status">
                                                                                        <label class="switch switch-default ">
                                                <input class="switch-input fn_ajax_action <?php if ($_smarty_tpl->tpl_vars['faq']->value->visible) {?>fn_active_class<?php }?>" data-controller="OkayCMS.FAQ.FAQEntity" data-action="visible" data-id="<?php echo $_smarty_tpl->tpl_vars['faq']->value->id;?>
" name="visible" value="1" type="checkbox"  <?php if ($_smarty_tpl->tpl_vars['faq']->value->visible) {?>checked=""<?php }?>/>
                                                <span class="switch-label"></span>
                                                <span class="switch-handle"></span>
                                            </label>
                                        </div>

                                        <div class="okay_list_boding okay_list_close">
                                                                                        <button data-hint="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->pages_delete, ENT_QUOTES, 'UTF-8', true);?>
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
            <div class="col-lg-12 col-md-12 col-sm 12 txt_center">
                <?php $_smarty_tpl->_subTemplateRender('file:pagination.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
            </div>
        </div>
    <?php } else { ?>
        <div class="heading_box mt-1">
            <div class="text_grey"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->no_faqs, ENT_QUOTES, 'UTF-8', true);?>
</div>
        </div>
    <?php }?>
</div><?php }
}
