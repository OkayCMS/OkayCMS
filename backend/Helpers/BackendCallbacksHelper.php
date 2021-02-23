<?php


namespace Okay\Admin\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\Request;
use Okay\Entities\CallbacksEntity;
use Okay\Core\Modules\Extender\ExtenderFacade;

class BackendCallbacksHelper
{
    /**
     * @var CallbacksEntity
     */
    private $callbacksEntity;

    /**
     * @var Request
     */
    private $request;

    public function __construct(
        EntityFactory $entityFactory,
        Request       $request
    ) {
        $this->callbacksEntity = $entityFactory->get(CallbacksEntity::class);
        $this->request         = $request;
    }

    public function delete($ids)
    {
        $this->callbacksEntity->delete($ids);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function processed($ids)
    {
        $this->callbacksEntity->update($ids, ['processed'=>1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function unprocessed($ids)
    {
        $this->callbacksEntity->update($ids, ['unprocessed'=>1]);
        ExtenderFacade::execute(__METHOD__, null, func_get_args());
    }

    public function buildFilter()
    {
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));

        if ($filter['limit'] = $this->request->get('limit', 'integer')) {
            $filter['limit'] = max(5, $filter['limit']);
            $filter['limit'] = min(100, $filter['limit']);
            $_SESSION['callback_num_admin'] = $filter['limit'];
        } elseif (!empty($_SESSION['callback_num_admin'])) {
            $filter['limit'] = $_SESSION['callback_num_admin'];
        } else {
            $filter['limit'] = 25;
        }

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

        $callbacksCount = $this->callbacksEntity->count($filter);
        // Показать все страницы сразу
        if($this->request->get('page') == 'all') {
            $filter['limit'] = $callbacksCount;
        }

        return ExtenderFacade::execute(__METHOD__, $filter, func_get_args());
    }

    public function findCallbacks($filter)
    {
        $callbacks = $this->callbacksEntity->find($filter);
        return ExtenderFacade::execute(__METHOD__, $callbacks, func_get_args());
    }

    public function countCallbacks($filter)
    {
        unset($filter['limit']);

        $callbacksCount = $this->callbacksEntity->count($filter);
        return ExtenderFacade::execute(__METHOD__, $callbacksCount, func_get_args());
    }
}