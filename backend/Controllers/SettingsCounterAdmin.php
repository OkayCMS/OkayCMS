<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendSettingsHelper;
use Okay\Admin\Requests\BackendSettingsRequest;

class SettingsCounterAdmin extends IndexAdmin
{
    public function fetch(
        BackendSettingsRequest $settingsRequest,
        BackendSettingsHelper  $backendSettingsHelper
    ){
        if ($this->request->method('POST')) {
            $counters = $settingsRequest->postCounters();
            $backendSettingsHelper->updateCounters($counters);
            $this->design->assign('message_success', 'saved');
        }

        $counters = $backendSettingsHelper->findCounters();
        $this->design->assign('counters', $counters);

        $this->response->addHeader('X-XSS-Protection:0');
        $this->response->setContent($this->design->fetch('settings_counter.tpl'));
    }
}
