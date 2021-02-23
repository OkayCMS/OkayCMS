<?php


namespace Okay\Core;


use Okay\Core\Settings;
use Okay\Core\ServiceLocator;

class Validator
{

    private $denied = [
        "<script", "</script",
        "<iframe", "</iframe",

    ];

    private $settings;
    private $recaptcha;

    public function __construct(Settings $settings, Recaptcha $recaptcha)
    {
        $this->settings = $settings;
        $this->recaptcha = $recaptcha;
    }

    /**
     * @param string $email
     * @param bool $is_required
     * if $email is empty AND !$is_required return true
     * @return bool
     */
    public function isEmail($email = "", $is_required = false)
    {
        // general
        if (!$this->isSafe($email)) {
            return false;
        }
        if (empty($email)) {
            return !$is_required;
        }
        // for email
        if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/ui", $email)) {
            return false;
        }
        return true;
    }

    /**
     * @param string $phone
     * @param bool $is_required
     * if $phone is empty AND !$is_required return true
     * @return bool
     */
    public function isPhone($phone = "", $is_required = false)
    {
        // general
        if (!$this->isSafe($phone)) {
            return false;
        }

        $phone = Phone::clear($phone);
        if (empty($phone)) {
            return !$is_required;
        }
        // for phone
        return Phone::isValid($phone);
    }

    /**
     * @param string $name
     * @param bool $is_required
     * if $name is empty AND !$is_required return true
     * @return bool
     */
    public function isName($name = "", $is_required = false)
    {
        // general
        if (!$this->isSafe($name)) {
            return false;
        }
        if (empty($name)) {
            return !$is_required;
        }
        // for name
        // ...
        return true;
    }
    
    public function isDomain($url = "", $is_required = false)
    {
        // general
        if (!$this->isSafe($url)) {
            return false;
        }
        if (empty($url)) {
            return !$is_required;
        }
        if (!preg_match("/^(?:https?://)?[a-zA-Zа-яА-Я\d]+?(?:-+[a-zA-Zа-яА-Я\d]+?)*\.(?:[a-zA-Zа-яА-Я\d.]+?(?:-+[a-zA-Zа-яА-Я\d]+?)*)+/?$/ui", $url)) {
            return false;
        }
        return true;
    }

    public function isAddress($address = "", $is_required = false)
    {
        // general
        return $this->isSafe($address, $is_required);
        // ...
        //return true;
    }

    public function isComment($comment = "", $is_required = false)
    {
        // general
        return $this->isSafe($comment, $is_required);
        // ...
        //return true;
    }

    /**
     * @param string $src
     * @param bool $is_required
     * if $src is empty AND $is_required return false
     * @return bool
     */
    public function isSafe($src = "", $is_required = false)
    {
        if (!empty($src)) {
            foreach ($this->denied as $item) {
                if (strpos($src, $item) !== false) {
                    return false;
                }
            }
        } elseif ($is_required) {
            return false;
        }
        return true;
    }

    public function verifyCaptcha($form, $captcha_code = '')
    {
        if ($this->settings->$form) {
            if ($this->settings->captcha_type == 'default'){
                if ($_SESSION[$form] != $captcha_code || empty($captcha_code)){
                    return false;
                }
                return true;
            } elseif ($this->settings->captcha_type == 'v2' 
                || $this->settings->captcha_type == 'invisible'
                || $this->settings->captcha_type == 'v3'){
                return $this->recaptcha->check();
            }
        }
        return true;
    }

}