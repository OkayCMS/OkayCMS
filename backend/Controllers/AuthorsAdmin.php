<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendAuthorsHelper;
use Okay\Admin\Requests\BackendAuthorsRequest;

class AuthorsAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendAuthorsHelper  $backendAuthorsHelper,
        BackendAuthorsRequest $authorsRequest
    ) {
        $filter = $backendAuthorsHelper->buildFilter();
        $this->design->assign('current_limit', $filter['limit']);

        // Обработка действий
        if ($this->request->method('post')) {
            // Сортировка
            $positions = $authorsRequest->postPositions();
            $backendAuthorsHelper->sortPositions($positions);

            // Действия с выбранными
            $ids = $authorsRequest->postCheck();
            switch ($authorsRequest->postAction()) {
                case 'enable': {
                    $backendAuthorsHelper->enable($ids);
                    break;
                }
                case 'disable': {
                    $backendAuthorsHelper->disable($ids);
                    break;
                }
                case 'delete': {
                    $backendAuthorsHelper->delete($ids);
                    break;
                }
                case 'duplicate': {
                    $backendAuthorsHelper->duplicate($ids);
                    break;
                }
            }
        }

        $authorsCount               = $backendAuthorsHelper->countAuthors($filter);
        list($filter, $pagesCount)  = $backendAuthorsHelper->makePagination($authorsCount, $filter);
        $authors                    = $backendAuthorsHelper->findAuthors($filter);

        $this->design->assign('authors_count', $authorsCount);
        $this->design->assign('pages_count',   $pagesCount);
        $this->design->assign('current_page',  $filter['page']);
        $this->design->assign('authors',       $authors);
        $this->response->setContent($this->design->fetch('authors.tpl'));
    }
}
