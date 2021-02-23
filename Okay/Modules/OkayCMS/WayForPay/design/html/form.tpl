<form method="post" action="https://secure.wayforpay.com/pay" accept-charset="utf-8">
    <input type="hidden" name="merchantAccount"     value="{$merchantAccount|escape}">
    <input type="hidden" name="orderReference"      value="{$orderReference|escape}">
    <input type="hidden" name="orderDate"           value="{$orderDate|escape}">
    <input type="hidden" name="merchantAuthType"    value="{$merchantAuthType|escape}">
    <input type="hidden" name="merchantDomainName"  value="{$merchantDomainName|escape}">
    <input type="hidden" name="merchantTransactionSecureType" value="{$merchantTransactionSecureType|escape}">
    <input type="hidden" name="currency"            value="{$currency|escape}">
    <input type="hidden" name="amount"              value="{$amount|escape}">
    {foreach $productName as $pName}
        <input type="hidden" name="productName[]"   value="{$pName|escape}">
    {/foreach}
    {foreach $productPrice as $pPrice}
        <input type="hidden" name="productPrice[]"  value="{$pPrice|escape}">
    {/foreach}
    {foreach $productCount as $pCount}
        <input type="hidden" name="productCount[]"  value="{$pCount|escape}">
    {/foreach}
    <input type="hidden" name="returnUrl"           value="{$returnUrl|escape}">
    <input type="hidden" name="serviceUrl"          value="{$serviceUrl|escape}">
    <input type="hidden" name="merchantSignature"   value="{$merchantSignature|escape}">
    <input type="hidden" name="clientFirstName"     value="{$clientFirstName|escape}">
    <input type="hidden" name="clientLastName"      value="{$clientLastName|escape}">
    <input type="hidden" name="clientEmail"         value="{$clientEmail|escape}">
    <input type="hidden" name="clientPhone"         value="{$clientPhone|escape}">
    {*<input type="hidden" name="clientCity"          value="{$clientCity|escape}">*}
    <input type="hidden" name="clientAddress"       value="{$clientAddress|escape}">
    <input type="hidden" name="language"            value="{$language|escape}">
    <input type="submit" class="button"             value="{$lang->form_to_pay}">
</form>