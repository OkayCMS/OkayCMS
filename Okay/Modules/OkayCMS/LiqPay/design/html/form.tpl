<form method="POST" action="https://www.liqpay.ua/api/3/checkout" accept-charset="utf-8">
    <input type="hidden" name="data" value="{$data|escape}"/>
    <input type="hidden" name="signature" value="{$sign|escape}"/>
    <input type="submit" class="button" value="{$lang->form_to_pay}">
</form>
