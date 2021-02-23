<?php


namespace Okay\Requests;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Phone;
use Okay\Core\Request;

class UserRequest
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return null|object
     * @throws \Exception
     */
    public function postRegisterUser()
    {
        $user = null;
        if ($this->request->post('register')) {
            $user = new \stdClass;
            $user->name         = $this->request->post('name');
            $user->last_name    = $this->request->post('last_name');
            $user->email        = $this->request->post('email');
            $user->phone        = Phone::toSave($this->request->post('phone'));
            $user->address      = $this->request->post('address');
            $user->password     = $this->request->post('password');
        }

        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }

    /**
     * @return null|object
     * @throws \Exception
     */
    public function postProfileUser()
    {
        $user = null;
        if ($this->request->post('user_save')) {
            $user = new \stdClass;
            $user->name         = $this->request->post('name');
            $user->last_name    = $this->request->post('last_name');
            $user->email        = $this->request->post('email');
            $user->phone        = Phone::toSave($this->request->post('phone'));
            $user->address      = $this->request->post('address');
            $user->preferred_delivery_id = $this->request->post('delivery_id', 'int');
            $user->preferred_payment_method_id = $this->request->post('payment_method_id', 'int');
        }

        return ExtenderFacade::execute(__METHOD__, $user, func_get_args());
    }

}