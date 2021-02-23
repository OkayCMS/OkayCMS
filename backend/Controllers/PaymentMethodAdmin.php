<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendPaymentsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendPaymentsRequest;
use Okay\Core\Modules\Modules;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;

class PaymentMethodAdmin extends IndexAdmin
{

    public function fetch(
        BackendPaymentsHelper  $backendPaymentsHelper,
        BackendPaymentsRequest $backendPaymentsRequest,
        BackendValidateHelper  $backendValidateHelper,
        CurrenciesEntity       $currenciesEntity,
        DeliveriesEntity       $deliveriesEntity,
        Modules                $modules
    ) {
        /*Принимаем данные о способе доставки*/
        if ($this->request->method('post')) {

            $paymentMethod     = $backendPaymentsRequest->postPayment();
            $paymentSettings   = $backendPaymentsRequest->postSettings();
            $paymentDeliveries = $backendPaymentsRequest->postPaymentDeliveries();

            if ($error = $backendValidateHelper->getPaymentValidateError($paymentMethod)) {
                $this->design->assign('message_error', $error);
            } else {
                /*Добавление/Обновление способа доставки*/
                if (empty($paymentMethod->id)) {
                    $preparedPayment = $backendPaymentsHelper->prepareAdd($paymentMethod);
                    $paymentMethod->id     = $backendPaymentsHelper->add($preparedPayment);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($paymentMethod->id);
                } else {
                    $preparedPayment = $backendPaymentsHelper->prepareUpdate($paymentMethod);
                    $backendPaymentsHelper->update($preparedPayment->id, $paymentMethod);

                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                if ($backendPaymentsRequest->postDeleteImage()) {
                    $backendPaymentsHelper->deleteImage($paymentMethod);
                }

                if ($image = $backendPaymentsRequest->fileImage()) {
                    $backendPaymentsHelper->uploadImage($image, $paymentMethod);
                }

                $backendPaymentsHelper->updateSettings($paymentMethod->id, $paymentSettings);
                $backendPaymentsHelper->updatePaymentDeliveries($paymentMethod->id, $paymentDeliveries);

                $this->postRedirectGet->redirect();
            }
        }

        $paymentMethodId = $this->request->get('id', 'integer');
        $paymentMethod   = $backendPaymentsHelper->getPaymentMethod($paymentMethodId);
        $deliveries      = $deliveriesEntity->find();
        $paymentModules  = $modules->getPaymentModules($this->manager->lang);
        $currencies = $currenciesEntity->find();

        $this->design->assign('deliveries',      $deliveries);
        $this->design->assign('payment_method',  $paymentMethod);
        $this->design->assign('payment_modules', $paymentModules);
        $this->design->assign('currencies',      $currencies);

        $this->response->setContent($this->design->fetch('payment_method.tpl'));
    }
}
