<?php 


namespace Okay\Modules\OkayCMS\DeliveryFields\Extenders;


use Okay\Core\Design;
use Okay\Core\Request;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtensionInterface;
use Okay\Entities\OrdersEntity;
use Okay\Modules\OkayCMS\DeliveryFields\Helpers\DeliveryFieldsHelper;
use Okay\Modules\OkayCMS\DeliveryFields\Entities\DeliveryFieldsValuesEntity;

class OrdersHelperExtender implements ExtensionInterface
{
    private DeliveryFieldsValuesEntity $deliveryFieldsValuesEntity;
    private Request $request;
    private Design $design;
    private DeliveryFieldsHelper $deliveryFieldsHelper;
    private EntityFactory $entityFactory;

    public function __construct(
        EntityFactory $entityFactory,
        Request $request,
        Design $design,
        DeliveryFieldsHelper $deliveryFieldsHelper
    ) {
        $this->deliveryFieldsValuesEntity = $entityFactory->get(DeliveryFieldsValuesEntity::class);
        $this->request = $request;
        $this->design = $design;
        $this->deliveryFieldsHelper = $deliveryFieldsHelper;
        $this->entityFactory = $entityFactory;
    }

    /**
     * @param $result
     * @param $order
     * @return void
     *
     * Після оформлення замовлення зберігаємо поля для способів доставки.
     */
    public function extendFinalCreateOrderProcedure($result, $order)
    {
        if (empty($order->delivery_id)) {
            return;
        }

        $deliveryFields = $this->request->post('delivery_fields');
        if (empty($deliveryFields[$order->delivery_id])) {
            return;
        }

        $selectedDeliveryFields = $deliveryFields[$order->delivery_id];
        foreach ($selectedDeliveryFields as $id => $value) {
            $this->deliveryFieldsValuesEntity->add([
                'order_id' => $order->id,
                'field_id' => $id,
                'value'    => $value,
            ]);
        }
    }

    /**
     * @param $result
     * @param $orderId
     * @return array|void
     * @throws \Exception
     *
     * Передаємо поля з даними на сторінку замовлення та в лист замовлення.
     */
    public function extendGetOrderPurchasesList($result, $orderId)
    {
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        $order = $ordersEntity->findOne([
            'id' => $orderId,
        ]);

        if (empty($order) || empty($order->delivery_id)) {
            return [];
        }
        $fields = $this->deliveryFieldsHelper->getOrderDeliveryFields((int)$orderId, (int)$order->delivery_id);
        $this->design->assign('deliveryFields', $fields);
    }
}