<?php


namespace Okay\Modules\OkayCMS\AdminGuide\Init;


use Okay\Core\Modules\AbstractInit;


class Init extends AbstractInit
{
    public function install()
    {
        $this->setBackendMainController('AdminGuideAdmin');
    }

    public function init()
    {
        $this->registerBackendController('AdminGuideAdmin');
        $this->addBackendControllerPermission('AdminGuideAdmin', 'okaycms__admin_guide');
    }

}