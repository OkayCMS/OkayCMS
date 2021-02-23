<?php


namespace Okay\Admin\Controllers;


use Okay\Entities\SubscribesEntity;

class SubscribeMailingAdmin extends IndexAdmin
{
    
    private $export_files_dir = 'backend/files/export_users/';

    /*Отображение подписчиков сайта*/
    public function fetch(SubscribesEntity $subscribesEntity)
    {
        /*Экспорт подписчиков*/
        if ($this->request->post('is_export')) {
            $this->design->assign('export_files_dir', $this->export_files_dir);
            $this->design->assign('sort', $this->request->get('sort'));
            $this->design->assign('keyword', $this->request->get('keyword'));
            $this->design->assign('export_files_dir', $this->export_files_dir);
            if (!is_writable($this->export_files_dir)) {
                $this->design->assign('message_error', 'no_permission');
            }
            $this->response->setContent($this->design->fetch('export_subscribes.tpl'));
            return ;
        }
        if ($this->request->method('post')) {
            $ids = $this->request->post('check');
            
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'delete': {
                        /*Удалить подписчика*/
                        $subscribesEntity->delete($ids);
                        break;
                    }
                }
            }
        }
        
        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;
        // Поиск
        $keyword = $this->request->get('keyword');
        if(!empty($keyword)) {
            $filter['keyword'] = $keyword;
            $this->design->assign('keyword', $keyword);
        }
        $subscribesCount = $subscribesEntity->count($filter);
        // Показать все страницы сразу
        if ($this->request->get('page') == 'all') {
            $filter['limit'] = $subscribesCount;
        }
        
        if ($filter['limit']>0) {
            $pagesCount = ceil($subscribesCount/$filter['limit']);
        } else {
            $pagesCount = 0;
        }
        $filter['page'] = min($filter['page'], $pagesCount);
        $this->design->assign('pages_count', $pagesCount);
        $this->design->assign('current_page', $filter['page']);
        
        $subscribes = $subscribesEntity->find($filter);
        
        $this->design->assign('subscribes', $subscribes);
        $this->design->assign('subscribes_count', $subscribesCount);
        $this->response->setContent($this->design->fetch('subscribe_mailing.tpl'));
    }
    
}
