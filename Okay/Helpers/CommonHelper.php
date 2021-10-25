<?php


namespace Okay\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Notify;
use Okay\Core\Response;
use Okay\Core\Router;
use Okay\Entities\CallbacksEntity;
use Okay\Entities\SubscribesEntity;
use Okay\Entities\UsersEntity;
use Okay\Requests\CommonRequest;

class CommonHelper
{
    private $validateHelper;
    private $notify;
    private $design;
    private $commonRequest;
    private $entityFactory;
    private $userHelper;

    public function __construct(
        ValidateHelper $validateHelper,
        Notify $notify,
        Design $design,
        CommonRequest $commonRequest,
        EntityFactory $entityFactory,
        UserHelper $userHelper
    ) {
        $this->validateHelper = $validateHelper;
        $this->notify = $notify;
        $this->design = $design;
        $this->commonRequest = $commonRequest;
        $this->entityFactory = $entityFactory;
        $this->userHelper = $userHelper;
    }
    
    public function rootPostProcedure()
    {
        if (($callback = $this->commonRequest->postCallback()) !== null) {

            /** @var CallbacksEntity $callbacksEntity */
            $callbacksEntity = $this->entityFactory->get(CallbacksEntity::class);

            /*Валидация данных клиента*/
            if ($error = $this->validateHelper->getCallbackValidateError($callback)) {
                $this->design->assign('call_error', $error, true);
            } elseif ($callbackId = $callbacksEntity->add($callback)) {
                $this->design->assign('call_sent', true, true);
                // Отправляем email
                $this->notify->emailCallbackAdmin($callbackId);
            } else {
                $this->design->assign('call_error', 'unknown error', true);
            }
        }

        if (($subscribe = $this->commonRequest->postSubscribe()) !== null) {

            /** @var SubscribesEntity $subscribesEntity */
            $subscribesEntity = $this->entityFactory->get(SubscribesEntity::class);

            /*Валидация данных клиента*/
            if ($error = $this->validateHelper->getSubscribeValidateError($subscribe)) {
                $this->design->assign('subscribe_error', $error, true);
            } elseif ($subscribeId = $subscribesEntity->add($subscribe)) {
                $this->design->assign('subscribe_success', true, true);
            }
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }
}