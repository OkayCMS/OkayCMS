<?php

namespace Okay\Controllers;

use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\Response;
use Okay\Entities\SubscribesEntity;
use Okay\Helpers\ValidateHelper;
use Okay\Requests\CommonRequest;

class SubscribeController
{
    public function ajaxSubscribe(
        CommonRequest $commonRequest,
        ValidateHelper $validateHelper,
        EntityFactory $entityFactory,
        Request $request,
        Response $response
    ) {

        if (($subscribe = $commonRequest->postSubscribe()) !== null) {

            /** @var SubscribesEntity $subscribesEntity */
            $subscribesEntity = $entityFactory->get(SubscribesEntity::class);

            if ($error = $validateHelper->getCustomerCsrfError($request->post('customer_csrf_token'))) {
                $response->setStatusCode(403);
                $result = [
                    'error' => $error,
                ];
            } elseif ($error = $validateHelper->getSubscribeValidateError($subscribe)) {
                $result = [
                    'error' => $error,
                ];
            } elseif ($subscribeId = $subscribesEntity->add($subscribe)) {
                $result = [
                    'success' => true,
                ];
            } else {
                $result = [
                    'error' => 'Subscribe error',
                ];
            }
        } else {
            $result = [
                'error' => 'Empty data',
            ];
        }

        $response->setContent(json_encode($result), RESPONSE_JSON);
    }
}
