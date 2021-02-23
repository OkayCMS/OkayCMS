<?php


namespace Okay\Entities;


use Okay\Core\Image;
use Okay\Core\Entity\Entity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class PaymentsEntity extends Entity
{

    protected static $fields = [
        'id',
        'module',
        'currency_id',
        'settings',
        'auto_submit',
        'enabled',
        'position',
        'image',
    ];

    protected static $langFields = [
        'name',
        'description'
    ];

    protected static $searchFields = [
        'name',
        'meta_keywords',
    ];

    protected static $defaultOrderFields = [
        'position'
    ];

    protected static $table = '__payment_methods';
    protected static $langObject = 'payment';
    protected static $langTable = 'payment_methods';
    protected static $tableAlias = 'p';

    protected function filter__delivery_id($deliveryId)
    {
        $this->select->where('id IN (SELECT payment_method_id FROM __delivery_payment dp WHERE dp.delivery_id=:delivery_id)')
            ->bindValue('delivery_id', (int)$deliveryId);
    }
    
    public function delete($ids)
    {
        /** @var Image $imageCore */
        $imageCore = $this->serviceLocator->getService(Image::class);

        $ids = (array)$ids;

        // Удаляем связь доставки с методоми оплаты
        $delete = $this->queryFactory->newDelete();
        $delete->from('__delivery_payment')
            ->where('payment_method_id IN (:payment_method_id)')
            ->bindValue('payment_method_id', $ids);

        $this->db->query($delete);

        foreach ($ids as $id) {
            $imageCore->deleteImage(
                $id,
                'image',
                self::class,
                $this->config->original_payments_dir,
                $this->config->resized_payments_dir
            );
        }

        return parent::delete($ids);
    }

    /*Выборка настроек способа оплаты*/
    public function getPaymentSettings($methodId)
    {
        $select = $this->queryFactory->newSelect();
        $select->from('__payment_methods')
            ->cols(['settings'])
            ->where('id=:id')
            ->bindValue('id', (int)$methodId);
        
        $this->db->query($select);
        $result = $this->db->result('settings');
        $settings = [];
        if (!empty($result)) {
            $settings = unserialize($result);
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], $settings, func_get_args());
    }

    /*Обновление настроек способа оплаты*/
    public function updatePaymentSettings($methodId, array $settings)
    {
        $settings = serialize($settings);
        $update = $this->queryFactory->newUpdate();
        $update->table('__payment_methods')
            ->cols(['settings' => $settings])
            ->where('id=:id')
            ->bindValue('id', (int)$methodId);
        $this->db->query($update);

        return ExtenderFacade::execute([static::class, __FUNCTION__], $methodId, func_get_args());
    }

    /*Выборка доступных способов оплаты для данного способа доставки*/
    public function getPaymentDeliveries($paymentId)
    {
        $select = $this->queryFactory->newSelect();
        $select->from('__delivery_payment')
            ->cols(['delivery_id'])
            ->where('payment_method_id = :payment_method_id')
            ->bindValue('payment_method_id', $paymentId);

        $this->db->query($select);
        $result = $this->db->results('delivery_id');
        return ExtenderFacade::execute([static::class, __FUNCTION__], $result, func_get_args());
    }

    /*Обновление способов оплаты у данного способа доставки*/
    public function updatePaymentDeliveries($paymentId, array $deliveriesIds)
    {
        $delete = $this->queryFactory->newDelete();
        $delete->from('__delivery_payment')
            ->where('payment_method_id = :payment_method_id')
            ->bindValue('payment_method_id', $paymentId);

        $this->db->query($delete);

        if (is_array($deliveriesIds)) {
            foreach($deliveriesIds as $dId) {
                $insert = $this->queryFactory->newInsert();
                $insert->into('__delivery_payment')
                    ->cols([
                        'payment_method_id' => $paymentId,
                        'delivery_id' => $dId,
                    ]);
                $this->db->query($insert);
            }
        }

        return ExtenderFacade::execute([static::class, __FUNCTION__], null, func_get_args());
    }

    protected function filter__has_image()
    {
        $this->select->where('images IS NOT NULL')
            ->where("images <> ''");
    }
}
