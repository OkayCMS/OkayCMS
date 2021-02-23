<?php


namespace Okay\Admin\Helpers;


use Okay\Admin\Requests\BackendOrdersRequest;
use Okay\Core\BackendTranslations;
use Okay\Core\EntityFactory;
use Okay\Core\Phone;
use Okay\Core\QueryFactory;
use Okay\Core\Request;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\DiscountsEntity;
use Okay\Entities\ManagersEntity;
use Okay\Entities\OrderHistoryEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\OrderLabelsEntity;
use Okay\Entities\PaymentsEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\PurchasesEntity;
use Okay\Entities\VariantsEntity;

class BackendOrderHistoryHelper
{
    
    /** @var OrderHistoryEntity */
    private $orderHistoryEntity;
    
    /** @var EntityFactory */
    private $entityFactory;
    
    /** @var BackendOrdersRequest */
    private $ordersRequest;
    
    /** @var Request */
    private $request;
    
    /** @var BackendTranslations */
    private $BT;

    /** @var QueryFactory */
    private $queryFactory;
    
    private static $purchasesNames;
    
    public function __construct(
        EntityFactory        $entityFactory,
        BackendOrdersRequest $ordersRequest,
        Request              $request,
        BackendTranslations  $backendTranslations,
        QueryFactory         $queryFactory
    ) {
        $this->request       = $request;
        $this->entityFactory = $entityFactory;
        $this->ordersRequest = $ordersRequest;
        $this->BT            = $backendTranslations;
        $this->queryFactory  = $queryFactory;

        $this->orderHistoryEntity = $entityFactory->get(OrderHistoryEntity::class);
    }

    /**
     * Метод обновляет историю заказа, вычисляя разницу между данными заказа до и после обновления
     * 
     * @param $orderBeforeUpdate
     * @param $orderAfterUpdate
     * @param $purchasesBeforeUpdate
     * @param $discountsBeforeUpdate
     * @return null
     * @throws \Exception
     */
    public function updateHistory(
        $orderBeforeUpdate,
        $orderAfterUpdate,
        $purchasesBeforeUpdate,
        $discountsBeforeUpdate
    ) {
        // Разворачиваем массив, чтобы ключем был id покупки
        $tmp = [];
        foreach ($purchasesBeforeUpdate as $key => $purchase) {
            $tmp[$purchase->id] = $purchase;
        }
        $purchasesBeforeUpdate = $tmp;
        
        /** @var ManagersEntity $managersEntity */
        $managersEntity = $this->entityFactory->get(ManagersEntity::class);
        
        /** @var PurchasesEntity $purchasesEntity */
        $purchasesEntity = $this->entityFactory->get(PurchasesEntity::class);

        /** @var DiscountsEntity $discountsEntity */
        $discountsEntity = $this->entityFactory->get(DiscountsEntity::class);

        foreach ($discountsBeforeUpdate as $discountBeforeUpdate) {
            if ($discountBeforeUpdate->entity == 'purchase')
                $discountBeforeUpdate->purchase = $tmp[$discountBeforeUpdate->entity_id];
        }

        $purchasesAfterUpdate = [];
        $discountsAfterUpdate = [];
        if (!empty($orderAfterUpdate->id)) {
            $purchasesAfterUpdate = $purchasesEntity->mappedBy('id')->find(['order_id' => $orderAfterUpdate->id]);
            $select = $this->queryFactory->newSelect();
            $select ->from(DiscountsEntity::getTable())
                    ->cols(['id'])
                    ->where("((`entity` = 'order' AND `entity_id` = :order_id) OR
                                    (`entity` = 'purchase' AND `entity_id` IN (SELECT `id` FROM `ok_purchases` WHERE `order_id` = :order_id)))")
                    ->bindValue('order_id', $orderAfterUpdate->id);
            $discountAfterUpdateIds = $select->results('id');
            if (!empty($discountAfterUpdateIds)) {
                $discountsAfterUpdate = $discountsEntity->mappedBy('id')->find(['id' => $discountAfterUpdateIds]);
            } else {
                $discountsAfterUpdate = [];
            }

            foreach ($discountsAfterUpdate as $discountAfterUpdate) {
                if ($discountAfterUpdate->entity == 'purchase') {
                    if (isset($purchasesBeforeUpdate[$discountAfterUpdate->entity_id])) {
                        $discountAfterUpdate->purchase = $purchasesBeforeUpdate[$discountAfterUpdate->entity_id];
                    } else if ($discountAfterUpdate->purchase = $purchasesAfterUpdate[$discountAfterUpdate->entity_id]) {
                        $discountAfterUpdate->purchase = $purchasesAfterUpdate[$discountAfterUpdate->entity_id];
                    }
                }
            }
        }
        
        $managerId = null;
        if (!empty($_SESSION['admin']) && ($manager = $managersEntity->get($_SESSION['admin']))) {
            $managerId = $manager->id;
        }
        
        if (empty($orderBeforeUpdate) || $orderBeforeUpdate->status_id != $orderAfterUpdate->status_id) {
            $this->orderHistoryEntity->add([
                'order_id' => $orderAfterUpdate->id,
                'manager_id' => $managerId,
                'new_status_id' => $orderAfterUpdate->status_id,
            ]);
        }
        
        if ($changeText = $this->getChangeOrderMessage(
            $orderBeforeUpdate,
            $orderAfterUpdate,
            $purchasesBeforeUpdate,
            $purchasesAfterUpdate,
            $discountsBeforeUpdate,
            $discountsAfterUpdate)) {
            $this->orderHistoryEntity->add([
                'order_id' => $orderAfterUpdate->id,
                'manager_id' => $managerId,
                'text' => implode('<br/>', $changeText),
            ]);
        }

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function setLabel($orderId, $labelId)
    {
        /** @var ManagersEntity $managersEntity */
        $managersEntity = $this->entityFactory->get(ManagersEntity::class);

        /** @var OrderLabelsEntity $orderLabelsEntity */
        $orderLabelsEntity = $this->entityFactory->get(OrderLabelsEntity::class);
        
        $managerId = null;
        if (!empty($_SESSION['admin']) && ($manager = $managersEntity->get($_SESSION['admin']))) {
            $managerId = $manager->id;
        }
        
        $orderLabel = $orderLabelsEntity->findOne(['id' => $labelId]);

        $changeMessage = $this->BT->getTranslation('order_history_add')
            . " "
            . $this->BT->getTranslation('order_history_label')
            . " \"{$orderLabel->name}\"";

        $this->orderHistoryEntity->add([
            'order_id' => $orderId,
            'manager_id' => $managerId,
            'text' =>  $changeMessage,
        ]);
    }
    
    public function removeLabel($orderId, $labelId)
    {
        /** @var ManagersEntity $managersEntity */
        $managersEntity = $this->entityFactory->get(ManagersEntity::class);

        /** @var OrderLabelsEntity $orderLabelsEntity */
        $orderLabelsEntity = $this->entityFactory->get(OrderLabelsEntity::class);

        $managerId = null;
        if (!empty($_SESSION['admin']) && ($manager = $managersEntity->get($_SESSION['admin']))) {
            $managerId = $manager->id;
        }

        $orderLabel = $orderLabelsEntity->findOne(['id' => $labelId]);

        $changeMessage = $this->BT->getTranslation('order_history_delete')
            . " "
            . $this->BT->getTranslation('order_history_label')
            . " \"{$orderLabel->name}\"";
        
        $this->orderHistoryEntity->add([
            'order_id' => $orderId,
            'manager_id' => $managerId,
            'text' =>  $changeMessage,
        ]);
    }
    
    /**
     * Метод сравнивает заказ до обновления и после, на предмет изменений
     * 
     * @param $orderBeforeUpdate
     * @param $orderAfterUpdate
     * @param array $purchasesBeforeUpdate
     * @param array $purchasesAfterUpdate
     * @param array $discountsBeforeUpdate
     * @param array $discountsAfterUpdate
     * @return array
     * @throws \Exception
     */
    private function getChangeOrderMessage(
        $orderBeforeUpdate,
        $orderAfterUpdate,
        $purchasesBeforeUpdate,
        $purchasesAfterUpdate,
        $discountsBeforeUpdate,
        $discountsAfterUpdate
    ) {
        $changeOrderMessage = [];
        $changeOrderMessage = $changeOrderMessage + $this->getChangePurchasesMessage($purchasesBeforeUpdate, $purchasesAfterUpdate);
        $changeOrderMessage = $changeOrderMessage + $this->getChangeDiscountsMessage($discountsBeforeUpdate, $discountsAfterUpdate);

        if ($historyComment = $this->request->post('history_comment')) {
            $changeOrderMessage[] = $historyComment;
        }
        
        // Все изменения только в созданном заказе
        if (!empty($orderBeforeUpdate->id) && !empty($orderAfterUpdate->id)) {
            
            if (property_exists($orderBeforeUpdate, 'delivery_id') 
                && property_exists($orderAfterUpdate, 'delivery_id')
                && $orderBeforeUpdate->delivery_id != $orderAfterUpdate->delivery_id
                && (!empty($orderBeforeUpdate->delivery_id) || !empty($orderAfterUpdate->delivery_id))) {
                /** @var DeliveriesEntity $deliveriesEntity */
                $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
                $deliveries = $deliveriesEntity->mappedBy('id')->find();
                
                
                $oldDeliveryName = isset($deliveries[$orderBeforeUpdate->delivery_id]) ? $deliveries[$orderBeforeUpdate->delivery_id]->name : '';
                $newDeliveryName = isset($deliveries[$orderAfterUpdate->delivery_id]) ? $deliveries[$orderAfterUpdate->delivery_id]->name : '';
                
                // Добавили доставку
                if (empty($orderBeforeUpdate->delivery_id)) {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_add') 
                        . " "
                        . $this->BT->getTranslation('order_history_delivery')
                        . " \"{$newDeliveryName}\"";
                // Удалили доставку
                } elseif (empty($orderAfterUpdate->delivery_id)) {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_delete')
                        . " "
                        . $this->BT->getTranslation('order_history_delivery')
                        . " \"{$oldDeliveryName}\"";
                // Изменили доставку
                } else {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                        . " "
                        . $this->BT->getTranslation('order_history_delivery')
                        . " "
                        . $this->BT->getTranslation('order_history_from')
                        . " \"{$oldDeliveryName}\" "
                        . $this->BT->getTranslation('order_history_to')
                        . " \"{$newDeliveryName}\"";
                }
            }

            // Изменили стоимость доставки
            if (property_exists($orderBeforeUpdate, 'delivery_price')
                && property_exists($orderAfterUpdate, 'delivery_price') 
                && $orderBeforeUpdate->delivery_price != $orderAfterUpdate->delivery_price) {
                $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                    . " "
                    . $this->BT->getTranslation('order_history_delivery_price')
                    . " "
                    . $this->BT->getTranslation('order_history_from')
                    . " \"{$orderBeforeUpdate->delivery_price}\" "
                    . $this->BT->getTranslation('order_history_to')
                    . " \"{$orderAfterUpdate->delivery_price}\"";
            }
            
            if (property_exists($orderBeforeUpdate, 'payment_method_id')
                && property_exists($orderAfterUpdate, 'payment_method_id')
                && $orderBeforeUpdate->payment_method_id != $orderAfterUpdate->payment_method_id
                && (!empty($orderBeforeUpdate->payment_method_id) || !empty($orderAfterUpdate->payment_method_id))) {
                /** @var PaymentsEntity $paymentsEntity */
                $paymentsEntity = $this->entityFactory->get(PaymentsEntity::class);
                $payments = $paymentsEntity->mappedBy('id')->find();

                $oldPaymentName = isset($payments[$orderBeforeUpdate->payment_method_id]) ? $payments[$orderBeforeUpdate->payment_method_id]->name : '';
                $newPaymentName = isset($payments[$orderAfterUpdate->payment_method_id]) ? $payments[$orderAfterUpdate->payment_method_id]->name : '';

                // Добавили оплату
                if (empty($orderBeforeUpdate->payment_method_id)) {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_add')
                        . " "
                        . $this->BT->getTranslation('order_history_payment')
                        . " \"{$newPaymentName}\"";
                // Удалили оплату
                } elseif (empty($orderAfterUpdate->payment_method_id)) {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_delete')
                        . " "
                        . $this->BT->getTranslation('order_history_payment')
                        . " \"{$oldPaymentName}\"";
                // Изменили оплату
                } else {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                        . " "
                        . $this->BT->getTranslation('order_history_payment')
                        . " "
                        . $this->BT->getTranslation('order_history_from')
                        . " \"{$oldPaymentName}\" "
                        . $this->BT->getTranslation('order_history_to')
                        . " \"{$newPaymentName}\"";
                }
            }

            if (property_exists($orderBeforeUpdate, 'paid')
                && property_exists($orderAfterUpdate, 'paid')
                && $orderBeforeUpdate->paid != $orderAfterUpdate->paid) {
                
                // Снял отметку оплачен
                if ($orderBeforeUpdate->paid) {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_unset_paid');
                // Отметил заказ оплаченным
                } elseif ($orderAfterUpdate->paid) {
                    $changeOrderMessage[] = $this->BT->getTranslation('order_history_set_paid');
                }
            }

            // Изменил имя
            if (property_exists($orderBeforeUpdate, 'name')
                && property_exists($orderAfterUpdate, 'name')
                && $orderBeforeUpdate->name != $orderAfterUpdate->name) {
                $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                    . " "
                    . $this->BT->getTranslation('order_history_name')
                    . " "
                    . $this->BT->getTranslation('order_history_from')
                    . " \"{$orderBeforeUpdate->name}\" "
                    . $this->BT->getTranslation('order_history_to')
                    . " \"{$orderAfterUpdate->name}\"";
            }

            // Изменил фамилию
            if (property_exists($orderBeforeUpdate, 'last_name')
                && property_exists($orderAfterUpdate, 'last_name')
                && $orderBeforeUpdate->last_name != $orderAfterUpdate->last_name) {
                $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                    . " "
                    . $this->BT->getTranslation('order_history_last_name')
                    . " "
                    . $this->BT->getTranslation('order_history_from')
                    . " \"{$orderBeforeUpdate->last_name}\" "
                    . $this->BT->getTranslation('order_history_to')
                    . " \"{$orderAfterUpdate->last_name}\"";
            }

            // Изменил адрес
            if (property_exists($orderBeforeUpdate, 'address')
                && property_exists($orderAfterUpdate, 'address')
                && $orderBeforeUpdate->address != $orderAfterUpdate->address) {
                $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                    . " "
                    . $this->BT->getTranslation('order_history_address')
                    . " "
                    . $this->BT->getTranslation('order_history_from')
                    . " \"{$orderBeforeUpdate->address}\" "
                    . $this->BT->getTranslation('order_history_to')
                    . " \"{$orderAfterUpdate->address}\"";
            }

            // Изменил телефон (Изменения сравниваются и выводятся с учетом форматирования)
            if (property_exists($orderBeforeUpdate, 'phone')
                && property_exists($orderAfterUpdate, 'phone')
                && ($bPhone = Phone::format($orderBeforeUpdate->phone)) != ($aPhone = Phone::format($orderAfterUpdate->phone))) {
                $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                    . " "
                    . $this->BT->getTranslation('order_history_phone')
                    . " "
                    . $this->BT->getTranslation('order_history_from')
                    . " \"{$bPhone}\" "
                    . $this->BT->getTranslation('order_history_to')
                    . " \"{$aPhone}\"";
            }

            // Изменил почту
            if (property_exists($orderBeforeUpdate, 'email')
                && property_exists($orderAfterUpdate, 'email')
                && $orderBeforeUpdate->email != $orderAfterUpdate->email) {
                $changeOrderMessage[] = $this->BT->getTranslation('order_history_change')
                    . " "
                    . $this->BT->getTranslation('order_history_email')
                    . " "
                    . $this->BT->getTranslation('order_history_from')
                    . " \"{$orderBeforeUpdate->email}\" "
                    . $this->BT->getTranslation('order_history_to')
                    . " \"{$orderAfterUpdate->email}\"";
            }
        }
        return ExtenderFacade::execute(__METHOD__, $changeOrderMessage, func_get_args());
    }

    /**
     * Метод сравнивает все покупки на предмет добавления, удаления или изменения
     * 
     * @param $purchasesBeforeUpdate
     * @param $purchasesAfterUpdate
     * @return array
     */
    private function getChangePurchasesMessage($purchasesBeforeUpdate, $purchasesAfterUpdate)
    {
        $changePurchasesMessage = [];
        
        foreach ($purchasesBeforeUpdate as $purchaseId => $purchase) {
            // Удалили покупку
            if (!isset($purchasesAfterUpdate[$purchaseId])) {
                $purchaseName = $this->getPurchaseName($purchase);
                $changePurchasesMessage[] = $this->BT->getTranslation('order_history_delete')
                    . " "
                    . $this->BT->getTranslation('order_history_product')
                    . " \"{$purchaseName}\" "
                    . $this->BT->getTranslation('order_history_from_order');
            // Изменили покупку?
            } elseif ($purchaseChanges = $this->getChangePurchaseMessage($purchase, $purchasesAfterUpdate[$purchaseId])) {
                $changePurchasesMessage[] = implode('<br/>', $purchaseChanges);
            }
        }

        foreach ($purchasesAfterUpdate as $purchaseId => $purchase) {
            if (!isset($purchasesBeforeUpdate[$purchaseId])) {
                $purchaseName = $this->getPurchaseName($purchase);

                // Добавили покупку
                $changePurchasesMessage[] = $this->BT->getTranslation('order_history_add')
                    . " "
                    . $this->BT->getTranslation('order_history_product')
                    . " \"{$purchaseName}\" "
                    . $this->BT->getTranslation('order_history_to_order');
            }
        }
        
        return ExtenderFacade::execute(__METHOD__, $changePurchasesMessage, func_get_args());
    }

    /**
     * Метод сравнивает две покупки, до обновления и после, на предмет изменений
     * 
     * @param $purchaseBeforeUpdate
     * @param $purchaseAfterUpdate
     * @return array
     */
    private function getChangePurchaseMessage($purchaseBeforeUpdate, $purchaseAfterUpdate)
    {
        $purchaseChanges = [];
        // Изменили вариант
        if (property_exists($purchaseBeforeUpdate, 'variant_id')
            && property_exists($purchaseAfterUpdate, 'variant_id')
            && $purchaseBeforeUpdate->variant_id != $purchaseAfterUpdate->variant_id
            && (!empty($purchaseBeforeUpdate->variant_name) || !empty($purchaseAfterUpdate->variant_name))) {
            $purchaseChanges[] = $this->BT->getTranslation('order_history_change')
                . " "
                . $this->BT->getTranslation('order_history_variant')
                . " "
                . $this->BT->getTranslation('order_history_from')
                . " \"{$purchaseBeforeUpdate->variant_name}\" "
                . $this->BT->getTranslation('order_history_to')
                . " \"{$purchaseAfterUpdate->variant_name}\"";
        }

        // Изменили цену
        if (property_exists($purchaseBeforeUpdate, 'undiscounted_price')
            && property_exists($purchaseAfterUpdate, 'undiscounted_price')
            && $purchaseBeforeUpdate->undiscounted_price != $purchaseAfterUpdate->undiscounted_price) {
            $purchaseName = $this->getPurchaseName($purchaseAfterUpdate);
            $purchaseChanges[] = $this->BT->getTranslation('order_history_change')
                . " "
                . $this->BT->getTranslation('order_history_purchase_price')
                . " \"{$purchaseName}\" "
                . $this->BT->getTranslation('order_history_from')
                . " \"{$purchaseBeforeUpdate->price}\" "
                . $this->BT->getTranslation('order_history_to')
                . " \"{$purchaseAfterUpdate->price}\"";
        }

        // Изменили количество
        if (property_exists($purchaseBeforeUpdate, 'amount')
            && property_exists($purchaseAfterUpdate, 'amount')
            && $purchaseBeforeUpdate->amount != $purchaseAfterUpdate->amount) {
            $purchaseName = $this->getPurchaseName($purchaseAfterUpdate);
            $purchaseChanges[] = $this->BT->getTranslation('order_history_change')
                . " "
                . $this->BT->getTranslation('order_history_purchase_amount')
                . " \"{$purchaseName}\" "
                . $this->BT->getTranslation('order_history_from')
                . " \"{$purchaseBeforeUpdate->amount}\" "
                . $this->BT->getTranslation('order_history_to')
                . " \"{$purchaseAfterUpdate->amount}\"";
        }
        return ExtenderFacade::execute(__METHOD__, $purchaseChanges, func_get_args());
    }

    /**
     * Метод сравнивает все скидки на предмет добавления, удаления или изменения
     *
     * @param $discountsBeforeUpdate
     * @param $discountsAfterUpdate
     * @return array
     */
    private function getChangeDiscountsMessage($discountsBeforeUpdate, $discountsAfterUpdate)
    {
        $changeDiscountsMessage = [];

        foreach ($discountsBeforeUpdate as $discountId => $discount) {
            // Удалили скидку
            if (!isset($discountsAfterUpdate[$discountId])) {
                $message = $this->BT->getTranslation('order_history_delete')
                    . " "
                    . $this->BT->getTranslation('order_history_discount')
                    . " \"{$discount->name}\" ";
                if ($discount->entity == 'purchase') {
                    $purchaseName = $this->getPurchaseName($discount->purchase);
                    $message .= $this->BT->getTranslation('order_history_from_product') . " \"{$purchaseName}\" ";
                } else {
                    $message .= $this->BT->getTranslation('order_history_from_order');
                }
                $changeDiscountsMessage[] = $message;
                // Изменили скидку
            } elseif ($discountChanges = $this->getChangeDiscountMessage($discount, $discountsAfterUpdate[$discountId])) {
                $changeDiscountsMessage[] = implode('<br/>', $discountChanges);
            }
        }

        foreach ($discountsAfterUpdate as $discountId => $discount) {
            if (!isset($discountsBeforeUpdate[$discountId])) {
                // Добавили скидку
                $message = $this->BT->getTranslation('order_history_add')
                    . " "
                    . $this->BT->getTranslation('order_history_discount')
                    . " \"{$discount->name}\" ";
                if ($discount->entity == 'purchase') {
                    $purchaseName = $this->getPurchaseName($discount->purchase);
                    $message .= $this->BT->getTranslation('order_history_to_product') . " \"{$purchaseName}\" ";
                } else {
                    $message .= $this->BT->getTranslation('order_history_to_order');
                }
                $changeDiscountsMessage[] = $message;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $changeDiscountsMessage, func_get_args());
    }

    /**
     * Метод сравнивает две скидки, до обновления и после, на предмет изменений
     *
     * @param $discountBeforeUpdate
     * @param $discountAfterUpdate
     * @return array
     */
    private function getChangeDiscountMessage($discountBeforeUpdate, $discountAfterUpdate)
    {
        $discountChanges = [];
        // Изменили значение или тип
        if (property_exists($discountBeforeUpdate, 'value')
            && property_exists($discountAfterUpdate, 'value')
            && property_exists($discountBeforeUpdate, 'type')
            && property_exists($discountAfterUpdate, 'value')
            && ($discountBeforeUpdate->value != $discountAfterUpdate->value
                || $discountBeforeUpdate->type != $discountAfterUpdate->type)) {
            $message = $this->BT->getTranslation('order_history_change')
                . " "
                . $this->BT->getTranslation('order_history_discount_value')
                . " "
                . $this->BT->getTranslation('order_history_in_discount')
                . " \"{$discountAfterUpdate->name}\" "
                . $this->BT->getTranslation('order_history_from')
                . " \"{$discountBeforeUpdate->value} "
                . ($discountBeforeUpdate->type == 'percent' ? "%\" " : "RUR\" ")
                . $this->BT->getTranslation('order_history_to')
                . " \"{$discountAfterUpdate->value} "
                . ($discountAfterUpdate->type == 'percent' ? "%\" " : "RUR\" ");
            if ($discountAfterUpdate->entity == 'purchase') {
                $purchaseName = $this->getPurchaseName($discountAfterUpdate->purchase);
                $message .= $this->BT->getTranslation('order_history_in_product') . " \"{$purchaseName}\"";
            } else {
                $message .= $this->BT->getTranslation('order_history_in_order');
            }
            $discountChanges[] = $message;
        }

        // Изменили название
        if (property_exists($discountBeforeUpdate, 'name')
            && property_exists($discountAfterUpdate, 'name')
            && $discountBeforeUpdate->name != $discountAfterUpdate->name) {
            $message = $this->BT->getTranslation('order_history_change')
                . " "
                . $this->BT->getTranslation('order_history_discount_name')
                . " "
                . $this->BT->getTranslation('order_history_in_discount')
                . " "
                . $this->BT->getTranslation('order_history_from')
                . " \"{$discountBeforeUpdate->name}\" "
                . $this->BT->getTranslation('order_history_to')
                . " \"{$discountAfterUpdate->name}\" ";
            if ($discountAfterUpdate->entity == 'purchase') {
                $purchaseName = $this->getPurchaseName($discountAfterUpdate->purchase);
                $message .= $this->BT->getTranslation('order_history_in_product') . " \"{$purchaseName}\"";
            } else {
                $message .= $this->BT->getTranslation('order_history_in_order');
            }
            $discountChanges[] = $message;
        }

        // Изменили описание
        if (property_exists($discountBeforeUpdate, 'description')
            && property_exists($discountAfterUpdate, 'description')
            && $discountBeforeUpdate->description != $discountAfterUpdate->description) {
            $message = $this->BT->getTranslation('order_history_change')
                . " "
                . $this->BT->getTranslation('order_history_discount_description')
                . " "
                . $this->BT->getTranslation('order_history_in_discount')
                . " \"{$discountAfterUpdate->name}\" "
                . $this->BT->getTranslation('order_history_from')
                . " \"{$discountBeforeUpdate->description}\" "
                . $this->BT->getTranslation('order_history_to')
                . " \"{$discountAfterUpdate->description}\" ";
            if ($discountAfterUpdate->entity == 'purchase') {
                $purchaseName = $this->getPurchaseName($discountAfterUpdate->purchase);
                $message .= $this->BT->getTranslation('order_history_in_product') . " \"{$purchaseName}\"";
            } else {
                $message .= $this->BT->getTranslation('order_history_in_order');
            }
            $discountChanges[] = $message;
        }

        // Изменили "от последней скидки"
        if (property_exists($discountBeforeUpdate, 'from_last_discount')
            && property_exists($discountAfterUpdate, 'from_last_discount')
            && $discountBeforeUpdate->from_last_discount != $discountAfterUpdate->from_last_discount) {
            $message = $this->BT->getTranslation('order_history_change')
                . " \""
                . $this->BT->getTranslation('order_history_discount_from_last_discount')
                . "\" "
                . $this->BT->getTranslation('order_history_in_discount')
                . " \"{$discountAfterUpdate->name}\" "
                . $this->BT->getTranslation('order_history_from')
                . " \""
                . ($discountBeforeUpdate->from_last_discount ? $this->BT->getTranslation('order_history_on') : $this->BT->getTranslation('order_history_off'))
                . "\" "
                . $this->BT->getTranslation('order_history_to')
                . " \""
                . ($discountAfterUpdate->from_last_discount ? $this->BT->getTranslation('order_history_on') : $this->BT->getTranslation('order_history_off'))
                . "\" ";
            if ($discountAfterUpdate->entity == 'purchase') {
                $purchaseName = $this->getPurchaseName($discountAfterUpdate->purchase);
                $message .= $this->BT->getTranslation('order_history_in_product') . " \"{$purchaseName}\"";
            } else {
                $message .= $this->BT->getTranslation('order_history_in_order');
            }
            $discountChanges[] = $message;
        }

        return ExtenderFacade::execute(__METHOD__, $discountChanges, func_get_args());
    }
    
    private function getPurchaseName($purchase)
    {
        $purchaseName = '';
        if (isset(self::$purchasesNames[$purchase->id])) {
            return self::$purchasesNames[$purchase->id];
        }
        
        if (!empty($purchase->product_name)) {
            $purchaseName = $purchase->product_name . (!empty($purchase->variant_name) ? " ({$purchase->variant_name})" : '');
        } elseif (!empty($purchase->product_id)) {
            /** @var ProductsEntity $productsEntity */
            $productsEntity = $this->entityFactory->get(ProductsEntity::class);
            $purchaseName = $productsEntity->cols(['name'])->findOne(['id' => $purchase->product_id]);
            
            if (!empty($purchase->variant_id)) {
                /** @var VariantsEntity $variantsEntity */
                $variantsEntity = $this->entityFactory->get(VariantsEntity::class);
                if ($variantName = $variantsEntity->cols(['name'])->findOne(['id' => $purchase->variant_id])) {
                    $purchaseName .= " ({$variantName})";
                }
            }
        }

        self::$purchasesNames[$purchase->id] = $purchaseName;
        
        return $purchaseName;
    }
    
    public function getHistory($orderId)
    {
        $orderHistory = [];
        if (!empty($orderId)) {
            
            /** @var ManagersEntity $managersEntity */
            $managersEntity = $this->entityFactory->get(ManagersEntity::class);
            $managers = $managersEntity->mappedBy('id')->find();
            $orderHistory = $this->orderHistoryEntity->find(['order_id' => $orderId]);

            foreach ($orderHistory as $item) {
                if ($item->manager_id && isset($managers[$item->manager_id])) {
                    $item->manager_name = $managers[$item->manager_id]->login;
                }
            }
            
        }
        return ExtenderFacade::execute(__METHOD__, $orderHistory, func_get_args());
    }
    
    public function findOrdersHistory(array $ordersIds)
    {
        $ordersHistory = [];
        if (!empty($ordersIds)) {
            
            /** @var ManagersEntity $managersEntity */
            $managersEntity = $this->entityFactory->get(ManagersEntity::class);
            $managers = $managersEntity->mappedBy('id')->find();
            
            foreach ($this->orderHistoryEntity->find(['order_id' => $ordersIds]) as $item) {
                if ($item->manager_id && isset($managers[$item->manager_id])) {
                    $item->manager_name = $managers[$item->manager_id]->login;
                }
                $ordersHistory[$item->order_id][] = $item;
            }
            
        }
        return ExtenderFacade::execute(__METHOD__, $ordersHistory, func_get_args());
    }
    
}