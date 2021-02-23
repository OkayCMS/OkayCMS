<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendPagesHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendPagesRequest;
use Okay\Entities\PagesEntity;

class PageAdmin extends IndexAdmin
{
    
    public function fetch(
        PagesEntity           $pagesEntity,
        BackendPagesRequest   $pagesRequest,
        BackendValidateHelper $backendValidateHelper,
        BackendPagesHelper    $backendPagesHelper
    ){
        /*Прием информации о страницу*/
        if ($this->request->method('POST')) {
            $page = $pagesRequest->postPage();

            if ($error = $backendValidateHelper->getPageValidateError($page)) {
                $this->design->assign('message_error', $error);
            } else {
                /*Добавление/Обновление страницы*/
                if (empty($page->id)) {
                    $page     = $backendPagesHelper->prepareAdd($page);
                    $page->id = $backendPagesHelper->add($page);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($page->id);
                } else {
                    // Запретим изменение системных url.
                    if ($error = $backendValidateHelper->getChangeSystemUrlValidateErrors($page)) {
                        $checkPage = $pagesEntity->get((int) $page->id);
                        $page->url = $checkPage->url;
                        $this->design->assign('message_error', $error);
                    }

                    $page = $backendPagesHelper->prepareUpdate($page);
                    $backendPagesHelper->update($page->id, $page);
                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                if (! $this->design->getVar('message_error')) {
                    $this->postRedirectGet->redirect();
                }
            }
        } else {
            $id = $pagesRequest->getId();
            if (!empty($id)) {
                $page = $backendPagesHelper->getPage((int) $id);
            } else {
                $page = new \stdClass;
                $page->visible = 1;
            }
        }
        
        $this->design->assign('page', $page);
        $this->response->setContent($this->design->fetch('page.tpl'));
    }
    
}
