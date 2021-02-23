<?php


namespace Okay\Core;


class BackendPostRedirectGet
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request  = $request;
        $this->response = $response;
    }

    public function __destruct()
    {
        if ($this->request->method('get')) {
            $this->clearStates();
        }
    }

    private function clearStates()
    {
        unset($_SESSION['new_entity_id']);
        unset($_SESSION['message_error']);
        unset($_SESSION['message_success']);
    }

    public function redirect()
    {
        if (empty($_SESSION['new_entity_id'])) {
            $this->response->redirectTo($this->request->url());
        }

        $this->response->redirectTo($this->request->url(['id' => $_SESSION['new_entity_id']]));
    }

    public function storeNewEntityId($id)
    {
        $_SESSION['new_entity_id'] = $id;
    }

    public function storeMessageError($message)
    {
        $_SESSION['message_error'] = $message;
    }

    public function storeMessageSuccess($message)
    {
        $_SESSION['message_success'] = $message;
    }

    public function matchMessageError()
    {
        if (empty($_SESSION['message_error'])) {
            return false;
        }

        $messageError = $_SESSION['message_error'];
        unset($_SESSION['message_error']);
        return $messageError;
    }

    public function matchMessageSuccess()
    {
        if (empty($_SESSION['message_success'])) {
            return false;
        }

        $messageSuccess = $_SESSION['message_success'];
        unset($_SESSION['message_success']);
        return $messageSuccess;
    }
}