<?php


namespace Okay\Helpers;


use Okay\Core\EntityFactory;
use Okay\Core\FrontTranslations;
use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\Request;
use Okay\Core\ServiceLocator;
use Okay\Core\Settings;
use Okay\Core\Validator;
use Okay\Entities\SubscribesEntity;
use Okay\Entities\UsersEntity;

class ValidateHelper
{

    private $validator;
    private $settings;
    private $request;
    private $frontTranslations;

    public function __construct(
        Validator $validator,
        Settings $settings,
        Request $request,
        FrontTranslations $frontTranslations
    ) {
        $this->validator = $validator;
        $this->settings = $settings;
        $this->request = $request;
        $this->frontTranslations = $frontTranslations;
    }

    public function getUserError($user, $currentUserId)
    {
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        /** @var UsersEntity $usersEntity */
        $usersEntity = $entityFactory->get(UsersEntity::class);

        $error = null;
        /*Валидация данных клиента*/
        if (($u = $usersEntity->get((string)$user->email)) && $u->id != $currentUserId) {
            $error = 'user_exists';
        } elseif (!$this->validator->isName($user->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isEmail($user->email, true)) {
            $error = 'empty_email';
        } elseif (!$this->validator->isPhone($user->phone)) {
            $error = 'empty_phone';
        } elseif (!$this->validator->isAddress($user->address)) {
            $error = 'empty_address';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
    
    public function getUserRegisterError($user)
    {
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        /** @var UsersEntity $usersEntity */
        $usersEntity = $entityFactory->get(UsersEntity::class);

        $captchaCode =  $this->request->post('captcha_code', 'string');
        $error = null;
        $userExists = $usersEntity->count(['email'=>$user->email]);

        /*Валидация данных клиента*/
        if ($userExists) {
            $error = 'user_exists';
        } elseif (!$this->validator->isName($user->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isEmail($user->email, true)) {
            $error = 'empty_email';
        } elseif (!$this->validator->isPhone($user->phone)) {
            $error = 'empty_phone';
        } elseif (!$this->validator->isAddress($user->address)) {
            $error = 'empty_address';
        } elseif (empty($user->password)) {
            $error = 'empty_password';
        } elseif ($this->settings->get('captcha_register') && !$this->validator->verifyCaptcha('captcha_register', $captchaCode)) {
            $error = 'captcha';
        }
        
        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getUserLoginError($email, $password)
    {
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        /** @var UsersEntity $usersEntity */
        $usersEntity = $entityFactory->get(UsersEntity::class);
        
        $error = null;
        
        $userId = $usersEntity->checkPassword($email, $password);

        /*Валидация данных клиента*//*todo мож разделить проверку*/
        if (!$this->validator->isEmail($email, true) || empty($password) || !$userId) {
            $error = 'login_incorrect';
        } 

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
    
    public function getFeedbackValidateError($feedback)
    {
        $captchaCode =  $this->request->post('captcha_code', 'string');
        
        $error = null;
        if (!$this->validator->isName($feedback->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isEmail($feedback->email, true)) {
            $error = 'empty_email';
        } elseif (!$this->validator->isComment($feedback->message, true)) {
            $error = 'empty_text';
        } elseif ($this->settings->get('captcha_feedback') && !$this->validator->verifyCaptcha('captcha_feedback', $captchaCode)) {
            $error = 'captcha';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }

    public function getCartValidateError($order)
    {
        $captchaCode =  $this->request->post('captcha_code', 'string');
        
        $error = null;
        if (!$this->validator->isName($order->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isEmail($order->email, true)) {
            $error = 'empty_email';
        } elseif (!$this->validator->isPhone($order->phone)) {
            $error = 'empty_phone';
        } elseif (!$this->validator->isAddress($order->address)) {
            $error = 'empty_address';
        } elseif (!$this->validator->isComment($order->comment)) {
            $error = 'empty_comment';
        } elseif ($this->settings->get('captcha_cart') && !$this->validator->verifyCaptcha('captcha_cart', $captchaCode)) {
            $error = 'captcha';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
    
    public function getCallbackValidateError($callback)
    {
        $captchaCode =  $this->request->post('captcha_code', 'string');
        
        $error = null;
        if (!$this->validator->isName($callback->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isPhone($callback->phone, true)) {
            $error = 'empty_phone';
        } elseif (!$this->validator->isComment($callback->message)) {
            $error = 'empty_comment';
        } elseif ($this->settings->get('captcha_callback') && !$this->validator->verifyCaptcha('captcha_callback', $captchaCode)) {
            $error = 'captcha';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
    
    public function getCommentValidateError($comment)
    {
        $captchaCode =  $this->request->post('captcha_code', 'string');

        $error = null;
        if (!$this->validator->isName($comment->name, true)) {
            $error = 'empty_name';
        } elseif (!$this->validator->isComment($comment->text, true)) {
            $error = 'empty_comment';
        } elseif (!$this->validator->isEmail($comment->email)) {
            $error = 'empty_email';
        } elseif ($this->settings->get('captcha_comment') && !$this->validator->verifyCaptcha('captcha_comment', $captchaCode)) {
            $error = 'captcha';
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
    
    public function getSubscribeValidateError($subscribe)
    {
        $SL = ServiceLocator::getInstance();
        $entityFactory = $SL->getService(EntityFactory::class);
        /** @var SubscribesEntity $subscribesEntity */
        $subscribesEntity = $entityFactory->get(SubscribesEntity::class);
        
        $error = null;
        if (!$this->validator->isEmail($subscribe->email, true)) {
            $error = $this->frontTranslations->getTranslation('form_enter_email');
        } elseif ($subscribesEntity->count(['email' => $subscribe->email]) > 0) {
            $error = $this->frontTranslations->getTranslation('index_subscribe_already');
        }

        return ExtenderFacade::execute(__METHOD__, $error, func_get_args());
    }
}