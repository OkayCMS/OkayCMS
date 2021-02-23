<?php


namespace Okay\Modules\OkayCMS\Integration1C\Backend\Controllers;


use Okay\Core\EntityFactory;
use Okay\Entities\OrderStatusEntity;
use Okay\Admin\Controllers\IndexAdmin;

class Description1CAdmin extends IndexAdmin
{
    public function fetch(EntityFactory $entityFactory)
    {
        $orderStatusEntity = $entityFactory->get(OrderStatusEntity::class);

        if ($this->request->method('post') && $this->request->post('status_1c')) {
            $statuses = $this->request->post('status_1c');
            foreach($statuses as $id => $status) {
                $orderStatusEntity->update($id, ['status_1c' => $status]);
            }
            
            $this->settings->set('integration1cBrandOptionName', $this->request->post('integration1cBrandOptionName'));
            $this->settings->set('integration1cGuidPriceFrom1C', $this->request->post('integration1cGuidPriceFrom1C'));
            $this->settings->set('integration1cGuidComparePriceFrom1C', $this->request->post('integration1cGuidComparePriceFrom1C'));
            
            $this->settings->set('integration1cFullUpdate', $this->request->post('integration1cFullUpdate', 'int'));
            $this->settings->set('integration1cOnlyEnabledCurrencies', $this->request->post('integration1cOnlyEnabledCurrencies', 'int'));
            $this->settings->set('integration1cStockFrom1c', $this->request->post('integration1cStockFrom1c', 'int'));
            $this->settings->set('integration1cImportProductsOnly', $this->request->post('integration1cImportProductsOnly', 'int'));
        }

        $ordersStatuses = $orderStatusEntity->find();
        $this->design->assign('orders_statuses', $ordersStatuses);

        $this->response->setContent($this->design->fetch('description.tpl'));
    }
}