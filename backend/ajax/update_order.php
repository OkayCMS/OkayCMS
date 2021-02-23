<?php

use Okay\Core\Design;
use Okay\Entities\OrderLabelsEntity;
use Okay\Admin\Helpers\BackendOrderHistoryHelper;

require_once 'configure.php';

if (!$managers->access('orders', $manager)) {
    exit();
}

/** @var Design $design */
$design = $DI->get(Design::class);

/** @var OrderLabelsEntity $orderLabelsEntity */
$orderLabelsEntity = $entityFactory->get(OrderLabelsEntity::class);

/** @var BackendOrderHistoryHelper $orderHistoryHelper */
$orderHistoryHelper = $DI->get(BackendOrderHistoryHelper::class);

$design->setTemplatesDir('backend/design/html');
$design->setCompiledDir('backend/design/compiled');

$result = [];
/*Принимаем метки, с которыми нужно сделать действие*/
if ($request->method("post")) {
    $order_id = $request->post("order_id", "integer");
    $state = $request->post("state", "string");
    $label_id = $request->post("label_id", "integer");

    if (empty($order_id) || empty($state)) {
        $result['success '] = false;
    } else {
        switch ($state) {
            case "add" : {
                $orderLabelsEntity->addOrderLabels($order_id, (array)$label_id);
                $orderHistoryHelper->setLabel($order_id, (int)$label_id);
                $result['success'] = true;
                break;
            }
            case "remove": {
                $orderLabelsEntity->deleteOrderLabels($order_id, (array)$label_id);
                $orderHistoryHelper->removeLabel($order_id, (int)$label_id);
                $result['success'] = true;
                break;
            }
        }
        $order = new \stdClass();
        $order->labels = $orderLabelsEntity->getOrdersLabels((array)$order_id);
        $design->assign("order", $order);
        $result['data'] = $design->fetch("labels_ajax.tpl");

    }

} else {
    $result['success ']= false;
}

$response->setContent(json_encode($result), RESPONSE_JSON);
$response->sendContent();