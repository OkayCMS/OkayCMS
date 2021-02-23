{$wrapper = '' scope=global}
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
    <script src="design/js/jquery/jquery.js"></script>
</head>
<body>
<div class="container d-table">
    <div class="d-100vh-va-middle">
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card-group">
                    <div class="card p-2">
                        <div class="">
                            {*Форма авторизации*}
                            <form method="post">
                                <input type=hidden name="session_id" value="{$smarty.session.id}">
                                {if $recovery_mod}
                                    <h1 class="auth_heading">Восстановление пароля</h1>
                                    <p class="auth_heading_promo">на сайте {$smarty.server.HTTP_HOST}</p>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            {include file='svg_icon.tpl' svgId='user_icon'}
                                        </span>
                                        <input name="new_login" value="" type="text" class="form-control" autofocus="" tabindex="1" placeholder="Введите логин">
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            {include file='svg_icon.tpl' svgId='pass_icon'}
                                        </span>
                                        <input type="password" name="new_password" value="" tabindex="2" class="form-control" placeholder="Введите пароль">
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            {include file='svg_icon.tpl' svgId='pass_icon'}
                                        </span>
                                        <input type="password" name="new_password_check" value="" tabindex="3" class="form-control" placeholder="Повторите пароль">
                                    </div>
                                    <div class="auth_buttons">
                                        <button type="submit" value="login" class="auth_buttons__login btn btn_blue btn_big btn-block" tabindex="3">Войти</button>
                                    </div>
                                {else}
                                    <h1 class="auth_heading">Вход в панель управления</h1>
                                    <p class="auth_heading_promo">{$smarty.server.HTTP_HOST}</p>

                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            {include file='svg_icon.tpl' svgId='user_icon'}
                                        </span>
                                        <input name="login" value="{$login}" type="text" class="form-control" autofocus="" tabindex="1" placeholder="Введите логин">
                                    </div>
                                    <div class="input-group mb-1">
                                        <span class="input-group-addon">
                                            {include file='svg_icon.tpl' svgId='pass_icon'}
                                        </span>
                                        <input type="password" name="password" value="" tabindex="2" class="form-control" placeholder="Введите пароль">
                                    </div>
                                    {if $error_message}
                                    <div class="mb-1 error_box">
                                        {if $error_message == 'auth_wrong'}
                                        Неверно введены логин или пароль.
                                        {if $limit_cnt}<br>Осталось {$limit_cnt} {$limit_cnt|plural:'попытка':'попыток':'попытки'}{/if}
                                        {elseif $error_message == 'limit_try'}
                                        Вы исчерпали количество попыток на сегодня.
                                        {/if}
                                    </div>
                                    {/if}
                                    <div class="auth_buttons">
                                        <a class="auth_buttons__recovery link px-0 mb-1 fn_recovery" href="">Забыли пароль?</a>
                                        <button type="submit" value="login" class="auth_buttons__login btn btn_blue btn_big btn-block" tabindex="3">Войти</button>
                                    </div>
                                {/if}
                            </form>
                            <div class="col-xs-12 mt-1 p-h fn_recovery_wrap hidden px-0">
                                <div class="fn_error" style="display: none;margin-bottom:15px;color: #bf1e1e;font-weight: 600;font-size: 15px;"></div>
                                <div class="fn_success" style="display: none;margin-bottom:15px;color: #13bb13;font-weight: 600;font-size: 15px;">Сообщение отправлено на емейл администратору</div>
                                <label class="fn_recovery_label">Введите email администратора для восстановления пароля</label>
                                <div class="input-group mb-1">
                                    <span class="input-group-addon">
                                        {include file='svg_icon.tpl' svgId='email'}
                                    </span>
                                    <input type="email" class="form-control fn_email" value="" name="recovery_email" placeholder="E-mail">
                                </div>

                                <button type="button" value="recovery" class="btn btn_border_blue fn_ajax_recover">Напомнить</button>
                            </div>
                        </div>
                    </div>
                    <div class="card card-inverse okay_bg py-3 hidden-md-down" style="width:50%">
                        <div class="card-block text-xs-center">
                            <div>
                                <p>
                                    <img src="design/images/system_logo.png" alt="OkayCMS" />
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
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
</script>
</body>
</html>
