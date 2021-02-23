{* Письмо восстановления пароля *}
{$subject = "`$lang->email_password_subject`" scope=global}


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$lang->password_remind_title}</title>
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">

    {include "design/{get_theme}/html/email/email_head.tpl"}
</head>

<body>
<div class="es-wrapper-color">
    <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td class="es-p0t es-p0b" valign="center">

                {* Header email *}
                {include "design/{get_theme}/html/email/email_header.tpl"}

                <table class="es-content" cellspacing="0" cellpadding="0" align="center">
                    <tbody>
                    <tr>
                        <td align="center">
                            <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center">
                                <tbody>
                                <tr>
                                    <td class="es-p10t es-p10b es-p20r es-p20l" align="center">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tbody>
                                            <tr>
                                                <td valign="top" align="center">
                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                        <tbody>
                                                        <tr>
                                                            <td class="es-p10t es-p15b" align="center">
                                                                <h1>{$lang->password_remind_title}.</h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p5t es-p5b es-p40r es-p40l" align="center">
                                                                <p>{$user->name|escape} {$lang->email_password_on_site}
                                                                    <a href='{url_generator route="main" absolute=1}'>{$settings->site_name}</a>
                                                                    {$lang->email_password_was_reply}
                                                                </p>
                                                                <p> {$lang->email_password_change_pass}</p>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p5t es-p10b" align="center">
                                                                <a class="es-button" target="_blank" href="{url_generator route="password_remind" code=$code absolute=1}">
                                                                    {$lang->user_change_password}
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p15t es-p10b" align="center">
                                                                <div style="font-size: 12px;color: #222;">
                                                                    <em>* {$lang->email_password_text}</em>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

                {* Footer email *}
                {include "design/{get_theme}/html/email/email_footer.tpl"}

            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
