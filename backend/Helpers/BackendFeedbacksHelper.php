<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Core\Settings;
use Okay\Entities\FeedbacksEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendFeedbacksHelper
{
    /**
     * @var FeedbacksEntity
     */
    private $feedbacksEntity;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Settings
     */
    private $settings;


    public function __construct(
        EntityFactory $entityFactory,
        Request       $request,
        Settings      $settings
    ){
        $this->feedbacksEntity = $entityFactory->get(FeedbacksEntity::class);
        $this->request         = $request;
        $this->settings        = $settings;
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['feedback_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['feedback_num_admin'])) {
            $filter['limit'] = $_SESSION['feedback_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

        $filter['current_limit'] = $filter['limit'];

        $filter['has_parent'] = false;

        // Сортировка по статусу
        $status = $this->request->get('status', 'string');
        if ($status == 'processed') {
            $filter['processed'] = 1;
        } elseif ($status == 'unprocessed') {
            $filter['processed'] = 0;
        }

        // Поиск
        $keyword = $this->request->get('keyword');
        if (!empty($keyword)) {
            $filter['keyword'] = $keyword;
        }

        $feedbacksCount = $this->feedbacksEntity->count($filter);
        // Показать все страницы сразу
        if($this->request->get('page') == 'all') {
            $filter['limit'] = $feedbacksCount;
        }

        $filter['sort'] = 'new_first';

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function getFilterStatus($filter)
    {
        $status = '';

        if (! isset($filter['processed'])) {
            return ExtenderFacade::execute(__METHOD__, $status, func_get_args());
        }

        if ($filter['processed'] === 1) {
            $status = 'processed';
        } elseif ($filter['processed'] === 0) {
            $status = 'unprocessed';
        }

        return ExtenderFacade::execute(__METHOD__, $status, func_get_args());
    }

    public function count($filter = [])
    {
        $count = $this->feedbacksEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $count, func_get_args());
    }

    public function selectAnswers($feedbacks)
    {
        $feedbackIds = [];
        foreach ($feedbacks as $feedback) {
            $feedbackIds[] = $feedback->id;
        }

        $adminAnswers = [];
        if (!empty($feedbackIds)) {
            foreach ($this->feedbacksEntity->find(['parent_id' => $feedbackIds]) as $f) {
                $adminAnswers[$f->parent_id][] = $f;
            }
        }

        return ExtenderFacade::execute(__METHOD__, $adminAnswers, func_get_args());
    }

    public function findFeedbacks($filter = [])
    {
        $feedbacks = $this->feedbacksEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $feedbacks, func_get_args());
    }

    public function delete($ids)
    {
        $result = $this->feedbacksEntity->delete($ids);
        return ExtenderFacade::execute(__METHOD__, $result, func_get_args());
    }

    public function prepareAddAnswer($feedback)
    {
        $feedback->is_admin  = 1;
        $feedback->email     = $this->settings->notify_from_email;
        $feedback->name      = $this->settings->notify_from_name;
        $feedback->processed = 1;
        $feedback->ip        = $_SERVER['REMOTE_ADDR'];
        $feedback->lang_id   = $_SESSION['admin_lang_id'];

        return ExtenderFacade::execute(__METHOD__, $feedback, func_get_args());
    }

    public function addAnswer($feedback)
    {
        if (empty($this->feedbacksEntity->get((int) $feedback->parent_id))) {
            return ExtenderFacade::execute(__METHOD__, false, func_get_args());
        }

        $insertId = $this->feedbacksEntity->add($feedback);
        return ExtenderFacade::execute(__METHOD__, $insertId, func_get_args());
    }
}