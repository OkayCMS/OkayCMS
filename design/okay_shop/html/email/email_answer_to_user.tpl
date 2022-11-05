{* Письмо ответа на комметарий или обращение пользователю
Для отладки ответного сообщение пользователю на комментарий пройдите по ссылке http://domain/backend/index.php?controller=EmailTemplatesAdmin&debug=emailCommentAnswerToUser&comment_id=1, измените параметр comment_id - это номер ответного комментария
Для отладки ответного сообщение пользователю на обратную связь пройдите по ссылке http://domain/backend/index.php?controller=EmailTemplatesAdmin&debug=emailFeedbackAnswerFoUser&feedback_id=1 для тестирования если потребуется, измените параметр feedback_id - это номер ответа пользователю.
*}

{if $object->type_obj == 'comment'}
    {$subject = "`$lang->email_comment_theme` `$settings->site_name`" scope=global}
{elseif $object->type_obj == 'feedback'}
    {$subject = "`$lang->email_feedback_subject`" scope=global}
{/if}

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{if $object->type_obj == 'comment'}{$lang->email_comment_answer_s}{elseif $object->type_obj == 'feedback'}{$lang->email_comment_answer_l}{/if}</title>
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
                                                                <h1>{if $object->type_obj == 'comment'}
                                                                        {$lang->email_comment_answer_s}
                                                                    {elseif $object->type_obj == 'feedback'}
                                                                        {$lang->email_comment_answer_l}
                                                                    {/if}.
                                                                </h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p5t es-p5b es-p40r es-p40l" align="center">
                                                                <p style="font-size: 16px;color: #5c5c5c;">
                                                                    {if $object->type_obj == 'comment'}
                                                                        {$lang->email_comment_hello} <i>{$object->name|escape}</i>.

                                                                        {$lang->email_comment_answer_user} <a href="{url_generator route="main" absolute=1}">{$settings->site_name}</a>
                                                                        {if $object->type == 'product'}
                                                                            {$lang->email_comment_product}: <div><strong>"{$object->product->name}"</strong></div>
                                                                        {elseif $object->type == 'post'}
                                                                            {$lang->email_comment_article}: <div><strong>"{$object->post->name}"</strong></div>
                                                                        {/if}
                                                                    {elseif $object->type_obj == 'feedback'}
                                                                        {$lang->email_comment_hello} <i>{$object->name|escape}</i>. {$lang->email_comment_answer_user_s}
                                                                        <a target="_blank" href="{url_generator route="main" absolute=1}">{$settings->site_name}</a>
                                                                    {/if}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        {if $object->type_obj == 'comment'}
                                                            <tr>
                                                                <td class="es-p10t es-p10b" align="center">
                                                                    {if $object->type == 'product'}
                                                                    <a class="es-button" target="_blank" href="{url_generator route="product" url=$object->product->url absolute=1}#comment_{$object->id}">
                                                                        {$lang->email_comment_look}
                                                                    </a>
                                                                    {elseif $object->type == 'post'}
                                                                    <a class="es-button" target="_blank" href="{url_generator route="post" url=$object->post->url absolute=1}#comment_{$object->id}">
                                                                        {$lang->email_comment_look}
                                                                    </a>{/if}
                                                                </td>
                                                            </tr>
                                                        {/if}
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

                <table class="es-content" cellspacing="0" cellpadding="0" align="center">
                    <tbody>
                    <tr>
                        <td align="center">
                            <table class="es-content-body" width="600" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center">
                                <tbody>
                                <tr>
                                    <td class="es-p15t es-p10b es-p20r es-p20l" align="left">
                                        <table width="100%" cellspacing="0" cellpadding="0" align="left">
                                            <tbody>
                                            <tr>
                                                <td class="es-p5t es-p5b" align="left">
                                                    <div class="es-comment-name">{$lang->email_comment_your_comment}
                                                        (<span class="es-comment-date">{$object->date|date} {$object->date|time}</span>):
                                                    </div>
                                                    <div class="es-comment-text es-p15">
                                                        {if $object->type_obj == 'comment'}
                                                            {$object->text|escape}
                                                        {elseif $object->type_obj == 'feedback'}
                                                            {$object->message|escape|nl2br}
                                                        {/if}
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="es-p15b es-p20r es-p20l" align="right">
                                        <table width="100%" cellspacing="0" cellpadding="0" align="right">
                                            <tbody>
                                            <tr>
                                                <td class="es-p5t es-p5b" align="left">
                                                    <div class="es-comment-name">{$lang->email_comment_admin_ans}
                                                        (<span class="es-comment-date">{$objectAnswer->date|date} {$objectAnswer->date|time}</span>):
                                                    </div>
                                                    <div class="es-comment-text es-p15">
                                                    {if $objectAnswer->type_obj == 'comment'}
                                                        {$objectAnswer->text|escape}
                                                    {elseif $object->type_obj == 'feedback'}
                                                        {$objectAnswer->message|escape}
                                                    {/if}
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

                {* Footer email *}
                {include "design/{get_theme}/html/email/email_footer.tpl"}

            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>

</html>