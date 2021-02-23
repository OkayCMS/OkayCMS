<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Config;
use Okay\Core\EntityFactory;
use Okay\Core\Image;
use Okay\Core\Request;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\PaymentsEntity;

class BackendPaymentsHelper
{
    /**
     * @var PaymentsEntity
     */
    private $paymentMethodsEntity;

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
        $this->paymentMethodsEntity = $entityFactory->get(PaymentsEntity::class);
        $this->request   = $request;
        $this->config    = $config;
        $this->imageCore = $imageCore;
    }

    public function disable(array $ids)
    {
        if (is_array($ids)) {
            $this->paymentMethodsEntity->update($ids, ['enabled'=>0]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function enable(array $ids)
    {
        if (is_array($ids)) {
            $this->paymentMethodsEntity->update($ids, ['enabled' => 1]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function delete(array $ids)
    {
        if (is_array($ids)) {
            $this->paymentMethodsEntity->delete($ids);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function sortPositions(array $positions)
    {
        $ids = array_keys($positions);
        sort($positions);

        foreach ($positions as $i=>$position) {
            $this->paymentMethodsEntity->update($ids[$i], ['position'=>$position]);
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function buildPaymentMethodsFilter()
    {
        $filter = [];
        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getPaymentMethodsCount($filter)
    {
        $obj = new \ArrayObject();
        $countFilter = $obj->getArrayCopy();
        unset($countFilter['limit']);
        $count = $this->paymentMethodsEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function findPaymentMethods($filter)
    {
        $posts = $this->paymentMethodsEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $posts, func_get_args());
    }

    public function prepareAdd($payment)
    {
        return ExtenderFacade::execute(__METHOD__, $payment, func_get_args());
    }

    public function add($payment)
    {
        $insertId = $this->paymentMethodsEntity->add($payment);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }

    public function prepareUpdate($payment)
    {
        return ExtenderFacade::execute(__METHOD__, $payment, func_get_args());
    }

    public function update($id, $payment)
    {
        $this->paymentMethodsEntity->update($id, $payment);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function getPaymentMethod($id)
    {
        $payment = $this->paymentMethodsEntity->get($id);
        if (!empty($payment->id)) {
            $payment->payment_deliveries = $this->paymentMethodsEntity->getPaymentDeliveries($payment->id);
            $payment->payment_settings = $this->paymentMethodsEntity->getPaymentSettings($payment->id);
        } else {
            $payment = new \stdClass();
            $payment->payment_deliveries = [];
            $payment->payment_settings = [];
        }
        return ExtenderFacade::execute(__METHOD__, $payment, func_get_args());
    }

    public function updateSettings($paymentId, array $paymentSettings)
    {
        $this->paymentMethodsEntity->updatePaymentSettings($paymentId, $paymentSettings);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function updatePaymentDeliveries($paymentId, array $deliveries)
    {
        $this->paymentMethodsEntity->updatePaymentDeliveries($paymentId, $deliveries);
        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
    
    public function deleteImage($brand)
    {
        $this->imageCore->deleteImage(
            $brand->id,
            'image',
            PaymentsEntity::class,
            $this->config->get('original_payments_dir'),
            $this->config->get('resized_payments_dir')
        );

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function uploadImage($image, $brand)
    {
        if (!empty($image['name']) && ($filename = $this->imageCore->uploadImage($image['tmp_name'], $image['name'], $this->config->get('original_payments_dir')))) {
            $this->imageCore->deleteImage(
                $brand->id,
                'image',
                PaymentsEntity::class,
                $this->config->get('original_payments_dir'),
                $this->config->get('resized_payments_dir')
            );

            $this->paymentMethodsEntity->update($brand->id, ['image'=>$filename]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}