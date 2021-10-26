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
        $filter['sort'] = $this->request->get('sort', 'string');
        if (empty($filter['sort'])) {
            $filter['sort'] = 'label';
        }

        $this->design->assign('sort', $filter['sort']);

        $allTranslations = $translationsEntity->find($filter);

        $filter['lang'] = $language->label;
        $currentTranslations = $translationsEntity->find($filter);

        foreach ($currentTranslations as $id => $cTranslation) {
            $cTranslation->has_module_translations = false;
            foreach ($allTranslations as $aTranslation) {
                if (isset($aTranslation[$id]) && isset($aTranslation[$id]->module)) {
                    $cTranslation->has_module_translations = true;
                    continue 2;
                }
            }
        }
        
        $this->design->assign('current_translations',  $currentTranslations);
        $this->design->assign('all_translations',      $allTranslations);

        $this->response->setContent($this->design->fetch('translations.tpl'));
    }
    
}
