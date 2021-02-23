<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendCallbacksHelper;
use Okay\Admin\Requests\BackendCallbacksRequest;

class CallbacksAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendCallbacksRequest $callbacksRequest,
        BackendCallbacksHelper  $backendCallbacksHelper
    ){
        // Обработка действий
        if($this->request->method('post')) {
            // Действия с выбранными
            $ids = $callbacksRequest->postCheck();
            switch($callbacksRequest->postAction()) {
                case 'delete': {
                    $backendCallbacksHelper->delete($ids);
                    break;
                }
                case 'processed': {
                    $backendCallbacksHelper->processed($ids);
                    break;
                }
                case 'unprocessed': {
                    $backendCallbacksHelper->unprocessed($ids);
                    break;
                }
            }
        }

        $filter = $backendCallbacksHelper->buildFilter();
        $this->design->assign('current_limit', $filter['limit']);

        if (isset($filter['status'])) {
            $this->design->assign('status', $filter['status']);
        }

        if (isset($filter['keyword'])) {
            $this->design->assign('keyword',       $filter['keyword']);
        }

        $callbacksCount = $backendCallbacksHelper->countCallbacks($filter);
        $callbacks      = $backendCallbacksHelper->findCallbacks($filter);

        $this->design->assign('pages_count',     ceil($callbacksCount/$filter['limit']));
        $this->design->assign('current_page',    $filter['page']);
        $this->design->assign('callbacks',       $callbacks);
        $this->design->assign('callbacks_count', $callbacksCount);
        $this->response->setContent($this->design->fetch('callbacks.tpl'));
    }
    
}
