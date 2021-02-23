<style type="text/css">
    .yamoney_kassa_buttons {
        display: flex;
        margin-bottom: 20px;
    }

    .ya_kassa_installments_button_container {
        margin-right: 20px;
    }

    .yamoney-pay-button {
        position: relative;
        height: 60px;
        width: 155px;
        border-radius: 4px;
        font-family: YandexSansTextApp-Regular, Arial, Helvetica, sans-serif;
        text-align: center;
    }

    .yamoney-pay-button button {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 4px;
        transition: 0.1s ease-out 0s;
        color: #000;
        box-sizing: border-box;
        outline: 0;
        border: 0;
        background: #FFDB4D;
        cursor: pointer;
        font-size: 12px;
    }

    .yamoney-pay-button button:hover, .yamoney-pay-button button:active {
        background: #f2c200;
    }

    .yamoney-pay-button button span {
        display: block;
        font-size: 20px;
        line-height: 20px;
    }

    .yamoney-pay-button_type_fly {
        box-shadow: 0 1px 0 0 rgba(0, 0, 0, 0.12), 0 5px 10px -3px rgba(0, 0, 0, 0.3);
    }

    .ya_checkout_button {
        cursor: pointer;
    }

    .ya_checkout_button:hover {
        background-color: #abff87;
    }
</style>
<div class="row">
    <form class="col-lg-7" method="POST" action="{url_generator route="OkayCMS.YooKassa.SendPaymentRequest" absolute=1}">
        <input type="hidden" name="payment_submit"/>
        <input type="hidden" name="payment_type" id="pm_yandex_money_payment_type" value="{$payment_type|escape}"/>
        <input type="hidden" name="amount" value="{$amount|escape}"/>
        <input type="hidden" name="order_id" value="{$order->id|escape}"/>

        {if $payment_settings->yandex_api_paymode === 'kassa'}
            <div class="yamoney_kassa_buttons">

                {if $payment_settings->yandex_show_installments_button}
                    <div class="ya_kassa_installments_button_container"></div>
                {/if}

                {if $payment_settings->yandex_show_pay_with_yandex_button}
                    <div class="yamoney-pay-button {if !$payment_settings->yandex_show_installments_button} yamoney-pay-button_type_fly{/if}">
                        <button type="submit"><span>Заплатить</span>через Яндекс</button>
                    </div>
                {else}
                    <input type="submit" name="submit-button" value="{$button_text}" class="btn_order">
                {/if}

            </div>
        {else}
            <input type="submit" name="submit-button" value="{$button_text}" class="btn_order">
        {/if}
    </form>
</div>

{if $payment_settings->yandex_api_paymode === 'kassa' && $settings_pay->yandex_show_installments_button}
{literal}
    <script src="https://static.yoomoney.ru/checkout-credit-ui/v1/index.js"></script>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            const yaShopId = {/literal}{$settings_pay['yandex_api_shopid']} {literal};
            const yaAmount = {/literal}{$amount}{literal};

            function createCheckoutCreditUI() {
                if (!CheckoutCreditUI) {
                    setTimeout(createCheckoutCreditUI, 200);
                }
                const yoomoneyСheckoutCreditUI = CheckoutCreditUI({
                    shopId: yaShopId,
                    sum: yaAmount
                });
                const checkoutCreditButton = yoomoneyСheckoutCreditUI({
                    type: 'button',
                    domSelector: '.ya_kassa_installments_button_container'
                });
                checkoutCreditButton.on('click', function () {
                    jQuery('#pm_yandex_money_payment_type').val('installments');
                });
            };
            setTimeout(createCheckoutCreditUI, 200);
        });
    </script>
{/literal}
{/if}
