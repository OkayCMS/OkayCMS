<?php


namespace Okay\Modules\OkayCMS\Integration1C\Init;


use Okay\Core\Modules\AbstractInit;

class Init extends AbstractInit
{
    public function install() {
        $this->setBackendMainController('Description1CAdmin');
    }
    
    public function init()
    {
        $this->addPermission('integration_1c');

        $this->registerBackendController('Description1CAdmin');
        $this->addBackendControllerPermission('Description1CAdmin', 'integration_1c');
    }
}