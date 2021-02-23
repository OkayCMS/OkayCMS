<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendOrderSettingsHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Admin\Requests\BackendOrderSettingsRequest;
use Okay\Core\BackendPostRedirectGet;
use Okay\Core\BackendTranslations;

class OrderSettingsAdmin extends IndexAdmin
{

    public function fetch(
        BackendOrdersHelper         $backendOrdersHelper,
        BackendOrderSettingsRequest $orderSettingsRequest,
        BackendOrderSettingsHelper  $backendOrderSettingsHelper,
        BackendPostRedirectGet      $backendPostRedirectGet,
        BackendTranslations         $backendTranslations
    ){
        /*Статусы заказов*/
        if ($this->request->post('statuses')) {
            if ($positions = $orderSettingsRequest->postPositions()) {
                $backendOrderSettingsHelper->sortStatusPositions($positions);
            }

            $statuses = $orderSettingsRequest->postStatuses();
            $backendOrderSettingsHelper->updateStatuses($statuses);

            $idsToDelete = $orderSettingsRequest->postCheck();

            $ordersStatuses = $backendOrdersHelper->findStatuses();
            $statusesNotToDelete = [];
            if (!empty($idsToDelete) && ($idsNotToDelete = $backendOrderSettingsHelper->statusCanBeDeleted($idsToDelete)) !== false) {
                
                foreach ($idsToDelete as $idToDelete) {
                    if (in_array($idToDelete, $idsNotToDelete)) {
                        $statusesNotToDelete[] = $ordersStatuses[$idToDelete]->name;
                    } else {
                        $backendOrderSettingsHelper->deleteStatuses($idToDelete);
                    }
                }
            }
            
            if (!empty($statusesNotToDelete)) {
                $backendPostRedirectGet->storeMessageError(
                    $backendTranslations->getTranslation('error_delete_statuses')
                    . ' '
                    . implode(', ', $statusesNotToDelete)
                );
            }
            
            $backendPostRedirectGet->redirect();
        }
        

        /*Метки заказов*/
        if ($this->request->post('labels')) {
            if ($positions = $orderSettingsRequest->postPositions()) {
                $backendOrderSettingsHelper->sortLabelPositions($positions);
            }

            $labels = $orderSettingsRequest->postLabels();
            $backendOrderSettingsHelper->updateLabels($labels);

            $idsToDelete = $orderSettingsRequest->postCheck();
            if (!empty($idsToDelete)) {
                $backendOrderSettingsHelper->deleteLabels($idsToDelete);
            }
            $backendPostRedirectGet->redirect();
        }
        // Отображение
        $ordersStatuses = $backendOrdersHelper->findStatuses();
        $this->design->assign('orders_statuses', $ordersStatuses);
        
        $labels = $backendOrdersHelper->findLabels();
        $this->design->assign('labels', $labels);

        $this->response->setContent($this->design->fetch('order_settings.tpl'));
    }

}

