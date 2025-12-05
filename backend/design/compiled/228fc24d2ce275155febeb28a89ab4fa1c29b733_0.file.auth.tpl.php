<?php
/* Smarty version 3.1.40, created on 2025-11-26 12:50:27
  from '/var/www/html/backend/design/html/auth.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.40',
  'unifunc' => 'content_6926db73cda907_41756809',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '228fc24d2ce275155febeb28a89ab4fa1c29b733' => 
    array (
      0 => '/var/www/html/backend/design/html/auth.tpl',
      1 => 1764098168,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:svg_icon.tpl' => 6,
  ),
),false)) {
function content_6926db73cda907_41756809 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('wrapper', '' ,false ,32);?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache">
    <META HTTP-EQUIV="Expires" CONTENT="-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Административная панель</title>

    <link href="design/css/okay.css" rel="stylesheet" type="text/css" />
    <link href="design/css/grid.css" rel="stylesheet" type="text/css" />
    <link rel="icon" href="design/images/favicon.png" type="image/x-icon">
    <?php echo '<script'; ?>
 src="design/js/jquery/jquery.js"><?php echo '</script'; ?>
>
</head>
<body>
<div class="container d-table">
    <div class="d-100vh-va-middle">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card-group">
                    <div class="card p-2">
                        <div class="">
                                                        <form method="post">
                                <input type=hidden name="session_id" value="<?php echo $_SESSION['id'];?>
">
                                <?php if ($_smarty_tpl->tpl_vars['recovery_mod']->value) {?>
                                    <h1 class="auth_heading">Восстановление пароля</h1>
                                    <p class="auth_heading_promo">на сайте <?php echo $_SERVER['HTTP_HOST'];?>
</p>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'user_icon'), 0, false);
?>
                                        </span>
                                        <input name="new_login" value="" type="text" class="form-control" autofocus="" tabindex="1" placeholder="Введите логин">
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'pass_icon'), 0, true);
?>
                                        </span>
                                        <input type="password" name="new_password" value="" tabindex="2" class="form-control" placeholder="Введите пароль">
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'pass_icon'), 0, true);
?>
                                        </span>
                                        <input type="password" name="new_password_check" value="" tabindex="3" class="form-control" placeholder="Повторите пароль">
                                    </div>
                                    <div class="auth_buttons">
                                        <button type="submit" value="login" class="auth_buttons__login btn btn_blue btn_big btn-block" tabindex="3">Войти</button>
                                    </div>
                                <?php } else { ?>
                                    <h1 class="auth_heading">Вход в панель управления</h1>
                                    <p class="auth_heading_promo"><?php echo $_SERVER['HTTP_HOST'];?>
</p>

                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'user_icon'), 0, true);
?>
                                        </span>
                                        <input name="login" value="<?php echo $_smarty_tpl->tpl_vars['login']->value;?>
" type="text" class="form-control" autofocus="" tabindex="1" placeholder="Введите логин">
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'pass_icon'), 0, true);
?>
                                        </span>
                                        <input type="password" name="password" value="" tabindex="2" class="form-control" placeholder="Введите пароль">
                                    </div>
                                    <?php if ($_smarty_tpl->tpl_vars['error_message']->value) {?>
                                    <div class="mb-1 error_box">
                                        <?php if ($_smarty_tpl->tpl_vars['error_message']->value == 'auth_wrong') {?>
                                        Неверно введены логин или пароль.
                                        <?php if ($_smarty_tpl->tpl_vars['limit_cnt']->value) {?><br>Осталось <?php echo $_smarty_tpl->tpl_vars['limit_cnt']->value;?>
 <?php echo call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'plural' ][ 0 ], array( $_smarty_tpl->tpl_vars['limit_cnt']->value,'попытка','попыток','попытки' ));
}?>
                                        <?php } elseif ($_smarty_tpl->tpl_vars['error_message']->value == 'limit_try') {?>
                                        Вы исчерпали количество попыток на сегодня.
                                        <?php }?>
                                    </div>
                                    <?php }?>
                                    <div class="auth_buttons">
                                        <a class="auth_buttons__recovery link px-0 mb-1 fn_recovery" href="">Забыли пароль?</a>
                                        <button type="submit" value="login" class="auth_buttons__login btn btn_blue btn_big btn-block" tabindex="3">Войти</button>
                                    </div>
                                <?php }?>
                            </form>
                            <div class="col-xs-12 mt-1 p-h fn_recovery_wrap hidden px-0">
                                <div class="fn_error" style="display: none;margin-bottom:15px;color: #bf1e1e;font-weight: 600;font-size: 15px;"></div>
                                <div class="fn_success" style="display: none;margin-bottom:15px;color: #13bb13;font-weight: 600;font-size: 15px;">Сообщение отправлено на емейл администратору</div>
                                <label class="fn_recovery_label">Введите email администратора для восстановления пароля</label>
                                <div class="input-group mb-1">
                                    <span class="input-group-addon">
                                        <?php $_smarty_tpl->_subTemplateRender('file:svg_icon.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('svgId'=>'email'), 0, true);
?>
                                    </span>
                                    <input type="email" class="form-control fn_email" value="" name="recovery_email" placeholder="E-mail">
                                </div>

                                <button type="button" value="recovery" class="btn btn_border_blue fn_ajax_recover">Напомнить</button>
                            </div>
                            <div class="mt-2 hidden-lg-up">
                                <div class="card-block--version">OkayCMS v.<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            </div>
                        </div>
                    </div>
                    <div class="card card-inverse okay_bg py-3 hidden-md-down" style="width:50%">
                        <div class="card-block text-xs-center">
                            <div>
                                <p>
                                    <img src="design/images/system_logo.png" alt="OkayCMS" />
                                </p>
                                <div class="card-block--version">Version <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['config']->value->version, ENT_QUOTES, 'UTF-8', true);?>
</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php echo '<script'; ?>
>
    $(function () {
        $(document).on("click", ".fn_recovery", function (e) {
            e.preventDefault();
            $(".fn_recovery_wrap").toggleClass("hidden");
            return false;
        });
        $(document).on("click", ".fn_ajax_recover", function () {
            link = window.location.href;
            email = $(".fn_email").val();
            //$(this).attr('disabled',true);
            $.ajax( {
                url: link,
                data: {
                    ajax_recovery : true,
                    recovery_email : email
                },
                method : 'get',
                dataType: 'json',
                success: function(data) {
                    if (data.send){
                        $(".fn_error").hide();
                        $(".fn_success").show();
                        $(".fn_recovery_label").remove();
                        $(".fn_email").remove();
                    } else if (data.error) {
                        switch (data.error) {
                            case 'wrong_email':
                                $(".fn_error").text('Введите корректный E-mail');
                                break;
                            case 'not_admin_email':
                                $(".fn_error").text('Указанный E-mail не принадлежит админу сайта');
                                break;
                        }
                        $(".fn_error").show();
                        $(".fn_success").hide();
                    }
                }
            })
        });
    })
<?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
