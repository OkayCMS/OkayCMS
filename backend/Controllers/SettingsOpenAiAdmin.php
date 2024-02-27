<?php


namespace Okay\Admin\Controllers;


use Okay\Admin\Helpers\BackendSettingsHelper;

class SettingsOpenAiAdmin extends IndexAdmin
{
    public function fetch(
        BackendSettingsHelper  $backendSettingsHelper
    ) {
        if ($this->request->method('post')) {
            $backendSettingsHelper->updateOpenAiSettings();
            $this->design->assign('message_success', 'saved');
        }
        $this->response->setContent($this->design->fetch('settings_open_ai.tpl'));
    }
}