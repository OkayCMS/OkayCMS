<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendPaymentsHelper;
use Okay\Admin\Requests\BackendPaymentsRequest;
use Okay\Entities\PaymentsEntity;

class PaymentMethodsAdmin extends IndexAdmin
{

    public function fetch(BackendPaymentsHelper $backendPaymentsHelper, BackendPaymentsRequest $backendPaymentsRequest)
    {
        // Обработка действий
        if ($this->request->method('post')) {
            $ids = $backendPaymentsRequest->postCheck();

            $positions = $backendPaymentsRequest->postPositions();
            $backendPaymentsHelper->sortPositions($positions);

            if (is_array($ids)) {
                switch ($backendPaymentsRequest->postAction()) {
                    case 'disable': {
                        $backendPaymentsHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendPaymentsHelper->enable($ids);
                        break;
                    }
                    case 'delete': {
                        $backendPaymentsHelper->delete($ids);
                        break;
                    }
                }
            }
        }

        // Отображение
        $filter          = $backendPaymentsHelper->buildPaymentMethodsFilter();
        $paymentMethods  = $backendPaymentsHelper->findPaymentMethods($filter);
        $paymentMethodsCount = $backendPaymentsHelper->getPaymentMethodsCount($filter);

        $this->design->assign('payment_methods', $paymentMethods);
        $this->design->assign('payment_methods_count', $paymentMethodsCount);
        $this->response->setContent($this->design->fetch('payment_methods.tpl'));
    }
    
}