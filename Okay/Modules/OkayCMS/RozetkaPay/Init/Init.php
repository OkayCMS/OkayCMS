<?php


namespace Okay\Modules\OkayCMS\RozetkaPay\Init;


use Okay\Core\Modules\AbstractInit;
class Init extends AbstractInit
{
    
    public function install()
    {
        $this->setModuleType(MODULE_TYPE_PAYMENT);
        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init()
    {
        $this->addPermission('okaycms__rozetkapay');

        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'okaycms__rozetkapay');
        $this->registerBackendController('RefundAdmin');
        $this->addBackendControllerPermission('RefundAdmin', 'okaycms__rozetkapay');
    }
}