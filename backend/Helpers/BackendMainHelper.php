<?php


namespace Okay\Admin\Helpers;


use Okay\Core\Design;
use Okay\Core\EntityFactory;
use Okay\Core\ManagerMenu;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Entities\CallbacksEntity;
use Okay\Entities\CommentsEntity;
use Okay\Entities\FeedbacksEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;

class BackendMainHelper
{

    private $entityFactory;
    private $managerMenu;
    private $design;
    
    public function __construct(EntityFactory $entityFactory, ManagerMenu $managerMenu, Design $design)
    {
        $this->entityFactory = $entityFactory;
        $this->managerMenu = $managerMenu;
        $this->design = $design;
    }

    public function evensCounters()
    {
        /** @var OrderStatusEntity $orderStatusesEntity */
        $orderStatusesEntity = $this->entityFactory->get(OrderStatusEntity::class);

        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var CommentsEntity $commentsEntity */
        $commentsEntity = $this->entityFactory->get(CommentsEntity::class);

        /** @var FeedbacksEntity $feedbacksEntity */
        $feedbacksEntity = $this->entityFactory->get(FeedbacksEntity::class);

        /** @var CallbacksEntity $callbacksEntity */
        $callbacksEntity = $this->entityFactory->get(CallbacksEntity::class);

        $newOrdersCounter = 0;
        if ($statusId = $orderStatusesEntity->order('position_asc')->cols(['id'])->find(['limit' => 1])) {
            $statusId = reset($statusId);

            $newOrdersCounter = $ordersEntity->count(['status_id' => $statusId]);
            $this->design->assign("new_orders_counter", $newOrdersCounter);
        }

        $newCommentsCounter = $commentsEntity->count(['approved'=>0]);
        $this->design->assign("new_comments_counter", $newCommentsCounter);

        $newFeedbacksCounter = $feedbacksEntity->count(['processed'=>0]);
        $this->design->assign("new_feedbacks_counter", $newFeedbacksCounter);

        $newCallbacksCounter = $callbacksEntity->count(['processed'=>0]);
        $this->design->assign("new_callbacks_counter", $newCallbacksCounter);

        $this->design->assign("all_counter", $newOrdersCounter+$newCommentsCounter+$newFeedbacksCounter+$newCallbacksCounter);
        
        $this->managerMenu->addCounter('left_orders_title', $newOrdersCounter);
        $this->managerMenu->addCounter('left_comments_title', $newCommentsCounter);
        $this->managerMenu->addCounter('left_feedbacks_title', $newFeedbacksCounter);
        $this->managerMenu->addCounter('left_callbacks_title', $newCallbacksCounter);

        return ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @return mixed|null|void
     */
    public function commonBeforeControllerProcedure()
    {
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    /**
     * @param $class
     * @return mixed|null|void
     */
    public function beforeControllerProcedure($class)
    {
        ExtenderFacade::execute([$class, 'fetch'], null, func_get_args());
    }
}