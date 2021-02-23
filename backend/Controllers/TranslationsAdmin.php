<?php


namespace Okay\Admin\Controllers;


use Okay\Core\Languages;
use Okay\Core\TemplateConfig\FrontTemplateConfig;
use Okay\Entities\LanguagesEntity;
use Okay\Entities\TranslationsEntity;

class TranslationsAdmin extends IndexAdmin
{
    
    public function fetch(
        TranslationsEntity $translationsEntity,
        FrontTemplateConfig $frontTemplateConfig,
        LanguagesEntity $languagesEntity,
        Languages $languagesCore
    ) {
        
        $lockedTheme = is_file('design/' . $frontTemplateConfig->getTheme() . '/locked');
        $this->design->assign('locked_theme', $lockedTheme);

        // Обработка действий
        if (!$lockedTheme && $this->request->method('post')) {
            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch($this->request->post('action')) {
                    case 'delete': {
                        /*Удалить перевод*/
                        $translationsEntity->delete($ids);
                        break;
                    }
                }
            }
        }
        $language = $languagesEntity->get($languagesCore->getLangId());

        $filter = [];
        $filter['lang'] = $language->label;
        $filter['sort'] = $this->request->get('sort', 'string');
        if (empty($filter['sort'])) {
            $filter['sort'] = 'label';
        }
        $this->design->assign('sort', $filter['sort']);
        $template_filter = $filter;
        $translations = $translationsEntity->find($filter);
        $template_filter['template_only'] = $template_filter['force'] = true;
        // Нам нужно будет использовать их как массив
        $translations_template = (array)$translationsEntity->find($template_filter);
        
        $this->design->assign('translations', $translations);
        $this->design->assign('translations_template', $translations_template);

        $this->response->setContent($this->design->fetch('translations.tpl'));
    }
    
}
