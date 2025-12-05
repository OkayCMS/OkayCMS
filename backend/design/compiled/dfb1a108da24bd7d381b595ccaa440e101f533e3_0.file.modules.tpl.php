<?php
/* Smarty version 3.1.40, created on 2025-11-26 11:29:10
  from '/var/www/html/backend/design/html/modules.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926c8663f8877_82686441',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dfb1a108da24bd7d381b595ccaa440e101f533e3' => 
    array (
      0 => '/var/www/html/backend/design/html/modules.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 5,
    'file:module_list.tpl' => 1,
  ),
),false)) {
function content_6926c8663f8877_82686441 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('meta_title', $_smarty_tpl->tpl_vars['btr']->value->modules_list_title ,false ,32);?>

<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="wrap_heading">
            <div class="box_heading heading_page">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_list_title, ENT_QUOTES, 'UTF-8', true);?>

            </div>
            <div class="box_btn_heading hidden-xs-down">
                <a class="btn btn_small btn-info fn_switch_add_module" href="javascript:;">
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'plus'), 0, false);
?>
                    <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'minus'), 0, true);
?>
                    <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_add_new_module, ENT_QUOTES, 'UTF-8', true);?>
</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="fn_hide_add_module hidden-xs-down" style="display: none;">
    <div class="boxed">
        <div class="">
            <div class="heading_box">
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_download_heading, ENT_QUOTES, 'UTF-8', true);?>

            </div>
            <form method="post" id="fn_download_module" action="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ModulesAdmin@downloadModule'),$_smarty_tpl ) );?>
">
                <div class="alert alert--center alert--icon alert--error fn_error_block hidden">
                    <div class="alert__content">
                        <div class="alert__title fn_error_text"></div>
                    </div>
                </div>
                <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
        
                <div class="alert  alert--icon alert--info">
                    <div class="alert__content">
                        <div class="alert__title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->alert_info, ENT_QUOTES, 'UTF-8', true);?>
</div>
                        <p>
                            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_url_description, ENT_QUOTES, 'UTF-8', true);?>
<a href="index.php?controller=ModulesAdmin@marketplace"><?php echo $_smarty_tpl->tpl_vars['btr']->value->modules_url_description_marketplace;?>
</a><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_url_description_2, ENT_QUOTES, 'UTF-8', true);?>

                        </p>
                        <p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_url_example, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->marketplace_url, ENT_QUOTES, 'UTF-8', true);?>
my_module/&#60;uniqueHash&#62;</p>
                    </div>
                </div>
                
                <div class="modules_add_new_modules">
                    <div class="input-group input-group--dabbl input-group--reset">
                        <span class="input-group-addon input-group-addon--left">URL</span>
                        <input class="form-control" type="text" name="access_url">
                    </div>
                    <button type="submit" class="btn btn_small btn_blue">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                        <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    
</div>

<div class="fn_toggle_wrap">
    <?php if ($_smarty_tpl->tpl_vars['modules']->value) {?>
        <form class="fn_form_list" method="post">
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <div class="heading_label"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->settings_general_email_module, ENT_QUOTES, 'UTF-8', true);?>
</div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="mb-1">
                        <input name="email_for_module" class="form-control" type="email" required
                               value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['settings']->value->email_for_module, ENT_QUOTES, 'UTF-8', true);?>
"/>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <button type="submit" class="btn btn_small btn_blue">
                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'checked'), 0, true);
?>
                        <span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_apply, ENT_QUOTES, 'UTF-8', true);?>
</span>
                    </button>
                </div>
            </div>
            <div class="okay_list products_list bg_white mb-1 fn_sort_list">
                <input type="hidden" name="session_id" value="<?php echo $_SESSION['id'];?>
">
                                <div class="okay_list_head">
                    <div class="okay_list_boding okay_list_drag"></div>
                    <div class="okay_list_heading okay_list_check">
                        <input class="hidden_check fn_check_all" type="checkbox" id="check_all_1" name="" value="" />
                        <label class="okay_ckeckbox" for="check_all_1"></label>
                    </div>
                    <div class="okay_list_heading okay_list_photo"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_photo, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    <div class="okay_list_heading okay_list_module_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_name, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    <div class="okay_list_heading okay_list_module_license_status hidden-md-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_status, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    <div class="okay_list_heading okay_list_module_version hidden-md-down"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_version, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    <div class="okay_list_heading okay_list_status"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_enable, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    <div class="okay_list_heading okay_list_setting okay_list_products_setting"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->general_activities, ENT_QUOTES, 'UTF-8', true);?>
</div>
                    <div class="okay_list_heading okay_list_close"></div>
                </div>

                                <div class="deliveries_wrap okay_list_body sortable fn_modules_list">
                    <?php $_smarty_tpl->_subTemplateRender('file:module_list.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
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
                                <option value="update"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->module_update, ENT_QUOTES, 'UTF-8', true);?>
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
    <?php } else { ?>
        <div class="heading_box mt-1">
            <div class="text_grey"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->no_modules, ENT_QUOTES, 'UTF-8', true);?>
</div>
        </div>
    <?php }?>
</div>

<?php echo '<script'; ?>
>
    $(document).on("click", ".fn_update_module", function () {
        $('.fn_form_list input[type="checkbox"][name*="check"]').attr('checked', false);
        $(this).closest(".fn_form_list").find('select[name="action"] option[value=update]').attr('selected', true);
        $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').attr('checked', true);
        $(this).closest(".fn_row").find('input[type="checkbox"][name*="check"]').click();
        $(this).closest(".fn_form_list").submit();
    });
    
    $(document).on("submit", "#fn_download_module", function (e) {
        e.preventDefault();
        let form = $(this),
            urlInput = form.find('[name="access_url"]'),
            errorBlock = form.find('.fn_error_block');
        urlInput.removeClass('error');
        errorBlock.addClass('hidden');
        if (!/<?php echo str_replace('/','\/',$_smarty_tpl->tpl_vars['config']->value->marketplace_url);?>
my_module\/\w+$/.test(urlInput.val())) {
            urlInput.addClass('error');
        }
        
        $.ajax({
            url: '<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ModulesAdmin@downloadModule'),$_smarty_tpl ) );?>
',
            dataType: "json",
            type: "POST",
            data: form.serialize(),
            success: function (data) {
                if (data.hasOwnProperty('installed_version')) {
                    $('.fn_error_text').text('<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['btr']->value->modules_already_installed, ENT_QUOTES, 'UTF-8', true);?>
 ' + data.installed_version);
                    errorBlock.removeClass('hidden');
                }
                if (data.hasOwnProperty('error')) {
                    $('.fn_error_text').text(data.error);
                    errorBlock.removeClass('hidden');
                }
                if (data.hasOwnProperty('success') && data.success === true) {
                    $('.fn_modules_list').prepend(data.modules);
                }
            }
        });
    });
    $(document).on("click", ".fn_switch", function (e) {
    e.preventDefault();

    $(this).next().slideToggle(300);

    if ($(this).hasClass("active")) {
      $(this).removeClass("active");
    } else {
      $(this).addClass("active");
    }
  });
    $(document).on('click', '.fn_continue_access', function () {
        let destination = $(this).data('target')
            + '?cite_return_url=<?php echo urlencode($_smarty_tpl->tpl_vars['rootUrl']->value);
echo urlencode($_SERVER['REQUEST_URI']);?>
'
            + '&continue_modules_expire=1'
            + '&checkout_domain=<?php echo $_smarty_tpl->tpl_vars['domain']->value;?>
'
            +'&checkout_email=<?php echo $_smarty_tpl->tpl_vars['settings']->value->email_for_module;?>
';
        $.ajax({
            url: '<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['url'][0], array( array('controller'=>'ModulesAdmin@resetModulesAccessExpiresCache'),$_smarty_tpl ) );?>
',
            type: 'GET',
            cache: false,
            contentType: false,
            processData: false
        }).done(function(response) {
            window.location.replace(destination);
        });
    });
<?php echo '</script'; ?>
>
<?php }
}
