<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendDeliveriesHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendDeliveriesRequest;
use Okay\Core\Modules\Modules;
use Okay\Entities\PaymentsEntity;

class DeliveryAdmin extends IndexAdmin
{
    
    public function fetch(
        PaymentsEntity           $paymentsEntity,
        BackendDeliveriesHelper  $backendDeliveriesHelper,
        BackendDeliveriesRequest $backendDeliveriesRequest,
        BackendValidateHelper    $backendValidateHelper,
        Modules                  $modules
    ) {
        /*Принимаем данные о способе доставки*/
        if ($this->request->method('post')) {
            $delivery         = $backendDeliveriesRequest->postDelivery();

            if ($error = $backendValidateHelper->getDeliveriesValidateError($delivery)) {
                $this->design->assign('message_error', $error);
            } else {
                /*Добавление/Обновление способа доставки*/
                if (empty($delivery->id)) {
                    $preparedDelivery = $backendDeliveriesHelper->prepareAdd($delivery);
                    $delivery->id     = $backendDeliveriesHelper->add($preparedDelivery);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($delivery->id);
                } else {
                    $preparedDelivery = $backendDeliveriesHelper->prepareUpdate($delivery);
                    $backendDeliveriesHelper->update($preparedDelivery->id, $delivery);

                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                if ($backendDeliveriesRequest->postDeleteImage()) {
                    $backendDeliveriesHelper->deleteImage($delivery);
                }

                if ($image = $backendDeliveriesRequest->fileImage()) {
                    $backendDeliveriesHelper->uploadImage($image, $delivery);
                }

                $deliverySettings = $backendDeliveriesRequest->postSettings();
                $deliveryPayments = $backendDeliveriesRequest->postDeliveryPayments();

                $backendDeliveriesHelper->updateSettings($delivery->id, $deliverySettings);
                $backendDeliveriesHelper->updateDeliveryPayments($delivery->id, $deliveryPayments);

                $this->postRedirectGet->redirect();
            }
        } else {
            $deliveryId = $this->request->get('id', 'integer');
            $delivery   = $backendDeliveriesHelper->getDelivery($deliveryId);
        }

        $paymentMethods  = $paymentsEntity->find();
        $deliveryModules = $modules->getDeliveryModules($this->manager->lang);

        $this->design->assign('payment_methods',  $paymentMethods);
        $this->design->assign('delivery',         $delivery);
        $this->design->assign('delivery_modules', $deliveryModules);

        $this->response->setContent($this->design->fetch('delivery.tpl'));
    }
    
}
