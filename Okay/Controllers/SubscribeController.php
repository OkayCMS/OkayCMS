<?php


namespace Okay\Controllers;


use Okay\Core\EntityFactory;
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
        Response $response
    ) {
        
        if (($subscribe = $commonRequest->postSubscribe()) !== null) {

            /** @var SubscribesEntity $subscribesEntity */
            $subscribesEntity = $entityFactory->get(SubscribesEntity::class);

            /*Валидация данных клиента*/
            if ($error = $validateHelper->getSubscribeValidateError($subscribe)) {
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
