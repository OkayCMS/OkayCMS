<!DOCTYPE html>
{*Печать заказа*}
{$wrapper='' scope=global}
<html>
<head>
    <title>{$btr->general_order_number|escape} {$order->id}</title>
    {* Метатеги *}
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="description" content="{$meta_description|escape}" />
    <style>
    body {
        width: 1000px;
        height: 1414px;
        /* to centre page on screen*/
        margin-left: auto;
        margin-right: auto;
        border: 1px solid #8c8c8c;
        line-height: 1.2;
        font-family: Trebuchet MS, times, arial, sans-serif;        
        font-size: 10pt;
        color: black;
        background-color: white;         
    }
    
    div#header{
        margin-left: 25px;
        margin-top: 20px;
        height: 90px;
        width: 950px;
    }
    div#company{
        margin-left: 25px;
        margin-top: 25px;
        height: 80px;
        width: 950px;
        font-size: 18px;
        line-height: 1.3;
    }
    div#customer{
        margin-left: 25px;
        min-height: 170px;
        width: 950px;
        margin-top: 10px;
    }
    div#customer table{
        margin-bottom: 20px;
        font-size: 18px;
        line-height: 1.5;
    }
    div#customer td{
        padding-top: 2px;
        padding-bottom: 2px;
    }
    div#customer .small{
        font-size: 17px;
    }
    div#purchases{
        margin-left: 25px;
        margin-top: 30px;
        margin-bottom: 20px;
        min-height: 100px;
        width: 950px;
        
    }
    div#purchases table{
        width: 950px;
        border-collapse:collapse
    }
    div#purchases table th
    {
        font-weight: normal;
        text-align: left;
        font-size: 20px;
    }
    div#purchases td, div#purchases th
    {
        font-size: 17px;
        padding-top: 10px;
        padding-bottom: 10px;
        margin: 0;        
    }
    div#purchases td
    {
        border-top: 1px solid #8c8c8c;     
    }
 
    div#total{
        float: right;
        margin-right: 25px;
        height: 100px;
        width: 500px;
        text-align: right;
        margin-top: 50px;
    }
    div#total table{
        width: 500px;
        float: right;
        border-collapse:collapse
    }
    div#total th
    {
        font-weight: normal;
        text-align: left;
        font-size: 20px;
        border-top: 1px solid #8c8c8c;     
    }
    div#total td
    {
        text-align: right;
        border-top: 1px solid #8c8c8c;     
        font-size: 17px;
        padding-top: 10px;
        padding-bottom: 10px;
        margin: 0;        
    }
    div#total .total
    {
        font-size: 30px;
    }
    td{
        vertical-align:top;
    }
    .td_pr_1{
        padding-right: 10px;
    }
    .td_pr_0{
        padding-right: 0;
    }
    h1{
        margin: 0;
        font-weight: normal;
        font-size: 40px;
    }
    h2{
        margin: 0;
        font-weight: normal;
        font-size: 24px;
    }
    p
    {
        margin: 0;
        font-size: 18px;
    }
    div#purchases td.align_right, div#purchases th.align_right
    {
        text-align: right;
    }
        div#purchases td.align_center, div#purchases th.align_center
    {
        text-align: center;
    }
    .custom_short_block {
        
    }
    .design_block_parent_element {
        position: relative;
        border: 1px solid transparent;
    }
    .design_block_parent_element.focus {
        border: 1px solid red;
    }
    .fn_design_block_name {
        position: absolute;
        top: -9px;
        left: 15px;
        background-color: #fff;
        padding: 0 10px;
        box-sizing: border-box;
        font-size: 14px;
        line-height: 14px;
        font-weight: 700;
        color: red;
        cursor: pointer;
        z-index: 1000;
    }
    .fn_design_block_name:hover {
        z-index: 1100;
    }
    </style>
    <script src="design/js/jquery/jquery.js"></script>
    <script>
        $(function(){
            $('.fn_design_block_name').parent().addClass('design_block_parent_element');
            $('.fn_design_block_name').on('mouseover', function () {
                $(this).parent().addClass('focus');
            });
            $('.fn_design_block_name').on('mouseout', function () {
                $(this).parent().removeClass('focus');
            });
        });
    </script>
</head>

<body _onload="window.print();">

{* {var_dump($settings)} *}

<div id="company">
    <h2>{$settings->site_name|escape}</h2>
    <p>{$rootUrl}</p>
    {if $settings->site_phones}
        {foreach $settings->site_phones as $phone}
            {if $phone@first}
                <p class="phone">{$phone|escape}</p>
            {/if}
        {/foreach}
    {/if}
</div>

<div id="header">
    <h1>{$btr->general_order_number|escape} {$order->id}</h1>
    <p>{$btr->order_print_from|escape} {$order->date|date}</p>
</div>

{*Информация о клиенте*}
<div id="customer">
    <table>
        <tr>
            <td class="td_pr_1">{$btr->order_print_recipient|escape}:</td>
            <td class="small">{$order->name|escape} {$order->last_name|escape}</td>
        </tr>      
        <tr>
            <td class="td_pr_1">{$btr->order_print_phone|escape}:</td>
            <td class="small">{$order->phone|phone}</td>
        </tr>    
        <tr>
            <td class="td_pr_1">{$btr->order_print_email|escape}:</td>
            <td class="small">{$order->email|escape}</td>
        </tr>
        {if $order->comment}
            <tr>
                <td class="td_pr_1">{$btr->order_print_comment|escape}:</td>
                <td class="small"><i>{$order->comment|escape|nl2br}</i></td>
            </tr>
        {/if}
        {get_design_block block="order_print_user_info"}
    </table>
    

    {if $order->note}
    <table>        
        <tr>
            <td><h2><i>{$btr->order_note|escape}</i></h2><i>{$order->note|escape|nl2br}</i></td>
        </tr>
    </table>
    {/if}

</div>

{*Информация о заказе*}
<div id="purchases">
    <table>
        <tr>
            <th class="td_pr_1">{$btr->order_print_product|escape}</th>
            <th class="td_pr_1"  width="100px">{$btr->general_sku|escape}</th>
            <th class="td_pr_1" width="110px">{$btr->general_price|escape}</th>
            <th class="align_center td_pr_1" width="80px">{$btr->general_amt|escape}</th>
            <th class="align_right td_pr_1" width="110px">{$btr->order_print_total|escape}</th>
        </tr>
        {foreach $purchases as $purchase}
        <tr>
            <td class="td_pr_1">
                <div class="view_purchase">
                    {if $purchase->product->name}
                        {$purchase->product->name|escape}
                    {else}
                        {$purchase->product_name|escape}
                    {/if}
                    {if $purchase->variant->name}
                        {$purchase->variant->name|escape}
                    {else}
                        {$purchase->variant_name|escape}
                    {/if}
                    {if $purchase->sku} 
                        ({$btr->general_sku|escape} 
                        {$purchase->sku|escape})
                    {/if}
                    {get_design_block block="order_print_purchase_name" vars=['purchase'=>$purchase]}
                    {if $purchase->discounts}
                        {foreach $purchase->discounts as $discount}
                            <br>
                            {$discount->name|escape}
                            {if $discount->description}
                                ({$discount->description|escape})
                            {/if}
                            {$discount->value|escape}{strip}
                            {if $discount->type == "absolute"}
                                &nbsp;{$currency->code|escape}
                            {else}
                                %
                            {/if}
                            {/strip}
                        {/foreach}
                    {/if}
                </div>
            </td>
            <td class="td_pr_1"  width="100px">
                {if $purchase->sku} 
                {$purchase->sku|escape}
                {else}
                ---
                {/if}
            </td>
            <td class="td_pr_1"  width="110px">
                <span class=view_purchase>{$purchase->price|escape}</span> {$currency->sign|escape}
            </td>
            <td class="align_center td_pr_1" width="80px">            
                <span class=view_purchase>
                    {$purchase->amount|escape} {if $purchase->units}{$purchase->units|escape}{else}{$settings->units|escape}{/if}
                </span>
            </td>
            <td class="align_right" width="110px">
                <span class=view_purchase>{$purchase->price*$purchase->amount}</span> {$currency->sign|escape}
            </td>
        </tr>
        {/foreach}
        {* Если стоимость доставки входит в сумму заказа *}
        {if $order->delivery_price>0}
        <tr>
            <td colspan=3>{$delivery->name|escape}{if $order->separate_delivery} ({$btr->general_paid_separately|escape}){/if}</td>
            <td class="align_right">{$order->delivery_price|convert:$currency->id}&nbsp;{$currency->sign|escape}</td>
        </tr>
        {/if}
        {get_design_block block="order_print_purchases_list_custom"}
    </table>
</div>

{$block = {get_design_block block="order_print_custom_block"}}

{if $block}
    <div class="custom_short_block">
        {$block}
    </div>
{/if}

<div id="total">
    <table>
        {foreach $discounts as $discount}
        <tr>
            <th>
                {$discount->name|escape}
                {if $discount->description}
                    ({$discount->description|escape})
                {/if}
            </th>
            <td>
                {$discount->value|escape}{strip}
                    {if $discount->type == "absolute"}
                        &nbsp;{$currency->code|escape}
                    {else}
                        %
                    {/if}
                {/strip}
            </td>
        </tr>
        {/foreach}
        <tr>
            <th>{$btr->general_total|escape}</th>
            <td class="total">{$order->total_price|convert:$currency->id}&nbsp;{$currency->sign|escape}</td>
        </tr>
        {if $payment_method}
        <tr>
            <td colspan="2">{$btr->order_print_payment|escape} {$payment_method->name|escape}</td>
        </tr>
        <tr>
            <th>{$btr->order_to_pay|escape}</th>
            <td class="total">{$order->total_price|convert:$payment_method->currency_id}&nbsp;{$payment_currency->sign|escape}</td>
        </tr>
        {/if}
        {get_design_block block="order_print_total_price_custom"}
    </table>
</div>
</body>
</html>

