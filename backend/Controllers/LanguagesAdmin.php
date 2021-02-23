<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\LanguagesEntity;

class LanguagesAdmin extends IndexAdmin
{
    
    public function fetch(LanguagesEntity $languagesEntity) {
        // Обработка действий
        if ($this->request->method('post')) {
            // Действия с выбранными
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'delete': {
                        /*Удаление языка*/
                        $languagesEntity->delete($ids);
                        break;
                    }
                    case 'disable': {
                        /*Выключение языка*/
                        $languagesEntity->update($ids, ['enabled'=>0]);
                        break;
                    }
                    case 'enable': {
                        /*Включение языка*/
                        $languagesEntity->update($ids, ['enabled'=>1]);
                        break;
                    }
                }
            }
            
            // Сортировка
            $positions = $this->request->post('positions');
            foreach($positions as $position=>$id) {
                $languagesEntity->update($id, ['position'=>$position+1]);
            }
        }
        
        $languages = $languagesEntity->find();
        $this->design->assign('languages', $languages);

        $this->response->setContent($this->design->fetch('languages.tpl'));
    }
    
}
