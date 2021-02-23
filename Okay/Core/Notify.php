<?php


namespace Okay\Core;


use Okay\Core\Modules\Extender\ExtenderFacade;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\BlogEntity;
use Okay\Entities\CallbacksEntity;
use Okay\Entities\CommentsEntity;
use Okay\Entities\CurrenciesEntity;
use Okay\Entities\DeliveriesEntity;
use Okay\Entities\FeedbacksEntity;
use Okay\Entities\OrdersEntity;
use Okay\Entities\OrderStatusEntity;
use Okay\Entities\ProductsEntity;
use Okay\Entities\UsersEntity;
use Okay\Helpers\NotifyHelper;
use Okay\Helpers\OrdersHelper;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;

class Notify
{

    protected $PHPMailer;
    protected $settings;
    protected $languages;
    protected $entityFactory;
    protected $ordersHelper;
    protected $frontTemplateConfig;
    protected $design;
    protected $backendTranslations;
    protected $frontTranslations;
    protected $notifyHelper;
    protected $rootDir;
    protected $logger;
    
    public function __construct(
        Settings $settings,
        Languages $languages,
        EntityFactory $entityFactory,
        Design $design,
        FrontTemplateConfig $frontTemplateConfig,
        OrdersHelper $ordersHelper,
        BackendTranslations $backendTranslations,
        FrontTranslations $frontTranslations,
        PHPMailer $PHPMailer,
        LoggerInterface $logger,
        NotifyHelper $notifyHelper,
        $rootDir
    ) {
        $this->PHPMailer = $PHPMailer;
        $this->settings = $settings;
        $this->languages = $languages;
        $this->design = $design;
        $this->frontTemplateConfig = $frontTemplateConfig;
        $this->ordersHelper = $ordersHelper;
        $this->entityFactory = $entityFactory;
        $this->backendTranslations = $backendTranslations;
        $this->frontTranslations = $frontTranslations;
        $this->logger = $logger;
        $this->notifyHelper = $notifyHelper;
        $this->rootDir = $rootDir;
    }

    /* SMTP отправка емейла*/
    public function SMTP($to, $subject, $message, $from = '', $replyTo = '', $debug = 0)
    {
        ob_start();
        $this->PHPMailer->IsSMTP(); // telling the class to use SMTP
        $this->PHPMailer->Host       = $this->settings->get('smtp_server');
        $this->PHPMailer->SMTPDebug  = $debug;
        $this->PHPMailer->SMTPAuth   = true;
        $this->PHPMailer->CharSet    = 'utf-8';
        
        if ($this->settings->get('disable_validate_smtp_certificate')) {
            $this->PHPMailer->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];
        }
        
        $this->PHPMailer->Port       = $this->settings->get('smtp_port');
        if ($this->PHPMailer->Port == 465) {
            $this->PHPMailer->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            // Добавляем протокол, если не указали
            $this->PHPMailer->Host = (strpos($this->PHPMailer->Host, "ssl://") === false) ? "ssl://".$this->PHPMailer->Host : $this->PHPMailer->Host;
        } elseif ($this->PHPMailer->Port == 587) {
            $this->PHPMailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            // Добавляем протокол, если не указали
            $this->PHPMailer->Host = (strpos($this->PHPMailer->Host, "tls://") === false) ? "tls://".$this->PHPMailer->Host : $this->PHPMailer->Host;
        }
        $this->PHPMailer->Username   = $this->settings->get('smtp_user');
        $this->PHPMailer->Password   = $this->settings->get('smtp_pass');
        $this->PHPMailer->SetFrom($this->settings->get('smtp_user'), $this->settings->get('notify_from_name'));
        
        if (!empty($replyTo)) {
            $this->PHPMailer->AddReplyTo($replyTo, $replyTo);
        } else {
            $this->PHPMailer->AddReplyTo($this->settings->get('smtp_user'), $this->settings->get('notify_from_name'));
        }
        
        $this->PHPMailer->Subject = $subject;

        $this->PHPMailer->MsgHTML($message);
        $this->PHPMailer->addCustomHeader("MIME-Version: 1.0\n");

        $recipients = explode(',',$to);
        if (!empty($recipients)) {
            foreach ($recipients as $i=>$r) {
                $this->PHPMailer->AddAddress($r);
            }
        } else {
            $this->PHPMailer->AddAddress($to);
        }
        
        $this->PHPMailer = ExtenderFacade::execute(__FUNCTION__, $this->PHPMailer, func_get_args());
        
        $success = $this->PHPMailer->Send();

        $this->PHPMailer->clearAddresses();

        if ($success) {
            ob_end_clean();
            return true;
        }
        
        if ($debug !== 0) {
            $trace = nl2br(ob_get_contents());
            ob_end_clean();
            return $trace;
        }

        if ($this->PHPMailer->SMTPDebug != 0) {
            $this->logger->notice("Can`t send mail to '{$to}', ErrorInfo: {$this->PHPMailer->ErrorInfo}", ['subject' => $subject]);
        } else {
            $this->logger->notice("Can`t send mail to '{$to}', ErrorInfo: For view details should enable debug mode", ['subject' => $subject]);
        }
    }

    /*Отправка емейла*/
    public function email($to, $subject, $message, $from = '', $replyTo = '')
    {
        $headers = "MIME-Version: 1.0\n" ;
        $headers .= "Content-type: text/html; charset=utf-8; \r\n";
        $headers .= "From: $from\r\n";
        if(!empty($replyTo)) {
            $headers .= "reply-to: $replyTo\r\n";
        }
        
        $subject = "=?utf-8?B?".base64_encode($subject)."?=";

        if ($this->settings->get('use_smtp')) {
            $this->SMTP($to, $subject, $message, $from, $replyTo);
        } else {
            mail($to, $subject, $message, $headers);
        }
    }

    /*Отправка емейла клиенту о заказе*/
    public function emailOrderUser($orderId, $debug = false)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);
        
        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
        
        /** @var OrderStatusEntity $ordersStatusEntity */
        $ordersStatusEntity = $this->entityFactory->get(OrderStatusEntity::class);
        
        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
        
        if (!($order = $ordersEntity->get(intval($orderId))) || empty($order->email)) {
            return false;
        }

        // если не нужно отправлять письмо, вызываем хелпер, может нужно сделать какую-то альтернативу
        if (!$this->notifyHelper->needSendEmailOrderUser($order)) {
            return $this->notifyHelper->notSendEmailOrderUser($order);
        }
        
        /*lang_modify...*/
        if (!empty($order->lang_id)) {
            $currentLangId = $this->languages->getLangId();
            $this->languages->setLangId($order->lang_id);
            
            // Переинициализируем на новый язык
            $this->frontTranslations->init();

            $currencies = $currenciesEntity->find(['enabled'=>1]);
            // Берем валюту из сессии
            if (isset($_SESSION['currency_id'])) {
                $currency = $currenciesEntity->get((int)$_SESSION['currency_id']);
            } else {
                $currency = reset($currencies);
            }
            
            $this->design->assign("currency", $currency);
            $this->settings->initSettings();
            $this->design->assign('settings', $this->settings);
            $this->design->assign('lang', $this->frontTranslations);
        }
        /*/lang_modify...*/
        
        $purchases = $this->ordersHelper->getOrderPurchasesList($order->id);
        $this->design->assign('purchases', $purchases);

        // Скидки
        $discounts = $this->ordersHelper->getDiscounts($order->id);
        $this->design->assign('discounts', $discounts);
        
        // Способ доставки
        $delivery = $deliveriesEntity->get($order->delivery_id);
        $this->design->assign('delivery', $delivery);
        
        $this->design->assign('order', $order);
        if (!empty($order->status_id)) {
            $orderStatuses = $ordersStatusEntity->get(intval($order->status_id));
            $this->design->assign('order_status', $orderStatuses);
        }
        
        // Отправляем письмо
        // Если в шаблон не передавалась валюта, передадим
        if ($this->design->smarty->getTemplateVars('currency') === null) {
            $this->design->assign('currency', current($currenciesEntity->find(['enabled'=>1])));
        }

        ExtenderFacade::execute(__METHOD__, null, func_get_args()); // todo удалить этот экстендер в будущих версиях
        $this->notifyHelper->finalEmailOrderUser($order);
        
        $emailTemplate = $this->design->fetch($this->rootDir.'design/'.$this->frontTemplateConfig->getTheme().'/html/email/email_order.tpl');
        $subject = $this->design->getVar('subject');
        
        if ($debug === false) {
            $from = ($this->settings->get('notify_from_name') ? $this->settings->get('notify_from_name')." <".$this->settings->get('notify_from_email').">" : $this->settings->get('notify_from_email'));
            $this->email($order->email, $subject, $emailTemplate, $from);
        }
        
        /*lang_modify...*/
        if (!empty($currentLangId)) {
            $this->languages->setLangId($currentLangId);

            // Вернем переводы на предыдущий язык
            $this->frontTranslations->init();
            
            $currencies = $currenciesEntity->find(['enabled'=>1]);
            // Берем валюту из сессии
            if (isset($_SESSION['currency_id'])) {
                $currency = $currenciesEntity->get((int)$_SESSION['currency_id']);
            } else {
                $currency = reset($currencies);
            }

            $this->design->assign("currency", $currency);
            $this->settings->initSettings();
            $this->design->assign('settings', $this->settings);
        }
        /*/lang_modify...*/

        if ($debug === true) {
            $this->design->assign('meta_title', $subject);
            return $emailTemplate;
        }
        
        return true;
    }

    /*Отправка емейла о заказе администратору*/
    public function emailOrderAdmin($orderId, $debug = false)
    {
        /** @var OrdersEntity $ordersEntity */
        $ordersEntity = $this->entityFactory->get(OrdersEntity::class);

        /** @var DeliveriesEntity $deliveriesEntity */
        $deliveriesEntity = $this->entityFactory->get(DeliveriesEntity::class);
        
        /** @var UsersEntity $usersEntity */
        $usersEntity = $this->entityFactory->get(UsersEntity::class);

        /** @var OrderStatusEntity $ordersStatusEntity */
        $ordersStatusEntity = $this->entityFactory->get(OrderStatusEntity::class);

        /** @var CurrenciesEntity $currenciesEntity */
        $currenciesEntity = $this->entityFactory->get(CurrenciesEntity::class);
        
        if (!($order = $ordersEntity->get(intval($orderId)))) {
            return false;
        }

        // если не нужно отправлять письмо, вызываем хелпер, может нужно сделать какую-то альтернативу
        if (!$this->notifyHelper->needSendEmailOrderAdmin($order)) {
            return $this->notifyHelper->notSendEmailOrderAdmin($order);
        }
        
        $purchases = $this->ordersHelper->getOrderPurchasesList($order->id);
        $this->design->assign('purchases', $purchases);

        // Скидки
        $discounts = $this->ordersHelper->getDiscounts($order->id);
        $this->design->assign('discounts', $discounts);
        
        // Способ доставки
        $delivery = $deliveriesEntity->get($order->delivery_id);
        $this->design->assign('delivery', $delivery);
        
        // Пользователь
        $user = $usersEntity->get(intval($order->user_id));
        $this->design->assign('user', $user);
        
        $this->design->assign('order', $order);

        if (!empty($order->status_id)) {
            $orderStatuses = $ordersStatusEntity->get(intval($order->status_id));
            $this->design->assign('order_status', $orderStatuses);
        }
        
        // В основной валюте
        $this->design->assign('main_currency', $currenciesEntity->getMainCurrency());

        // Перевод админки
        $this->backendTranslations->initTranslations($this->settings->get('email_lang'));
        $this->design->assign('btr', $this->backendTranslations);

        $this->notifyHelper->finalEmailOrderAdmin($order);

        // Отправляем письмо
        $emailTemplate = $this->design->fetch($this->rootDir.'backend/design/html/email/email_order_admin.tpl');

        $subject = $this->design->getVar('subject');
        
        if ($debug === true) {
            $this->design->assign('meta_title', $subject);
            return $emailTemplate;
        } else {
            $replyTo = (!empty($order->email) ? $order->email : null);
            $this->email($this->settings->get('order_email'), $subject, $emailTemplate, $this->settings->get('notify_from_email'), $replyTo);
        }
        return true;
    }

    /*Отправка емейла о комментарии администратору*/
    public function emailCommentAdmin($commentId, $debug = false)
    {
        /** @var CommentsEntity $commentsEntity */
        $commentsEntity = $this->entityFactory->get(CommentsEntity::class);
        
        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);
        
        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);
        
        if (!($comment = $commentsEntity->get(intval($commentId)))) {
            return false;
        }
        
        if ($comment->type == 'product') {
            $comment->product = $productsEntity->get(intval($comment->object_id));
        } elseif ($comment->type == 'blog') {
            $comment->post = $blogEntity->get(intval($comment->object_id));
        } elseif ($comment->type == 'news') {
            $comment->post = $blogEntity->get(intval($comment->object_id));
        }
        
        $this->design->assign('comment', $comment);
        
        // Перевод админки
        $this->backendTranslations->initTranslations($this->settings->get('email_lang'));
        $this->design->assign('btr', $this->backendTranslations);
        
        // Отправляем письмо
        $emailTemplate = $this->design->fetch($this->rootDir.'backend/design/html/email/email_comment_admin.tpl');
        $subject = $this->design->getVar('subject');
        
        if ($debug === true) {
            $this->design->assign('meta_title', $subject);
            return $emailTemplate;
        } else {
            $replyTo = (!empty($comment->email) ? $comment->email : null);
            $this->email($this->settings->get('comment_email'), $subject, $emailTemplate, $this->settings->get('notify_from_email'), $replyTo);
        }
        return true;
    }

    /*Отправка емейла администратору о заказе обратного звонка*/
    public function emailCallbackAdmin($callbackId, $debug = false)
    {
        /** @var CallbacksEntity $callbacksEntity */
        $callbacksEntity = $this->entityFactory->get(CallbacksEntity::class);
        
        if (!($callback = $callbacksEntity->get(intval($callbackId)))) {
            return false;
        }
        $this->design->assign('callback', $callback);

        // Перевод админки
        $this->backendTranslations->initTranslations($this->settings->get('email_lang'));
        $this->design->assign('btr', $this->backendTranslations);
        
        // Отправляем письмо
        $emailTemplate = $this->design->fetch($this->rootDir.'backend/design/html/email/email_callback_admin.tpl');
        $subject = $this->design->getVar('subject');

        if ($debug === true) {
            $this->design->assign('meta_title', $subject);
            return $emailTemplate;
        } else {
            $this->email($this->settings->get('comment_email'), $subject, $emailTemplate, $this->settings->get('notify_from_email'));
        }
        return true;
    }

    /*Отправка емейла с ответом на комментарий клиенту*/
    public function emailCommentAnswerToUser($commentId)
    {

        /** @var CommentsEntity $commentsEntity */
        $commentsEntity = $this->entityFactory->get(CommentsEntity::class);

        /** @var ProductsEntity $productsEntity */
        $productsEntity = $this->entityFactory->get(ProductsEntity::class);

        /** @var BlogEntity $blogEntity */
        $blogEntity = $this->entityFactory->get(BlogEntity::class);
        
        if(!($comment = $commentsEntity->get(intval($commentId)))
                || !($parentComment = $commentsEntity->get(intval($comment->parent_id)))
                || !$parentComment->email) {
            return false;
        }

        $templateDir = $this->design->getTemplatesDir();
        $compiledDir = $this->design->getCompiledDir();
        $this->design->setTemplatesDir('design/'.$this->frontTemplateConfig->getTheme().'/html');
        $this->design->setCompiledDir('compiled/' . $this->frontTemplateConfig->getTheme());
        
        /*lang_modify...*/
        if (!empty($parentComment->lang_id)) {
            $currentLangId = $this->languages->getLangId();
            $this->languages->setLangId($parentComment->lang_id);

            // Переинициализируем на новый язык
            $this->frontTranslations->init();

            $this->settings->initSettings();
            $this->design->assign('settings', $this->settings);
            $this->design->assign('lang', $this->frontTranslations);
        }
        /*/lang_modify...*/

        if ($parentComment->type == 'product') {
            $parentComment->product = $productsEntity->get(intval($parentComment->object_id));
        } elseif ($parentComment->type == 'blog') {
            $parentComment->post = $blogEntity->get(intval($parentComment->object_id));
        } elseif ($parentComment->type == 'news') {
            $parentComment->post = $blogEntity->get(intval($parentComment->object_id));
        }

        $this->design->assign('comment', $comment);
        $this->design->assign('parent_comment', $parentComment);

        // Отправляем письмо
        $emailTemplate = $this->design->fetch($this->rootDir.'design/'.$this->frontTemplateConfig->getTheme().'/html/email/email_comment_answer_to_user.tpl');
        $subject = $this->design->getVar('subject');

        $this->email($parentComment->email, $subject, $emailTemplate, $this->settings->get('notify_from_email'));

        $this->design->setTemplatesDir($templateDir);
        $this->design->setCompiledDir($compiledDir);
        
        /*lang_modify...*/
        if (!empty($currentLangId)) {
            $this->languages->setLangId($currentLangId);
            
            // Вернем переводы на предыдущий язык
            $this->frontTranslations->init();
            
            $this->settings->initSettings();
            $this->design->assign('settings', $this->settings);
        }
        /*/lang_modify...*/

        return true;
    }

    /*Отправка емейла о восстановлении пароля клиенту*/
    public function emailPasswordRemind($userId, $code)
    {
        /** @var UsersEntity $usersEntity */
        $usersEntity = $this->entityFactory->get(UsersEntity::class);

        if(!($user = $usersEntity->get(intval($userId)))) {
            return false;
        }

        $currentLangId = $this->languages->getLangId();

        $this->settings->initSettings();
        $this->design->assign('settings', $this->settings);
        $this->design->assign('lang', $this->frontTranslations);
        
        $this->design->assign('user', $user);
        $this->design->assign('code', $code);
        
        // Отправляем письмо
        $email_template = $this->design->fetch($this->rootDir.'design/'.$this->frontTemplateConfig->getTheme().'/html/email/email_password_remind.tpl');
        $subject = $this->design->getVar('subject');
        $from = ($this->settings->notify_from_name ? $this->settings->notify_from_name." <".$this->settings->notify_from_email.">" : $this->settings->notify_from_email);
        $this->email($user->email, $subject, $email_template, $from);
        
        $this->design->smarty->clearAssign('user');
        $this->design->smarty->clearAssign('code');

        return true;
    }

    /*Отправка емейла о заявке с формы обратной связи администратору*/
    public function emailFeedbackAdmin($feedbackId, $debug = false)
    {

        /** @var UsersEntity $feedbackEntity */
        $feedbackEntity = $this->entityFactory->get(FeedbacksEntity::class);
        
        if (!($feedback = $feedbackEntity->get(intval($feedbackId)))) {
            return false;
        }
        
        $this->design->assign('feedback', $feedback);

        // Перевод админки
        $this->backendTranslations->initTranslations($this->settings->get('email_lang'));
        $this->design->assign('btr', $this->backendTranslations);
        
        // Отправляем письмо
        $emailTemplate = $this->design->fetch($this->rootDir.'backend/design/html/email/email_feedback_admin.tpl');
        $subject = $this->design->getVar('subject');
        

        if ($debug === true) {
            $this->design->assign('meta_title', $subject);
            return $emailTemplate;
        } else {
            $replyTo = (!empty($feedback->email) ? $feedback->email : null);
            $this->email($this->settings->get('comment_email'), $subject, $emailTemplate, $this->settings->get('notify_from_email'), $replyTo);
        }
        
        return true;
    }

    /*Отправка емейла с ответом на заявку с формы обратной связи клиенту*/
    public function emailFeedbackAnswerFoUser($comment_id,$text)
    {

        /** @var FeedbacksEntity $feedbackEntity */
        $feedbackEntity = $this->entityFactory->get(FeedbacksEntity::class);

        if(!($feedback = $feedbackEntity->get(intval($comment_id)))) {
            return false;
        }

        $templateDir = $this->design->getTemplatesDir();
        $compiledDir = $this->design->getCompiledDir();
        $this->design->setTemplatesDir('design/'.$this->frontTemplateConfig->getTheme().'/html');
        $this->design->setCompiledDir('compiled/' . $this->frontTemplateConfig->getTheme());
        
        /*lang_modify...*/
        if (!empty($feedback->lang_id)) {
            $currentLangId = $this->languages->getLangId();
            $this->languages->setLangId($feedback->lang_id);
            
            $this->frontTranslations->init();

            $this->design->assign('lang', $this->frontTranslations);
        }
        /*/lang_modify...*/

        $this->design->assign('feedback', $feedback);
        $this->design->assign('text', $text);

        // Отправляем письмо
        $email_template = $this->design->fetch($this->rootDir.'design/'.$this->frontTemplateConfig->getTheme().'/html/email/email_feedback_answer_to_user.tpl');
        $subject = $this->design->getVar('subject');
        $from = ($this->settings->get('notify_from_name') ? $this->settings->get('notify_from_name')." <".$this->settings->get('notify_from_email').">" : $this->settings->get('notify_from_email'));
        $this->email($feedback->email, $subject, $email_template, $from, $from);

        $this->design->setTemplatesDir($templateDir);
        $this->design->setCompiledDir($compiledDir);
        
        /*lang_modify...*/
        if (!empty($currentLangId)) {
            $this->languages->setLangId($currentLangId);
        }
        /*/lang_modify...*/
        
        return true;
    }

    /*Отправка емейла на восстановление пароля администратора*/
    public function passwordRecoveryAdmin($email, $code)
    {
        if (empty($email) || empty($code)){
            return false;
        }

        // Перевод админки
        $this->backendTranslations->initTranslations($this->settings->get('email_lang'));
        $this->design->assign('btr', $this->backendTranslations);
        
        $this->design->assign('code',$code);
        $this->design->assign('recovery_url', Request::getRootUrl() . '/backend/index.php?controller=AuthAdmin&code='.$code);
        $email_template = $this->design->fetch($this->rootDir.'backend/design/html/email/email_admin_recovery.tpl');
        $subject = $this->design->getVar('subject');
        $this->email($email, $subject, $email_template, $this->settings->get('notify_from_name'));
        
        return true;
    }
    
}
