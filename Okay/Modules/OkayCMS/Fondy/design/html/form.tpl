<form id="fondy_to_checkout" method="POST" action="https://api.fondy.eu/api/checkout/redirect/">
    {foreach $form_data as $key => $value}
        <input type="hidden" name="{$key|escape}" value="{$value|escape}">
    {/foreach}
    <input type="submit" class="button"  value="{$lang->form_to_pay}">
</form>
    