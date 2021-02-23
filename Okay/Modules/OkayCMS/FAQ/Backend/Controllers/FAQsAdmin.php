<?php


namespace Okay\Modules\OkayCMS\FAQ\Backend\Controllers;


use Okay\Modules\OkayCMS\FAQ\Entities\FAQEntity;
use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\EntityFactory;

class FAQsAdmin extends IndexAdmin
{
    public function fetch(EntityFactory $entityFactory)
    {
        /** @var FAQEntity $FAQEntity */
        $FAQEntity = $entityFactory->get(FAQEntity::class);

        if ($this->request->method('post')) {
            $ids = $this->request->post('check');
            if (is_array($ids)) {
                switch ($this->request->post('action')) {
                    case 'disable': {
                        $FAQEntity->update($ids, ['visible'=>0]);
                        break;
                    }
                    case 'enable': {
                        $FAQEntity->update($ids, ['visible'=>1]);
                        break;
                    }
                    case 'delete': {
                        $FAQEntity->delete($ids);
                        break;
                    }
                }
            }

            // Сортировка
            $positions = $this->request->post('positions');
            if (!empty($positions)) {
                $ids = array_keys($positions);
                sort($positions);
                foreach($positions as $i=>$position) {
                    $FAQEntity->update($ids[$i], ['position'=>$position]);
                }
            }
        }

        $filter = [];
        $filter['page'] = max(1, $this->request->get('page', 'integer'));
        $filter['limit'] = 20;

        $keyword = $this->request->get('keyword', 'string');
        if(!empty($keyword)) {
            $filter['keyword'] = $keyword;
            $this->design->assign('keyword', $keyword);
        }

        $faqs_count = $FAQEntity->count($filter);
        if($this->request->get('page') == 'all') {
            $filter['limit'] = $faqs_count;
        }

        $faqs = $FAQEntity->find($filter);
        $this->design->assign('faqs_count', $faqs_count);
        $this->design->assign('pages_count', ceil($faqs_count/$filter['limit']));
        $this->design->assign('current_page', $filter['page']);
        $this->design->assign('faqs', $faqs);
        $this->response->setContent($this->design->fetch('faqs.tpl'));
    }
}