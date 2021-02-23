<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendOrderHistoryHelper;
use Okay\Admin\Helpers\BackendOrdersHelper;
use Okay\Entities\OrderLabelsEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\UsersEntity;

class OrdersAdmin extends IndexAdmin
{
    
    public function fetch(
        OrderLabelsEntity   $orderLabelsEntity,
        BackendOrdersHelper $backendOrdersHelper,
        BackendOrderHistoryHelper $backendOrderHistoryHelper,
        OrdersEntity $ordersEntity,
        UsersEntity $usersEntity
    ) {
        //> Обработка действий
        if ($this->request->method('post')) {
            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'delete': {
                        $backendOrdersHelper->delete($ids);
                        break;
                    }
                    case 'change_status': {
                        $backendOrdersHelper->changeStatus($ids);
                        break;
                    }
                    case 'set_label': {
                        $backendOrdersHelper->setLabel($ids);
                        break;
                    }
                    case 'unset_label': {
                        $backendOrdersHelper->unsetLabel($ids);
                        break;
                    }
                }
            }
        }

        $filter      = $backendOrdersHelper->buildFilter();
        $orders      = $backendOrdersHelper->findOrders($filter);
        $orders      = $backendOrdersHelper->attachLabels($orders);
        $orders      = $backendOrdersHelper->attachLastUpdate($orders);
        $allStatuses = $backendOrdersHelper->findStatuses();
        $ordersCount = $backendOrdersHelper->count($filter);

        $countStatusesFilter = $backendOrdersHelper->buildCountStatusesFilter($filter);
        
        // Считаем количество заказов по всем фильтрам, кроме статуса
        $countOrdersForStatuses = $ordersEntity->count($countStatusesFilter);
        if ($countOrdersForStatuses > 0) {
            if (empty($filter['from_date']) || empty($filter['to_date'])) {
                $dates = $ordersEntity->cols([
                    'MIN(o.date) AS min',
                    'MAX(o.date) AS max'
                ])->findOrdersDates($countStatusesFilter);

                if (empty($filter['from_date'])) {
                    $this->design->assign('orders_from_date', $dates->min);
                }
                
                if (empty($filter['to_date'])) {
                    $this->design->assign('orders_to_date', $dates->max);
                }
            }
            
            $countOrdersByStatuses = $ordersEntity->countOrdersByStatuses($countStatusesFilter);
            $this->design->assign('count_orders_by_statuses', $countOrdersByStatuses);
        }
        
        $this->design->assign('count_orders_for_statuses', $countOrdersForStatuses);
        
        if (isset($filter['keyword'])) {
            $this->design->assign('keyword', $filter['keyword']);
        }

        if (isset($filter['status_id'])) {
            $this->design->assign('status_id', $filter['status_id']);
        }

        if (isset($filter['label'])) {
            $this->design->assign('label_id', $filter['label']);
        }

        if (isset($filter['from_date'])) {
            $this->design->assign('from_date', $filter['from_date']);
        }

        if (isset($filter['to_date'])) {
            $this->design->assign('to_date', $filter['to_date']);
        }

        if (!empty($filter['user_id'])) {
            $this->design->assign('order_user', $usersEntity->findOne(['id' => $filter['user_id']]));
        }

        $this->design->assign('pages_count',   ceil($ordersCount/$filter['limit']));
        $this->design->assign('current_page',  $filter['page']);
        $this->design->assign('orders_count',  $ordersCount);
        $this->design->assign('orders',        $orders);
        $this->design->assign('all_status',    $allStatuses);
        $this->design->assign('orders_status', $allStatuses);

        if (!empty($orders)) {
            $ordersHistory = $backendOrderHistoryHelper->findOrdersHistory(array_keys($orders));
            $this->design->assign('orders_history', $ordersHistory);
        }
        
        // Метки заказов
        $labels = $orderLabelsEntity->find();
        $this->design->assign('labels', $labels);

        $this->response->setContent($this->design->fetch('orders.tpl'));
    }
    
}
