{*
Для отладки данного сообщение пройдите по ссылке http://domain/backend/index.php?controller=EmailTemplatesAdmin&debug=emailCommentAdmin&comment_id=1
если потребуется, измените параметр comment_id
*}

{if $comment->approved}
    {$subject="`$btr->email_comment_from` `$comment->name|escape`" scope=global}
{else}
    {$subject="`$btr->email_comment_from` `$comment->name|escape` `$btr->email_awaits`" scope=global}
{/if}

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>
        {$btr->email_new_review|escape}
        {if $comment->type == 'product'}
        {$btr->email_to_product|escape}
        {elseif $comment->type == 'blog'}
        {$btr->email_to_article|escape}
        {elseif $comment->type == 'news'}
        {$btr->email_to_news|escape}
        {/if}
    </title>
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">

    {include "backend/design/html/email/email_head.tpl"}
</head>

<body>
<div class="es-wrapper-color">
    <table class="es-wrapper" width="100%" cellspacing="0" cellpadding="0">
        <tbody>
        <tr>
            <td class="es-p25t es-p25b" valign="center">

                {* Header email *}
                {include "backend/design/html/email/email_header.tpl"}

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
                                                                <h1>
                                                                    {$btr->email_new_review|escape}
                                                                    {if $comment->type == 'product'}
                                                                    {$btr->email_to_product|escape}
                                                                    {elseif $comment->type == 'blog'}
                                                                    {$btr->email_to_article|escape}
                                                                    {elseif $comment->type == 'news'}
                                                                    {$btr->email_to_news|escape}
                                                                    {/if}.<br>
                                                                </h1>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p5t es-p5b es-p40r es-p40l" align="center">
                                                                <p style="font-size: 16px;color: #5c5c5c;">{$btr->email_inform_first|escape} {$comment->name|escape} оставил отзыв
                                                                    {if $comment->type == 'product'}
                                                                    {$btr->email_to_product|escape}
                                                                    {elseif $comment->type == 'blog'}
                                                                    {$btr->email_to_article|escape}
                                                                    {elseif $comment->type == 'news'}
                                                                    {$btr->email_to_news|escape}
                                                                    {/if}
                                                                </p>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="es-p15t es-p10b" align="center">
                                                                {if $comment->type == 'product'}
                                                                <a class="es-button" target="_blank" href="{url_generator route="product" url=$comment->product->url absolute=1}#comment_{$comment->id}">
                                                                    {$btr->email_order_info|escape}
                                                                </a>
                                                                {elseif $comment->type == 'blog'}
                                                                <a class="es-button" target="_blank" href="{url_generator route='blog_item' url=$comment->post->url absolute=1}#comment_{$comment->id}">
                                                                    {$btr->email_order_info|escape}
                                                                </a>
                                                                {elseif $comment->type == 'news'}
                                                                <a class="es-button" target="_blank" href="{url_generator route='news_item' url=$comment->post->url absolute=1}#comment_{$comment->id}">
                                                                    {$btr->email_order_info|escape}
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
                                    <td class="es-p30b es-p20r es-p20l" align="left">
                                        <table width="100%" cellspacing="0" cellpadding="0" align="left">
                                            <tbody>
                                            <tr>
                                                <td class="es-p20t es-p10b" align="left">
                                                    <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                        <tbody>
                                                        <tr>
                                                            <td class="es-m-p0r es-m-p20b" width="100%" valign="top" align="center">
                                                                <table width="100%" cellspacing="0" cellpadding="0">
                                                                    <tbody>
                                                                    <tr>
                                                                        <td class="esd-block-text">
                                                                            <h4>{$btr->email_information|escape}:</h4>
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
                                            <tr>
                                                <td class="es-m-p20b" width="100%" align="left">
                                                    <table class="es-table-infobox" cellspacing="1" cellpadding="1" border="0" align="left">
                                                        <tbody>
                                                        <tr valign="top">
                                                            <td class="es-p5t es-p5b" width="180"><span>{$btr->email_order_name|escape}:</span></td>
                                                            <td class="es-p5t es-p5b"><span>{$comment->name|escape}</span></td>
                                                        </tr>
                                                        {if $comment->email}
                                                        <tr valign="top">
                                                            <td class="es-p5t es-p5b" width="180"><span>{$btr->email_order_email|escape}:</span></td>
                                                            <td class="es-p5t es-p5b"><span>{$comment->email|escape}</span></td>
                                                        </tr>
                                                        {/if}
                                                        <tr valign="top">
                                                            <td class="es-p5t es-p5b" width="180"><span>{$btr->email_time|escape}:</span></td>
                                                            <td class="es-p5t es-p5b"><span>{$comment->date|date} {$comment->date|time}</span></td>
                                                        </tr>
                                                        <tr valign="top">
                                                            <td class="es-p5t es-p5b" width="180"><span>IP:</span></td>
                                                            <td class="es-p5t es-p5b">
                                                                {$comment->ip|escape}
                                                                (<a style="font-size: 13px;" href='http://www.ip-adress.com/ip_tracer/{$comment->ip}/'>{$btr->email_where|escape}</a>)
                                                            </td>
                                                        </tr>
                                                        <tr valign="top">
                                                            <td class="es-p5t es-p5b" width="180"><span>{$btr->general_comment|escape}:</span></td>
                                                            <td class="es-p5t es-p5b"><span>{$comment->text|escape|nl2br}</span></td>
                                                        </tr>
                                                        {get_design_block block="email_comment_admin_total_info"}
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
                {include "backend/design/html/email/email_footer.tpl"}

            </td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>
