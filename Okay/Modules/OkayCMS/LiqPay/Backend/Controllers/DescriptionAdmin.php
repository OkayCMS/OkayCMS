<?php


namespace Okay\Modules\OkayCMS\LiqPay\Backend\Controllers;


use Okay\Admin\Controllers\IndexAdmin;

class DescriptionAdmin extends IndexAdmin
{
    public function fetch()
    {
        if ($this->request->method('POST')) {
            $this->settings->set('liq_pay_pay_send_receipts_client_after_fiscalization', $this->request->post('liq_pay_pay_send_receipts_client_after_fiscalization', 'integer', 0));
            $this->settings->set('liq_pay_pay_send_receipts_admin_after_fiscalization', $this->request->post('liq_pay_pay_send_receipts_admin_after_fiscalization', 'integer', 0));

            $this->design->assign('message_success', 'saved');
        }

        $this->response->setContent($this->design->fetch('description.tpl'));
    }
}