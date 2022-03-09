{* Письмо ответа на комметарий пользователю *}
{$subject = "`$lang->email_comment_theme` `$settings->site_name`" scope=global}


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
                                                                    {/if}.</h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p5t es-p5b es-p40r es-p40l" align="center">
                                                                <p style="font-size: 16px;color: #5c5c5c;">
                                                                    {if $object->type_obj == 'comment'}
                                                                        {$lang->email_comment_hello} <i>{$parent_comment->name|escape}</i>.

                                                                        {$lang->email_comment_answer_user} <a href="{url_generator route="main" absolute=1}">{$settings->site_name}</a>
                                                                        {if $parent_comment->type == 'product'}
                                                                            {$lang->email_comment_product}: <div><strong>"{$parent_comment->product->name}"</strong></div>
                                                                        {/if}
                                                                        {if $parent_comment->type == 'post'}
                                                                        {$lang->email_comment_article}: <div><strong>"{$parent_comment->post->name}"</strong></div>
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
                                                                    <a class="es-button" target="_blank" href="{url_generator route="product" url=$parent_comment->product->url absolute=1}#comment_{$parent_comment->id}">
                                                                        {$lang->email_comment_look}
                                                                    </a>
                                                                    {elseif $object->type == 'post'}
                                                                    <a class="es-button" target="_blank" href="{url_generator route="post" url=$parent_comment->post->url absolute=1}#comment_{$parent_comment->id}">
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
                                                    <div class="es-comment-user es-p15">
                                                        <div class="es-comment-date">
                                                            <span>{$object->date|date} {$object->date|time}</span>
                                                        </div>
                                                        <div class="es-comment-name">{$lang->email_comment_your_comment}:</div>

                                                        <div class="es-comment-text">
                                                            {if $object->type_obj == 'comment'}
                                                                {$parent_comment->text|escape}
                                                            {elseif $object->type_obj == 'feedback'}
                                                                {$object->message|escape|nl2br}
                                                            {/if}
                                                        </div>
                                                        <div class="es-comment-name">{$lang->email_comment_your_comment}</div>
                                                        <div class="es-comment-text">{$parent_comment->text|escape}</div>
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
                                                    <div class="es-comment-admin es-p15">
                                                        <div class="es-comment-date">
                                                            <span>{$smarty.now|date} {$smarty.now|time}</span>
                                                        </div>
                                                        <div class="es-comment-name">{$lang->email_comment_admin_ans}:</div>
                                                        {if $object->type_obj == 'comment'}
                                                            <div class="es-comment-text">{$object->text|escape}</div>
                                                        {elseif $object->type_obj == 'feedback'}
                                                            <div class="es-comment-text">{$text|escape}</div>
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