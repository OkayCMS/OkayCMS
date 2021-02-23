<?php


namespace Okay\Modules\OkayCMS\FastOrder\Init;


use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Core\Modules\AbstractInit;
use Okay\Modules\OkayCMS\FastOrder\Extenders\BackendExtender;

class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init()
    {
        $this->addPermission('okaycms__fast_order');

        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'okaycms__fast_order');

        $this->addFrontBlock('front_after_footer_content', 'fast_order_form.tpl');
        
        $this->registerChainExtension(
            [BackendSettingsHelper::class, 'updateGeneralSettings'],
            [BackendExtender::class, 'updateSettings']
        );
    }
}