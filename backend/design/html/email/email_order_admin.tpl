{*
Для отладки данного сообщение пройдите по ссылке http://domain/backend/index.php?controller=EmailTemplatesAdmin&debug=emailOrderAdmin&order_id=1
если потребуется, измените параметр order_id
*}
{if $order->paid}
    {$subject = "`$btr->email_order` `$order->id` `$btr->email_paid`" scope=global}
{else}
    {$subject = "`$btr->email_new_order` `$order->id`" scope=global}
{/if}

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{$btr->email_new_order|escape} № {$order->id}</title>
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="telephone=no" name="format-detection">

    {include "backend/design/html/email/email_head.tpl"}
</head>
<body class="body_email">
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
                                                                    <h1>{$btr->email_new_order|escape} <span class="es-number-order">№ {$order->id}</span><br></h1>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="es-p10t es-p0b es-p40r es-p40l" align="center">
                                                                    <p>{$btr->email_inform_order_first|escape} <strong>№ {$order->id}</strong> {$btr->email_from|escape} <strong>{$order->date|date}:{$order->date|time}.</strong><br> {$btr->email_inform_order_last|escape}
                                                                        <span class="es-status-color">{$order_status->name|escape}</span></p>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="es-p15t es-p10b" align="center">
                                                                    <a href="{$rootUrl}/backend/index.php?controller=OrderAdmin&id={$order->id}" class="es-button" target="_blank" >{$btr->email_order_info|escape}</a>
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
                                                                            <td class="esd-block-text" align="left">
                                                                                <h4>{$btr->email_details_order|escape}:</h4>
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
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_number_s|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>№ {$order->id}</span></td>
                                                            </tr>
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_date_s|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order->date|date}:{$order->date|time}</span></td>
                                                            </tr>
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_status_s|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order_status->name|escape}</span></td>
                                                            </tr>
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_payment_status|escape}:</span></td>
                                                                <td class="es-p5t es-p5b">
                                                                        <span>
                                                                            {if $order->paid == 1}
                                                                                 {$btr->email_paid|escape}
                                                                             {else}
                                                                                 {$btr->email_not_paid|escape}
                                                                             {/if}
                                                                        </span>
                                                                </td>
                                                            </tr>
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_name|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order->name|escape} {$order->last_name|escape}</span></td>
                                                            </tr>
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_email|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order->email|escape}</span></td>
                                                            </tr>
                                                            {if $order->phone}
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_phone|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order->phone|phone}</span></td>
                                                            </tr>
                                                            {/if}
                                                            {if $order->address}
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_address|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order->address|escape}</span></td>
                                                            </tr>
                                                            {/if}
                                                            {if $order->comment}
                                                            <tr valign="top">
                                                                <td class="es-p5t es-p5b" width="180px"><span>{$btr->email_order_comment|escape}:</span></td>
                                                                <td class="es-p5t es-p5b"><span>{$order->comment|escape|nl2br}</span></td>
                                                            </tr>
                                                            {/if}
                                                            {get_design_block block="email_order_admin_contact_info"}
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
                                            <table width="100%" cellspacing="0" cellpadding="0" align="center">
                                                <tbody>
                                                    <tr>
                                                        <td class="es-p20t es-p10b" align="left">
                                                            <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                                <tbody>
                                                                <tr>
                                                                    <td class="es-m-p0r es-m-p20b" align="left">
                                                                        <table class="100%" cellspacing="0" cellpadding="0" align="left">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td class="esd-block-text" align="left">
                                                                                    <h4>{$btr->email_order_purchases}:</h4>
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
                                                        <td class="" align="left">
                                                            <table>
                                                                <tbody>
                                                                <tr>
                                                                    <td class="" align="left">
                                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td width="560" valign="top" align="center">
                                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                                        <tbody>
                                                                                        <tr>
                                                                                            <td class="es-p10b" align="center">
                                                                                                <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
                                                                                                    <tbody>
                                                                                                    <tr>
                                                                                                        <td style="border-bottom: 1px solid #dbdbdb; background: #dbdbdb; height: 1px; width: 100%; margin: 0px;"></td>
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
                                                                {foreach $purchases as $purchase}
                                                                <tr>
                                                                    <td class="es-p10t es-p10b" align="left">
                                                                        <table class="es-left" cellspacing="0" cellpadding="0" align="left">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td class="es-m-p0r es-m-p20b" width="178" valign="top" align="center">
                                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                                        <tbody>
                                                                                        <tr>
                                                                                            <td align="center">
                                                                                                <a href="{url_generator route="product" url=$purchase->product->url absolute=1}">
                                                                                                {if $purchase->product->image}
                                                                                                <img align="middle" src="{$purchase->product->image->filename|resize:120:120}" />
                                                                                                {else}
                                                                                                <img width="100" height="100" src="{$rootUrl}/backend/design/images/no_image.png" alt="{$purchase->product->name|escape}" title="{$purchase->product->name|escape}">
                                                                                                {/if}
                                                                                                </a>
                                                                                            </td>
                                                                                        </tr>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        <table width="380px" cellspacing="0" cellpadding="0" align="right">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td width="100%" align="left">
                                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                                        <tbody>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <a href="{url_generator route='product' url=$purchase->product->url absolute=1}" style="font-family: 'Trebuchet MS';font-size: 16px;color: #222;text-decoration: none;line-height: normal;">{$purchase->product_name|escape}</a><br />
                                                                                                <span class="es-p5t"><em><span style="color: rgb(128, 128, 128); font-size: 12px;">{$purchase->variant_name|escape}</span></em></span>
                                                                                                {if $purchase->variant->stock == 0}
                                                                                                <div class="es-p5t" style="color: #000; font-size: 12px;font-weight: 600">{$lang->product_pre_order}</div>
                                                                                                {/if}
                                                                                                {get_design_block block="email_order_admin_purchase_name" vars=['purchase' => $purchase]}
                                                                                            </td>
                                                                                            <td style="text-align: center;" width="60">
                                                                                                {$purchase->amount} {if $purchase->units}{$purchase->units|escape}{else}{$settings->units}{/if}
                                                                                            </td>
                                                                                            <td style="text-align: right;" width="100">
                                                                                                <b>{$purchase->price|convert:$currency->id}&nbsp;{$currency->sign}</b>
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
                                                                    <td class="" align="left">
                                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td width="560" valign="top" align="center">
                                                                                    <table width="100%" cellspacing="0" cellpadding="0">
                                                                                        <tbody>
                                                                                        <tr>
                                                                                            <td class="es-p10b" align="center">
                                                                                                <table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
                                                                                                    <tbody>
                                                                                                    <tr>
                                                                                                        <td style="border-bottom: 1px solid #dbdbdb; background: #dbdbdb; height: 1px; width: 100%; margin: 0px;"></td>
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
                                                                {/foreach}

                                                                {get_design_block block="email_order_admin_custom_block"}
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td class="es-p15t" align="left">
                                                            <table width="100%" cellspacing="0" cellpadding="0">
                                                                <tbody>
                                                                <tr>
                                                                    <td valign="top" align="center">
                                                                        <table width="100%" cellspacing="0" cellpadding="0">
                                                                            <tbody>
                                                                            <tr>
                                                                                <td align="right">
                                                                                    <table style="width: 500px;" cellspacing="1" cellpadding="1" border="0" align="right">
                                                                                        <tbody>
                                                                                        {if $order->discount}
                                                                                        <tr>
                                                                                            <td style="text-align: right; font-size: 18px; line-height: 150%;">{$btr->email_order_discount}:</td>
                                                                                            <td style="text-align: right; font-size: 18px; line-height: 150%; color: #000;">{$order->discount}&nbsp;%</td>
                                                                                        </tr>
                                                                                        {/if}

                                                                                        {if $order->coupon_discount>0}
                                                                                        <tr>
                                                                                            <td style="text-align: right; font-size: 18px; line-height: 150%;">{$btr->email_order_coupon} {$order->coupon_code}:</td>
                                                                                            <td style="text-align: right; font-size: 18px; line-height: 150%; color: #000;">&minus;{$order->coupon_discount}&nbsp;{$currency->sign}</td>
                                                                                        </tr>
                                                                                        {/if}

                                                                                        {if $order->separate_delivery || !$order->separate_delivery && $order->delivery_price > 0}
                                                                                        <tr>
                                                                                            <td style="text-align: right; font-size: 18px; line-height: 150%;">{$delivery->name|escape}:</td>
                                                                                            <td style="text-align: right; font-size: 18px; line-height: 150%; color: #000;">
                                                                                                {if !$order->separate_delivery}{$order->delivery_price|convert:$currency->id}&nbsp;{$currency->sign} {else}{/if}
                                                                                            </td>
                                                                                        </tr>
                                                                                        {/if}

                                                                                        <tr class="es-p5t">
                                                                                            <td style="text-align: right; font-size: 20px; line-height: 150%;"><strong>{$btr->email_order_total}:</strong></td>
                                                                                            <td style="text-align: right; font-size: 20px; line-height: 150%; color: #F36D17;"><strong>{$order->total_price|convert:$currency->id}&nbsp;{$currency->sign}</strong></td>
                                                                                        </tr>
                                                                                        {get_design_block block="email_order_admin_total"}
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

