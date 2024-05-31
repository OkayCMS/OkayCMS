{if $payment_method->name === 'RozetkaPay' && $order->paid}
    <div class="box_btn_heading" style="margin-left: 10px !important;">
        <a class="btn btn_small btn-info" href="/backend/index.php?controller=OkayCMS.RozetkaPay.RefundAdmin@execute?&order={$order->id}">
            <span>{$btr->rozetka_pay_refund|escape}</span>
        </a>
    </div>
{/if}