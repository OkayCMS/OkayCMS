<?php


namespace Okay\Modules\OkayCMS\YooKassa\Controllers;


use Okay\Modules\OkayCMS\YooKassa\YooMoneyCallbackHandler;
use Okay\Controllers\AbstractController;

class CallbackController extends AbstractController
{
    public function payOrder(YooMoneyCallbackHandler $handler) {
        $action    = $this->request->get('action');

        if ($action == 'notify') {
            $handler->processNotification();
            return;
        }

        $handler->processReturnUrl();
    }
}