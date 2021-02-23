<?php


namespace Okay\Modules\OkayCMS\FAQ\Init;


use Okay\Core\Modules\EntityField;
use Okay\Core\Modules\AbstractInit;
use Okay\Modules\OkayCMS\FAQ\Entities\FAQEntity;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('FAQsAdmin');
        $this->migrateEntityTable(FAQEntity::class, [
            (new EntityField('id'))->setIndexPrimaryKey()->setTypeInt(11, false)->setAutoIncrement(),
            (new EntityField('question'))->setTypeText()->setIsLang(),
            (new EntityField('answer'))->setTypeText()->setIsLang()->setNullable(),
            (new EntityField('visible'))->setTypeTinyInt(1),
            (new EntityField('position'))->setTypeInt(11),
        ]);
    }
    
    public function init()
    {
        $this->registerBackendController('FAQsAdmin');
        $this->addBackendControllerPermission('FAQsAdmin', 'okaycms__faq__faq');

        $this->registerBackendController('FAQAdmin');
        $this->addBackendControllerPermission('FAQAdmin', 'okaycms__faq__faq');

        $this->extendUpdateObject('OkayCMS.FAQ.FAQEntity', 'okaycms__faq__faq', FAQEntity::class);

        $this->extendBackendMenu('left_faq_title', [
            'left_faq_title' => ['FAQsAdmin', 'FAQAdmin'],
        ]);
    }
}