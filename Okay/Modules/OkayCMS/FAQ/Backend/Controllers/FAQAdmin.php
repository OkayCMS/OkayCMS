<?php


namespace Okay\Modules\OkayCMS\FAQ\Backend\Controllers;


use Okay\Modules\OkayCMS\FAQ\Entities\FAQEntity;
use Okay\Admin\Controllers\IndexAdmin;
use Okay\Core\EntityFactory;

class FAQAdmin extends IndexAdmin
{
    public function fetch(EntityFactory $entityFactory)
    {
        /** @var FAQEntity $FAQEntity */
        $FAQEntity = $entityFactory->get(FAQEntity::class);

        $faq = new \stdClass();
        if ($this->request->method('post')) {
            $faq->id       = $this->request->post('id', 'integer');
            $faq->question = $this->request->post('question');
            $faq->visible  = $this->request->post('visible', 'boolean');
            $faq->answer   = $this->request->post('answer');

            if (empty($faq->id)) {
                $faq->id = $FAQEntity->add($faq);
                $faq = $FAQEntity->get($faq->id);
                $this->design->assign('message_success', 'added');
            } else {
                $FAQEntity->update($faq->id, $faq);
                $faq = $FAQEntity->get($faq->id);
                $this->design->assign('message_success', 'updated');
            }
        } else {
            $faq->id = $this->request->get('id', 'integer');
            $faq = $FAQEntity->get(intval($faq->id));
        }

        $this->design->assign('faq', $faq);
        $this->response->setContent($this->design->fetch('faq.tpl'));
    }
}