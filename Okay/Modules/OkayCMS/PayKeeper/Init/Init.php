<?php


namespace Okay\Modules\OkayCMS\PayKeeper\Init;


use Okay\Core\Modules\AbstractInit;

class Init extends AbstractInit
{
    
    public function install()
    {
        $this->setModuleType(MODULE_TYPE_PAYMENT);
        $this->setBackendMainController('DescriptionAdmin');
    }

    public function init(){
        $this->addPermission('okaycms__pay_keeper');

        $this->registerBackendController('DescriptionAdmin');
        $this->addBackendControllerPermission('DescriptionAdmin', 'pay_keeper');
    }
}