<?php

namespace Okay\Core;

class Recaptcha
{

    private $settings;

    private $request;

    private $secret_key;
    private $url = 'https://www.google.com/recaptcha/api/siteverify';

    public function __construct(Settings $settings, Request $request)
    {
        $this->settings = $settings;
        $this->request  = $request;
        
        switch ($this->settings->captcha_type) {
            case 'invisible':
                $this->secret_key = $this->settings->secret_recaptcha_invisible;
                break;
            case 'v2':
                $this->secret_key = $this->settings->secret_recaptcha;
                break;
            case 'v3':
                $this->secret_key = $this->settings->secret_recaptcha_v3;
                break;
        }
    }

    public function check()
    {
        $response = $this->request();
        // В случае инвалидных ключей пропускаем пользователя
        if (isset($response['error-codes']) && reset($response['error-codes']) == 'invalid-input-secret') {
            return true; // TODO add to events list
        }
        
        if ($response['success'] == false) {
            return false;
        }
        
        // Для третей версии нужно дополнительно определить можно ли пропускать с таким уровнем "человечности"
        if ($this->settings->captcha_type == 'v3') {
            return $this->calcIsHumanV3($response);
        }
        
        return true;
    }
    
    private function calcIsHumanV3($response)
    {
        
        $action = $response['action'];
        $score  = (float)$response['score'];
        switch ($action) {
            case 'cart':
                $min_score = (float)$this->settings->recaptcha_scores['cart'];
                break;
            case 'product':
                $min_score = (float)$this->settings->recaptcha_scores['product'];
                break;
            default:
                $min_score = (float)$this->settings->recaptcha_scores['other'];
        }

        return $min_score <= $score;
    }
    
    private function request()
    {
        $curl = curl_init($this->url);

        $params = http_build_query(array(
            'secret'   => $this->secret_key,
            'response' => $this->getResponseKey(),
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ));

        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        
        return json_decode($response, true);
    }

    private function getResponseKey()
    {
        if ($this->settings->captcha_type == 'v2' || $this->settings->captcha_type == 'invisible'){
            return $this->request->post('g-recaptcha-response');
        } 
        
        if ($this->settings->captcha_type == 'v3'){
            return $this->request->post('recaptcha_token');
        }
    }
    
}