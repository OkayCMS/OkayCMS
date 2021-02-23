{* Письмо ответа на комметарий пользователю *}
{$subject = "`$lang->email_comment_theme` `$settings->site_name`" scope=global}


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$lang->email_comment_answer_s}</title>
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
                                                                <h1>{$lang->email_comment_answer_s}.</h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p5t es-p5b es-p40r es-p40l" align="center">
                                                                <p style="font-size: 16px;color: #5c5c5c;">
                                                                    {$lang->email_comment_hello} <i>{$parent_comment->name|escape}</i>.

                                                                    {$lang->email_comment_answer_user} <a href="{url_generator route="main" absolute=1}">{$settings->site_name}</a>
                                                                    {if $parent_comment->type == 'product'}
                                                                        {$lang->email_comment_product}: <div><strong>"{$parent_comment->product->name}"</strong></div>
                                                                    {/if}
                                                                    {if $parent_comment->type == 'blog'}
                                                                        {$lang->email_comment_article}: <div><strong>"{$parent_comment->post->name}"</strong></div>
                                                                    {/if}
                                                                    {if $parent_comment->type == 'news'}
                                                                        {$lang->email_comment_news}: <div><strong>"{$parent_comment->post->name}"</strong></div>
                                                                    {/if}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p10t es-p10b" align="center">
                                                                {if $comment->type == 'product'}
                                                                <a class="es-button" target="_blank" href="{url_generator route="product" url=$parent_comment->product->url absolute=1}#comment_{$parent_comment->id}">
                                                                    {$lang->email_comment_look}
                                                                </a>
                                                                {elseif $comment->type == 'blog'}
                                                                <a class="es-button" target="_blank" href="{url_generator route="blog_item" url=$parent_comment->post->url absolute=1}#comment_{$parent_comment->id}">
                                                                    {$lang->email_comment_look}
                                                                </a>
                                                                {elseif $comment->type == 'news'}
                                                                <a class="es-button" target="_blank" href="{url_generator route="news_item" url=$parent_comment->post->url absolute=1}#comment_{$parent_comment->id}">
                                                                    {$lang->email_comment_look}
                                                                </a>
                                                                {/if}
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
                                                            <span>{$parent_comment->date|date} {$parent_comment->date|time}</span>
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
                                                            <span>{$comment->date|date} {$comment->date|time}</span>
                                                        </div>
                                                        <div class="es-comment-name">{$lang->email_comment_admin_ans}</div>
                                                        <div class="es-comment-text">{$comment->text|escape}</div>
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