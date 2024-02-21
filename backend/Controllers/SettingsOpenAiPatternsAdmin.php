<?php


namespace Okay\Admin\Controllers;


use Giggsey\Locale\Locale;
use libphonenumber\PhoneNumberUtil;
use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Core\BackendTranslations;
use Okay\Core\Phone;

class SettingsOpenAiPatternsAdmin extends IndexAdmin
{
    public function fetch()
    {
        if ($this->request->method('post')) {

            $this->settings->set('chatgpt_generate_api_key', $this->request->post('chatgpt_generate_api_key', null, ''));

            if ($this->request->post('settings_open_ai_success', null, 0) == 1) {
                $this->settings->update('settings_open_ai_patterns_product_meta_title', strip_tags($this->request->post('settings_open_ai_patterns_product_meta_title', null, '')));
                $this->settings->update('settings_open_ai_patterns_product_meta_keywords', strip_tags($this->request->post('settings_open_ai_patterns_product_meta_keywords', null, '')));
                $this->settings->update('settings_open_ai_patterns_product_meta_description', strip_tags($this->request->post('settings_open_ai_patterns_product_meta_description', null, '')));
                $this->settings->update('settings_open_ai_patterns_product_annotation', strip_tags($this->request->post('settings_open_ai_patterns_product_annotation', null, '')));
                $this->settings->update('settings_open_ai_patterns_product_description', strip_tags($this->request->post('settings_open_ai_patterns_product_description', null, '')));

                $this->settings->update('settings_open_ai_patterns_category_meta_title', strip_tags($this->request->post('settings_open_ai_patterns_category_meta_title', null, '')));
                $this->settings->update('settings_open_ai_patterns_category_meta_keywords', strip_tags($this->request->post('settings_open_ai_patterns_category_meta_keywords', null, '')));
                $this->settings->update('settings_open_ai_patterns_category_meta_description', strip_tags($this->request->post('settings_open_ai_patterns_category_meta_description', null, '')));
                $this->settings->update('settings_open_ai_patterns_category_meta_h1', strip_tags($this->request->post('settings_open_ai_patterns_category_meta_h1', null, '')));
                $this->settings->update('settings_open_ai_patterns_category_annotation', strip_tags($this->request->post('settings_open_ai_patterns_category_annotation', null, '')));
                $this->settings->update('settings_open_ai_patterns_category_description', strip_tags($this->request->post('settings_open_ai_patterns_category_description', null, '')));

                $this->settings->update('settings_open_ai_patterns_brand_meta_title', strip_tags($this->request->post('settings_open_ai_patterns_brand_meta_title', null, '')));
                $this->settings->update('settings_open_ai_patterns_brand_meta_keywords', strip_tags($this->request->post('settings_open_ai_patterns_brand_meta_keywords', null, '')));
                $this->settings->update('settings_open_ai_patterns_brand_meta_description', strip_tags($this->request->post('settings_open_ai_patterns_brand_meta_description', null, '')));
                $this->settings->update('settings_open_ai_patterns_brand_meta_h1', strip_tags($this->request->post('settings_open_ai_patterns_brand_meta_h1', null, '')));
                $this->settings->update('settings_open_ai_patterns_brand_annotation', strip_tags($this->request->post('settings_open_ai_patterns_brand_annotation', null, '')));
                $this->settings->update('settings_open_ai_patterns_brand_description', strip_tags($this->request->post('settings_open_ai_patterns_brand_description', null, '')));
            }

            $this->design->assign('message_success', 'saved');
        }
        
        $this->response->setContent('settings_ai_patterns.tpl');
    }

    public function clear(string $string) {

        if (!empty($string)) {
            return null;
        }

        return strval(preg_replace('/[^\p{L}\p{Nd}\d\s%]/ui', '', $string));

    }
}