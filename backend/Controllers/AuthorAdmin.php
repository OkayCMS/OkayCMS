<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendAuthorsHelper;
use Okay\Admin\Helpers\BackendValidateHelper;
use Okay\Admin\Requests\BackendAuthorsRequest;
use Okay\Helpers\AuthorsHelper;

class AuthorAdmin extends IndexAdmin
{
    
    public function fetch(
        BackendAuthorsRequest  $authorsRequest,
        BackendValidateHelper $backendValidateHelper,
        BackendAuthorsHelper   $backendAuthorsHelper,
        AuthorsHelper $authorsHelper
    ) {
        if ($this->request->method('post')) {
            $author = $authorsRequest->postAuthor();

            if ($error = $backendValidateHelper->getAuthorsValidateError($author)) {
                $this->design->assign('message_error', $error);
            } else {
                // Бренд
                if (empty($author->id)) {
                    $preparedAuthor = $backendAuthorsHelper->prepareAdd($author);
                    $author->id     = $backendAuthorsHelper->add($preparedAuthor);

                    $this->postRedirectGet->storeMessageSuccess('added');
                    $this->postRedirectGet->storeNewEntityId($author->id);
                } else {
                    $preparedAuthor = $backendAuthorsHelper->prepareUpdate($author);
                    $backendAuthorsHelper->update($preparedAuthor->id, $preparedAuthor);

                    $this->postRedirectGet->storeMessageSuccess('updated');
                }

                // Картинка
                if ($authorsRequest->postDeleteImage()) {
                    $backendAuthorsHelper->deleteImage($author);
                }

                if ($image = $authorsRequest->fileImage()) {
                    $backendAuthorsHelper->uploadImage($image, $author);
                }

                $this->postRedirectGet->redirect();
            }
        } else {
            $authorId = $this->request->get('id', 'integer');
            $author   = $backendAuthorsHelper->getAuthor($authorId);
        }

        $this->design->assign('socials', $authorsHelper->getSocials($author));
        $this->design->assign('author', $author);
        $this->response->setContent($this->design->fetch('author.tpl'));
    }
    
}
