<?php


namespace Okay\Admin\Controllers;


use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\TranslationsEntity;

class TranslationAdmin extends IndexAdmin
{

    /*Работа с переводом*/
    public function fetch(TranslationsEntity $translationsEntity, FrontTemplateConfig $frontTemplateConfig, LanguagesEntity $languagesEntity)
    {
        
        $languages = $languagesEntity->find();
        
        $locked_theme = is_file('design/' . $frontTemplateConfig->getTheme() . '/locked');
        $this->design->assign('locked_theme', $locked_theme);

        $translation = new \stdClass();
        if(!$locked_theme && $this->request->method('post')) {
            // id - предыдущий label
            $translation->id    = $this->request->post('id');
            $translation->label = trim($this->request->post('label'));
            $translation->label = str_replace(" ", '_', $translation->label);
            $translation->label = preg_replace("/[^a-z0-9\-_]/i", "", $translation->label);
            
            if ($languages){
                foreach($languages as $lang) {
                    $field = 'lang_'.$lang->label;
                    $translation->$field = $this->request->post($field);
                    $translation->values[$lang->id] = $translation->$field;
                }
            }
            $exist = $translationsEntity->templateOnly(true)->get($translation->label);
            
            if(!$translation->label) {
                $this->design->assign('message_error', 'label_empty');
            } elseif($exist && $exist->id!=$translation->id) {
                $this->design->assign('message_error', 'label_exists');
            } elseif(false) { // todo возможно сделать проверку, чтобы переменная не была класом
                $this->design->assign('message_error', 'label_is_class');
            } else {
                /*Добавление/Удаление перевода*/
                if(empty($translation->id)) {
                    $this->design->assign('message_success', 'added');
                } else {
                    $this->design->assign('message_success', 'updated');
                }
                $translation->id = $translationsEntity->update($translation->id, $translation);
            }
        } else {
            $translation->id = $this->request->get('id');
        }

        if(!empty($translation->id)) {
            $translation = $translationsEntity->get($translation->id);
        }
        
        $this->design->assign('languages', $languages);
        
        $this->design->assign('translation', $translation);
        $this->response->setContent($this->design->fetch('translation.tpl'));
    }
    
}
