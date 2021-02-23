<?php


namespace Okay\Modules\OkayCMS\FAQ\Controllers;


use Okay\Modules\OkayCMS\FAQ\Entities\FAQEntity;
use Okay\Controllers\AbstractController;
use Okay\Core\EntityFactory;

class FAQController extends AbstractController
{
    public function render(EntityFactory $entityFactory)
    {
        /** @var FAQEntity $FAQEntity */
        $FAQEntity = $entityFactory->get(FAQEntity::class);

        $faqs = $FAQEntity->find(['visible' => 1]);
        $this->design->assign('faqs', $faqs);

        if ($this->page) {
            $this->design->assign('meta_title', $this->page->meta_title);
            $this->design->assign('meta_keywords', $this->page->meta_keywords);
            $this->design->assign('meta_description', $this->page->meta_description);
            $this->design->assign('breadcrumbs', [$this->page->name]);
        }
        
        $this->response->setContent('faq.tpl');
    }
}
