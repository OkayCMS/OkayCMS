<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendDeliveriesHelper;
use Okay\Admin\Requests\BackendDeliveriesRequest;

class DeliveriesAdmin extends IndexAdmin
{
    
    public function fetch(BackendDeliveriesHelper $backendDeliveriesHelper, BackendDeliveriesRequest $backendDeliveriesRequest)
    {
        // Обработка действий
        if ($this->request->method('post')) {
            $ids = $backendDeliveriesRequest->postCheck();

            $positions = $backendDeliveriesRequest->postPositions();
            $backendDeliveriesHelper->sortPositions($positions);
            
            if (is_array($ids)) {
                switch ($backendDeliveriesRequest->postAction()) {
                    case 'disable': {
                        $backendDeliveriesHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendDeliveriesHelper->enable($ids);
                        break;
                    }
                    case 'delete': {
                        $backendDeliveriesHelper->delete($ids);
                        break;
                    }
                }
            }
        }
        
        // Отображение
        $filter          = $backendDeliveriesHelper->buildDeliveriesFilter();
        $deliveries      = $backendDeliveriesHelper->findDeliveries($filter);
        $deliveriesCount = $backendDeliveriesHelper->getDeliveriesCount($filter);
        
        $this->design->assign('deliveries', $deliveries);
        $this->design->assign('deliveries_count', $deliveriesCount);
        $this->response->setContent($this->design->fetch('deliveries.tpl'));
    }
    
}
