<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendPagesHelper;
use Okay\Admin\Requests\BackendPagesRequest;

class PagesAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendPagesRequest $pagesRequest,
        BackendPagesHelper  $backendPagesHelper
    ){
        // Обработка действий
        if ($this->request->method('post')) {
            $positions = $pagesRequest->postPositions();
            $backendPagesHelper->sortPositions($positions);

            // Действия с выбранными
            $ids    = $pagesRequest->postCheck();
            $action = $pagesRequest->postAction();
            if (is_array($ids)) {
                switch ($action) {
                    case 'disable': {
                        $backendPagesHelper->disable($ids);
                        break;
                    }
                    case 'enable': {
                        $backendPagesHelper->enable($ids);
                        break;
                    }
                    case 'delete': {
                        if (!$backendPagesHelper->delete($ids)) {
                            $this->design->assign('message_error', 'url_system');
                        }
                        break;
                    }
                    case 'duplicate': {
                        $backendPagesHelper->duplicate($ids);
                        break;
                    }
                }
            }
        }
        
        // Отображение
        $pages = $backendPagesHelper->findPages();
        
        $this->design->assign('pages', $pages);
        $this->response->setContent($this->design->fetch('pages.tpl'));
    }
    
}
