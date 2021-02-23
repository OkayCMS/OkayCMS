<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\DeliveriesEntity;

class BackendDeliveriesHelper
{
    /**
     * @var DeliveriesEntity
     */
    private $deliveriesEntity;

    /**
     * @var Request
     */
    private $request;
    
    /**
     * @var Config
     */
    private $config;
    
    /**
     * @var Image
     */
    private $imageCore;

    public function __construct(
        EntityFactory $entityFactory,
        Request $request,
        Config $config,
        Image $imageCore
    ) {
        $this->deliveriesEntity = $entityFactory->get(DeliveriesEntity::class);
        $this->request   = $request;
        $this->config    = $config;
        $this->imageCore = $imageCore;
    }

    public function disable(array $ids)
    {
        if (is_array($ids)) {
            $this->deliveriesEntity->update($ids, ['visible'=>0]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable(array $ids)
    {
        if (is_array($ids)) {
            $this->deliveriesEntity->update($ids, ['visible' => 1]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete(array $ids)
    {
        if (is_array($ids)) {
            $this->deliveriesEntity->delete($ids);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortPositions(array $positions)
    {
        $ids = array_keys($positions);
        sort($positions);

        foreach ($positions as $i=>$position) {
            $this->deliveriesEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function buildDeliveriesFilter()
    {
        $filter = [];
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getDeliveriesCount($filter)
    {
        $obj = new \ArrayObject();
        $countFilter = $obj->getArrayCopy();
        unset($countFilter['limit']);
        $count = $this->deliveriesEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function findDeliveries($filter)
    {
        $posts = $this->deliveriesEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    public function prepareAdd($delivery)
    {
        if (empty($delivery->paid)) {
            $delivery->price            = 0;
            $delivery->free_from        = 0;
            $delivery->separate_payment = 0;
        }

        return ExtenderFacade::execute(__METHOD__, $delivery, func_get_args());
    }

    public function add($delivery)
    {
        $insertId = $this->deliveriesEntity->add($delivery);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($delivery)
    {
        if (empty($delivery->paid)) {
            $delivery->price            = 0;
            $delivery->free_from        = 0;
            $delivery->separate_payment = 0;
        }

        return ExtenderFacade::execute(__METHOD__, $delivery, func_get_args());
    }

    public function update($id, $delivery)
    {
        $this->deliveriesEntity->update($id, $delivery);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getDelivery($id)
    {
        $delivery = $this->deliveriesEntity->get($id);
        if (!empty($delivery->id)) {
            $delivery->delivery_payments = $this->deliveriesEntity->getDeliveryPayments($delivery->id);
            $delivery->delivery_settings = $this->deliveriesEntity->getSettings($delivery->id);
        } else {
            $delivery = new \stdClass();
            $delivery->delivery_payments = [];
            $delivery->delivery_settings = [];
        }
        return ExtenderFacade::execute(__METHOD__, $delivery, func_get_args());
    }

    public function updateSettings($deliveryId, array $deliverySettings)
    {
        $this->deliveriesEntity->updateSettings($deliveryId, $deliverySettings);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updateDeliveryPayments($deliveryId, array $payments)
    {
        $this->deliveriesEntity->updateDeliveryPayments($deliveryId, $payments);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function deleteImage($delivery)
    {
        $this->imageCore->deleteImage(
            $delivery->id,
            'image',
            DeliveriesEntity::class,
            $this->config->get('original_deliveries_dir'),
            $this->config->get('resized_deliveries_dir')
        );

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadImage($image, $delivery)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->get('original_deliveries_dir')))) {
            $this->imageCore->deleteImage(
                $delivery->id,
                'image',
                DeliveriesEntity::class,
                $this->config->get('original_deliveries_dir'),
                $this->config->get('resized_deliveries_dir')
            );

            $this->deliveriesEntity->update($delivery->id, ['image'=>$filename]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}