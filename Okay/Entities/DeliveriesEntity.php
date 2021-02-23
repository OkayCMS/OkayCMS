<?php


namespace Okay\Entities;


use Okay\Core\Entity\Entity;
use Okay\Core\Image;
use Okay\Core\Modules\Extender\ExtenderFacade;

class DeliveriesEntity extends Entity
{

    protected static $fields = [
        'id',
        'free_from',
        'price',
        'enabled',
        'position',
        'separate_payment',
        'paid',
        'image',
        'settings',
        'module_id',
        'hide_front_delivery_price',
    ];

    protected static $langFields = [
        'name',
        'description'
    ];

    protected static $defaultOrderFields = [
        'position'
    ];

    protected static $table = '__deliveries';
    protected static $langObject = 'delivery';
    protected static $langTable = 'deliveries';
    protected static $tableAlias = 'd';

    public function add($delivery)
    {
        if (empty($delivery->price)) {
            $delivery->price = 0.00;
        }

        if (empty($delivery->free_from)) {
            $delivery->free_from = 0.00;
        }

        return parent::add($delivery);
    }

    public function delete($ids)
    {
        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);
        
        $ids = (array)$ids;
        
        // Удаляем связь доставки с методоми оплаты
        $delete = $this->queryFactory->newDelete();
        $delete->from('__delivery_payment')
            ->where('delivery_id IN (:delivery_id)')
            ->bindValue('delivery_id', $ids);
        
        $this->db->query($delete);

        foreach ($ids as $id) {
            $imageCore->deleteImage(
                $id,
                'image',
                self::class,
                $this->config->original_deliveries_dir,
                $this->config->resized_deliveries_dir
            );
        }
        
        return parent::delete($ids);
    }

    /*Выборка доступных способов оплаты для данного способа доставки*/
    public function getDeliveryPayments($deliveryId)
    {
        $select = $this->queryFactory->newSelect();
        $select->from('__delivery_payment')
            ->cols(['payment_method_id'])
            ->where('delivery_id = :delivery_id')
            ->bindValue('delivery_id', $deliveryId);
        
        $this->db->query($select);

        $results = $this->db->results('payment_method_id');
        return ExtenderFacade::execute([static::class, __FUNCTION__], $results, func_get_args());
    }

    /*Обновление способов оплаты у данного способа доставки*/
    public function updateDeliveryPayments($deliveryId, array $paymentMethodsIds)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__delivery_payment')
            ->where('delivery_id = :delivery_id')
            ->bindValue('delivery_id', $deliveryId);
        
        $this->db->query($delete);
        
        if (is_array($paymentMethodsIds)) {
            foreach($paymentMethodsIds as $pId) {
                $insert = $this->queryFactory->newInsert();
                $insert->into('__delivery_payment')
                    ->cols([
                        'delivery_id' => $deliveryId,
                        'payment_method_id' => $pId,
                    ]);
                $this->db->query($insert);
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    public function getSettings($deliveryId)
    {
        $select = $this->queryFactory->newSelect();
        $select->from(self::getTable())
            ->cols(['settings'])
            ->where('id=:id')
            ->bindValue('id', (int)$deliveryId);

        $this->db->query($select);
        $result = $this->db->result('settings');
        $settings = [];
        if (!empty($result)) {
            $settings = unserialize($result);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $settings, func_get_args());
    }

    public function updateSettings($deliveryId, array $settings)
    {
        $settings = serialize($settings);
        $update = $this->queryFactory->newUpdate();
        $update->table(self::getTable())
            ->cols(['settings' => $settings])
            ->where('id=:id')
            ->bindValue('id', (int)$deliveryId);
        $this->db->query($update);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $deliveryId, func_get_args());
    }
    
}
